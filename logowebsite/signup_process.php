<?php

session_start();

include("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: signup.php");
    exit;
}

/* ================= GET FORM DATA ================= */

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$city = trim($_POST['city'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


/* ================= VALIDATION ================= */

if (
    $fullname === '' ||
    $email === '' ||
    $mobile === '' ||
    $gender === '' ||
    $city === '' ||
    $password === '' ||
    $confirm_password === ''
) {
    header("Location: signup.php?msg=" . urlencode("Please fill all required fields."));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?msg=" . urlencode("Please enter a valid email address."));
    exit;
}

if ($password !== $confirm_password) {
    header("Location: signup.php?msg=" . urlencode("Passwords do not match."));
    exit;
}


/* ================= CHECK EMAIL ================= */

$check = mysqli_prepare(
    $conn,
    "SELECT user_id FROM users WHERE email = ? LIMIT 1"
);

mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    mysqli_stmt_close($check);

    header("Location: signup.php?msg=" . urlencode("This email is already registered."));
    exit;
}

mysqli_stmt_close($check);


/* ================= PASSWORD ================= */

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


/* ================= PROFILE PHOTO ================= */

$profilePhoto = NULL;

if (
    isset($_FILES['profile_photo']) &&
    $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK
) {

    $uploadDir = "images/profile/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = $_FILES['profile_photo']['name'];
    $tmpName = $_FILES['profile_photo']['tmp_name'];

    $extension = strtolower(
        pathinfo($fileName, PATHINFO_EXTENSION)
    );

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        header("Location: signup.php?msg=" . urlencode("Invalid profile photo format."));
        exit;
    }

    $newFileName = "user_" . time() . "_" . uniqid() . "." . $extension;

    $destination = $uploadDir . $newFileName;

    if (move_uploaded_file($tmpName, $destination)) {
        $profilePhoto = $destination;
    }
}


/* ================= INSERT USER ================= */

$query = mysqli_prepare(
    $conn,
    "INSERT INTO users
    (fullname, email, mobile, gender, dob, city, address, profile_photo, password)
    VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $query,
    "sssssssss",
    $fullname,
    $email,
    $mobile,
    $gender,
    $dob,
    $city,
    $address,
    $profilePhoto,
    $hashedPassword
);

if (!mysqli_stmt_execute($query)) {

    mysqli_stmt_close($query);

    header("Location: signup.php?msg=" . urlencode("Account creation failed."));
    exit;
}

$userId = mysqli_insert_id($conn);

mysqli_stmt_close($query);


/* ================= CREATE LOGIN SESSION ================= */

$_SESSION['user_id'] = $userId;
$_SESSION['user_type'] = 'user';
$_SESSION['username'] = $fullname;
$_SESSION['email'] = $email;
$_SESSION['mobile'] = $mobile;
$_SESSION['user_image'] = $profilePhoto;
$nameParts = preg_split('/\s+/', trim($fullname), 2);
$_SESSION['first_name'] = $nameParts[0] ?? '';
$_SESSION['last_name'] = $nameParts[1] ?? '';
$_SESSION['city'] = $city;
$_SESSION['dob'] = $dob;
$_SESSION['gender'] = $gender;

$_SESSION['cart'] = sync_user_cart($conn, $_SESSION['user_id'], $_SESSION['cart'] ?? []);


/* ================= REDIRECT ================= */

header("Location: myprofile.php?signup=success");
exit;

?>