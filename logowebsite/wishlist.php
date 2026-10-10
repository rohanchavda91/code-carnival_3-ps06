<?php
session_start();

include_once("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['remove_wishlist'])) {
    $productId = (int)$_GET['remove_wishlist'];
    if (($_SESSION['user_type'] ?? '') === 'user' && !empty($_SESSION['user_id'])) {
        remove_user_product($conn, 'wishlist', (int)$_SESSION['user_id'], $productId);
    } else {
        $wishlistIndex = array_search($productId, $_SESSION['wishlist'], true);
        if ($wishlistIndex !== false) {
            unset($_SESSION['wishlist'][$wishlistIndex]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        }
    }

    header("Location: wishlist.php");
    exit;
}
include("includes/header.php");
include("includes/navbar.php");


$products = [];
$result = mysqli_query($conn, "SELECT * FROM products WHERE LOWER(TRIM(COALESCE(status, ''))) <> 'out of stock' AND stock > 0 ORDER BY product_id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => (int)$row['product_id'],
        'name' => $row['product_name'],
        'description' => $row['description'],
        'brand' => $row['category'],
        'category' => strtolower($row['category']),
        'price' => (float)$row['price'],
        'old_price' => (float)$row['price'] + 200,
        'image' => 'uploads/' . $row['image']
    ];
}

$wishlistItems = [];
$wishlistIds = ($_SESSION['user_type'] ?? '') === 'user' && !empty($_SESSION['user_id'])
    ? get_user_product_ids($conn, 'wishlist', (int)$_SESSION['user_id'])
    : $_SESSION['wishlist'];
if (!empty($wishlistIds)) {
    foreach ($products as $product) {
        if (in_array($product['id'], $wishlistIds, true)) {
            $wishlistItems[] = $product;
        }
    }
}
?>

<section class="booking-section wishlist-section">
    <div class="booking-container wishlist-page">
        <h2>Your Wishlist</h2>
        <p class="booking-text">Saved beauty products will appear here.</p>

        <?php if (!empty($_GET['added'])) : ?>
            <div class="wishlist-note">Product added to your wishlist successfully.</div>
        <?php endif; ?>

        <?php if (empty($wishlistItems)) : ?>
            <div class="empty-state wishlist-empty">
                <h3>No beauties saved yet.</h3>
                <p>Visit the shop and tap the wishlist button to save your favourites.</p>
            </div>
        <?php else : ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlistItems as $product) : ?>
                    <div class="wishlist-card">
                        <div class="wishlist-image-wrap">
                            <a href="wishlist.php?remove_wishlist=<?php echo $product['id']; ?>" class="wishlist-heart" title="Remove from wishlist">♥</a>
                            <img src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <h4>₹<?php echo number_format($product['price']); ?></h4>
                        <a href="shop.php?search=<?php echo urlencode($product['name']); ?>" class="btn">View Product</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include("includes/footer.php"); ?>
