<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <h2>GlowNest Beauty Studio</h2>
            <p>
                Experience premium beauty services, skincare,
                hair styling, bridal makeup and luxury treatments.
            </p>
        </div>

        <div class="footer-links">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="shop.php">Shop</a>
            <a href="contact.php">Contact</a>
        </div>

        <div class="footer-contact">
            <h3>Contact Us</h3>
            <p>+91 98765 43210</p>
            <p>info@glownest.com</p>

            <div class="social-icons">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© <?php echo date("Y"); ?> GlowNest Beauty Studio. All Rights Reserved.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Mobile Drawer Navigation Logic ---
    const mobileToggle = document.getElementById('mobileNavToggle');
    const drawerCloseBtn = document.getElementById('drawerCloseBtn');
    const mainNavLinks = document.getElementById('mainNavLinks');
    const navOverlay = document.getElementById('mobileNavOverlay');

    function openMobileMenu() {
        if (mainNavLinks) mainNavLinks.classList.add('active');
        if (navOverlay) navOverlay.classList.add('active');
        if (mobileToggle) {
            mobileToggle.classList.add('active');
            mobileToggle.setAttribute('aria-expanded', 'true');
        }
        document.body.classList.add('mobile-nav-open');
    }

    function closeMobileMenu() {
        if (mainNavLinks) mainNavLinks.classList.remove('active');
        if (navOverlay) navOverlay.classList.remove('active');
        if (mobileToggle) {
            mobileToggle.classList.remove('active');
            mobileToggle.setAttribute('aria-expanded', 'false');
        }
        document.body.classList.remove('mobile-nav-open');
    }

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (mainNavLinks && mainNavLinks.classList.contains('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeMobileMenu();
        });
    }

    if (navOverlay) {
        navOverlay.addEventListener('click', closeMobileMenu);
    }

    // Close when clicking any nav link inside mobile drawer
    if (mainNavLinks) {
        const navAnchors = mainNavLinks.querySelectorAll('a');
        navAnchors.forEach(function (a) {
            a.addEventListener('click', function () {
                if (window.innerWidth <= 992) {
                    closeMobileMenu();
                }
            });
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMobileMenu();
            if (userMenu) userMenu.classList.remove('active');
        }
    });

    // Reset when resizing window to desktop view
    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) {
            closeMobileMenu();
        }
    });

    // --- User Avatar Menu / Dropdown Logic ---
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        const dropdown = userMenu.querySelector('.dropdown');
        if (dropdown) {
            userMenu.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = userMenu.classList.contains('active');
                document.querySelectorAll('.user-menu.active').forEach(function (menu) {
                    menu.classList.remove('active');
                });
                if (!isOpen) {
                    userMenu.classList.add('active');
                }
            });
            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }
    }

    document.addEventListener('click', function () {
        if (userMenu) userMenu.classList.remove('active');
    });
});
</script>
</body>
</html>