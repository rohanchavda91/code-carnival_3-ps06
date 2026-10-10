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

ensure_store_tables($conn);

$query = "SELECT * FROM products ORDER BY product_id DESC";
$result = mysqli_query($conn, $query);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        "id" => (int)$row['product_id'],
        "name" => $row['product_name'],
        "description" => $row['description'],
        "brand" => $row['brand'] ?? '',
        "category" => strtolower(trim($row['category'] ?? '')),
        "status" => $row['status'] ?? 'Available',
        "price" => (float)$row['price'],
        "old_price" => (float)$row['price'] + 200,
        "rating" => 5,
        "reviews" => 120,
        "image" => "uploads/" . $row['image']
    ];
}

function matchPrice($price, $selectedPrices)
{
    foreach ($selectedPrices as $range) {
        if ($range === "under-500" && $price < 500) {
            return true;
        }

        if ($range === "500-1000" && $price >= 500 && $price <= 1000) {
            return true;
        }

        if ($range === "above-1000" && $price > 1000) {
            return true;
        }
    }

    return false;
}

function renderHiddenFilters($name, $values)
{
    if (empty($values)) {
        return;
    }

    foreach ($values as $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '[]" value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">';
    }
}

function normalizeCategory($category)
{
    return preg_replace('/[^a-z0-9]/', '', strtolower(trim($category)));
}

/* ================= WISHLIST ================= */

if (isset($_GET['toggle_wishlist'])) {

    $productId = (int)$_GET['toggle_wishlist'];

    if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {

        $userId = (int)$_SESSION['user_id'];

        $check = mysqli_prepare(
            $conn,
            "SELECT wishlist_id FROM wishlist
             WHERE user_id = ? AND product_id = ?"
        );

        mysqli_stmt_bind_param($check, "ii", $userId, $productId);
        mysqli_stmt_execute($check);

        $result = mysqli_stmt_get_result($check);
        $exists = mysqli_fetch_assoc($result);

        mysqli_stmt_close($check);

        if ($exists) {

            $delete = mysqli_prepare(
                $conn,
                "DELETE FROM wishlist
                 WHERE user_id = ? AND product_id = ?"
            );

            mysqli_stmt_bind_param($delete, "ii", $userId, $productId);
            mysqli_stmt_execute($delete);
            mysqli_stmt_close($delete);

        } else {

            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO wishlist (user_id, product_id)
                 VALUES (?, ?)"
            );

            mysqli_stmt_bind_param($insert, "ii", $userId, $productId);
            mysqli_stmt_execute($insert);
            mysqli_stmt_close($insert);
        }

    } else {

        $wishlistIndex = array_search(
            $productId,
            $_SESSION['wishlist'],
            true
        );

        if ($wishlistIndex === false) {
            $_SESSION['wishlist'][] = $productId;
        } else {
            unset($_SESSION['wishlist'][$wishlistIndex]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        }
    }

    $redirectParams = $_GET;
    unset($redirectParams['toggle_wishlist']);

    header("Location: shop.php?" . http_build_query($redirectParams));
    exit;
}

/* ================= CART ================= */

if (isset($_GET['toggle_cart'])) {

    $productId = (int)$_GET['toggle_cart'];

    if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {

        $userId = (int)$_SESSION['user_id'];

        $check = mysqli_prepare(
            $conn,
            "SELECT cart_id FROM cart
             WHERE user_id = ? AND product_id = ?"
        );

        mysqli_stmt_bind_param($check, "ii", $userId, $productId);
        mysqli_stmt_execute($check);

        $result = mysqli_stmt_get_result($check);
        $exists = mysqli_fetch_assoc($result);

        mysqli_stmt_close($check);

        if ($exists) {

            $delete = mysqli_prepare(
                $conn,
                "DELETE FROM cart
                 WHERE user_id = ? AND product_id = ?"
            );

            mysqli_stmt_bind_param($delete, "ii", $userId, $productId);
            mysqli_stmt_execute($delete);
            mysqli_stmt_close($delete);

        } else {

            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO cart (user_id, product_id)
                 VALUES (?, ?)"
            );

            mysqli_stmt_bind_param($insert, "ii", $userId, $productId);
            mysqli_stmt_execute($insert);
            mysqli_stmt_close($insert);
        }

    } 
    else
     {
        $cartIndex = array_search(
            $productId,
            $_SESSION['cart'],
            true
        );

        if ($cartIndex === false) {
            $_SESSION['cart'][] = $productId;
        } else {
            unset($_SESSION['cart'][$cartIndex]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }

    $redirectParams = $_GET;
    unset($redirectParams['toggle_cart']);

    header("Location: shop.php?" . http_build_query($redirectParams));
    exit;
}


if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {

    $userId = (int)$_SESSION['user_id'];

    $_SESSION['wishlist'] = [];

    $wishlistResult = mysqli_query(
        $conn,
        "SELECT product_id FROM wishlist
         WHERE user_id = $userId"
    );

    while ($row = mysqli_fetch_assoc($wishlistResult)) {
        $_SESSION['wishlist'][] = (int)$row['product_id'];
    }

    $_SESSION['cart'] = [];

    $cartResult = mysqli_query(
        $conn,
        "SELECT product_id FROM cart
         WHERE user_id = $userId"
    );

    while ($row = mysqli_fetch_assoc($cartResult)) {
        $_SESSION['cart'][] = (int)$row['product_id'];
    }
}

if (isset($_GET['view_product'])) {
    $viewProductId = (int)$_GET['view_product'];
    mysqli_query($conn, "UPDATE products SET view_count = view_count + 1 WHERE product_id = $viewProductId");

    $redirectParams = $_GET;
    unset($redirectParams['view_product']);
    header("Location: shop.php?" . http_build_query($redirectParams));
    exit;
}

if (isset($_GET['buy_now'])) {
    $productId = (int)$_GET['buy_now'];
    header("Location: checkout.php?buy_now=" . $productId);
    exit;
}

$selectedCategories = array();
if (!empty($_GET['category'])) {
    if (is_array($_GET['category'])) {
        $selectedCategories = array_map('trim', $_GET['category']);
    } else {
        $selectedCategories = array(trim($_GET['category']));
    }
}

$selectedPrices = array();
if (!empty($_GET['price'])) {
    if (is_array($_GET['price'])) {
        $selectedPrices = array_map('trim', $_GET['price']);
    } else {
        $selectedPrices = array(trim($_GET['price']));
    }
}

$search = trim($_GET['search'] ?? '');
$sort = trim($_GET['sort'] ?? '');

$normalizedSelectedCategories = array_map('normalizeCategory', $selectedCategories);

$shopCategories = [];
foreach (get_category_settings_map($conn) as $categorySlug => $settings) {
    $categoryLabel = trim((string)($settings['title'] ?? $categorySlug));
    if ($categorySlug !== '' && $categoryLabel !== '') {
        $shopCategories[normalizeCategory($categorySlug)] = $categoryLabel;
    }
}

foreach ($products as $product) {
    $categoryName = trim($product['category']);
    $categorySlug = normalizeCategory($categoryName);

    if ($categoryName !== '' && $categorySlug !== '') {
        $shopCategories[$categorySlug] = ucwords(str_replace(['-', '_'], ' ', $categoryName));
    }
}
ksort($shopCategories);

$matchesProductFilters = function ($product) use ($selectedPrices, $search, $normalizedSelectedCategories) {
    $matchesSearch = empty($search)
        || stripos($product['name'], $search) !== false
        || stripos($product['description'], $search) !== false
        || stripos($product['brand'], $search) !== false
        || stripos($product['category'], $search) !== false;

    $matchesCategory = empty($normalizedSelectedCategories)
        || in_array(normalizeCategory($product['category']), $normalizedSelectedCategories, true);

    $matchesPrice = empty($selectedPrices) || matchPrice($product['price'], $selectedPrices);

    return $matchesSearch && $matchesCategory && $matchesPrice;
};

$filteredProducts = array_values(array_filter($products, $matchesProductFilters));

$hasCategoryMatch = !empty($normalizedSelectedCategories) && count(array_filter($filteredProducts, function ($product) use ($normalizedSelectedCategories) {
    return in_array(normalizeCategory($product['category']), $normalizedSelectedCategories, true);
})) > 0;

usort($filteredProducts, function ($a, $b) use ($sort, $hasCategoryMatch, $normalizedSelectedCategories) {
    if ($hasCategoryMatch) {
        $aMatchesCategory = in_array(normalizeCategory($a['category']), $normalizedSelectedCategories, true);
        $bMatchesCategory = in_array(normalizeCategory($b['category']), $normalizedSelectedCategories, true);

        if ($aMatchesCategory !== $bMatchesCategory) {
            return $aMatchesCategory ? -1 : 1;
        }
    }

    if ($sort === 'low') {
        return $a['price'] <=> $b['price'];
    }
    if ($sort === 'high') {
        return $b['price'] <=> $a['price'];
    }
    if ($sort === 'rating') {
        return $b['rating'] <=> $a['rating'];
    }

    return 0;
});

$wishlistQuery = array(
    'search' => $search,
    'sort' => $sort
);

foreach ($selectedCategories as $category) {
    $wishlistQuery['category'][] = $category;
}

foreach ($selectedPrices as $price) {
    $wishlistQuery['price'][] = $price;
}

include("includes/header.php");
include("includes/navbar.php");
?>


<section class="shop-page">

        <div class="sidebar" id="shopSidebar">
            <div class="sidebar-header-mobile">
                <h3><i class="fa-solid fa-sliders"></i> Filter Products</h3>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close filters">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <h2>My Account</h2>

            <a href="login.php" class="side-btn">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login
            </a>

            <a href="signup.php" class="side-btn">
                <i class="fa-solid fa-user-plus"></i>
                Sign Up
            </a>

            <hr>

            <form action="shop.php" method="GET" class="filter-form">
                <h3>Categories</h3>

                <?php foreach ($shopCategories as $categorySlug => $categoryLabel): ?>
                    <label>
                        <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array($categorySlug, $normalizedSelectedCategories, true) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($categoryLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                <?php endforeach; ?>

                <hr>

                <h3>Price</h3>

                <label>
                    <input type="checkbox" name="price[]" value="under-500" <?php echo in_array('under-500', $selectedPrices, true) ? 'checked' : ''; ?>>
                    Under ₹500
                </label>

                <label>
                    <input type="checkbox" name="price[]" value="500-1000" <?php echo in_array('500-1000', $selectedPrices, true) ? 'checked' : ''; ?>>
                    ₹500 - ₹1000
                </label>

                <label>
                    <input type="checkbox" name="price[]" value="above-1000" <?php echo in_array('above-1000', $selectedPrices, true) ? 'checked' : ''; ?>>
                    Above ₹1000
                </label>

                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn">Apply Filters</button>
            </form>

        </div>


       <div class="products">

    <h1>Beauty Shop</h1>

    <p>
        Discover premium beauty products for glowing skin,
        healthy hair and flawless makeup.
    </p>

    <div class="shop-search">

        <form action="shop.php" method="GET" class="search-form">

            <?php renderHiddenFilters('category', $selectedCategories); ?>
            <?php renderHiddenFilters('price', $selectedPrices); ?>

            <input
                type="text"
                id="shop-search-input"
                name="search"
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Search Beauty Products..."
            >

            <button
                type="button"
                id="clear-shop-search"
                class="clear-shop-search"
                aria-label="Clear search"
                title="Clear search"
                <?php echo $search === '' ? 'hidden' : ''; ?>
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>

            <button type="submit" class="btn">Search</button>

        </form>

    </div>

    <div class="shop-filter-bar-mobile">
        <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle" aria-label="Open filters" title="Open filters">
            <i class="fa-solid fa-sliders"></i>
            <?php
            $activeFilterCount = count($selectedCategories) + count($selectedPrices);
            if ($activeFilterCount > 0): ?>
                <span class="filter-count-badge"><?php echo $activeFilterCount; ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- PRODUCTS MUST BE INSIDE .products -->

    <div class="product-grid">

        <?php if (empty($filteredProducts)) : ?>

            <div class="empty-state">

                <h3>No products match your selection.</h3>

                <p>
                    Try changing the search, brand, price or category filters.
                </p>

            </div>

        <?php else : ?>

            <?php foreach ($filteredProducts as $product) : ?>

                <div class="product-card<?php echo $product['status'] === 'Out of Stock' ? ' is-out-of-stock' : ''; ?>">

                    <div class="product-image-wrap">
                        <img
                            src="<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        >
                        <?php if ($product['status'] === 'Out of Stock') : ?>
                            <span class="out-of-stock-overlay">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <h3>
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>

                    <div class="rating">
                        ⭐⭐⭐⭐⭐ (<?php echo $product['reviews']; ?>)
                    </div>

                    <h4>
                        ₹<?php echo number_format($product['price']); ?>

                        <span>
                            ₹<?php echo number_format($product['old_price']); ?>
                        </span>
                    </h4>

                    <div class="product-buttons">

                        <a
                            href="shop.php?<?php echo http_build_query(
                                array_merge(
                                    $wishlistQuery,
                                    ['toggle_wishlist' => $product['id']]
                                )
                            ); ?>"
                            class="btn2"
                        >
                            <?php
                            echo in_array(
                                $product['id'],
                                $_SESSION['wishlist'],
                                true
                            )
                            ? '♥ Saved'
                            : '❤ Wishlist';
                            ?>
                        </a>
                        <a
                            href="shop.php?<?php echo http_build_query(
                                array_merge(
                                    $wishlistQuery,
                                    ['toggle_cart' => $product['id']]
                                )
                            ); ?>"
                            class="btn btn-cart<?php echo $product['status'] === 'Out of Stock' ? ' disabled' : ''; ?>"
                            <?php echo $product['status'] === 'Out of Stock' ? 'aria-disabled="true" onclick="return false;"' : ''; ?>
                        >
                            <?php
                            echo in_array(
                                $product['id'],
                                $_SESSION['cart'],
                                true
                            )
                            ? '✓ Added'
                            : 'Add to Cart';
                            ?>
                        </a>
                        <a
                            href="checkout.php?buy_now=<?php echo $product['id']; ?>"
                            class="btn btn-secondary<?php echo $product['status'] === 'Out of Stock' ? ' disabled' : ''; ?>"
                            <?php echo $product['status'] === 'Out of Stock' ? 'aria-disabled="true" onclick="return false;"' : ''; ?>
                        >
                            Buy Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>
</section> 

<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchForm = document.querySelector('.shop-search .search-form');
    var searchInput = document.getElementById('shop-search-input');
    var clearButton = document.getElementById('clear-shop-search');

    if (searchInput && clearButton) {
        searchInput.addEventListener('input', function () {
            clearButton.hidden = searchInput.value.trim() === '';
        });
    }

    if (clearButton && searchForm && searchInput) {
        clearButton.addEventListener('click', function () {
            searchInput.value = '';
            clearButton.hidden = true;
            searchForm.submit();
        });
    }

    // Mobile filter drawer toggle
    var filterToggle = document.getElementById('mobileFilterToggle');
    var sidebar = document.getElementById('shopSidebar');
    var sidebarClose = document.getElementById('sidebarCloseBtn');

    if (filterToggle && sidebar) {
        filterToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', function () {
            sidebar.classList.remove('active');
        });
    }
});
</script>

<!-- ================= SORT BY ================= -->

<div class="sort-section">

    <form action="shop.php" method="GET">
        <?php renderHiddenFilters('category', $selectedCategories); ?>
        <?php renderHiddenFilters('price', $selectedPrices); ?>
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <label><b>Sort By : </b></label>

        <select name="sort">
            <option value="" <?php echo $sort === '' ? 'selected' : ''; ?>>Select</option>
            <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest</option>
            <option value="low" <?php echo $sort === 'low' ? 'selected' : ''; ?>>Price : Low to High</option>
            <option value="high" <?php echo $sort === 'high' ? 'selected' : ''; ?>>Price : High to Low</option>
            <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Top Rated</option>
        </select>

        <button class="btn" type="submit">Apply</button>
    </form>

</div>

<!-- ================= TODAY'S OFFERS ================= -->

<section class="today-offer">

    <h2>Today's Beauty Offers</h2>

    <div class="offer-box">

        <div>

            <h3>🔥 Flat 30% OFF</h3>

            <p>On all Skin Care Products</p>

        </div>

        <div>

            <h3>🎁 Buy 2 Get 1 Free</h3>

            <p>Hair Care Collection</p>

        </div>

        <div>

            <h3>🚚 Free Delivery</h3>

            <p>Orders Above ₹999</p>

        </div>

    </div>

</section>

<?php
include("includes/footer.php");
?>