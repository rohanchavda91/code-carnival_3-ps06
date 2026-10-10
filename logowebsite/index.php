<?php
session_start();

include_once("config.php");

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        subscriber_id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_subscribe'])) {
    $subscriberEmail = trim($_POST['email'] ?? '');

    if (!filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?subscribe=error");
        exit;
    }

    $subscriberQuery = mysqli_prepare(
        $conn,
        "INSERT INTO newsletter_subscribers (email) VALUES (?)
         ON DUPLICATE KEY UPDATE email = VALUES(email)"
    );
    mysqli_stmt_bind_param($subscriberQuery, "s", $subscriberEmail);

    if (mysqli_stmt_execute($subscriberQuery)) {
        mysqli_stmt_close($subscriberQuery);
        header("Location: index.php?subscribe=success");
        exit;
    }

    mysqli_stmt_close($subscriberQuery);
    header("Location: index.php?subscribe=error");
    exit;
}

include("includes/header.php");
include("includes/navbar.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

$query = "SELECT * FROM products WHERE LOWER(TRIM(COALESCE(status, ''))) <> 'out of stock' AND stock > 0 ORDER BY view_count DESC, product_id DESC LIMIT 4";
$result = mysqli_query($conn, $query);
$featuredProducts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $featuredProducts[] = $row;
}
?>

<!-- ================= HOME SECTION ================= -->

<section class="hero" id="home">

    <div class="hero-text">

        <span class="eyebrow">
            ✦ Beauty. Confidence. You.
        </span>

        <h1>
            Glow Every Day
            <br>
            with <span>GlowNest</span>
        </h1>

        <p>
            Book your favorite beauty services and
            <br>
            shop the best makeup products – all in one place!
        </p>


         <div class="hero-buttons">

            <a href="booking.php" class="btn">
                <i class="fa-regular fa-calendar"></i>
                Book Appointment
            </a>

            <a href="category.php" class="btn btn-secondary">
                <i class="fa-solid fa-bag-shopping"></i>
                Shop Now
            </a>

        </div>
         <div class="hero-features">

            <div class="feature">
                <i class="fa-solid fa-award"></i>
                <span>Expert Beauticians</span>
            </div>

            <div class="feature">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>Premium Products</span>
            </div>

            <div class="feature">
                <i class="fa-solid fa-shield-heart"></i>
                <span>Hygienic & Safe</span>
            </div>

        </div>

    </div>

    <div class="hero-image">
        <img src="images/image.png" alt="Beauty Salon">

    </div>

</section>

<!-- ================= FEATURES ================= -->

<section class="features">

    <div class="box">

        <i class="fa-solid fa-spa"></i>

        <h3>Premium Services</h3>

        <p>
            Quality beauty care with experienced professionals.
        </p>

    </div>

    <div class="box">

        <i class="fa-solid fa-user-check"></i>

        <h3>Expert Beauticians</h3>

        <p>
            Certified beauty specialists.
        </p>

    </div>

    <div class="box">

        <i class="fa-solid fa-heart"></i>

        <h3>Safe Products</h3>

        <p>
            100% skin-friendly beauty products.
        </p>

    </div>

    <div class="box">

        <i class="fa-solid fa-gem"></i>

        <h3>Luxury Experience</h3>

        <p>
            Relaxing ambience with premium care.
        </p>

    </div>

</section>

<!-- ================= SERVICES ================= -->

<section class="services" id="category">
    <h2>Our Popular Services</h2>
    <div class="service-container">
        <div class="card">
            <img src="images/image copy.png" alt="Facial treatment">
            <h3>Facial</h3>
            <p>Professional facial treatments.</p>
            <h4>₹999</h4>
        </div>
        <div class="card">
            <img src="images/image copy 2.png" alt="Bridal makeup">
            <h3>Bridal Makeup</h3>
            <p>Special bridal packages.</p>
            <h4>₹12999</h4>
        </div>
        <div class="card">
            <img src="images/image copy 3.png" alt="Hair styling">
            <h3>Hair Styling</h3>
            <p>Professional haircut & styling.</p>
            <h4>₹699</h4>
        </div>
        <div class="card">
            <img src="images/image copy 4.png" alt="Nail art">
            <h3>Nail Art</h3>
            <p>Stylish nail designs.</p>
            <h4>₹499</h4>
        </div>
    </div>
</section>

<!-- ================= SHOP PREVIEW ================= -->

<section class="shop" id="shop">

    <h2>Shop Products</h2>

    <div class="service-container">

    <?php

$query = "SELECT * FROM products ORDER BY product_id DESC LIMIT 4";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result))
{

?>

<div class="card">

    <img src="uploads/<?php echo $row['image']; ?>" alt="">

    <h3><?php echo $row['product_name']; ?></h3>

    <p><?php echo $row['description']; ?></p>

    <h4>₹<?php echo $row['price']; ?></h4>

    <a href="shop.php?search=<?php echo urlencode($row['product_name']); ?>" class="btn">

        View Details

    </a>

</div>

<?php

}

?>
</div>

</section>


<!-- ================= GALLERY ================= -->

<section class="gallery">

    <h2>Beauty Gallery</h2>

    <div class="gallery-container">

        <img src="images/image copy 12.png" alt="">
        <img src="images/image copy 13.png" alt="">
        <img src="images/image copy 14.png" alt="">
        <img src="images/image copy 15.png" alt="">
        <img src="images/image copy 16.png" alt="">
        <img src="images/image copy 17.png" alt="">
        <img src="images/image copy 18.png" alt="">
        <img src="images/image copy 19.png" alt="">
        <img src="images/image copy 20.png" alt="">
        <img src="images/image copy 6.png" alt="">


    </div>

</section>
        <!-- ================= NEWSLETTER ================= -->

<section class="newsletter">

    <h2>Subscribe Our Newsletter</h2>

    <p>
        Get beauty tips, special offers and latest updates.
    </p>

    <?php if (($_GET['subscribe'] ?? '') === 'success'): ?>
        <p class="newsletter-message success" role="status">Subscribed successfully.</p>
    <?php elseif (($_GET['subscribe'] ?? '') === 'error'): ?>
        <p class="newsletter-message error" role="alert">Please enter a valid email address.</p>
    <?php endif; ?>

    <form action="index.php" method="post">

        <input type="hidden" name="newsletter_subscribe" value="1">

        <input type="email"
               name="email"
               placeholder="Enter Your Email"
               required>

        <button type="submit">
            Subscribe
        </button>

    </form>

</section>

<!-- ================= CONTACT SECTION ================= -->

<section class="contact" id="contact">

    <h2>Contact & Booking</h2>

    <div class="contact-grid">

        <div class="contact-info">

            <p>
                Call us or fill the form to book an appointment.
            </p>

            <p>
                <i class="fa-solid fa-phone"></i>
                +91 9876543210
            </p>

            <p>
                <i class="fa-solid fa-envelope"></i>
                info@glownest.com
            </p>

            <p>
                <i class="fa-solid fa-location-dot"></i>
                Rajkot, Gujarat
            </p>

        </div>

        <form class="contact-form"
              action=""
              method="post">

            <input
            type="text"
            name="name"
            placeholder="Your Name"
            required>

            <input
            type="email"
            name="email"
            placeholder="Email"
            required>

            <input
            type="tel"
            name="phone"
            placeholder="Phone"
            required>

            <br><br>

            <textarea
            name="message"
            rows="5"
            placeholder="Message / Preferred Date">
            </textarea>

            <button
            type="submit"
            class="btn">

            Send Message

            </button>

        </form>

    </div>

</section>

<?php
include("includes/footer.php");
?>