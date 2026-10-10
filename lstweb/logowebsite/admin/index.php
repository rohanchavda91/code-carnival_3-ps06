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

// 1. Fetch User & Login Statistics
$userStats = get_user_login_stats($conn);
$total_users = $userStats['total_users'];
$total_login_users = $userStats['logged_in_users'];
$total_logins = $userStats['total_logins'];
$active_now = $userStats['active_now'];
$logged_out_sessions = $userStats['logged_out'];

// 2. Fetch Appointment Statistics
$apptStats = get_appointment_stats($conn);
$total_appointments = $apptStats['total'];
$pending_appointments = $apptStats['pending'];
$confirmed_appointments = $apptStats['confirmed'];
$completed_appointments = $apptStats['completed'];
$cancelled_appointments = $apptStats['cancelled'];

// 3. Fetch Contact Messages Statistics
$msgStats = get_contact_messages_stats($conn);
$total_messages = $msgStats['total'];
$new_messages = $msgStats['new'];
$read_messages = $msgStats['read'];

// 4. Fetch Orders & Store Overview
$total_orders = 0;
$total_revenue = 0.0;
$orderRes = mysqli_query($conn, "SELECT COUNT(*) as cnt, SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'returned', 'refund successful') THEN price ELSE 0 END) as rev FROM orders");
if ($orderRes && $oRow = mysqli_fetch_assoc($orderRes)) {
    $total_orders = (int)($oRow['cnt'] ?? 0);
    $total_revenue = (float)($oRow['rev'] ?? 0.0);
}

$total_products = 0;
$prodRes = mysqli_query($conn, "SELECT COUNT(*) FROM products");
if ($prodRes && $pRow = mysqli_fetch_array($prodRes)) {
    $total_products = (int)$pRow[0];
}

// 5. Fetch Recent User Logins & Logouts (Latest 6)
$recentSessions = [];
$rlQuery = "SELECT l.*, u.fullname, u.email, u.mobile 
            FROM user_login_history l 
            LEFT JOIN users u ON l.user_id = u.user_id 
            ORDER BY l.id DESC LIMIT 6";
$rlRes = mysqli_query($conn, $rlQuery);
if ($rlRes) {
    while ($row = mysqli_fetch_assoc($rlRes)) {
        $recentSessions[] = $row;
    }
}

// 6. Fetch Recent Contact Messages (Latest 5)
$recentMessages = [];
$rmQuery = "SELECT * FROM contact_messages ORDER BY message_id DESC LIMIT 5";
$rmRes = mysqli_query($conn, $rmQuery);
if ($rmRes) {
    while ($row = mysqli_fetch_assoc($rmRes)) {
        $recentMessages[] = $row;
    }
}

// 7. Fetch Recent Appointments (Latest 5)
$recentAppointments = [];
$raQuery = "SELECT * FROM bookings ORDER BY booking_id DESC LIMIT 5";
$raRes = mysqli_query($conn, $raQuery);
if ($raRes) {
    while ($row = mysqli_fetch_assoc($raRes)) {
        $recentAppointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GlowNest Beautiparlor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
    <style>
        .user-initial-badge {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff0f3;
            color: #d85d7d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }
    </style>
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
            <a href="index.php" class="active">
                <i class="fa-solid fa-gauge"></i>
                Dashboard
            </a>
            <a href="manage-appointments.php">
                <i class="fa-solid fa-calendar-check"></i>
                Manage Appointments
                <?php if ($pending_appointments > 0): ?>
                    <span class="badge-count badge-orange"><?php echo $pending_appointments; ?></span>
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
                <?php if ($new_messages > 0): ?>
                    <span class="badge-count badge-pink"><?php echo $new_messages; ?></span>
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
                    <p class="page-small-title">GLOWNEST BEAUTIPARLOR</p>
                    <h1>Admin Dashboard</h1>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="manage-appointments.php" class="action-link-btn" style="text-decoration:none;">
                    <i class="fa-solid fa-calendar-check"></i>
                    Appointments (<?php echo $pending_appointments; ?> Pending)
                </a>
                <a href="add-product.php" class="save-btn" style="text-decoration: none;">
                    <i class="fa-solid fa-plus"></i>
                    Add Product
                </a>
            </div>
        </div>

        <!-- PRIMARY STAT CARDS (ROW 1 - 4 CARDS AS IN SCREENSHOT) -->
        <div class="stats-grid">
            <!-- 1. TOTAL USERS -->
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_users); ?></h3>
                    <p>Total Registered Users</p>
                </div>
            </div>

            <!-- 2. ACTIVE LOGGED IN USERS -->
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($active_now); ?> Active / <?php echo number_format($total_login_users); ?></h3>
                    <p>Active Now / Logged In Users</p>
                </div>
            </div>

            <!-- 3. TOTAL APPOINTMENTS -->
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_appointments); ?></h3>
                    <p>Appointments (<?php echo $pending_appointments; ?> Pending)</p>
                </div>
            </div>

            <!-- 4. CONTACT MESSAGES -->
            <div class="stat-card">
                <div class="stat-icon purple" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_messages); ?></h3>
                    <p>Inquiries (<?php echo $new_messages; ?> New Unread)</p>
                </div>
            </div>
        </div>

        <!-- SECONDARY STORE OVERVIEW METRICS (ROW 2) -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff7ed; color:#ea580c;">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Store Revenue (<?php echo $total_orders; ?> Orders)</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#f3e8ff; color:#7e22ce;">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_products); ?></h3>
                    <p>Active Store Products</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#059669;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($confirmed_appointments); ?></h3>
                    <p>Confirmed Appointments</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#fef2f2; color:#dc2626;">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($cancelled_appointments); ?></h3>
                    <p>Cancelled Appointments</p>
                </div>
            </div>
        </div>

        <!-- SECTION 1: USER LOGIN & LOGOUT ACTIVITY (USER REQUEST) -->
        <div class="product-table-card" style="margin-bottom: 28px;">
            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>User Login & Logout Activity</h2>
                    <p>Track which users have logged in and logged out, with exact timestamps, session duration, and live status.</p>
                </div>
                <div class="table-controls">
                    <a href="user-logins.php" class="action-link-btn" style="text-decoration:none;">
                        <i class="fa-solid fa-list-check"></i>
                        View Full Log (<?php echo $total_logins; ?>)
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>Logout Time</th>
                            <th>Session Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSessions)): ?>
                            <tr>
                                <td colspan="7" class="no-products">
                                    <i class="fa-solid fa-users-slash"></i>
                                    <p>No user login activity recorded yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentSessions as $s): ?>
                                <?php
                                $isLoggedOut = (!empty($s['logout_time']) || $s['status'] === 'logged_out');
                                $statusClass = $isLoggedOut ? 'logged-out' : 'active';
                                $statusLabel = $isLoggedOut ? 'Logged Out' : 'Active Now';

                                // Calculate Duration
                                $durationText = '—';
                                if (!empty($s['login_time'])) {
                                    $start = strtotime($s['login_time']);
                                    $end = $isLoggedOut ? strtotime($s['logout_time']) : time();
                                    $diffSec = max(0, $end - $start);

                                    $hrs = floor($diffSec / 3600);
                                    $mins = floor(($diffSec % 3600) / 60);
                                    $secs = $diffSec % 60;

                                    if ($hrs > 0) {
                                        $durationText = "{$hrs}h {$mins}m";
                                    } elseif ($mins > 0) {
                                        $durationText = "{$mins} mins";
                                    } else {
                                        $durationText = "{$secs} secs";
                                    }

                                    if (!$isLoggedOut) {
                                        $durationText .= ' (online)';
                                    }
                                }

                                $initial = strtoupper(substr($s['fullname'] ?? 'U', 0, 1));
                                ?>
                                <tr>
                                    <td><span class="product-id-badge">#<?php echo $s['id']; ?></span></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div class="user-initial-badge"><?php echo $initial; ?></div>
                                            <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($s['fullname'] ?: 'User #' . $s['user_id']); ?></div>
                                        </div>
                                    </td>
                                    <td><span style="color:#64748b; font-size:12.5px;"><?php echo htmlspecialchars($s['email'] ?: '—'); ?></span></td>
                                    <td>
                                        <span style="font-size:12px; color:#1e293b; font-weight:500;">
                                            <i class="fa-regular fa-clock" style="color:#2e7d32; margin-right:4px;"></i>
                                            <?php echo date('d M Y, h:i A', strtotime($s['login_time'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($s['logout_time'])): ?>
                                            <span style="font-size:12px; color:#64748b;">
                                                <i class="fa-regular fa-clock" style="color:#dc2626; margin-right:4px;"></i>
                                                <?php echo date('d M Y, h:i A', strtotime($s['logout_time'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:#94a3b8; font-size:12px;">Active Session</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight:600; font-size:12px; color:<?php echo $isLoggedOut ? '#64748b' : '#2e7d32'; ?>;">
                                            <?php echo $durationText; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 2: CUSTOMER CONTACT MESSAGES (USER REQUEST) -->
        <div class="product-table-card" style="margin-bottom: 28px;">
            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>Recent Contact Messages & Inquiries</h2>
                    <p>Messages submitted by customers through the website contact form.</p>
                </div>
                <div class="table-controls">
                    <a href="contact-messages.php" class="action-link-btn" style="text-decoration:none;">
                        <i class="fa-solid fa-inbox"></i>
                        Manage All Messages (<?php echo $total_messages; ?>)
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Customer Name</th>
                            <th>Contact Info</th>
                            <th>Subject</th>
                            <th>Message Preview</th>
                            <th>Received At</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentMessages)): ?>
                            <tr>
                                <td colspan="8" class="no-products">
                                    <i class="fa-solid fa-envelope-open"></i>
                                    <p>No customer messages received yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentMessages as $msg): ?>
                                <?php
                                $isNew = strtolower($msg['status']) === 'new';
                                $statusClass = $isNew ? 'new' : 'read';
                                $statusLabel = $isNew ? 'New' : 'Read';
                                ?>
                                <tr>
                                    <td><span class="product-id-badge">#<?php echo $msg['message_id']; ?></span></td>
                                    <td><div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($msg['name']); ?></div></td>
                                    <td>
                                        <div style="font-size:12.5px; color:#d85d7d;"><?php echo htmlspecialchars($msg['email']); ?></div>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($msg['mobile']); ?></small>
                                    </td>
                                    <td><span style="font-weight:600; color:#2c2c36;"><?php echo htmlspecialchars($msg['subject']); ?></span></td>
                                    <td>
                                        <div style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#64748b;">
                                            <?php echo htmlspecialchars($msg['message']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size:12px; color:#64748b;">
                                            <?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="contact-messages.php" class="view-btn" title="View Full Message" style="display:inline-flex;">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 3: RECENT APPOINTMENTS -->
        <div class="product-table-card">
            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>Recent Appointments</h2>
                    <p>Latest beauty and salon service bookings submitted by customers.</p>
                </div>
                <div class="table-controls">
                    <a href="manage-appointments.php" class="action-link-btn" style="text-decoration:none;">
                        <i class="fa-solid fa-calendar-check"></i>
                        View All Bookings (<?php echo $total_appointments; ?>)
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAppointments)): ?>
                            <tr>
                                <td colspan="7" class="no-products">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                    <p>No appointments recorded yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentAppointments as $app): ?>
                                <?php
                                $st = strtolower($app['status'] ?? 'pending');
                                $badgeClass = 'pending';
                                if ($st === 'confirmed') $badgeClass = 'confirmed';
                                elseif ($st === 'completed') $badgeClass = 'completed';
                                elseif ($st === 'cancelled') $badgeClass = 'cancelled';
                                ?>
                                <tr>
                                    <td><span class="product-id-badge">#BK-<?php echo $app['booking_id']; ?></span></td>
                                    <td>
                                        <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($app['fullname']); ?></div>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($app['mobile']); ?></small>
                                    </td>
                                    <td><span style="font-weight:600; color:#d85d7d;"><?php echo htmlspecialchars($app['service']); ?></span></td>
                                    <td>
                                        <div><?php echo date('d M Y', strtotime($app['booking_date'])); ?></div>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($app['booking_time']); ?></small>
                                    </td>
                                    <td><span class="category-tag"><?php echo htmlspecialchars($app['payment_method'] ?? 'Pay at Salon'); ?></span></td>
                                    <td>
                                        <span class="status-pill <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="manage-appointments.php?search=<?php echo urlencode($app['booking_id']); ?>" class="edit-btn" title="Manage Appointment" style="display:inline-flex;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
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
