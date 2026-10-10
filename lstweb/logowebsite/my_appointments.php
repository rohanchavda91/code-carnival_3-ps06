<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);

$isLoggedIn = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0 && ($_SESSION['user_type'] ?? '') === 'user';
$currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : 0;
$sessionBookingIds = array_filter(array_map('intval', $_SESSION['placed_bookings'] ?? []));

$flashMsg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

// Handle Appointment Cancellation by User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    $cancelId = (int)($_POST['booking_id'] ?? 0);

    if ($cancelId > 0) {
        $allowed = false;
        if ($currentUserId > 0) {
            $chk = mysqli_prepare($conn, "SELECT status FROM bookings WHERE booking_id = ? AND user_id = ? LIMIT 1");
            if ($chk) {
                mysqli_stmt_bind_param($chk, "ii", $cancelId, $currentUserId);
                mysqli_stmt_execute($chk);
                $res = mysqli_stmt_get_result($chk);
                if ($row = mysqli_fetch_assoc($res)) {
                    $curSt = strtolower(trim((string)$row['status']));
                    if (in_array($curSt, ['pending', 'confirmed'], true)) {
                        $allowed = true;
                    }
                }
                mysqli_stmt_close($chk);
            }
        } elseif (in_array($cancelId, $sessionBookingIds, true)) {
            $res = mysqli_query($conn, "SELECT status FROM bookings WHERE booking_id = $cancelId LIMIT 1");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $curSt = strtolower(trim((string)$row['status']));
                if (in_array($curSt, ['pending', 'confirmed'], true)) {
                    $allowed = true;
                }
            }
        }

        if ($allowed) {
            $up = mysqli_prepare($conn, "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
            if ($up) {
                mysqli_stmt_bind_param($up, "i", $cancelId);
                mysqli_stmt_execute($up);
                mysqli_stmt_close($up);
                $_SESSION['flash_msg'] = [
                    'type' => 'success',
                    'msg' => "Appointment #BK-$cancelId has been cancelled."
                ];
            }
        } else {
            $_SESSION['flash_msg'] = [
                'type' => 'error',
                'msg' => "This appointment cannot be cancelled or was not found."
            ];
        }
    }

    header("Location: my_appointments.php");
    exit;
}

// Fetch Appointments
$bookingConditions = [];
if ($currentUserId > 0) {
    if (!empty($sessionBookingIds)) {
        $bookingConditions[] = "(user_id = $currentUserId OR booking_id IN (" . implode(',', $sessionBookingIds) . "))";
    } else {
        $bookingConditions[] = "user_id = $currentUserId";
    }
} elseif (!empty($sessionBookingIds)) {
    $bookingConditions[] = "booking_id IN (" . implode(',', $sessionBookingIds) . ")";
} else {
    $bookingConditions[] = "1 = 0";
}

$statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
$searchTerm = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM bookings WHERE " . implode(' AND ', $bookingConditions) . " ORDER BY booking_id DESC";
$result = mysqli_query($conn, $sql);

$appointments = [];
$totalUserAppts = 0;
$pendingUserAppts = 0;
$confirmedUserAppts = 0;
$completedUserAppts = 0;
$cancelledUserAppts = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $st = strtolower(trim((string)($row['status'] ?? 'pending')));
        $totalUserAppts++;
        if ($st === 'pending') $pendingUserAppts++;
        elseif ($st === 'confirmed') $confirmedUserAppts++;
        elseif ($st === 'completed') $completedUserAppts++;
        elseif ($st === 'cancelled') $cancelledUserAppts++;

        $matchesStatus = ($statusFilter === 'all') || ($statusFilter === $st);
        $matchesSearch = ($searchTerm === '') ||
            (stripos($row['service'] ?? '', $searchTerm) !== false) ||
            (stripos((string)$row['booking_id'], $searchTerm) !== false) ||
            (stripos($row['staff'] ?? '', $searchTerm) !== false);

        if ($matchesStatus && $matchesSearch) {
            $appointments[] = $row;
        }
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= APPOINTMENTS BANNER ================= -->
<section class="myorder-banner">
    <div class="myorder-banner-content">
        <h1>My Salon Appointments</h1>
        <p>Track, manage, and review your beauty session bookings in real time.</p>
    </div>
</section>

<!-- ================= APPOINTMENTS CONTENT ================= -->
<section class="myorder-section" style="padding: 40px 20px 60px; max-width: 1200px; margin: 0 auto;">

    <!-- TOP NAVIGATION PILL TABS -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 25px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="my_appointments.php" class="btn" style="background: #ff4f81; color: #fff; text-decoration: none; padding: 8px 18px; border-radius: 25px; font-size: 14px; font-weight: 500;">
                <i class="fa-solid fa-calendar-check"></i> My Appointments (<?php echo $totalUserAppts; ?>)
            </a>
            <a href="myorder.php" class="outline-btn" style="text-decoration: none; padding: 8px 18px; border-radius: 25px; font-size: 14px; font-weight: 500; color: #555; border: 1px solid #ddd;">
                <i class="fa-solid fa-truck-fast"></i> My Orders
            </a>
        </div>
        <a href="booking.php" class="btn" style="background: #2d3436; color: #fff; text-decoration: none; padding: 8px 18px; border-radius: 25px; font-size: 13px; font-weight: 500;">
            <i class="fa-solid fa-plus"></i> Book New Session
        </a>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if ($flashMsg): ?>
        <div class="checkout-alert <?php echo ($flashMsg['type'] === 'error') ? 'checkout-alert-error' : 'checkout-alert-success'; ?>" style="margin-bottom: 20px; border-radius: 10px;">
            <i class="fa-solid <?php echo ($flashMsg['type'] === 'error') ? 'fa-circle-exclamation' : 'fa-circle-check'; ?>"></i>
            <div><?php echo htmlspecialchars($flashMsg['msg']); ?></div>
        </div>
    <?php endif; ?>

    <!-- FILTER BAR & SEARCH -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; background: #fff; padding: 16px 20px; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); border: 1px solid #f0e5eb;">
        <!-- Status Filter Links -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="my_appointments.php?status=all<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'all' ? '#ff4f81' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'all' ? '#ff4f81' : '#fff'; ?>; color: <?php echo $statusFilter === 'all' ? '#fff' : '#555'; ?>;">
                All (<?php echo $totalUserAppts; ?>)
            </a>
            <a href="my_appointments.php?status=pending<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'pending' ? '#d97706' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'pending' ? '#d97706' : '#fff'; ?>; color: <?php echo $statusFilter === 'pending' ? '#fff' : '#555'; ?>;">
                Pending (<?php echo $pendingUserAppts; ?>)
            </a>
            <a href="my_appointments.php?status=confirmed<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'confirmed' ? '#059669' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'confirmed' ? '#059669' : '#fff'; ?>; color: <?php echo $statusFilter === 'confirmed' ? '#fff' : '#555'; ?>;">
                Confirmed (<?php echo $confirmedUserAppts; ?>)
            </a>
            <a href="my_appointments.php?status=completed<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'completed' ? '#0284c7' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'completed' ? '#0284c7' : '#fff'; ?>; color: <?php echo $statusFilter === 'completed' ? '#fff' : '#555'; ?>;">
                Completed (<?php echo $completedUserAppts; ?>)
            </a>
            <a href="my_appointments.php?status=cancelled<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'cancelled' ? '#dc2626' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'cancelled' ? '#dc2626' : '#fff'; ?>; color: <?php echo $statusFilter === 'cancelled' ? '#fff' : '#555'; ?>;">
                Cancelled (<?php echo $cancelledUserAppts; ?>)
            </a>
        </div>

        <!-- Search Input -->
        <form method="GET" action="my_appointments.php" style="display: flex; gap: 6px; align-items: center; margin: 0;">
            <?php if ($statusFilter !== 'all'): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
            <?php endif; ?>
            <div style="position: relative; display: flex; align-items: center;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; color: #888; font-size: 13px;"></i>
                <input type="text" name="search" placeholder="Search service or #ID..." value="<?php echo htmlspecialchars($searchTerm); ?>" style="padding: 7px 12px 7px 32px; border: 1px solid #ddd; border-radius: 20px; font-size: 13px; outline: none; min-width: 180px;">
            </div>
            <button type="submit" class="btn" style="padding: 6px 12px; font-size: 12px; border-radius: 20px; background: #ff4f81; color: #fff;">Search</button>
        </form>
    </div>

    <!-- APPOINTMENTS LIST / CARDS -->
    <?php if (empty($appointments)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px solid #f0e5eb; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
            <div style="width: 70px; height: 70px; margin: 0 auto 16px; background: #fff0f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ff4f81; font-size: 28px;">
                <i class="fa-regular fa-calendar-xmark"></i>
            </div>
            <h3 style="font-size: 20px; color: #2d3436; margin-bottom: 8px;">No Appointments Found</h3>
            <?php if (!$isLoggedIn && empty($sessionBookingIds)): ?>
                <p style="color: #666; font-size: 14px; max-width: 450px; margin: 0 auto 20px;">You are not logged in. Please log in to view your booked beauty appointments.</p>
                <a href="login.php" class="btn" style="padding: 10px 24px; text-decoration: none; border-radius: 25px; background: #ff4f81; color: #fff; font-weight: 500;">
                    <i class="fa-solid fa-right-to-bracket"></i> Login Now
                </a>
            <?php else: ?>
                <p style="color: #666; font-size: 14px; max-width: 450px; margin: 0 auto 20px;">You haven't scheduled any appointments matching this view yet.</p>
                <a href="booking.php" class="btn" style="padding: 10px 24px; text-decoration: none; border-radius: 25px; background: #ff4f81; color: #fff; font-weight: 500;">
                    <i class="fa-solid fa-calendar-plus"></i> Book an Appointment
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 18px;">
            <?php foreach ($appointments as $app): ?>
                <?php
                $st = strtolower(trim((string)$app['status']));
                $badgeBg = '#fff8e6';
                $badgeColor = '#d97706';
                $badgeBorder = '#fde68a';
                $badgeIcon = 'fa-hourglass-half';

                if ($st === 'confirmed') {
                    $badgeBg = '#ecfdf5';
                    $badgeColor = '#059669';
                    $badgeBorder = '#a7f3d0';
                    $badgeIcon = 'fa-circle-check';
                } elseif ($st === 'completed') {
                    $badgeBg = '#f0f9ff';
                    $badgeColor = '#0284c7';
                    $badgeBorder = '#bae6fd';
                    $badgeIcon = 'fa-spa';
                } elseif ($st === 'cancelled') {
                    $badgeBg = '#fef2f2';
                    $badgeColor = '#dc2626';
                    $badgeBorder = '#fecaca';
                    $badgeIcon = 'fa-ban';
                }
                ?>
                <div class="appointment-card" style="background: #fff; border: 1px solid #f0e5eb; border-radius: 14px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; flex-direction: column; gap: 14px; transition: transform 0.2s ease;">
                    
                    <!-- Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #f7f1f4; padding-bottom: 12px;">
                        <div>
                            <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: #ff4f81; background: #fff0f5; padding: 3px 8px; border-radius: 6px;">
                                #BK-<?php echo $app['booking_id']; ?>
                            </span>
                            <span style="color: #888; font-size: 12px; margin-left: 8px;">
                                Booked on <?php echo date('d M Y, h:i A', strtotime($app['created_at'])); ?>
                            </span>
                        </div>
                        <!-- Status Badge (Part 3 Requirement) -->
                        <div>
                            <span class="status-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; border: 1px solid <?php echo $badgeBorder; ?>;">
                                <i class="fa-solid <?php echo $badgeIcon; ?>"></i>
                                <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                        <!-- Service & Staff -->
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff0f5; color: #ff4f81; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-spa"></i>
                            </div>
                            <div>
                                <small style="color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Service</small>
                                <h3 style="font-size: 16px; color: #2d3436; margin: 2px 0 4px;"><?php echo htmlspecialchars($app['service']); ?></h3>
                                <p style="font-size: 13px; color: #666; margin: 0;">Specialist: <b><?php echo htmlspecialchars($app['staff'] ?: 'Any Available Specialist'); ?></b></p>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-regular fa-calendar-check"></i>
                            </div>
                            <div>
                                <small style="color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Scheduled Date & Slot</small>
                                <div style="font-size: 15px; font-weight: 600; color: #2d3436; margin: 2px 0 4px;">
                                    <?php echo date('d M Y (l)', strtotime($app['booking_date'])); ?>
                                </div>
                                <p style="font-size: 13px; color: #666; margin: 0;">Time: <b><?php echo htmlspecialchars($app['booking_time']); ?></b></p>
                            </div>
                        </div>

                        <!-- Payment & Salon Location -->
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <small style="color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Payment & Venue</small>
                                <div style="font-size: 13px; font-weight: 600; color: #2d3436; margin: 2px 0 2px;">
                                    <?php echo htmlspecialchars($app['payment_method'] ?: 'Pay at Salon'); ?>
                                </div>
                                <p style="font-size: 12px; color: #777; margin: 0;">123 Beauty Street, Mumbai</p>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($app['message'])): ?>
                        <div style="background: #faf8f9; padding: 10px 14px; border-radius: 8px; font-size: 12px; color: #555; border-left: 3px solid #ff4f81;">
                            <strong>Your Note:</strong> <?php echo htmlspecialchars($app['message']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Card Actions -->
                    <?php if (in_array($st, ['pending', 'confirmed'], true)): ?>
                        <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f7f1f4; padding-top: 10px;">
                            <form method="POST" action="my_appointments.php" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" style="margin: 0;">
                                <input type="hidden" name="action" value="cancel_appointment">
                                <input type="hidden" name="booking_id" value="<?php echo $app['booking_id']; ?>">
                                <button type="submit" class="outline-btn" style="padding: 6px 14px; border-radius: 8px; font-size: 12px; color: #dc2626; border: 1px solid #fca5a5; background: #fff; cursor: pointer;">
                                    <i class="fa-solid fa-xmark"></i> Cancel Appointment
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<?php include("includes/footer.php"); ?>
