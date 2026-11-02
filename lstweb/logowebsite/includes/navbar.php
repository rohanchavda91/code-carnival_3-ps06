<div class="header-wrap">
    
    <div class="menu-bar">

        <div class="menu-left-group">
            <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="nav-logo">
                <a href="index.php">
                    <img src="images/logoglownest.png" alt="GlowNest Logo">
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
                $_SESSION['wishlist'] = array_values(array_unique(get_user_product_ids($conn, 'wishlist', (int)$_SESSION['user_id'])));
                $_SESSION['cart'] = array_values(array_unique(get_user_product_ids($conn, 'cart', (int)$_SESSION['user_id'])));
                $wishlist_count = count($_SESSION['wishlist']);
                $cart_count = count($_SESSION['cart']);
            } else {
                $wishlist_count = count($_SESSION['wishlist'] ?? []);
                $cart_count = count($_SESSION['cart'] ?? []);
            }
            $current_page = basename($_SERVER['PHP_SELF']);
            $menu_items = [
                ['slug' => 'index.php', 'label' => 'Home', 'icon' => 'fa-house'],
                ['slug' => 'category.php', 'label' => 'Category', 'icon' => 'fa-layer-group'],
                ['slug' => 'shop.php', 'label' => 'Shop', 'icon' => 'fa-bag-shopping'],
                ['slug' => 'booking.php', 'label' => 'Booking', 'icon' => 'fa-calendar-check'],
                ['slug' => 'my_appointments.php', 'label' => 'Appointments', 'icon' => 'fa-calendar-days'],
                ['slug' => 'about.php', 'label' => 'About', 'icon' => 'fa-circle-info'],
                ['slug' => 'contact.php', 'label' => 'Contact', 'icon' => 'fa-phone'],
                ['slug' => 'myorder.php', 'label' => 'My Order', 'icon' => 'fa-truck-fast'],
            ];

            foreach ($menu_items as $item) {
                $active_class = ($current_page === $item['slug']) ? 'active' : '';
                echo '<a href="' . htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') . '" class="' . $active_class . '"><i class="fa-solid ' . $item['icon'] . ' nav-item-icon"></i><span>' . htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') . '</span></a>';
            }

            $loggedInUsername = trim((string)($_SESSION['username'] ?? ''));
            $accountLabel = $loggedInUsername !== '' ? $loggedInUsername : 'Account';
            $isLoggedIn = !empty($_SESSION['user_id']);
            $isAdmin = (($_SESSION['user_type'] ?? '') === 'admin');
            ?>

            <!-- Drawer Quick Account Links (Mobile) -->
            <div class="drawer-account-section">
                <p class="drawer-section-heading">Account & Settings</p>
                <?php if ($isAdmin): ?>
                    <a href="admin/index.php"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a>
                    <a href="admin/manage-appointments.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
                    <a href="logout.php" style="color:#e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                <?php elseif ($isLoggedIn): ?>
                    <a href="myprofile.php"><i class="fa-solid fa-user"></i> My Profile</a>
                    <a href="my_appointments.php"><i class="fa-solid fa-calendar-check"></i> My Appointments</a>
                    <a href="myorder.php"><i class="fa-solid fa-truck-fast"></i> My Orders</a>
                    <a href="myprofile.php?tab=address"><i class="fa-solid fa-location-dot"></i> Saved Address</a>
                    <a href="logout.php" style="color:#e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                    <a href="signup.php"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="menu-actions">

            <a href="search.php" class="icon-box search-icon" aria-label="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>

            <a href="wishlist.php" class="icon-box" aria-label="Wishlist">
                <i class="fa-solid fa-heart"></i>
                <?php if ($wishlist_count > 0): ?>
                    <span class="count"><?php echo $wishlist_count; ?></span>
                <?php endif; ?>
            </a>

            <a href="cart.php" class="icon-box" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- User Menu with Interactive Dropdown -->
            <div class="user-menu-wrapper" id="userMenuWrapper">
                <a href="<?php echo $isLoggedIn ? ($isAdmin ? 'admin/index.php' : 'user_dashboard.php') : 'login.php'; ?>" class="user-menu" id="userMenuTrigger" aria-label="<?php echo htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="avatar">
                        <?php if (!empty($_SESSION['user_image']) && file_exists(__DIR__ . '/../' . $_SESSION['user_image'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
                        <?php else: ?>
                            <i class="fa-solid fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <span class="user-menu-name">Hi, <?php echo htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </a>

                <div class="user-dropdown-card" id="userDropdownCard">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-avatar">
                            <?php if (!empty($_SESSION['user_image']) && file_exists(__DIR__ . '/../' . $_SESSION['user_image'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['user_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div class="user-dropdown-info">
                            <strong><?php echo htmlspecialchars($accountLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small><?php echo !empty($_SESSION['email']) ? htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') : 'Beauty Member'; ?></small>
                        </div>
                    </div>
                    <div class="user-dropdown-links">
                        <?php if ($isAdmin): ?>
                            <a href="admin/index.php"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a>
                            <a href="admin/manage-appointments.php"><i class="fa-solid fa-calendar-check"></i> Salon Appointments</a>
                            <a href="admin/manage-orders.php"><i class="fa-solid fa-truck-fast"></i> Store Orders</a>
                            <a href="admin/manage-product.php"><i class="fa-solid fa-boxes-stacked"></i> Products</a>
                            <a href="logout.php" class="dropdown-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <?php elseif ($isLoggedIn): ?>
                            <a href="user_dashboard.php"><i class="fa-solid fa-user"></i> My Dashboard</a>
                            <a href="my_appointments.php"><i class="fa-solid fa-calendar-check"></i> My Appointments</a>
                            <a href="myorder.php"><i class="fa-solid fa-truck-fast"></i> My Orders</a>
                            <a href="myprofile.php?tab=address"><i class="fa-solid fa-location-dot"></i> Saved Address</a>
                            <a href="wishlist.php"><i class="fa-solid fa-heart"></i> My Wishlist</a>
                            <a href="logout.php" class="dropdown-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <?php else: ?>
                            <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Log In</a>
                            <a href="signup.php"><i class="fa-solid fa-user-plus"></i> Sign In</a>
                            <a href="booking.php"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

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
