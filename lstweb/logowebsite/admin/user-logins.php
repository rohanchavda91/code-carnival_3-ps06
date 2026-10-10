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

// Fetch user login statistics
$loginStats = get_user_login_stats($conn);

// Fetch contact message and appointment counts for sidebar badges
$msgStats = get_contact_messages_stats($conn);
$apptStats = get_appointment_stats($conn);
$pending_appointments = $apptStats['pending'] ?? 0;

// Filter and Search
$filterStatus = strtolower(trim($_GET['status'] ?? 'all'));
$searchKeyword = trim($_GET['search'] ?? '');

$whereClauses = [];
$params = [];
$paramTypes = "";

if ($filterStatus === 'active') {
    $whereClauses[] = "((l.status = 'active' OR l.status IS NULL) AND l.logout_time IS NULL)";
} elseif ($filterStatus === 'logged_out') {
    $whereClauses[] = "(l.status = 'logged_out' OR l.logout_time IS NOT NULL)";
}

if ($searchKeyword !== '') {
    $whereClauses[] = "(u.fullname LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR l.ip_address LIKE ?)";
    $like = "%$searchKeyword%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $paramTypes .= "ssss";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$querySql = "SELECT l.*, u.fullname, u.email, u.mobile, u.profile_image 
             FROM user_login_history l
             LEFT JOIN users u ON l.user_id = u.user_id
             $whereSql
             ORDER BY l.id DESC LIMIT 200";

$sessions = [];
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $querySql);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $sessions[] = $r;
    }
    mysqli_stmt_close($stmt);
} else {
    $res = mysqli_query($conn, $querySql);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $sessions[] = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login & Logout Audit | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .user-avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #fff0f3;
            color: #d85d7d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(216,93,125,0.15);
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
            <a href="index.php">
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
                <?php if (($msgStats['new'] ?? 0) > 0): ?>
                    <span class="badge-count badge-pink"><?php echo $msgStats['new']; ?></span>
                <?php endif; ?>
            </a>
            <a href="user-logins.php" class="active">
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
            <div style="display:flex; align-items:center; gap:14px;">
                <button type="button" class="admin-mobile-toggle" onclick="toggleAdminSidebar()" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <p class="page-small-title">AUDIT & SECURITY</p>
                    <h1>User Login & Logout Activity</h1>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="window.location.reload();" class="action-link-btn" style="cursor:pointer;">
                    <i class="fa-solid fa-rotate"></i>
                    Refresh Log
                </button>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($loginStats['total_users']); ?></h3>
                    <p>Total Registered Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($loginStats['logged_in_users']); ?></h3>
                    <p>Unique Users Logged In</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange" style="background:#e8f5e9; color:#2e7d32;">
                    <i class="fa-solid fa-signal"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($loginStats['active_now']); ?></h3>
                    <p>Active Now</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($loginStats['logged_out']); ?></h3>
                    <p>Logged Out Sessions</p>
                </div>
            </div>
        </div>

        <!-- SESSIONS TABLE CARD -->
        <div class="product-table-card">

            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>Tracked User Sessions</h2>
                    <p>Real-time audit log of customer logins, logouts, session duration, and live status.</p>
                </div>

                <!-- CONTROLS: SEARCH & FILTERS -->
                <div class="table-controls">
                    <div class="search-input-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="search-input" placeholder="Search user or email..." onkeyup="filterSessionsTable();">
                    </div>

                    <select id="status-filter" class="filter-select" onchange="applyStatusFilter(this.value);">
                        <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Sessions (<?php echo $loginStats['total_logins']; ?>)</option>
                        <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Active Now (<?php echo $loginStats['active_now']; ?>)</option>
                        <option value="logged_out" <?php echo $filterStatus === 'logged_out' ? 'selected' : ''; ?>>Logged Out (<?php echo $loginStats['logged_out']; ?>)</option>
                    </select>
                </div>
            </div>

            <!-- TABLE WRAPPER -->
            <div class="table-wrapper">
                <table class="product-table" id="sessions-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>Logout Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="8" class="no-products">
                                    <i class="fa-solid fa-users-slash"></i>
                                    <p>No user login/logout activity recorded yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sessions as $s): ?>
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
                                <tr data-id="<?php echo $s['id']; ?>" data-text="<?php echo htmlspecialchars(strtolower(($s['fullname'] ?? '') . ' ' . ($s['email'] ?? '') . ' ' . ($s['mobile'] ?? '') . ' ' . ($s['ip_address'] ?? ''))); ?>">
                                    <td>
                                        <span class="product-id-badge">#<?php echo $s['id']; ?></span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="user-avatar-circle"><?php echo $initial; ?></div>
                                            <div>
                                                <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($s['fullname'] ?: 'User #' . $s['user_id']); ?></div>
                                                <?php if (!empty($s['mobile'])): ?>
                                                    <small style="color:#64748b; font-size:11.5px;"><?php echo htmlspecialchars($s['mobile']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="color:#64748b; font-size:12.5px;"><?php echo htmlspecialchars($s['email'] ?: '—'); ?></span>
                                    </td>
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
                                    <td>
                                        <code style="font-size:11px; background:#f8fafc; padding:3px 6px; border-radius:4px; color:#64748b;">
                                            <?php echo htmlspecialchars($s['ip_address'] ?: '127.0.0.1'); ?>
                                        </code>
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

function applyStatusFilter(status) {
    window.location.href = 'user-logins.php?status=' + encodeURIComponent(status);
}

function filterSessionsTable() {
    const input = document.getElementById('search-input');
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#sessions-table tbody tr');

    rows.forEach(row => {
        if (!row.dataset.text) return;
        if (row.dataset.text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
