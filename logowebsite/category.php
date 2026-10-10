<?php
session_start();

include_once("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);
include("includes/header.php");
include("includes/navbar.php");

$category_defaults = [
    'makeup' => ['title' => 'Makeup', 'image' => 'images/image copy 13.png', 'description' => 'Lipstick, foundation, primer, compact, kajal and more.'],
    'skincare' => ['title' => 'Skin Care', 'image' => 'images/image copy 5.png', 'description' => 'Face wash, serum, moisturizer and sunscreen.'],
    'haircare' => ['title' => 'Hair Care', 'image' => 'images/image copy 6.png', 'description' => 'Shampoo, conditioner, hair oil and hair masks.'],
    'nailcare' => ['title' => 'Nail Care', 'image' => 'images/image copy 4.png', 'description' => 'Nail polish, nail art, remover and nail kits.'],
    'fragrance' => ['title' => 'Fragrances', 'image' => 'images/perfume.jpg', 'description' => 'Perfumes, body mist, deodorants and gift sets.'],
    'tools' => ['title' => 'Beauty Tools', 'image' => 'images/beautytools.jpg', 'description' => 'Makeup brushes, blenders, dryers and trimmers.'],
];

$categories = [];
$category_settings = get_category_settings_map($conn);
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
    $categories[$slug] = [
        'title' => $settings['title'] ?? ucfirst($slug),
        'image' => get_category_image_url($settings['image'] ?? ''),
        'description' => !empty($settings['description']) ? $settings['description'] : $default_description,
        'slug' => $slug,
    ];
}

$category_result = mysqli_query($conn, "SELECT category FROM products WHERE stock > 0 AND TRIM(category) <> '' GROUP BY category ORDER BY category ASC");
if ($category_result) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $slug = strtolower(trim($row['category']));
        $settings = $category_settings[$slug] ?? [
            'title' => trim($row['category']),
            'image' => 'images/beautytools.jpg',
            'description' => 'Explore our latest products in this beauty collection.'
        ];

        $category = [
            'title' => $settings['title'] ?? trim($row['category']),
            'image' => get_category_image_url($settings['image'] ?? ''),
            'description' => !empty($settings['description']) ? $settings['description'] : $default_description,
            'slug' => $slug,
        ];
        $categories[$slug] = $category;
    }
}
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
