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

// Handle Status Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');
    $orderNumber = trim($_POST['order_number'] ?? '');

    if ($orderId > 0 && $newStatus !== '') {
        $updated = update_order_tracking_status($conn, $orderId, $newStatus);
        if ($updated) {
            $_SESSION['flash_msg'] = [
                'type' => 'success',
                'msg' => "Order " . ($orderNumber ? "#$orderNumber" : "#$orderId") . " status successfully updated to '$newStatus'."
            ];
        } else {
            $_SESSION['flash_msg'] = [
                'type' => 'danger',
                'msg' => "Failed to update order status. Please select a valid status."
            ];
        }
    } else {
        $_SESSION['flash_msg'] = [
            'type' => 'danger',
            'msg' => "Invalid order ID or status value."
        ];
    }

    $redirectUrl = "manage-orders.php";
    if (!empty($_GET['status'])) {
        $redirectUrl .= "?status=" . urlencode($_GET['status']);
    }
    header("Location: $redirectUrl");
    exit;
}

// Calculate Dashboard Order Statistics
$stat_total_orders = 0;
$stat_processing = 0;
$stat_in_transit = 0;
$stat_delivered = 0;
$stat_cancelled = 0;
$stat_delayed = 0;
$stat_returned = 0;
$stat_total_revenue = 0.0;

$stats_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN LOWER(status) IN ('order placed', 'confirmed', 'packed') THEN 1 ELSE 0 END) as processing_count,
    SUM(CASE WHEN LOWER(status) IN ('shipped', 'out for delivery') THEN 1 ELSE 0 END) as in_transit_count,
    SUM(CASE WHEN LOWER(status) = 'delivered' THEN 1 ELSE 0 END) as delivered_count,
    SUM(CASE WHEN LOWER(status) IN ('delayed', 'order delayed') THEN 1 ELSE 0 END) as delayed_count,
    SUM(CASE WHEN LOWER(status) = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
    SUM(CASE WHEN LOWER(status) IN ('returned', 'refund processing', 'refund successful') THEN 1 ELSE 0 END) as returned_count,
    SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'returned', 'refund successful') THEN price ELSE 0 END) as total_revenue
    FROM orders";

$stats_result = mysqli_query($conn, $stats_query);
if ($stats_result && $row = mysqli_fetch_assoc($stats_result)) {
    $stat_total_orders  = (int)($row['total_orders'] ?? 0);
    $stat_processing    = (int)($row['processing_count'] ?? 0);
    $stat_in_transit    = (int)($row['in_transit_count'] ?? 0);
    $stat_delivered     = (int)($row['delivered_count'] ?? 0);
    $stat_delayed       = (int)($row['delayed_count'] ?? 0);
    $stat_cancelled     = (int)($row['cancelled_count'] ?? 0);
    $stat_returned      = (int)($row['returned_count'] ?? 0);
    $stat_total_revenue = (float)($row['total_revenue'] ?? 0.0);
}

// Fetch Orders with Optional Filters
$statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
$searchTerm = trim($_GET['search'] ?? '');

$whereClauses = [];
if ($statusFilter !== 'all' && $statusFilter !== '') {
    $safeStatus = mysqli_real_escape_string($conn, $statusFilter);
    if ($safeStatus === 'delayed') {
        $whereClauses[] = "LOWER(status) IN ('delayed', 'order delayed')";
    } else {
        $whereClauses[] = "LOWER(status) = '$safeStatus'";
    }
}

if ($searchTerm !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $searchTerm);
    $whereClauses[] = "(order_number LIKE '%$safeSearch%' OR customer_name LIKE '%$safeSearch%' OR customer_phone LIKE '%$safeSearch%' OR product_name LIKE '%$safeSearch%')";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(' AND ', $whereClauses) : "";
$ordersQuery = "SELECT * FROM orders $whereSql ORDER BY order_id DESC";
$ordersResult = mysqli_query($conn, $ordersQuery);
$orders = [];
if ($ordersResult) {
    while ($r = mysqli_fetch_assoc($ordersResult)) {
        $orders[] = $r;
    }
}

$standardStages = [
    'Order Placed',
    'Confirmed',
    'Packed',
    'Shipped',
    'Out for Delivery',
    'Delivered',
    'Delayed',
    'Returned',
    'Refund Processing',
    'Refund Successful',
    'Cancelled'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .order-meta-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            background: #f4f0f3;
            color: #635d6e;
        }
        .order-meta-pill.cod {
            background: #fff4e5;
            color: #b45309;
        }
        .order-meta-pill.prepaid {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .order-number-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fdf2f6;
            color: var(--admin-primary);
            font-family: monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #fae1eb;
        }
        .order-date-text {
            color: #888292;
            font-size: 11px;
            margin-top: 3px;
            display: block;
        }
    </style>
</head>
<body>

<div class="admin-page">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="admin-logo">
            Glow<span>Nest</span>
        </div>
        <p class="admin-label">ADMIN DASHBOARD</p>

        <nav class="sidebar-nav">
            <a href="manage-product.php">
                <i class="fa-solid fa-gauge"></i>
                Dashboard
            </a>
            <a href="manage-orders.php" class="active">
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
            <a href="../index.php" target="_blank">
                <i class="fa-solid fa-globe"></i>
                View Website
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Exit Admin
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <!-- TOP BAR -->
        <div class="admin-topbar">
            <div>
                <p class="page-small-title">ORDER DISPATCH & TRACKING</p>
                <h1>Manage Orders</h1>
            </div>
            <a href="../shop.php" target="_blank" class="action-link-btn">
                <i class="fa-solid fa-bag-shopping"></i>
                View Store
            </a>
        </div>

        <!-- FLASH NOTIFICATION BANNER -->
        <?php if ($flash_msg): ?>
            <div class="alert-banner alert-<?php echo htmlspecialchars($flash_msg['type'] ?? 'success'); ?>">
                <i class="fa-solid <?php echo ($flash_msg['type'] ?? '') === 'danger' ? 'fa-triangle-exclamation' : 'fa-circle-check'; ?>"></i>
                <span><?php echo htmlspecialchars($flash_msg['msg'] ?? ''); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <!-- DASHBOARD STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_total_orders); ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_processing); ?></h3>
                    <p>Processing / Packed</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_in_transit); ?></h3>
                    <p>Shipped / In Transit</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_delivered); ?></h3>
                    <p>Delivered</p>
                </div>
            </div>
        </div>

        <!-- ORDERS TABLE CARD -->
        <div class="product-table-card">

            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>Customer Orders &amp; Live Status</h2>
                    <p>Track packages, view customer addresses, and update order statuses in real-time.</p>
                </div>

                <!-- CONTROLS: SEARCH & FILTERS -->
                <div class="table-controls">
                    <form method="GET" action="manage-orders.php" class="search-input-box" style="margin: 0;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Order #, Name, Phone...">
                        <?php if ($statusFilter !== 'all'): ?>
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <?php endif; ?>
                    </form>

                    <select id="status-filter" class="filter-select" onchange="location.href='manage-orders.php?status=' + encodeURIComponent(this.value) + '<?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>';">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses (<?php echo $stat_total_orders; ?>)</option>
                        <option value="order placed" <?php echo $statusFilter === 'order placed' ? 'selected' : ''; ?>>Order Placed</option>
                        <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="packed" <?php echo $statusFilter === 'packed' ? 'selected' : ''; ?>>Packed</option>
                        <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="out for delivery" <?php echo $statusFilter === 'out for delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                        <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Delivered (<?php echo $stat_delivered; ?>)</option>
                        <option value="delayed" <?php echo $statusFilter === 'delayed' ? 'selected' : ''; ?>>Delayed (<?php echo $stat_delayed; ?>)</option>
                        <option value="returned" <?php echo $statusFilter === 'returned' ? 'selected' : ''; ?>>Returned / Refund (<?php echo $stat_returned; ?>)</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled (<?php echo $stat_cancelled; ?>)</option>
                    </select>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table class="product-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order Details</th>
                            <th>Customer Info</th>
                            <th>Product</th>
                            <th>Qty &amp; Total</th>
                            <th>Payment</th>
                            <th>Current Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $row):
                            $statusInfo = get_order_status_data($row);
                            $rawStatus = $row['status'] ?? 'Order Placed';
                            $imageFile = !empty($row['image']) ? '../uploads/' . basename($row['image']) : '../images/beautytools.jpg';
                            $isCod = stripos($row['payment_method'] ?? '', 'cash') !== false || stripos($row['payment_method'] ?? '', 'cod') !== false;
                            $chipClass = strtolower(str_replace(' ', '-', $statusInfo['chip'] ?? 'placed'));
                        ?>
                            <tr>
                                <td>
                                    <span class="order-number-badge">
                                        <i class="fa-solid fa-receipt"></i>
                                        #<?php echo htmlspecialchars($row['order_number'] ?: $row['order_id']); ?>
                                    </span>
                                    <span class="order-date-text">
                                        <i class="fa-regular fa-clock"></i>
                                        <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="order-customer-box">
                                        <strong><?php echo htmlspecialchars($row['customer_name'] ?: 'Customer'); ?></strong>
                                        <div class="customer-contact">
                                            <?php if (!empty($row['customer_phone'])): ?>
                                                <span><i class="fa-solid fa-phone" style="color: var(--admin-primary); font-size: 10px;"></i> <?php echo htmlspecialchars($row['customer_phone']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($row['customer_email'])): ?>
                                                <span><i class="fa-regular fa-envelope" style="color: var(--admin-primary); font-size: 10px;"></i> <?php echo htmlspecialchars($row['customer_email']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($row['delivery_address'])): ?>
                                            <div class="customer-address" title="<?php echo htmlspecialchars($row['delivery_address']); ?>">
                                                <i class="fa-solid fa-location-dot" style="color: #999;"></i>
                                                <?php echo htmlspecialchars(mb_strimwidth($row['delivery_address'], 0, 70, '...')); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="product-thumb" style="width: 44px; height: 44px; flex-basis: 44px; min-width: 44px;">
                                            <?php if (!empty($row['image']) && file_exists(__DIR__ . '/../uploads/' . basename($row['image']))): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars(basename($row['image'])); ?>" alt="Product">
                                            <?php else: ?>
                                                <img src="../images/beautytools.jpg" alt="Product" onerror="this.style.display='none';">
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="product-name-title" style="font-size: 12.5px;"><?php echo htmlspecialchars($row['product_name']); ?></span>
                                            <span class="category-tag" style="font-size: 10.5px; padding: 2px 7px;"><?php echo htmlspecialchars($row['category'] ?: 'Beauty'); ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div>
                                        <span class="stock-badge">Qty: <b><?php echo (int)($row['quantity'] ?? 1); ?></b></span>
                                        <div class="price-text" style="font-size: 13.5px; margin-top: 2px;">
                                             ₹<?php echo number_format((float)$row['price'], 2); ?>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="order-meta-pill <?php echo $isCod ? 'cod' : 'prepaid'; ?>">
                                        <i class="fa-solid <?php echo $isCod ? 'fa-hand-holding-dollar' : 'fa-credit-card'; ?>"></i>
                                        <?php echo htmlspecialchars($row['payment_method'] ?: 'Cash on Delivery'); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo htmlspecialchars($chipClass); ?>">
                                        <?php echo htmlspecialchars($statusInfo['status'] ?? $rawStatus); ?>
                                    </span>
                                    <div style="margin-top: 4px;">
                                        <?php if ($statusInfo['is_delivered']): ?>
                                            <small style="color: #27ae60; font-weight: 600; font-size: 11px;">
                                                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($statusInfo['delivered_date_text']); ?>
                                            </small>
                                        <?php elseif ($statusInfo['is_cancelled']): ?>
                                            <small style="color: #e74c3c; font-weight: 600; font-size: 11px;">
                                                <i class="fa-solid fa-ban"></i> <?php echo htmlspecialchars($statusInfo['cancelled_date_text']); ?>
                                            </small>
                                        <?php elseif ($statusInfo['is_delayed']): ?>
                                            <small style="color: #d97706; font-weight: 600; font-size: 11px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Delayed (Stage <?php echo (int)($row['tracking_stage'] ?? 4); ?>/6)
                                            </small>
                                        <?php elseif ($statusInfo['is_refunded']): ?>
                                            <small style="color: #0284c7; font-weight: 600; font-size: 11px;">
                                                <i class="fa-solid fa-rotate-left"></i> <?php echo htmlspecialchars($statusInfo['refund_date_text']); ?>
                                            </small>
                                        <?php else: ?>
                                            <small style="color: #888; font-size: 11px;">Stage <?php echo (int)($row['tracking_stage'] ?? 1); ?>/6</small>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <form method="POST" action="manage-orders.php<?php echo $statusFilter !== 'all' ? '?status=' . urlencode($statusFilter) : ''; ?>" class="order-status-form">
                                        <input type="hidden" name="order_id" value="<?php echo (int)$row['order_id']; ?>">
                                        <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($row['order_number']); ?>">
                                        <select name="status" aria-label="Update order status">
                                            <?php foreach ($standardStages as $stageOption): ?>
                                                <option value="<?php echo htmlspecialchars($stageOption); ?>" <?php echo strcasecmp($rawStatus, $stageOption) === 0 ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($stageOption); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_order_status" title="Update status">
                                            <i class="fa-solid fa-check"></i>
                                            Save
                                        </button>
                                    </form>
                                    <a href="../tracking.php?order=<?php echo (int)$row['order_id']; ?>" target="_blank" class="view-tracking-link">
                                        <i class="fa-solid fa-map-location-dot"></i> Live Tracking
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="no-orders-row">
                            <td colspan="7" class="no-products">
                                <i class="fa-solid fa-box-open"></i>
                                <p>No orders found matching the selected criteria.</p>
                                <a href="manage-orders.php">
                                    <i class="fa-solid fa-rotate-left"></i> Reset Filters
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

</div>

</body>
</html>
