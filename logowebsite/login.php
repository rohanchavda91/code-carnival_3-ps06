<?php
session_start();

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= LOGIN BANNER ================= -->

<section class="login-banner">

    <div class="login-banner-content">

        <h1>Welcome Back</h1>

        <p>

            Login to continue shopping and manage your bookings.

        </p>

    </div>

</section>

<!-- ================= LOGIN SECTION ================= -->

<section class="login-section">

    <div class="login-box">

        <h2>Login</h2>

        <p class="login-text">

            Enter your account details.

        </p>

        <form action="login_process.php" method="POST">

            <!-- Email / Username -->

            <div class="form-group">

                <label>Email Address or Username</label>

                <input
                    type="text"
                    name="email"
                    placeholder="Enter your email or admin username"
                    required>

            </div>

            <!-- Password -->

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required>

            </div>
            <!-- Remember Me & Forgot Password -->

            <div class="login-options">

                <label class="remember-me">

                    <input type="checkbox" name="remember">

                    Remember Me

                </label>

                <a href="forgot-password.php" class="forgot-password">

                    Forgot Password?

                </a>

            </div>

            <!-- Login Button -->

            <div class="login-button">

                <button type="submit" class="btn">

                    Login

                </button>

            </div>


            <!-- OR -->

            <div class="login-divider">

                <span>OR</span>

            </div>

            <!-- Social Login -->

            <div class="social-login">

                <button type="button" class="google-btn">

                    <i class="fa-brands fa-google"></i>

                    Login with Google

                </button>

                <button type="button" class="facebook-btn">

                    <i class="fa-brands fa-facebook-f"></i>

                    Login with Facebook

                </button>

            </div>

            <!-- Signup -->

            <div class="signup-link">

                <p>

                    Don't have an account?

                    <a href="signup.php">

                        Create Account

                    </a>

                </p>

            </div>

                        <!-- Login Message -->

            <?php

            if(isset($_GET['msg']))
            {

                echo "<div class='login-message'>".$_GET['msg']."</div>";

            }

            ?>

        </form>

        <!-- Security Tips -->

        <div class="security-box">

            <h3>Security Tips</h3>

            <ul>

                <li>✔ Never share your password.</li>

                <li>✔ Always logout after using a public computer.</li>

                <li>✔ Keep your account information updated.</li>

                <li>✔ Use a strong password.</li>

            </ul>

        </div>

    </div>

</section>

<!-- ================= SUPPORT ================= -->

<section class="support-section">

    <div class="support-card">

        <i class="fa-solid fa-headset"></i>

        <h3>Need Help?</h3>

        <p>

            Contact our support team if you're having trouble logging in.

        </p>

        <a href="contact.php" class="btn">

            Contact Support

        </a>

    </div>

</section>

<?php

include("includes/footer.php");

?>