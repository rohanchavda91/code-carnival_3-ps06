<?php
session_start();

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

include_once("config.php");
include_once("includes/order_helpers.php");

if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_id'] <= 0 || ($_SESSION['user_type'] ?? '') !== 'user') {
    header("Location: login.php?msg=" . urlencode("Please login to view your cart."));
    exit;
}

ensure_store_tables($conn);

if (isset($_GET['checkout'])) {
    header("Location: checkout.php?from_cart=1");
    exit;
}

if (isset($_GET['buy_now'])) {
    $productId = (int)$_GET['buy_now'];
    header("Location: checkout.php?buy_now=" . $productId . "&from=cart");
    exit;
}

if (isset($_GET['remove_from_cart'])) {
    $productId = (int)$_GET['remove_from_cart'];
    if (($_SESSION['user_type'] ?? '') === 'user' && !empty($_SESSION['user_id'])) {
        remove_user_product($conn, 'cart', (int)$_SESSION['user_id'], $productId);
    } else {
        $cartIndex = array_search($productId, $_SESSION['cart'], true);
        if ($cartIndex !== false) {
            unset($_SESSION['cart'][$cartIndex]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }

    header("Location: cart.php");
    exit;
}

$products = [];
$result = mysqli_query($conn, "SELECT * FROM products WHERE LOWER(TRIM(COALESCE(status, ''))) <> 'hidden' AND LOWER(TRIM(COALESCE(status, ''))) <> 'out of stock' AND stock > 0 ORDER BY product_id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => (int)$row['product_id'],
        'name' => $row['product_name'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'image' => 'uploads/' . $row['image']
    ];
}

$cartItems = [];
$cartIds = ($_SESSION['user_type'] ?? '') === 'user' && !empty($_SESSION['user_id'])
    ? get_user_product_ids($conn, 'cart', (int)$_SESSION['user_id'])
    : $_SESSION['cart'];
if (!empty($cartIds)) {
    foreach ($products as $product) {
        if (in_array($product['id'], $cartIds, true)) {
            $cartItems[] = $product;
        }
    }
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'];
}

include("includes/header.php");
include("includes/navbar.php");
?>

<section class="cart-page">
    <div class="cart-hero">
        <div>
            <p class="eyebrow">Luxury Shopping Bag</p>
            <h2>Your Beauty Cart</h2>
            <p>Curated essentials and premium beauty picks are waiting for you.</p>
        </div>
        <a href="shop.php" class="btn">Continue Shopping</a>
    </div>

    <div class="cart-layout">
        <div class="cart-items">
            <?php if (empty($cartItems)) : ?>
                <div class="empty-state">
                    <h3>Your cart is empty.</h3>
                    <p>Add products from the shop to see them here.</p>
                </div>
            <?php else : ?>
                <?php foreach ($cartItems as $item) : ?>
                    <div class="cart-item-card">
                        <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="cart-meta">
                                <span>Qty: 1</span>
                                <span>₹<?php echo number_format($item['price']); ?></span>
                            </div>
                        </div>
                        <div class="cart-item-actions">
                            <a href="cart.php?remove_from_cart=<?php echo $item['id']; ?>" class="btn small-btn">Remove</a>
                            <a href="checkout.php?buy_now=<?php echo $item['id']; ?>&from=cart" class="btn small-btn buy-item-btn">Buy Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <aside class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($subtotal); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            <div class="summary-row">
                <span>Discount</span>
                <span>-₹200</span>
            </div>
            <div class="summary-row total-row">
                <span>Total</span>
                <span>₹<?php echo number_format(max($subtotal - 200, 0)); ?></span>
            </div>
            <a href="checkout.php?from_cart=1" class="btn full-width">Proceed to Checkout</a>
            <p class="summary-note">Free delivery on orders above ₹2,000.</p>
        </aside>
    </div>
</section>

<?php include("includes/footer.php"); ?>
