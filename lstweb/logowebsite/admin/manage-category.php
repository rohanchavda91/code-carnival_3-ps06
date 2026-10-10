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
ensure_category_settings_table($conn);

function get_admin_category_image_path($image)
{
    $image = trim((string)$image);
    if ($image === '') {
        return '';
    }

    if (strpos($image, 'images/') === 0) {
        return '../' . $image;
    }

    return '../uploads/' . $image;
}

$flash_msg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

$category_settings = get_category_settings_map($conn);
$category_rows = [];
foreach ($category_settings as $slug => $settings) {
    $normalized_slug = normalize_category_slug($slug);
    if ($normalized_slug === '') {
        continue;
    }

    $category_rows[$normalized_slug] = [
        'slug' => $normalized_slug,
        'title' => $settings['title'] ?? ucfirst($normalized_slug),
        'image' => $settings['image'] ?? '',
        'description' => $settings['description'] ?? '',
        'product_count' => 0,
    ];
}

$category_result = mysqli_query($conn, "SELECT category, COUNT(*) AS product_count FROM products WHERE TRIM(category) <> '' GROUP BY category ORDER BY category ASC");
if ($category_result) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $slug = normalize_category_slug($row['category'] ?? '');
        if ($slug === '') {
            continue;
        }

        $settings = $category_settings[$slug] ?? [
            'title' => trim((string)($row['category'] ?? '')),
            'image' => '',
            'description' => 'Explore our latest products in this beauty collection.'
        ];

        $category_rows[$slug] = [
            'slug' => $slug,
            'title' => $settings['title'] ?? trim((string)($row['category'] ?? '')),
            'image' => $settings['image'] ?? '',
            'description' => $settings['description'] ?? 'Explore our latest products in this beauty collection.',
            'product_count' => (int)($row['product_count'] ?? 0),
        ];
    }
}
ksort($category_rows);
$category_rows = array_values($category_rows);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $slug = normalize_category_slug($_POST['slug'] ?? '');
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $currentImage = trim((string)($_POST['current_image'] ?? ''));
    $newImage = $currentImage;

    if ($slug === '' || $title === '' || strlen($slug) > 100 || strlen($title) > 100) {
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Enter a valid category title and slug.'];
        header("Location: manage-category.php");
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_orig = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_orig, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($file_ext, $allowed, true)) {
            $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Only JPG, JPEG, PNG, WEBP, and GIF images are allowed.'];
            header("Location: manage-category.php");
            exit;
        }

        $upload_folder = "../uploads/";
        if (!file_exists($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        $newImage = 'cat_' . $slug . '_' . time() . '.' . $file_ext;
        if (!move_uploaded_file($file_tmp, $upload_folder . $newImage)) {
            $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Failed to upload category image.'];
            header("Location: manage-category.php");
            exit;
        }

        if (!empty($currentImage) && file_exists($upload_folder . $currentImage) && $currentImage !== $newImage) {
            unlink($upload_folder . $currentImage);
        }
    }

    $save = mysqli_prepare($conn, "INSERT INTO category_settings (slug, title, image, description, is_deleted) VALUES (?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE title = VALUES(title), image = VALUES(image), description = VALUES(description), is_deleted = 0");
    if (!$save) {
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Unable to save category settings.'];
        header("Location: manage-category.php");
        exit;
    }

    mysqli_stmt_bind_param($save, 'ssss', $slug, $title, $newImage, $description);
    if (!mysqli_stmt_execute($save)) {
        mysqli_stmt_close($save);
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Unable to save category. Please try again.'];
        header("Location: manage-category.php");
        exit;
    }
    mysqli_stmt_close($save);

    $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => 'Category saved successfully.'];
    header("Location: manage-category.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $slug = normalize_category_slug($_POST['slug'] ?? '');
    $currentImage = $category_settings[$slug]['image'] ?? '';
    $productCount = 0;

    if ($slug === '') {
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Category slug is missing.'];
        header("Location: manage-category.php");
        exit;
    }

    if ($slug === 'general') {
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'General category cannot be deleted because shop products use it as a fallback.'];
        header("Location: manage-category.php");
        exit;
    }

    $productMatchResult = mysqli_query($conn, "SELECT product_id, category FROM products WHERE TRIM(category) <> ''");
    if ($productMatchResult) {
        while ($productRow = mysqli_fetch_assoc($productMatchResult)) {
            $productCategory = trim((string)($productRow['category'] ?? ''));
            $productSlug = normalize_category_slug($productCategory);
            $candidateSlugs = [$productSlug, strtolower($productCategory), str_replace(' ', '-', strtolower($productCategory)), str_replace('_', '-', strtolower($productCategory))];
            if (in_array($slug, array_unique($candidateSlugs), true)) {
                $productCount++;
            }
        }
    }

    $moveResult = mysqli_query($conn, "SELECT product_id, category FROM products WHERE TRIM(category) <> ''");
    if ($moveResult) {
        while ($productRow = mysqli_fetch_assoc($moveResult)) {
            $productCategory = trim((string)($productRow['category'] ?? ''));
            $productSlug = normalize_category_slug($productCategory);
            $candidateSlugs = [$productSlug, strtolower($productCategory), str_replace(' ', '-', strtolower($productCategory)), str_replace('_', '-', strtolower($productCategory))];
            if (in_array($slug, array_unique($candidateSlugs), true)) {
                $productId = (int)($productRow['product_id'] ?? 0);
                if ($productId > 0) {
                    mysqli_query($conn, "UPDATE products SET category = 'general' WHERE product_id = $productId");
                }
            }
        }
    }

    $categoryResult = mysqli_query($conn, "SELECT slug FROM category_settings WHERE TRIM(slug) <> ''");
    if ($categoryResult) {
        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
            $categorySlug = normalize_category_slug($categoryRow['slug'] ?? '');
            if ($categorySlug === $slug) {
                mysqli_query($conn, "UPDATE category_settings SET is_deleted = 1 WHERE slug = '" . mysqli_real_escape_string($conn, $categoryRow['slug']) . "'");
            }
        }
    }

    $delete = mysqli_prepare($conn, "UPDATE category_settings SET is_deleted = 1 WHERE slug = ?");
    if (!$delete) {
        $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Unable to remove category. Please try again.'];
    } else {
        mysqli_stmt_bind_param($delete, 's', $slug);
        mysqli_stmt_execute($delete);
        mysqli_stmt_close($delete);

        if ($currentImage !== '' && file_exists("../uploads/" . $currentImage)) {
            unlink("../uploads/" . $currentImage);
        }

        $message = $productCount > 0
            ? 'Category removed successfully. Products were moved to General.'
            : 'Category removed successfully.';
        $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => $message];
    }

    header("Location: manage-category.php");
    exit;
}

if (isset($_GET['remove_image']) && !empty($_GET['slug'])) {
    $slug = normalize_category_slug($_GET['slug'] ?? '');
    $current = $category_settings[$slug]['image'] ?? '';

    if ($slug !== '') {
        $update = mysqli_prepare($conn, "INSERT INTO category_settings (slug, title, image, description) VALUES (?, ?, '', ?) ON DUPLICATE KEY UPDATE image = VALUES(image), title = VALUES(title), description = VALUES(description)");
        $currentSettings = $category_settings[$slug] ?? [];
        $defaultTitle = $currentSettings['title'] ?? ucfirst($slug);
        $defaultDescription = $currentSettings['description'] ?? 'Explore our latest products in this beauty collection.';

        if ($update) {
            mysqli_stmt_bind_param($update, 'sss', $slug, $defaultTitle, $defaultDescription);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        }

        if (!empty($current) && file_exists("../uploads/" . $current)) {
            unlink("../uploads/" . $current);
        }
    }

    $_SESSION['flash_msg'] = ['type' => 'success', 'msg' => 'Category image removed successfully.'];
    header("Location: manage-category.php");
    exit;
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
    <title>Manage Categories | GlowNest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/responsive.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .category-settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px; }
        .category-item { background: #fff; border: 1px solid #f0e5eb; border-radius: 18px; padding: 18px; box-shadow: 0 10px 18px rgba(0,0,0,0.04); }
        .category-item img { width: 100%; height: 180px; object-fit: contain; background: #fffafc; border-radius: 12px; padding: 14px; box-sizing: border-box; }
        .category-link-row { display: flex; justify-content: space-between; gap: 10px; align-items: center; margin-top: 14px; }
        .category-button { display: inline-flex; align-items: center; justify-content: center; padding: 10px 14px; border-radius: 10px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
        .category-button.primary { background: var(--admin-primary); color: #fff; }
        .category-button.secondary { background: #f4f1f6; color: #4d4858; }
        .category-button.danger { background: #fff0f2; color: #bd315b; }
        .category-button.danger:hover { background: #bd315b; color: #fff; }
        .category-form-panel { display: none; margin-top: 28px; }
        .category-form-panel.active { display: block; }
        .category-create-panel { display: grid; grid-template-columns: minmax(220px, .7fr) minmax(0, 1.3fr); gap: 28px; margin-bottom: 30px; padding: 24px; border: 1px solid #f0e5eb; border-radius: 16px; background: linear-gradient(135deg, #fff8fb, #fff); }
        .category-create-panel h3 { margin: 0 0 8px; color: #302832; }
        .category-create-panel p { margin: 0; color: #756d75; font-size: 13px; line-height: 1.6; }
        .category-create-form { display: grid; gap: 16px; }
        .category-create-form .form-row { margin: 0; }
        .category-create-form .form-group { gap: 6px; }
        .category-create-form .form-footer { margin-top: 0; }
        .category-image-preview { display: flex; align-items: center; gap: 14px; margin-top: 12px; padding: 12px; border: 1px solid #f0e5eb; border-radius: 12px; background: #fff; }
        .category-image-preview .preview-box { width: 76px; height: 76px; overflow: hidden; border-radius: 10px; background: #fffafc; }
        .category-image-preview img { width: 100%; height: 100%; object-fit: contain; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        @media (max-width: 768px) { .form-row, .category-create-panel { grid-template-columns: 1fr; } }
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
            <a href="manage-category.php" class="active">
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

    <main class="admin-main">
        <div class="admin-topbar">
            <div style="display:flex; align-items:center; gap:14px;">
                <button type="button" class="admin-mobile-toggle" onclick="toggleAdminSidebar()" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <p class="page-small-title">CATEGORY MANAGEMENT</p>
                    <h1>Manage Categories</h1>
                </div>
            </div>
            <a href="manage-product.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i>Back to Products</a>
        </div>

        <?php if ($flash_msg): ?>
            <div class="alert-banner alert-<?php echo htmlspecialchars($flash_msg['type'] ?? 'success'); ?>">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo htmlspecialchars($flash_msg['msg'] ?? ''); ?></span>
                <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        <?php endif; ?>

        <div class="product-form-card">
            <div class="form-heading">
                <div class="form-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <h2>Category Artwork</h2>
                    <p>Create categories and manage the image, title and description shown on the store.</p>
                </div>
            </div>

            <div class="category-create-panel">
                <div>
                    <h3>Create New Category</h3>
                    <p>Add a category now, then assign products to it from Add Product.</p>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" class="category-create-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new-category-title">Category Title</label>
                            <input id="new-category-title" type="text" name="title" placeholder="e.g. Body Care" maxlength="100" required>
                        </div>
                        <div class="form-group">
                            <label for="new-category-slug">Category Slug</label>
                            <input id="new-category-slug" type="text" name="slug" placeholder="e.g. body-care" maxlength="100" pattern="[A-Za-z0-9 -]+" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new-category-description">Description</label>
                        <textarea id="new-category-description" name="description" rows="3" placeholder="Short description for this category" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Category Image</label>
                        <div class="file-upload-box">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <div class="upload-text">Upload category image</div>
                            <div class="upload-hint">JPG, PNG, WEBP or GIF (Max 5MB)</div>
                            <input type="file" id="new-category-image" name="image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewCategoryImage(this, 'new-category-preview', 'new-category-file-name');">
                        </div>
                        <div class="category-image-preview" id="new-category-preview" style="display:none;">
                            <div class="preview-box"><img src="#" alt="New category preview"></div>
                            <div><small style="color: var(--admin-primary); font-weight:600;">Selected Image Preview</small><br><small id="new-category-file-name" style="color:#666;"></small></div>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="save_category" class="save-btn"><i class="fa-solid fa-plus"></i> Create Category</button>
                    </div>
                </form>
            </div>

            <div class="category-settings-grid">
                <?php foreach ($category_rows as $row): ?>
                    <?php $rowSlug = htmlspecialchars($row['slug']); ?>
                    <div class="category-item">
                                        <?php $adminImagePath = get_admin_category_image_path($row['image']); ?>
                                        <?php $adminImageFile = strpos($row['image'], 'images/') === 0 ? __DIR__ . '/../' . $row['image'] : __DIR__ . '/../uploads/' . $row['image']; ?>
                                        <?php if ($adminImagePath !== '' && file_exists($adminImageFile)): ?>
                                            <img src="<?php echo htmlspecialchars($adminImagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php else: ?>
                            <img src="../images/default-category.jpg" alt="<?php echo htmlspecialchars($row['title']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display:none; height:180px; align-items:center; justify-content:center; background:#fffafc; border-radius:12px; color:#b58ea0; font-weight:600;">No image</div>
                        <?php endif; ?>
                        <div class="category-link-row">
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                            <span><?php echo (int)$row['product_count']; ?> items</span>
                        </div>
                        <div class="category-link-row" style="margin-top: 12px;">
                            <button type="button" class="category-button secondary toggle-category-form" data-target="edit-category-<?php echo $row['slug']; ?>">Edit Category</button>
                            <?php if ($row['slug'] !== 'general'): ?>
                                <form action="" method="POST" onsubmit="return confirm('Remove this category and move its products to General?');" style="display:inline;">
                                    <input type="hidden" name="slug" value="<?php echo $rowSlug; ?>">
                                    <button type="submit" name="delete_category" class="category-button danger"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="category-button danger" disabled title="General category is reserved for products without a category."><i class="fa-solid fa-trash"></i> Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach ($category_rows as $row): ?>
            <div class="product-form-card category-form-panel" id="edit-category-<?php echo htmlspecialchars($row['slug']); ?>">
                <div class="form-heading">
                    <div class="form-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div>
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p>Update the category card used on the public website.</p>
                    </div>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="slug" value="<?php echo htmlspecialchars($row['slug']); ?>">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($row['image']); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title-<?php echo htmlspecialchars($row['slug']); ?>">Category Title</label>
                            <input id="title-<?php echo htmlspecialchars($row['slug']); ?>" type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="slug-<?php echo htmlspecialchars($row['slug']); ?>">Slug</label>
                            <input id="slug-<?php echo htmlspecialchars($row['slug']); ?>" type="text" value="<?php echo htmlspecialchars($row['slug']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description-<?php echo htmlspecialchars($row['slug']); ?>">Description</label>
                        <textarea id="description-<?php echo htmlspecialchars($row['slug']); ?>" name="description" rows="3" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Category Image</label>
                        <div class="file-upload-box">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <div class="upload-text">Click or drag & drop image here</div>
                            <div class="upload-hint">Supports JPG, PNG, WEBP, GIF (Max 5MB)</div>
                            <input type="file" id="category-image-<?php echo htmlspecialchars($row['slug']); ?>" name="image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewCategoryImage(this, 'category-preview-<?php echo htmlspecialchars($row['slug']); ?>', 'category-file-name-<?php echo htmlspecialchars($row['slug']); ?>');">
                        </div>
                        <div class="category-image-preview" id="category-preview-<?php echo htmlspecialchars($row['slug']); ?>" style="display:none;">
                            <div class="preview-box"><img src="#" alt="Category preview"></div>
                            <div><small style="color: var(--admin-primary); font-weight:600;">Selected Image Preview</small><br><small id="category-file-name-<?php echo htmlspecialchars($row['slug']); ?>" style="color:#666;"></small></div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <?php if (!empty($row['image'])): ?>
                            <a href="?remove_image=1&amp;slug=<?php echo urlencode($row['slug']); ?>" class="category-button danger" onclick="return confirm('Remove this category image?');"><i class="fa-solid fa-trash"></i> Remove Image</a>
                        <?php endif; ?>
                        <button type="submit" name="save_category" class="save-btn"><i class="fa-solid fa-check"></i> Save Category</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </main>
</div>

<script>
    function previewCategoryImage(input, previewId, fileNameId) {
        const previewWrapper = document.getElementById(previewId);
        const previewImg = previewWrapper ? previewWrapper.querySelector('img') : null;
        const fileName = document.getElementById(fileNameId);

        if (!previewWrapper || !previewImg || !fileName) {
            return;
        }

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function (event) {
                previewImg.src = event.target.result;
                fileName.textContent = file.name;
                previewWrapper.style.display = 'flex';
            };

            reader.readAsDataURL(file);
        } else {
            previewWrapper.style.display = 'none';
            previewImg.src = '#';
            fileName.textContent = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-category-form');
        const panels = document.querySelectorAll('.category-form-panel');

        toggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetPanel = document.getElementById(targetId);

                panels.forEach(function (panel) {
                    panel.classList.remove('active');
                });

                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });

    function toggleAdminSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    }
</script>
</body>
</html>
