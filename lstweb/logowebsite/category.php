<?php
session_start();

include_once("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);
include("includes/header.php");
include("includes/navbar.php");

$categories = [];
$category_settings = get_category_settings_map($conn);
$deleted_category_slugs = [];
$deleted_result = mysqli_query($conn, "SELECT slug FROM category_settings WHERE is_deleted = 1");
if ($deleted_result) {
    while ($deleted_row = mysqli_fetch_assoc($deleted_result)) {
        $deleted_slug = normalize_category_slug($deleted_row['slug'] ?? '');
        if ($deleted_slug !== '') {
            $deleted_category_slugs[$deleted_slug] = true;
        }
    }
}
$default_description = 'Explore our latest products in this beauty collection.';

function get_category_image_url($image)
{
    $image = trim((string)$image);
    if ($image === '') {
        return 'images/beautytools.jpg';
    }

    if (strpos($image, '/') !== false || strpos($image, '\\') !== false) {
        return $image;
    }

    return 'uploads/' . $image;
}

foreach ($category_settings as $slug => $settings) {
    $normalized_slug = normalize_category_slug($slug);
    if ($normalized_slug === '') {
        continue;
    }

    $categories[$normalized_slug] = [
        'title' => $settings['title'] ?? ucfirst($normalized_slug),
        'image' => get_category_image_url($settings['image'] ?? ''),
        'description' => !empty($settings['description']) ? $settings['description'] : $default_description,
        'slug' => $normalized_slug,
    ];
}

$category_result = mysqli_query($conn, "SELECT category FROM products WHERE stock > 0 AND LOWER(TRIM(COALESCE(status, ''))) <> 'hidden' AND TRIM(category) <> '' GROUP BY category ORDER BY category ASC");
if ($category_result) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $slug = normalize_category_slug($row['category'] ?? '');
        if ($slug === '') {
            continue;
        }

        if (isset($deleted_category_slugs[$slug])) {
            continue;
        }

        $settings = $category_settings[$slug] ?? [
            'title' => trim((string)($row['category'] ?? '')),
            'image' => 'images/beautytools.jpg',
            'description' => $default_description
        ];

        $category = [
            'title' => $settings['title'] ?? trim((string)($row['category'] ?? '')),
            'image' => get_category_image_url($settings['image'] ?? ''),
            'description' => !empty($settings['description']) ? $settings['description'] : $default_description,
            'slug' => $slug,
        ];
        $categories[$slug] = $category;
    }
}

ksort($categories);
$categories = array_values($categories);
?>

<!-- ================= CATEGORY BANNER ================= -->

<section class="category-banner">

    <div class="banner-content">

        <h1>Beauty Categories</h1>

        <p>
            Discover our premium beauty products and services
            for every beauty need.
        </p>

    </div>

</section>

<!-- ================= CATEGORY SECTION ================= -->

<section class="category-section">

    <h2>Shop By Category</h2>

    <p class="category-text">

        Choose your favourite beauty category and explore
        premium quality products.

    </p>

    <div class="category-grid">
        <?php if (empty($categories)): ?>
            <div class="empty-state"><h3>Categories are coming soon.</h3><p>New collections will appear here as products are added.</p></div>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <img src="<?php echo htmlspecialchars($category['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="category-card-content">
                        <h3><?php echo htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="shop.php?category=<?php echo urlencode($category['slug']); ?>" class="btn">Explore <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</section>

<!-- ================= CATEGORY BENEFITS ================= -->

<section class="category-benefits">

    <div class="benefit-box">

        <i class="fa-solid fa-truck-fast"></i>

        <h3>Fast Delivery</h3>

        <p>Get your products delivered quickly.</p>

    </div>

    <div class="benefit-box">

        <i class="fa-solid fa-shield-heart"></i>

        <h3>100% Genuine Products</h3>

        <p>Only trusted beauty brands available.</p>

    </div>

    <div class="benefit-box">

        <i class="fa-solid fa-tags"></i>

        <h3>Best Offers</h3>

        <p>Enjoy exciting discounts every day.</p>

    </div>

</section>

<!-- ================= FEATURED BRANDS ================= -->

<section class="featured-brands">

    <h2>Our Premium Brands</h2>

    <p>We proudly offer products from trusted beauty brands.</p>

    <div class="brand-grid">

        <div class="brand-card">
            <img src="images/lakme.png" >
            <h3>Lakme</h3>
        </div>

        <div class="brand-card">
            <img src="images/image copy 22.png">
            <h3>Maybelline</h3>
        </div>

        <div class="brand-card">
            <img src="images/image copy 23.png">
            <h3>L'Oréal</h3>
        </div>

        <div class="brand-card">
            <img src="images/image copy 24.png">
            <h3>Mamaearth</h3>
        </div>

    </div>

</section>

<!-- ================= SPECIAL OFFER ================= -->

<section class="special-offer">

    <div class="offer-content">

        <h2>Glow More, Spend Less!</h2>

        <p>

            Flat <strong>30% OFF</strong> on selected beauty products.
            Hurry! Limited Time Offer.

        </p>

        <a href="shop.php" class="btn">

            Shop Now

        </a>

    </div>

</section>

<!-- ================= BEAUTY TIPS ================= -->

<section class="beauty-tips">

    <h2>Beauty Tips</h2>

    <div class="tips-grid">

        <div class="tip-card">

            <i class="fa-solid fa-spa"></i>

            <h3>Healthy Skin</h3>

            <p>

                Cleanse, moisturize and always wear sunscreen.

            </p>

        </div>

        <div class="tip-card">

            <i class="fa-solid fa-droplet"></i>

            <h3>Stay Hydrated</h3>

            <p>

                Drink enough water daily for naturally glowing skin.

            </p>

        </div>

        <div class="tip-card">

            <i class="fa-solid fa-scissors"></i>

            <h3>Hair Care</h3>

            <p>

                Trim your hair regularly to reduce split ends.

            </p>

        </div>

    </div>

</section>

<!-- ================= BOOK NOW ================= -->

<section class="book-now">

    <div class="book-now-card">
        <div class="book-now-content">
            <span class="book-now-badge">Beauty Appointment</span>
            <h2>Book your appointment today</h2>
            <p>
                Let GlowNest make you shine with premium beauty care, expert styling and a relaxed luxury experience.
            </p>
            <a href="booking.php" class="btn btn-light">
                Book Appointment
            </a>
        </div>

        <div class="brand-showcase">
            <div class="brand-showcase-grid">
                <img src="images/lakme.png" alt="Lakme">
                <img src="images/image copy 22.png" alt="Maybelline">
                <img src="images/image copy 23.png" alt="L'Oréal">
                <img src="images/image copy 24.png" alt="Mamaearth">
            </div>
        </div>
    </div>

</section>
<br><br>

<?php
include("includes/footer.php");
?>
