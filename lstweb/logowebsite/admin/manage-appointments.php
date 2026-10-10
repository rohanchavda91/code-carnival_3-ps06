<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php?msg=" . urlencode("Admin login required."));
    exit;
}

include("../config.php");
include_once("../includes/order_helpers.php");
ensure_store_tables($conn);

$flash_msg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

// 1. Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment_status'])) {
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');
    $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    if ($bookingId > 0 && in_array($newStatus, $validStatuses, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE booking_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $newStatus, $bookingId);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_msg'] = [
                    'type' => 'success',
                    'msg' => "Appointment #BK-$bookingId status updated to '$newStatus'."
                ];
            } else {
                $_SESSION['flash_msg'] = [
                    'type' => 'danger',
                    'msg' => "Failed to update appointment status: " . mysqli_error($conn)
                ];
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION['flash_msg'] = [
            'type' => 'danger',
            'msg' => "Invalid appointment ID or status selected."
        ];
    }

    $redirectUrl = "manage-appointments.php";
    if (!empty($_GET['status'])) {
        $redirectUrl .= "?status=" . urlencode($_GET['status']);
    }
    header("Location: $redirectUrl");
    exit;
}

// 2. Handle Delete Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appointment'])) {
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    if ($bookingId > 0) {
        $delStmt = mysqli_prepare($conn, "DELETE FROM bookings WHERE booking_id = ?");
        if ($delStmt) {
            mysqli_stmt_bind_param($delStmt, "i", $bookingId);
            if (mysqli_stmt_execute($delStmt)) {
                $_SESSION['flash_msg'] = [
                    'type' => 'success',
                    'msg' => "Appointment #BK-$bookingId was permanently deleted."
                ];
            } else {
                $_SESSION['flash_msg'] = [
                    'type' => 'danger',
                    'msg' => "Could not delete appointment: " . mysqli_error($conn)
                ];
            }
            mysqli_stmt_close($delStmt);
        }
    }

    header("Location: manage-appointments.php");
    exit;
}

// 3. Fetch Appointment Statistics
$apptStats = get_appointment_stats($conn);
$total_count = $apptStats['total'];
$pending_count = $apptStats['pending'];
$confirmed_count = $apptStats['confirmed'];
$completed_count = $apptStats['completed'];
$cancelled_count = $apptStats['cancelled'];

// Fetch contact messages stats for badge
$msgStats = get_contact_messages_stats($conn);

// 4. Filters & Search
$statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
$searchTerm = trim($_GET['search'] ?? '');

$whereClauses = [];
if ($statusFilter !== 'all' && $statusFilter !== '') {
    $safeStatus = mysqli_real_escape_string($conn, $statusFilter);
    $whereClauses[] = "LOWER(status) = '$safeStatus'";
}

if ($searchTerm !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $searchTerm);
    $numericSearch = preg_replace('/[^0-9]/', '', $searchTerm);
    $idCondition = $numericSearch !== '' ? "booking_id = " . (int)$numericSearch . " OR " : "";
    $whereClauses[] = "($idCondition fullname LIKE '%$safeSearch%' OR mobile LIKE '%$safeSearch%' OR email LIKE '%$safeSearch%' OR service LIKE '%$safeSearch%' OR staff LIKE '%$safeSearch%')";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(' AND ', $whereClauses) : "";
$query = "SELECT * FROM bookings $whereSql ORDER BY booking_id DESC";
$result = mysqli_query($conn, $query);

$appointments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments | GlowNest Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-page">

    <!-- MOBILE SIDEBAR BACKDROP -->
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay" onclick="toggleAdminSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="admin-logo" style="margin:0; padding:0 12px; font-size:26px;">GlowNest</h2>
                <p class="admin-label" style="margin:2px 0 0 0; padding:0 12px;">ADMIN DASHBOARD</p>
            </div>
            <button type="button" class="admin-sidebar-close" onclick="toggleAdminSidebar()" aria-label="Close sidebar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="sidebar-nav" style="margin-top: 24px;">
            <a href="index.php">
                <i class="fa-solid fa-gauge"></i>
                Dashboard
            </a>
            <a href="manage-appointments.php" class="active">
                <i class="fa-solid fa-calendar-check"></i>
                Manage Appointments
                <?php if ($pending_count > 0): ?>
                    <span class="badge-count badge-orange"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="manage-orders.php">
                <i class="fa-solid fa-truck-fast"></i>
                Manage Orders
            </a>
            <a href="manage-product.php">
                <i class="fa-solid fa-boxes-stacked"></i>
                Manage Products
            </a>
            <a href="add-product.php">
                <i class="fa-solid fa-plus-circle"></i>
                Add Product
            </a>
            <a href="manage-category.php">
                <i class="fa-solid fa-layer-group"></i>
                Manage Categories
            </a>
            <a href="contact-messages.php">
                <i class="fa-solid fa-envelope"></i>
                Contact Messages
                <?php if (($msgStats['new'] ?? 0) > 0): ?>
                    <span class="badge-count badge-pink"><?php echo $msgStats['new']; ?></span>
                <?php endif; ?>
            </a>
            <a href="user-logins.php">
                <i class="fa-solid fa-clock-rotate-left"></i>
                User Logins
            </a>
            <a href="../index.php" target="_blank">
                <i class="fa-solid fa-globe"></i>
                View Website
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Exit Admin
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <!-- TOP BAR -->
        <div class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" class="admin-mobile-toggle" onclick="toggleAdminSidebar()" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <p class="page-small-title">APPOINTMENT MANAGEMENT</p>
                    <h1>Salon Appointments</h1>
                </div>
            </div>
            <div>
                <a href="../booking.php" target="_blank" class="save-btn" style="text-decoration: none; padding: 9px 16px; border-radius: 8px; font-weight: 500; font-size: 13px; display: inline-flex; align-items: center; gap: 7px;">
                    <i class="fa-solid fa-plus"></i>
                    New Booking
                </a>
            </div>
        </div>

        <!-- FLASH NOTIFICATION BANNER -->
        <?php if ($flash_msg): ?>
            <div class="alert-banner alert-<?php echo htmlspecialchars($flash_msg['type'] ?? 'success'); ?>" style="margin-bottom: 20px;">
                <i class="fa-solid <?php echo ($flash_msg['type'] === 'danger') ? 'fa-circle-exclamation' : 'fa-circle-check'; ?>"></i>
                <span><?php echo htmlspecialchars($flash_msg['msg'] ?? ''); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <!-- STATS COUNTERS -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 22px;">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_count); ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange" style="background:#fff8e6; color:#d97706;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($pending_count); ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green" style="background:#ecfdf5; color:#059669;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($confirmed_count); ?></h3>
                    <p>Confirmed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#f0f9ff; color:#0284c7;">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($completed_count); ?></h3>
                    <p>Completed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef2f2; color:#dc2626;">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($cancelled_count); ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>
        </div>

        <!-- APPOINTMENTS TABLE CARD -->
        <div class="product-table-card">

            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>Manage Bookings</h2>
                    <p>Review appointments, update booking status, and view customer requests.</p>
                </div>

                <!-- CONTROLS: SEARCH & FILTERS -->
                <div class="table-controls">
                    <form method="GET" action="manage-appointments.php" class="search-input-box" style="margin: 0;">
                        <?php if ($statusFilter !== 'all'): ?>
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <?php endif; ?>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" placeholder="Search customer, phone, service..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </form>
                </div>
            </div>

            <!-- FILTER TABS -->
            <div class="order-filter-tabs" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f1f2f6;">
                <a href="manage-appointments.php?status=all<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                   class="tab-btn <?php echo $statusFilter === 'all' ? 'active' : ''; ?>"
                   style="padding: 7px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'all' ? '#ff4f81' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'all' ? '#ff4f81' : '#fff'; ?>; color: <?php echo $statusFilter === 'all' ? '#fff' : '#555'; ?>;">
                    All (<?php echo $total_count; ?>)
                </a>
                <a href="manage-appointments.php?status=pending<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                   class="tab-btn <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>"
                   style="padding: 7px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'pending' ? '#d97706' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'pending' ? '#d97706' : '#fff'; ?>; color: <?php echo $statusFilter === 'pending' ? '#fff' : '#555'; ?>;">
                    Pending (<?php echo $pending_count; ?>)
                </a>
                <a href="manage-appointments.php?status=confirmed<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                   class="tab-btn <?php echo $statusFilter === 'confirmed' ? 'active' : ''; ?>"
                   style="padding: 7px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'confirmed' ? '#059669' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'confirmed' ? '#059669' : '#fff'; ?>; color: <?php echo $statusFilter === 'confirmed' ? '#fff' : '#555'; ?>;">
                    Confirmed (<?php echo $confirmed_count; ?>)
                </a>
                <a href="manage-appointments.php?status=completed<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                   class="tab-btn <?php echo $statusFilter === 'completed' ? 'active' : ''; ?>"
                   style="padding: 7px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'completed' ? '#0284c7' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'completed' ? '#0284c7' : '#fff'; ?>; color: <?php echo $statusFilter === 'completed' ? '#fff' : '#555'; ?>;">
                    Completed (<?php echo $completed_count; ?>)
                </a>
                <a href="manage-appointments.php?status=cancelled<?php echo $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : ''; ?>" 
                   class="tab-btn <?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>"
                   style="padding: 7px 14px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid <?php echo $statusFilter === 'cancelled' ? '#dc2626' : '#e0e0e0'; ?>; background: <?php echo $statusFilter === 'cancelled' ? '#dc2626' : '#fff'; ?>; color: <?php echo $statusFilter === 'cancelled' ? '#fff' : '#555'; ?>;">
                    Cancelled (<?php echo $cancelled_count; ?>)
                </a>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="table-container" style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer Info</th>
                            <th>Service & Specialist</th>
                            <th>Date & Time Slot</th>
                            <th>Notes / Payment</th>
                            <th>Status</th>
                            <th style="min-width: 200px;">Change Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #888; padding: 36px 16px;">
                                    <i class="fa-regular fa-calendar-xmark" style="font-size: 32px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                                    No appointments found for the selected filter or search.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $app): ?>
                                <?php
                                $st = strtolower($app['status'] ?? 'pending');
                                $badgeColor = '#d97706';
                                $badgeBg = '#fff8e6';
                                if ($st === 'confirmed') {
                                    $badgeColor = '#059669';
                                    $badgeBg = '#ecfdf5';
                                } elseif ($st === 'completed') {
                                    $badgeColor = '#0284c7';
                                    $badgeBg = '#f0f9ff';
                                } elseif ($st === 'cancelled') {
                                    $badgeColor = '#dc2626';
                                    $badgeBg = '#fef2f2';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <strong style="color: #2d3436;">#BK-<?php echo $app['booking_id']; ?></strong>
                                        <br>
                                        <small style="color: #888; font-size: 11px;">
                                            <?php echo date('d M, h:i A', strtotime($app['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #2d3436; font-size: 14px;">
                                            <?php echo htmlspecialchars($app['fullname']); ?>
                                        </div>
                                        <div style="font-size: 12px; color: #555; margin-top: 2px;">
                                            <i class="fa-solid fa-phone" style="color: #ff4f81; font-size: 11px;"></i> <?php echo htmlspecialchars($app['mobile']); ?>
                                        </div>
                                        <div style="font-size: 12px; color: #777;">
                                            <i class="fa-regular fa-envelope" style="color: #888; font-size: 11px;"></i> <?php echo htmlspecialchars($app['email']); ?>
                                        </div>
                                        <?php if (!empty($app['user_id'])): ?>
                                            <span style="display:inline-block; margin-top:3px; background:#f0f9ff; color:#0284c7; font-size:10px; padding:1px 6px; border-radius:8px;">User #<?php echo $app['user_id']; ?></span>
                                        <?php else: ?>
                                            <span style="display:inline-block; margin-top:3px; background:#f5f5f5; color:#777; font-size:10px; padding:1px 6px; border-radius:8px;">Guest</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #ff4f81;">
                                            <i class="fa-solid fa-spa"></i> <?php echo htmlspecialchars($app['service']); ?>
                                        </div>
                                        <div style="font-size: 12px; color: #666; margin-top: 3px;">
                                            Staff: <b><?php echo htmlspecialchars($app['staff'] ?: 'Any Available Staff'); ?></b>
                                        </div>
                                        <?php if (!empty($app['reference_image'])): ?>
                                            <div style="margin-top: 4px;">
                                                <a href="../uploads/<?php echo htmlspecialchars($app['reference_image']); ?>" target="_blank" style="font-size: 11px; color: #0284c7; text-decoration: underline;">
                                                    <i class="fa-solid fa-image"></i> View Photo Reference
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #333;">
                                            <i class="fa-regular fa-calendar" style="color: #ff4f81;"></i>
                                            <?php echo date('d M Y (D)', strtotime($app['booking_date'])); ?>
                                        </div>
                                        <div style="font-size: 12px; color: #555; margin-top: 2px;">
                                            <i class="fa-regular fa-clock" style="color: #888;"></i>
                                            Slot: <b><?php echo htmlspecialchars($app['booking_time']); ?></b>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; color: #444;">
                                            <b>Payment:</b> <?php echo htmlspecialchars($app['payment_method'] ?: 'Pay at Salon'); ?>
                                        </div>
                                        <?php if (!empty($app['message'])): ?>
                                            <div style="font-size: 11px; color: #666; margin-top: 4px; max-width: 180px; word-break: break-word; background: #fafafa; padding: 4px 6px; border-radius: 4px;">
                                                <i>"<?php echo htmlspecialchars($app['message']); ?>"</i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; border: 1px solid <?php echo $badgeColor; ?>22;">
                                            <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- QUICK STATUS UPDATE FORM -->
                                        <form method="POST" action="manage-appointments.php<?php echo $statusFilter !== 'all' ? '?status=' . urlencode($statusFilter) : ''; ?>" style="display: flex; gap: 6px; align-items: center;">
                                            <input type="hidden" name="booking_id" value="<?php echo $app['booking_id']; ?>">
                                            <input type="hidden" name="update_appointment_status" value="1">
                                            <select name="status" style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 12px; background: #fff;">
                                                <option value="Pending" <?php echo $st === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Confirmed" <?php echo $st === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="Completed" <?php echo $st === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?php echo $st === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="save-btn" style="padding: 6px 10px; font-size: 11px; border-radius: 6px;" title="Update Status">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <!-- DELETE APPOINTMENT FORM -->
                                        <form method="POST" action="manage-appointments.php" onsubmit="return confirm('Are you sure you want to delete appointment #BK-<?php echo $app['booking_id']; ?>? This cannot be undone.');">
                                            <input type="hidden" name="booking_id" value="<?php echo $app['booking_id']; ?>">
                                            <input type="hidden" name="delete_appointment" value="1">
                                            <button type="submit" class="action-btn delete-btn" title="Delete Booking" style="background:none; border:none; cursor:pointer;">
                                                <i class="fa-solid fa-trash-can" style="color: #e74c3c;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

</div>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}
</script>

</body>
</html>
