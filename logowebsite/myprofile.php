<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!in_array($_SESSION['user_type'] ?? '', ['user', 'admin'], true)) {
    header("Location: login.php?msg=" . urlencode("Please login to view your profile."));
    exit;
}

$isAdmin = ($_SESSION['user_type'] === 'admin');
$username = isset($_SESSION['username']) && $_SESSION['username'] !== '' ? $_SESSION['username'] : 'GlowNest User';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : 'your@email.com';
$userPhone = isset($_SESSION['mobile']) && $_SESSION['mobile'] !== '' ? $_SESSION['mobile'] : 'Not added yet';
$nameParts = preg_split('/\s+/', trim($username), 2);
$firstName = !empty($_SESSION['first_name']) ? $_SESSION['first_name'] : ($nameParts[0] ?? '');
$lastName = !empty($_SESSION['last_name']) ? $_SESSION['last_name'] : ($nameParts[1] ?? '');
$profileImage = $_SESSION['user_image'] ?? '';
$hasProfileImage = $profileImage !== '' && file_exists(__DIR__ . '/' . $profileImage);
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
$validTabs = ['profile', 'activity', 'settings', 'rate'];
if (!in_array($tab, $validTabs, true)) {
    $tab = 'profile';
}

include("includes/header.php");
include("includes/navbar.php");
$profileMessage = $_GET['msg'] ?? '';
?>

<?php if ($profileMessage !== ''): ?>
    <div class="profile-message" role="status">
        <?php echo htmlspecialchars($profileMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<section class="myorder-banner">
    <div class="myorder-banner-content">
        <h1>My Profile</h1>
        <p>Manage your personal information and account settings with confidence.</p>
    </div>
</section>

<section class="profile-section">
    <div class="profile-tabs">
        <a href="myprofile.php?tab=profile" class="tab-link <?php echo $tab === 'profile' ? 'active' : ''; ?>">My Profile</a>
        <a href="myprofile.php?tab=activity" class="tab-link <?php echo $tab === 'activity' ? 'active' : ''; ?>">My Activity</a>
        <a href="myprofile.php?tab=settings" class="tab-link <?php echo $tab === 'settings' ? 'active' : ''; ?>">Settings</a>
        <a href="myprofile.php?tab=rate" class="tab-link <?php echo $tab === 'rate' ? 'active' : ''; ?>">Rate GlowNest</a>
        <a href="logout.php" class="tab-link logout">Logout</a>
    </div>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-image">
                <?php if ($hasProfileImage): ?>
                    <img src="<?php echo htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image">
                <?php else: ?>
                    <i class="fa-solid fa-user" aria-label="Default profile icon"></i>
                <?php endif; ?>
            </div>

            <h2><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="member-text">Premium Member</p>

            <div class="profile-stats">
                <div class="stat-box"><h3>12</h3><span>Orders</span></div>
                <div class="stat-box"><h3>5</h3><span>Bookings</span></div>
                <div class="stat-box"><h3>8</h3><span>Wishlist</span></div>
            </div>

            <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="profile-photo-form">
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($userPhone, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="dob" value="<?php echo htmlspecialchars($_SESSION['dob'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="gender" value="<?php echo htmlspecialchars($_SESSION['gender'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($_SESSION['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="file" id="profile-photo-input" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="visually-hidden">
                <label for="profile-photo-input" class="change-photo-btn">
                    <i class="fa-solid fa-camera"></i>
                    Change Photo
                </label>
            </form>
            <?php if ($hasProfileImage): ?>
                <form action="update-profile.php" method="POST" class="remove-photo-form" onsubmit="return confirm('Remove your profile photo?');">
                    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($userPhone, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="dob" value="<?php echo htmlspecialchars($_SESSION['dob'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="gender" value="<?php echo htmlspecialchars($_SESSION['gender'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="city" value="<?php echo htmlspecialchars($_SESSION['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="remove_profile_photo" value="1">
                    <button type="submit" class="remove-photo-btn">
                        <i class="fa-solid fa-trash"></i>
                        Remove Photo
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="profile-details">
            <?php if ($tab === 'profile'): ?>
                <div class="section-title">
                    <h2>Personal Information</h2>
                    <p>Keep your profile updated for a smoother beauty experience.</p>
                </div>

                <?php if ($isAdmin): ?>
                    <div class="tab-content">
                        <p>Admin account is active. Use the Admin Dashboard to manage products.</p>
                        <a href="admin/manage-product.php" class="btn">Open Admin Dashboard</a>
                    </div>
                <?php else: ?>
                <form action="update-profile.php" method="POST" class="profile-form">
                    <div class="profile-row">
                        <div class="profile-input">
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="Enter First Name" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="profile-input">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="profile-row">
                        <div class="profile-input">
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="profile-input">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="profile-row">
                        <div class="profile-input">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" value="<?php echo htmlspecialchars($userPhone, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="profile-input">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo htmlspecialchars($_SESSION['dob'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="profile-row">
                        <div class="profile-input">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="Female" <?php echo (($_SESSION['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Male" <?php echo (($_SESSION['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Other" <?php echo (($_SESSION['gender'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="profile-input">
                            <label>City</label>
                            <input type="text" name="city" placeholder="Enter City" value="<?php echo htmlspecialchars($_SESSION['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="profile-button">
                        <button type="submit">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            <?php elseif ($tab === 'activity'): ?>
                <div class="section-title">
                    <h2>My Activity</h2>
                    <p>See your recent activity and purchases on GlowNest.</p>
                </div>
                <div class="tab-content">
                    <ul class="activity-list">
                        <li><i class="fa-solid fa-circle-check"></i> Ordered premium skincare set</li>
                        <li><i class="fa-solid fa-circle-check"></i> Saved 3 products to wishlist</li>
                        <li><i class="fa-solid fa-circle-check"></i> Updated profile details</li>
                        <li><i class="fa-solid fa-circle-check"></i> Booked a salon appointment</li>
                    </ul>
                </div>
            <?php elseif ($tab === 'settings'): ?>
                <div class="section-title">
                    <h2>Settings</h2>
                    <p>Manage your account preferences and security options.</p>
                </div>
                <div class="tab-content settings-list">
                    <div class="settings-item"><i class="fa-solid fa-key"></i> Change Password</div>
                    <div class="settings-item"><i class="fa-solid fa-bell"></i> Notification Preferences</div>
                    <div class="settings-item"><i class="fa-solid fa-image"></i> Update Profile Photo</div>
                    <div class="settings-item"><i class="fa-solid fa-user-pen"></i> Edit Personal Details</div>
                </div>
            <?php else: ?>
                <div class="section-title">
                    <h2>Rate GlowNest</h2>
                    <p>Share your feedback to help us improve your beauty experience.</p>
                </div>
                <div class="tab-content rate-section">
                    <div class="rating-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <textarea placeholder="Write your review" rows="5"></textarea>
                    <button class="btn submit-review" type="button">Submit Review</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-extra">
        <div class="profile-box">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h3>
            <ul>
                <li><i class="fa-solid fa-circle-check"></i> Profile Updated Successfully</li>
                <li><i class="fa-solid fa-circle-check"></i> Last Login: Today</li>
                <li><i class="fa-solid fa-circle-check"></i> Beauty Appointment Booked</li>
            </ul>
        </div>

        <div class="profile-box">
            <h3><i class="fa-solid fa-user-shield"></i> Account Information</h3>
            <div class="account-info">
                <p><strong>Member Since:</strong> 2026</p>
                <p><strong>Account Type:</strong> <?php echo $isAdmin ? 'Administrator' : 'Premium User'; ?></p>
                <p><strong>Status:</strong> <span class="active-status">Active</span></p>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>

<script>
    const profilePhotoInput = document.getElementById('profile-photo-input');

    if (profilePhotoInput) {
        profilePhotoInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                this.form.submit();
            }
        });
    }
</script>
