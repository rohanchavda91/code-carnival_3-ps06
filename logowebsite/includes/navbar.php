<div class="header-wrap">

    <!-- ================= MENU ================= -->
    <div class="menu-bar">

        <div class="menu-left-group">
            <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="nav-logo">
                <a href="index.php">
                    <img src="images/logo1.png" alt="GlowNest Logo">
                </a>
            </div>
        </div>

        <div class="menu-links" id="mainNavLinks">
            <div class="mobile-drawer-header">
                <span class="drawer-title"><i class="fa-solid fa-sparkles"></i> GlowNest Menu</span>
                <button type="button" class="drawer-close-btn" id="drawerCloseBtn" aria-label="Close menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            include_once(__DIR__ . '/../config.php');
            include_once(__DIR__ . '/order_helpers.php');
            ensure_store_tables($conn);

            if (!isset($_SESSION['wishlist'])) {
                $_SESSION['wishlist'] = [];
            }

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (($_SESSION['user_type'] ?? '') === 'user' && !empty($_SESSION['user_id'])) {
                $wishlist_count = count(get_user_product_ids($conn, 'wishlist', (int)$_SESSION['user_id']));
                $cart_count = count(get_user_product_ids($conn, 'cart', (int)$_SESSION['user_id']));
            } else {
                $wishlist_count = count($_SESSION['wishlist']);
                $cart_count = count($_SESSION['cart']);
            }
            $current_page = basename($_SERVER['PHP_SELF']);
            $menu_items = [
                ['slug' => 'index.php', 'label' => 'Home', 'icon' => 'fa-house'],
                ['slug' => 'category.php', 'label' => 'Category', 'icon' => 'fa-layer-group'],
                ['slug' => 'shop.php', 'label' => 'Shop', 'icon' => 'fa-bag-shopping'],
                ['slug' => 'booking.php', 'label' => 'Booking', 'icon' => 'fa-calendar-check'],
                ['slug' => 'about.php', 'label' => 'About', 'icon' => 'fa-circle-info'],
                ['slug' => 'contact.php', 'label' => 'Contact', 'icon' => 'fa-phone'],
                ['slug' => 'myorder.php', 'label' => 'My Order', 'icon' => 'fa-truck-fast'],
            ];

            foreach ($menu_items as $item) {
                $active_class = ($current_page === $item['slug']) ? 'active' : '';
                echo '<a href="' . htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') . '" class="' . $active_class . '"><i class="fa-solid ' . $item['icon'] . ' nav-item-icon"></i><span>' . htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') . '</span></a>';
            }

            $loggedInUsername = trim((string)($_SESSION['username'] ?? ''));
            $accountLabel = $loggedInUsername !== '' ? $loggedInUsername : 'Login';
            ?>
        </div>

        <div class="menu-actions">

           <a href="search.php" class="icon-box search-icon" aria-label="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>

            <a href="wishlist.php" class="icon-box" aria-label="Wishlist">
                <i class="fa-solid fa-heart"></i>
                <span class="count"><?php echo $wishlist_count > 0 ? $wishlist_count : ''; ?></span>
            </a>

            <a href="cart.php" class="icon-box" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="count"><?php echo $cart_count > 0 ? $cart_count : ''; ?></span>
            </a>

            <a href="<?php echo !empty($_SESSION['user_id']) ? 'myprofile.php' : 'login.php'; ?>" class="user-menu" aria-label="<?php echo htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="avatar">
                    <?php if (!empty($_SESSION['user_image']) && file_exists(__DIR__ . '/../' . $_SESSION['user_image'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
                <span class="user-menu-name">Hi, <?php echo htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>

        </div>

    </div>

    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <!-- ================= TOP HEADER ================= -->
    <div class="top-bar">

        <div class="top-left">

            <div class="brand-text">

                <h3>✦ Enhancing Your Natural Beauty</h3>

            </div>

        </div>

        <div class="top-right">

            <p>
                <i class="fa-solid fa-location-dot"></i>
                <span>123 Beauty Street, Mumbai, India</span>
            </p>

            <p>
                <i class="fa-regular fa-clock"></i>
                <span>Mon - Sun : 9:00 AM - 8:00 PM</span>
            </p>

            <p>
                <i class="fa-solid fa-phone"></i>
                <a href="tel:+919876543210" style="color: inherit; text-decoration: none;">+91 9876543210</a>
            </p>

        </div>

    </div>

</div>
