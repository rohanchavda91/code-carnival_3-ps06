<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

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
$validTabs = ['profile', 'address', 'activity', 'settings', 'rate'];
if (!in_array($tab, $validTabs, true)) {
    $tab = 'profile';
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$userOrderCount = 0;
$userBookingCount = 0;
$userWishlistCount = 0;
$userAddress = '';

if ($userId > 0) {
    $oRes = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE user_id = $userId");
    if ($oRes && $oRow = mysqli_fetch_array($oRes)) {
        $userOrderCount = (int)$oRow[0];
    }

    $bRes = mysqli_query($conn, "SELECT COUNT(*) FROM bookings WHERE user_id = $userId");
    if ($bRes && $bRow = mysqli_fetch_array($bRes)) {
        $userBookingCount = (int)$bRow[0];
    }

    $wRes = mysqli_query($conn, "SELECT COUNT(*) FROM wishlist WHERE user_id = $userId");
    if ($wRes && $wRow = mysqli_fetch_array($wRes)) {
        $userWishlistCount = (int)$wRow[0];
    }

    $aStmt = mysqli_prepare($conn, "SELECT address, city FROM users WHERE user_id = ? LIMIT 1");
    if ($aStmt) {
        mysqli_stmt_bind_param($aStmt, "i", $userId);
        mysqli_stmt_execute($aStmt);
        $aRes = mysqli_stmt_get_result($aStmt);
        if ($aRow = mysqli_fetch_assoc($aRes)) {
            $userAddress = $aRow['address'] ?? '';
        }
        mysqli_stmt_close($aStmt);
    }
}

// Handle Address Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address']) && $userId > 0) {
    $newAddress = trim($_POST['address'] ?? '');
    $newCity = trim($_POST['city'] ?? '');
    $addrStmt = mysqli_prepare($conn, "UPDATE users SET address = ?, city = ? WHERE user_id = ?");
    if ($addrStmt) {
        mysqli_stmt_bind_param($addrStmt, "ssi", $newAddress, $newCity, $userId);
        mysqli_stmt_execute($addrStmt);
        mysqli_stmt_close($addrStmt);
        $_SESSION['city'] = $newCity;
        header("Location: myprofile.php?tab=address&msg=" . urlencode("Saved address updated successfully."));
        exit;
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<section class="myorder-banner">
    <div class="myorder-banner-content">
        <h1>My Profile</h1>
        <p>Manage your personal information, address, and account settings with confidence.</p>
    </div>
</section>

<section class="profile-section">
    <div class="profile-tabs">
        <a href="myprofile.php?tab=profile" class="tab-link <?php echo $tab === 'profile' ? 'active' : ''; ?>">My Profile</a>
        <a href="my_appointments.php" class="tab-link">My Appointments</a>
        <a href="myprofile.php?tab=address" class="tab-link <?php echo $tab === 'address' ? 'active' : ''; ?>">Saved Address</a>
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
                <a href="myorder.php" class="stat-box" style="text-decoration:none; color:inherit;">
                    <h3><?php echo $userOrderCount; ?></h3>
                    <span>Orders</span>
                </a>
                <a href="my_appointments.php" class="stat-box" style="text-decoration:none; color:inherit;">
                    <h3><?php echo $userBookingCount; ?></h3>
                    <span>Bookings</span>
                </a>
                <a href="wishlist.php" class="stat-box" style="text-decoration:none; color:inherit;">
                    <h3><?php echo $userWishlistCount; ?></h3>
                    <span>Wishlist</span>
                </a>
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
            <?php elseif ($tab === 'address'): ?>
                <div class="section-title">
                    <h2>Saved Delivery Address</h2>
                    <p>Manage your default shipping address for quick beauty shopping.</p>
                </div>
                <form action="myprofile.php?tab=address" method="POST" class="profile-form">
                    <input type="hidden" name="update_address" value="1">
                    <div class="profile-row" style="grid-template-columns: 1fr;">
                        <div class="profile-input">
                            <label><i class="fa-solid fa-location-dot" style="color:#ff4f81;"></i> Street / Delivery Address</label>
                            <textarea name="address" rows="3" placeholder="Enter flat, building, street, landmark, area..." style="width:100%; padding:12px; border:1px solid #e2d9de; border-radius:10px; font-family:inherit; font-size:14px; box-sizing:border-box;"><?php echo htmlspecialchars($userAddress, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>
                    <div class="profile-row">
                        <div class="profile-input">
                            <label><i class="fa-solid fa-city" style="color:#ff4f81;"></i> City</label>
                            <input type="text" name="city" placeholder="Enter City (e.g. Mumbai)" value="<?php echo htmlspecialchars($_SESSION['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="profile-input">
                            <label><i class="fa-solid fa-globe" style="color:#ff4f81;"></i> Country</label>
                            <input type="text" value="India" readonly style="background:#f8f9fa; color:#777;">
                        </div>
                    </div>
                    <div class="profile-button">
                        <button type="submit">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Save Delivery Address
                        </button>
                    </div>
                </form>
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
