<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php?msg=" . urlencode("Admin login required."));
    exit;
}

include("../config.php");
if (file_exists("../includes/order_helpers.php")) {
    include_once("../includes/order_helpers.php");
    if (function_exists('ensure_store_tables')) {
        ensure_store_tables($conn);
    }
}

// Fetch Flash Message if present
$flash_msg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

// Calculate Dashboard Quick Statistics
$stat_total = 0;
$stat_available = 0;
$stat_out_of_stock = 0;
$stat_total_units = 0;

$stats_query = "SELECT 
    COUNT(*) as total_count,
    SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as available_count,
    SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_count,
    SUM(stock) as total_units
    FROM products";

$stats_result = mysqli_query($conn, $stats_query);
if ($stats_result && $row = mysqli_fetch_assoc($stats_result)) {
    $stat_total        = intval($row['total_count'] ?? 0);
    $stat_available    = intval($row['available_count'] ?? 0);
    $stat_out_of_stock = intval($row['out_of_stock_count'] ?? 0);
    $stat_total_units  = intval($row['total_units'] ?? 0);
}

// Fetch All Products for Table
$query = "SELECT * FROM products ORDER BY product_id DESC";
$result = mysqli_query($conn, $query);

// Collect distinct categories for dropdown filter
$categories = [];
$products = [];
if ($result) {
    while ($r = mysqli_fetch_assoc($result)) {
        $products[] = $r;
        if (!empty($r['category']) && !in_array($r['category'], $categories)) {
            $categories[] = $r['category'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
            <a href="manage-product.php" class="active">
                <i class="fa-solid fa-gauge"></i>
                Dashboard
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
                <p class="page-small-title">PRODUCT MANAGEMENT</p>
                <h1>Manage Products</h1>
            </div>
            <a href="add-product.php" class="save-btn" style="text-decoration: none;">
                <i class="fa-solid fa-plus"></i>
                Add New Product
            </a>
        </div>

        <!-- FLASH NOTIFICATION BANNER -->
        <?php if ($flash_msg): ?>
            <div class="alert-banner alert-<?php echo htmlspecialchars($flash_msg['type'] ?? 'success'); ?>">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo htmlspecialchars($flash_msg['msg'] ?? ''); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <!-- DASHBOARD STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_total); ?></h3>
                    <p>Total Products</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_available); ?></h3>
                    <p>Available Products</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_out_of_stock); ?></h3>
                    <p>Out of Stock</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stat_total_units); ?></h3>
                    <p>Total Inventory Units</p>
                </div>
            </div>
        </div>

        <!-- PRODUCTS TABLE CARD -->
        <div class="product-table-card">

            <div class="table-header-bar">
                <div class="table-heading">
                    <h2>All Products Catalog</h2>
                    <p>View, edit, filter, or manage all product items in your store.</p>
                </div>

                <!-- CONTROLS: SEARCH & FILTERS -->
                <div class="table-controls">
                    <div class="search-input-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="search-input" placeholder="Search products..." onkeyup="filterProducts();">
                    </div>

                    <select id="category-filter" class="filter-select" onchange="filterProducts();">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($cat)); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="status-filter" class="filter-select" onchange="filterProducts();">
                        <option value="all">All Status</option>
                        <option value="available">Available</option>
                        <option value="out of stock">Out of Stock</option>
                    </select>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table class="product-table" id="productTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $row): ?>
                            <?php $rowStatus = normalize_product_status($row['status'] ?? 'Available', (int)($row['stock'] ?? 0)); ?>
                            <tr data-name="<?php echo htmlspecialchars(strtolower($row['product_name'] . ' ' . $row['product_id'])); ?>"
                                data-category="<?php echo htmlspecialchars(strtolower($row['category'])); ?>"
                                data-status="<?php echo htmlspecialchars(strtolower($rowStatus)); ?>">

                                <td>
                                    <span class="product-id-badge">#<?php echo $row['product_id']; ?></span>
                                </td>

                                <td>
                                    <div class="product-thumb">
                                        <?php if (!empty($row['image']) && file_exists("../uploads/" . $row['image'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Thumbnail">
                                        <?php else: ?>
                                            <i class="fa-solid fa-image"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="product-name-title"><?php echo htmlspecialchars($row['product_name']); ?></span>
                                </td>

                                <td>
                                    <span class="category-tag"><?php echo htmlspecialchars($row['category']); ?></span>
                                </td>

                                <td>
                                    <span class="price-text">₹<?php echo number_format($row['price'], 2); ?></span>
                                </td>

                                <td>
                                    <?php 
                                    $stock = intval($row['stock']);
                                    if ($stock === 0) {
                                        echo '<span class="stock-badge zero">0 (Out)</span>';
                                    } elseif ($stock <= 10) {
                                        echo '<span class="stock-badge low">' . $stock . ' (Low)</span>';
                                    } else {
                                        echo '<span class="stock-badge">' . $stock . '</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php $displayStatus = normalize_product_status($row['status'] ?? 'Available', (int)($row['stock'] ?? 0)); ?>
                                    <?php if ($displayStatus === 'Available'): ?>
                                        <span class="status-pill available">Available</span>
                                    <?php else: ?>
                                        <span class="status-pill unavailable">Out of Stock</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <a href="edit-product.php?id=<?php echo $row['product_id']; ?>" class="edit-btn" title="Edit Product">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="delete-product.php?id=<?php echo $row['product_id']; ?>" class="delete-btn" title="Delete Product" onclick="return confirm('Are you sure you want to delete product \'<?php echo htmlspecialchars(addslashes($row['product_name'])); ?>\'?');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="no-products-row">
                            <td colspan="8" class="no-products">
                                <i class="fa-solid fa-box-open"></i>
                                <p>No products found in store inventory.</p>
                                <a href="add-product.php">
                                    <i class="fa-solid fa-plus"></i> Add Your First Product
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

<script>
function filterProducts() {
    const searchValue = document.getElementById('search-input').value.toLowerCase().trim();
    const categoryValue = document.getElementById('category-filter').value.toLowerCase();
    const statusValue = document.getElementById('status-filter').value.toLowerCase();

    const table = document.getElementById('productTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        if (row.id === 'no-products-row') continue;

        const nameData = row.getAttribute('data-name') || '';
        const catData = row.getAttribute('data-category') || '';
        const statusData = row.getAttribute('data-status') || '';

        const matchesSearch = searchValue === '' || nameData.includes(searchValue);
        const matchesCategory = categoryValue === 'all' || catData === categoryValue;
        const matchesStatus = statusValue === 'all' || statusData === statusValue;

        if (matchesSearch && matchesCategory && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
}
</script>

</body>
</html>