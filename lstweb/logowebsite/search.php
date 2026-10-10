<?php
session_start();
include_once("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);

$search = trim($_GET['q'] ?? '');
$products = [];
$categories = [];

$product_result = mysqli_query($conn, "SELECT product_id, product_name, brand, category, description, price, image FROM products WHERE LOWER(TRIM(COALESCE(status, ''))) <> 'hidden' AND LOWER(TRIM(COALESCE(status, ''))) <> 'out of stock' AND stock > 0 ORDER BY product_id DESC");
if ($product_result) {
    while ($row = mysqli_fetch_assoc($product_result)) {
        $products[] = $row;
        $category_name = trim($row['category']);
        if ($category_name !== '' && !isset($categories[strtolower($category_name)])) {
            $categories[strtolower($category_name)] = $category_name;
        }
    }
}

$matching_products = array_filter($products, function ($product) use ($search) {
    if ($search === '') {
        return true;
    }

    return stripos($product['product_name'], $search) !== false
        || stripos((string)$product['description'], $search) !== false
        || stripos((string)$product['brand'], $search) !== false
        || stripos($product['category'], $search) !== false;
});

$matching_categories = array_filter($categories, function ($category) use ($search) {
    return $search === '' || stripos($category, $search) !== false;
});

include("includes/header.php");
include("includes/navbar.php");
?>

<section class="search-page">
    <div class="search-hero">
        <span class="search-eyebrow"><i class="fa-solid fa-sparkles"></i> GlowNest discovery</span>
        <h1>Find your next beauty essential</h1>
        <p>Search products, browse categories, and discover something made for your routine.</p>

        <form action="search.php" method="GET" class="catalog-search-form">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="search" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search lipstick, serum, hair care..." autofocus>
            <button type="submit" class="btn">Search</button>
        </form>

        <div class="search-tools">
            <button type="button" class="search-tool-button" onclick="document.getElementById('gallery-search').click()">
                <i class="fa-regular fa-images"></i> Choose from gallery
            </button>
            <button type="button" class="search-tool-button" onclick="document.getElementById('camera-search').click()">
                <i class="fa-solid fa-camera"></i> Use camera
            </button>
            <input id="gallery-search" class="visually-hidden" type="file" accept="image/*" onchange="showSearchFile(this)">
            <input id="camera-search" class="visually-hidden" type="file" accept="image/*" capture="environment" onchange="showSearchFile(this)">
        </div>
        <p id="search-file-status" class="search-file-status" aria-live="polite"></p>
    </div>

    <?php if ($search !== '' && !empty($matching_categories)): ?>
        <div class="search-results-heading">
            <div>
                <span class="search-eyebrow">Collections</span>
                <h2>Matching categories</h2>
            </div>
        </div>
        <div class="search-category-list">
            <?php foreach ($matching_categories as $category): ?>
                <a class="search-category-chip" href="shop.php?category=<?php echo urlencode(strtolower($category)); ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="search-results-heading">
        <div>
            <span class="search-eyebrow">Catalog</span>
            <h2><?php echo $search === '' ? 'Explore all products' : 'Search results'; ?></h2>
        </div>
        <span class="search-result-count"><?php echo count($matching_products); ?> products found</span>
    </div>

    <div class="product-grid search-product-grid">
        <?php if (empty($matching_products)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h3>No products found</h3>
                <p>Try a different product name or explore all categories in the shop.</p>
                <a class="btn" href="shop.php">Browse shop</a>
            </div>
        <?php else: ?>
            <?php foreach ($matching_products as $product): ?>
                <article class="product-card">
                    <?php $product_image = trim($product['image']) !== '' ? 'uploads/' . $product['image'] : 'images/beautytools.jpg'; ?>
                    <img src="<?php echo htmlspecialchars($product_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="product-category-label"><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <h3><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($product['description'] ?? 'Premium beauty essential.', ENT_QUOTES, 'UTF-8'); ?></p>
                    <h4>Rs. <?php echo number_format((float)$product['price']); ?></h4>
                    <a href="shop.php?search=<?php echo urlencode($product['product_name']); ?>" class="btn btn-cart">View in shop <i class="fa-solid fa-arrow-right"></i></a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
function showSearchFile(input) {
    var status = document.getElementById('search-file-status');
    status.textContent = input.files.length ? input.files[0].name + ' selected. Type a product name to search the catalog.' : '';
}
</script>
