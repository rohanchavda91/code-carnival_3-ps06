<?php
session_start();

include("config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_type'] ?? '') !== 'user') {
    header("Location: login.php?msg=" . urlencode("Please login to update your profile."));
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$fullname = trim($firstName . ' ' . $lastName);
$fullname = $fullname !== '' ? $fullname : trim($_SESSION['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$profilePhoto = null;
$oldProfilePhoto = $_SESSION['user_image'] ?? '';
$city = trim($_POST['city'] ?? '');
$removeProfilePhoto = isset($_POST['remove_profile_photo']) && $_POST['remove_profile_photo'] === '1';

if ($userId < 1 || $fullname === '' || $email === '' || $mobile === '') {
    header("Location: myprofile.php?tab=profile&msg=" . urlencode("Please fill all required fields."));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: myprofile.php?tab=profile&msg=" . urlencode("Please enter a valid email address."));
    exit;
}

$check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1");

mysqli_stmt_bind_param($check, "si", $email, $userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    mysqli_stmt_close($check);
    header("Location: myprofile.php?tab=profile&msg=" . urlencode("This email is already registered."));
    exit;
}

if (!$removeProfilePhoto && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
        header("Location: myprofile.php?tab=profile&msg=" . urlencode("Photo upload failed."));
        exit;
    }

    if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
        header("Location: myprofile.php?tab=profile&msg=" . urlencode("Photo must be 5 MB or smaller."));
        exit;
    }

    $imageInfo = @getimagesize($_FILES['profile_photo']['tmp_name']);
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedMimeTypes, true)) {
        header("Location: myprofile.php?tab=profile&msg=" . urlencode("Please select a JPG, PNG, or WEBP image."));
        exit;
    }

    $uploadDir = __DIR__ . '/images/profile/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        header("Location: myprofile.php?tab=profile&msg=" . urlencode("Photo upload failed."));
        exit;
    }

    $extension = image_type_to_extension($imageInfo[2], false);
    $fileName = 'user_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
        header("Location: myprofile.php?tab=profile&msg=" . urlencode("Photo upload failed."));
        exit;
    }

    $profilePhoto = 'images/profile/' . $fileName;
}

mysqli_stmt_close($check);

$update = mysqli_prepare(
    $conn,
    "UPDATE users
     SET fullname = ?, email = ?, mobile = ?, gender = NULLIF(?, ''),
         dob = NULLIF(?, ''), city = ?,
         profile_photo = IF(? = 1, NULL, COALESCE(?, profile_photo))
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $update,
    "ssssssisi",
    $fullname,
    $email,
    $mobile,
    $gender,
    $dob,
    $city,
    $removeProfilePhoto,
    $profilePhoto,
    $userId
);

if (!mysqli_stmt_execute($update)) {
    mysqli_stmt_close($update);
    header("Location: myprofile.php?tab=profile&msg=" . urlencode("Profile update failed."));
    exit;
}

mysqli_stmt_close($update);

$_SESSION['username'] = $fullname;
$_SESSION['email'] = $email;
$_SESSION['mobile'] = $mobile;
$_SESSION['city'] = $city;
$_SESSION['dob'] = $dob;
$_SESSION['gender'] = $gender;

if ($removeProfilePhoto || $profilePhoto !== null) {
    $_SESSION['user_image'] = $removeProfilePhoto ? '' : $profilePhoto;
    if ($oldProfilePhoto !== '' && strpos($oldProfilePhoto, 'images/profile/') === 0) {
        $oldPhotoPath = __DIR__ . '/' . $oldProfilePhoto;
        if (is_file($oldPhotoPath)) {
            unlink($oldPhotoPath);
        }
    }
}

header("Location: myprofile.php?tab=profile");
exit;
?>
