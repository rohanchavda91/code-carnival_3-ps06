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

// Handle Message Status Actions (Mark Read / Mark New / Delete)
$flash_msg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $msgId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'mark_read') {
        $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET status = 'Read' WHERE message_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $msgId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => "Message #$msgId marked as read."];
        header("Location: contact-messages.php");
        exit;
    } elseif ($act === 'mark_new') {
        $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET status = 'New' WHERE message_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $msgId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => "Message #$msgId marked as new."];
        header("Location: contact-messages.php");
        exit;
    } elseif ($act === 'delete') {
        $stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE message_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $msgId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => "Message #$msgId has been deleted."];
        header("Location: contact-messages.php");
        exit;
    }
}

// Fetch Contact Message Statistics
$msgStats = get_contact_messages_stats($conn);

// Fetch Pending Appointments for badge
$apptStats = get_appointment_stats($conn);
$pending_appointments = $apptStats['pending'] ?? 0;

// Filter and Search
$filterStatus = strtolower(trim($_GET['status'] ?? 'all'));
$searchKeyword = trim($_GET['search'] ?? '');

$whereClauses = [];
$params = [];
$paramTypes = "";

if ($filterStatus === 'new') {
    $whereClauses[] = "LOWER(status) = 'new'";
} elseif ($filterStatus === 'read') {
    $whereClauses[] = "LOWER(status) != 'new'";
}

if ($searchKeyword !== '') {
    $whereClauses[] = "(name LIKE ? OR email LIKE ? OR mobile LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $like = "%$searchKeyword%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $paramTypes .= "sssss";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$querySql = "SELECT * FROM contact_messages $whereSql ORDER BY message_id DESC";

$messages = [];
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $querySql);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $messages[] = $r;
    }
    mysqli_stmt_close($stmt);
} else {
    $res = mysqli_query($conn, $querySql);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $messages[] = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .msg-modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .msg-modal-backdrop.active {
            display: flex;
        }
        .msg-modal-content {
            background: #fff;
            border-radius: 16px;
            max-width: 600px;
            width: 100%;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
        }
        .msg-meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #faf7f8;
            padding: 14px;
            border-radius: 10px;
            margin: 16px 0;
            font-size: 13px;
        }
        .msg-body-box {
            background: #fff;
            border: 1px solid #f0e6e8;
            padding: 16px;
            border-radius: 10px;
            white-space: pre-wrap;
            line-height: 1.6;
            color: #333;
            max-height: 250px;
            overflow-y: auto;
            font-size: 13.5px;
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
            <a href="contact-messages.php" class="active">
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
            <div style="display:flex; align-items:center; gap:14px;">
                <button type="button" class="admin-mobile-toggle" onclick="toggleAdminSidebar()" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <p class="page-small-title">CUSTOMER INQUIRIES</p>
                    <h1>Contact Messages</h1>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="../contact.php" target="_blank" class="action-link-btn" style="text-decoration:none;">
                    <i class="fa-solid fa-paper-plane"></i>
                    Open Contact Form
                </a>
            </div>
        </div>

        <!-- FLASH NOTIFICATION -->
        <?php if ($flash_msg): ?>
            <div class="alert-banner alert-<?php echo htmlspecialchars($flash_msg['type'] ?? 'success'); ?>">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo htmlspecialchars($flash_msg['msg'] ?? ''); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($msgStats['total']); ?></h3>
                    <p>Total Messages</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($msgStats['new']); ?></h3>
                    <p>New (Unread) Inquiries</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($msgStats['read']); ?></h3>
                    <p>Read Messages</p>
                </div>
            </div>
        </div>

        <!-- MESSAGES TABLE CARD -->
        <div class="product-table-card">

            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>All Customer Inquiries</h2>
                    <p>View, read, manage, or delete messages sent via your website contact form.</p>
                </div>

                <!-- CONTROLS: SEARCH & FILTERS -->
                <div class="table-controls">
                    <div class="search-input-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="search-input" placeholder="Search sender, subject..." onkeyup="filterMessagesTable();">
                    </div>

                    <select id="status-filter" class="filter-select" onchange="applyStatusFilter(this.value);">
                        <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Messages (<?php echo $msgStats['total']; ?>)</option>
                        <option value="new" <?php echo $filterStatus === 'new' ? 'selected' : ''; ?>>New / Unread (<?php echo $msgStats['new']; ?>)</option>
                        <option value="read" <?php echo $filterStatus === 'read' ? 'selected' : ''; ?>>Read (<?php echo $msgStats['read']; ?>)</option>
                    </select>
                </div>
            </div>

            <!-- TABLE WRAPPER -->
            <div class="table-wrapper">
                <table class="product-table" id="messages-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Customer Name</th>
                            <th>Contact Info</th>
                            <th>Subject</th>
                            <th>Message Preview</th>
                            <th>Received At</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="8" class="no-products">
                                    <i class="fa-solid fa-envelope-circle-check"></i>
                                    <p>No customer messages found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <?php
                                $isNew = strtolower($msg['status']) === 'new';
                                $statusClass = $isNew ? 'new' : 'read';
                                $statusLabel = $isNew ? 'New' : 'Read';
                                ?>
                                <tr data-id="<?php echo $msg['message_id']; ?>" data-text="<?php echo htmlspecialchars(strtolower($msg['name'] . ' ' . $msg['email'] . ' ' . $msg['mobile'] . ' ' . $msg['subject'] . ' ' . $msg['message'])); ?>">
                                    <td>
                                        <span class="product-id-badge">#<?php echo $msg['message_id']; ?></span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($msg['name']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12.5px;">
                                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: #d85d7d; text-decoration: none;">
                                                <i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                                            <a href="tel:<?php echo htmlspecialchars($msg['mobile']); ?>" style="color: #64748b; text-decoration: none;">
                                                <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($msg['mobile']); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: #2c2c36;"><?php echo htmlspecialchars($msg['subject']); ?></span>
                                    </td>
                                    <td>
                                        <div style="max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #64748b;">
                                            <?php echo htmlspecialchars($msg['message']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: #64748b;">
                                            <i class="fa-regular fa-clock" style="color: #d85d7d; margin-right: 4px;"></i>
                                            <?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="justify-content: flex-end;">
                                            <!-- VIEW MODAL BUTTON -->
                                            <button type="button" class="view-btn" title="View Full Message" onclick="openMessageModal(<?php echo htmlspecialchars(json_encode($msg)); ?>)">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <!-- TOGGLE STATUS BUTTON -->
                                            <?php if ($isNew): ?>
                                                <a href="contact-messages.php?action=mark_read&id=<?php echo $msg['message_id']; ?>" class="edit-btn" title="Mark as Read" style="background:#e8f5e9; color:#2e7d32; border-color:#c8e6c9;">
                                                    <i class="fa-solid fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="contact-messages.php?action=mark_new&id=<?php echo $msg['message_id']; ?>" class="edit-btn" title="Mark as New" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- DELETE BUTTON -->
                                            <a href="contact-messages.php?action=delete&id=<?php echo $msg['message_id']; ?>" class="delete-btn" title="Delete Message" onclick="return confirm('Are you sure you want to delete message #<?php echo $msg['message_id']; ?>?');">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
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

<!-- MESSAGE VIEW MODAL -->
<div class="msg-modal-backdrop" id="msgModalBackdrop">
    <div class="msg-modal-content">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p class="page-small-title" id="modalTicket">MESSAGE DETAILS</p>
                <h2 id="modalSubject" style="font-family:'Playfair Display', serif; font-size:22px; color:#1e293b; margin-top:2px;"></h2>
            </div>
            <button type="button" onclick="closeMessageModal()" style="background:none; border:none; font-size:22px; cursor:pointer; color:#888;">&times;</button>
        </div>

        <div class="msg-meta-row">
            <div>
                <strong>From:</strong> <span id="modalSender"></span><br>
                <strong>Email:</strong> <span id="modalEmail"></span>
            </div>
            <div>
                <strong>Phone:</strong> <span id="modalPhone"></span><br>
                <strong>Date:</strong> <span id="modalDate"></span>
            </div>
        </div>

        <div>
            <label style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase;">Message:</label>
            <div class="msg-body-box" id="modalMessage"></div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; pt-3; border-top:1px solid #f0e6e8;">
            <div id="modalReplyBtnWrap"></div>
            <button type="button" class="cancel-btn" onclick="closeMessageModal()">Close</button>
        </div>
    </div>
</div>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}

function applyStatusFilter(status) {
    window.location.href = 'contact-messages.php?status=' + encodeURIComponent(status);
}

function filterMessagesTable() {
    const input = document.getElementById('search-input');
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#messages-table tbody tr');

    rows.forEach(row => {
        if (!row.dataset.text) return;
        if (row.dataset.text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openMessageModal(data) {
    document.getElementById('modalTicket').textContent = 'INQUIRY #' + data.message_id;
    document.getElementById('modalSubject').textContent = data.subject || 'No Subject';
    document.getElementById('modalSender').textContent = data.name || 'Unknown';
    document.getElementById('modalEmail').innerHTML = '<a href="mailto:' + data.email + '" style="color:#d85d7d;">' + data.email + '</a>';
    document.getElementById('modalPhone').innerHTML = '<a href="tel:' + data.mobile + '" style="color:#333;">' + (data.mobile || '—') + '</a>';
    document.getElementById('modalDate').textContent = data.created_at || '—';
    document.getElementById('modalMessage').textContent = data.message || '';

    const replyWrap = document.getElementById('modalReplyBtnWrap');
    replyWrap.innerHTML = '<a href="mailto:' + encodeURIComponent(data.email) + '?subject=' + encodeURIComponent('Re: ' + data.subject) + '" class="save-btn" style="text-decoration:none;"><i class="fa-solid fa-reply"></i> Reply via Email</a>';

    document.getElementById('msgModalBackdrop').classList.add('active');
}

function closeMessageModal() {
    document.getElementById('msgModalBackdrop').classList.remove('active');
}

// Close modal on click outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('msgModalBackdrop');
    if (e.target === modal) {
        closeMessageModal();
    }
});
</script>

</body>
</html>
