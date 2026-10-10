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

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name'] ?? '');
    $brand        = trim($_POST['brand'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $price        = floatval($_POST['price'] ?? 0);
    $stock        = intval($_POST['stock'] ?? 0);
    $status_value = trim($_POST['status'] ?? 'Available');
    $status       = normalize_product_status($status_value, $stock);

    // Validation
    if (empty($product_name) || empty($category) || $price < 0 || $stock < 0) {
        $error_msg = "Please fill in all required fields accurately.";
    } else {
        $image_name = "";
        $upload_folder = "../uploads/";

        if (!file_exists($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp   = $_FILES['image']['tmp_name'];
            $file_orig  = $_FILES['image']['name'];
            $file_ext   = strtolower(pathinfo($file_orig, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($file_ext, $allowed_exts)) {
                $image_name = uniqid("prod_") . "_" . time() . "." . $file_ext;
                move_uploaded_file($file_tmp, $upload_folder . $image_name);
            } else {
                $error_msg = "Invalid image file format. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.";
            }
        }

        if (empty($error_msg)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (product_name, brand, category, description, price, stock, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssdiss", $product_name, $brand, $category, $description, $price, $stock, $image_name, $status);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    $_SESSION['flash_msg'] = [
                        'type' => 'success',
                        'msg'  => "Product '" . htmlspecialchars($product_name) . "' has been added successfully!"
                    ];
                    header("Location: manage-product.php");
                    exit;
                } else {
                    $error_msg = "Database Error: " . mysqli_error($conn);
                }
            } else {
                $error_msg = "Failed to prepare database query.";
            }
        }
    }
}

// Fetch badge counts
$apptStats = get_appointment_stats($conn);
$pending_appointments = $apptStats['pending'] ?? 0;
$msgStats = get_contact_messages_stats($conn);
$new_messages = $msgStats['new'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
            <a href="add-product.php" class="active">
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

        <div class="admin-topbar">
            <div style="display:flex; align-items:center; gap:14px;">
                <button type="button" class="admin-mobile-toggle" onclick="toggleAdminSidebar()" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <p class="page-small-title">PRODUCT MANAGEMENT</p>
                    <h1>Add New Product</h1>
                </div>
            </div>
            <a href="manage-product.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Products
            </a>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-banner alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <!-- FORM CARD -->
        <div class="product-form-card">

            <div class="form-heading">
                <div class="form-icon">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <h2>Product Details</h2>
                    <p>Fill in the required product specifications to list it on your store.</p>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">

                <div class="form-grid">

                    <!-- Product Name -->
                    <div class="form-group">
                        <label for="product_name">
                            Product Name <span class="required-star">*</span>
                        </label>
                        <input
                            type="text"
                            id="product_name"
                            name="product_name"
                            placeholder="e.g. Glowing Face Serum"
                            value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>"
                            required
                        >
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category">
                            Category <span class="required-star">*</span>
                        </label>
                        <input
                            type="text"
                            id="category"
                            name="category"
                            list="category-suggestions"
                            placeholder="e.g. Skincare, Makeup, Haircare"
                            value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>"
                            required
                        >
                        <datalist id="category-suggestions">
                            <option value="Skincare">
                            <option value="Makeup">
                            <option value="Haircare">
                            <option value="Fragrance">
                            <option value="Body Care">
                        </datalist>
                    </div>

                    <!-- Brand -->
                    <div class="form-group">
                        <label for="brand">Brand Name</label>
                        <input
                            type="text"
                            id="brand"
                            name="brand"
                            placeholder="e.g. Lakme, Maybelline"
                            value="<?php echo htmlspecialchars($_POST['brand'] ?? ''); ?>"
                        >
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="price">
                            Price (₹) <span class="required-star">*</span>
                        </label>
                        <div class="input-icon">
                            <span>₹</span>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                placeholder="0.00"
                                min="0"
                                step="0.01"
                                value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                                required
                            >
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="form-group">
                        <label for="stock">
                            Stock Quantity <span class="required-star">*</span>
                        </label>
                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            placeholder="Enter quantity available"
                            min="0"
                            value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>"
                            required
                        >
                    </div>

                    <!-- Description -->
                    <div class="form-group full-width">
                        <label for="description">
                            Product Description <span class="required-star">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Write a clear and engaging product description..."
                            required
                        ><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Image Upload with Live Preview -->
                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="file-upload-box" id="drop-zone">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <div class="upload-text">Click or drag & drop image here</div>
                            <div class="upload-hint">Supports JPG, PNG, WEBP, GIF (Max 5MB)</div>
                            <input type="file" id="image-input" name="image" accept="image/*" onchange="previewImage(this);">
                        </div>
                        <div class="preview-container" id="preview-wrapper" style="display: none;">
                            <div class="preview-box">
                                <img id="image-preview" src="#" alt="Preview">
                            </div>
                            <div>
                                <small style="color: var(--admin-primary); font-weight: 600;">Selected Image Preview</small><br>
                                <small id="file-name-display" style="color: #666;"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Availability Status</label>
                        <select id="status" name="status">
                            <option value="Available" <?php echo (($_POST['status'] ?? 'Available') === 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Out of Stock" <?php echo (($_POST['status'] ?? 'Available') === 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
                            <option value="Hidden" <?php echo (($_POST['status'] ?? 'Available') === 'Hidden') ? 'selected' : ''; ?>>Hidden (temporarily hidden from shop)</option>
                        </select>
                    </div>

                </div>

                <div class="form-footer">
                    <button type="reset" class="cancel-btn" onclick="document.getElementById('preview-wrapper').style.display='none';">
                        <i class="fa-solid fa-rotate-left"></i> Reset Form
                    </button>
                    <button type="submit" name="add_product" class="save-btn">
                        <i class="fa-solid fa-plus"></i> Save Product
                    </button>
                </div>

            </form>

        </div>

    </main>

</div>

<script>
function previewImage(input) {
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewImg = document.getElementById('image-preview');
    const fileNameDisplay = document.getElementById('file-name-display');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            fileNameDisplay.textContent = file.name;
            previewWrapper.style.display = 'flex';
        }

        reader.readAsDataURL(file);
    } else {
        previewWrapper.style.display = 'none';
    }
}

function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}
</script>

</body>
</html>