<?php
session_start();

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= SIGNUP BANNER ================= -->

<section class="signup-banner">

    <div class="signup-banner-content">

        <h1>Create Your Account</h1>

        <p>

            Join GlowNest Beautiparlor and enjoy a premium beauty experience.

        </p>

    </div>

</section>

<!-- ================= SIGNUP SECTION ================= -->

<section class="signup-section">

    <div class="signup-box">

        <h2>Register Now</h2>

        <p class="signup-text">

            Fill in your details to create a new account.

        </p>

        <form action="signup_process.php" method="POST" enctype="multipart/form-data">

            <!-- Full Name -->

            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="fullname"
                    placeholder="Enter your full name"
                    required>

            </div>

            <!-- Email -->

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required>

            </div>

            <!-- Mobile -->

            <div class="form-group">

                <label>Mobile Number</label>

                <input
                    type="tel"
                    name="mobile"
                    placeholder="Enter your mobile number"
                    required>

            </div>

            <!-- Gender -->

            <div class="form-group">

                <label>Gender</label>

                <select name="gender" required>

                    <option value="">Select Gender</option>

                    <option>Female</option>

                    <option>Male</option>

                    <option>Other</option>

                </select>

            </div>

                        <!-- Date of Birth -->

            <div class="form-group">

                <label>Date of Birth</label>

                <input
                    type="date"
                    name="dob">

            </div>

            <!-- City -->

            <div class="form-group">

                <label>City</label>

                <input
                    type="text"
                    name="city"
                    placeholder="Enter your city"
                    required>

            </div>

            <!-- Address -->

            <div class="form-group">

                <label>Address</label>

                <textarea
                    name="address"
                    rows="4"
                    placeholder="Enter your address (Optional)"></textarea>

            </div>

            <!-- Profile Photo -->

            <div class="form-group">

                <label>Profile Photo (Optional)</label>

                <input
                    type="file"
                    name="profile_photo"
                    accept="image/*">

            </div>

            <!-- Password -->

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Create Password"
                    required>

            </div>

            <!-- Confirm Password -->

            <div class="form-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm Password"
                    required>

            </div>
                        <!-- Terms & Conditions -->

            <div class="terms-box">

                <label>

                    <input type="checkbox" required>

                    I agree to the Terms & Conditions and Privacy Policy.

                </label>

            </div>

            <!-- Register Button -->

            <div class="signup-button">

                <button type="submit" class="btn">

                    Create Account

                </button>

            </div>

            <!-- Login Link -->

            <div class="login-link">

                <p>

                    Already have an account?

                    <a href="login.php">

                        Login Here

                    </a>

                </p>

            </div>

        </form>

        <!-- Member Benefits -->

        <div class="member-benefits">

            <h3>Benefits of Joining GlowNest</h3>

            <ul>

                <li>💖 Easy Appointment Booking</li>

                <li>🛍 Save Favourite Products</li>

                <li>📦 Track Your Orders</li>

                <li>⭐ Write Product Reviews</li>

                <li>🎁 Exclusive Member Offers</li>

            </ul>

        </div>

    </div>

</section>

<!-- ================= SUPPORT SECTION ================= -->

<section class="support-section">

    <div class="support-card">

        <i class="fa-solid fa-headset"></i>

        <h3>Need Help?</h3>

        <p>

            Our support team is always ready to help you.

        </p>

        <a href="contact.php" class="btn">

            Contact Us

        </a>

    </div>

</section>

<?php

include("includes/footer.php");

?>