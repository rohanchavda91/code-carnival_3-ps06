<?php

session_start();

include("config.php");
include_once("includes/order_helpers.php");
ensure_store_tables($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php?msg=" . urlencode("Invalid request method."));
    exit;
}

$loginValue = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($loginValue === '' || $password === '') {
    header("Location: login.php?msg=" . urlencode("Please enter your email/username and password."));
    exit;
}


/* =====================================================
   ADMIN LOGIN
===================================================== */

$enteredEmail = strtolower($loginValue);
$adminUser = strtolower(ADMIN_USERNAME);

if ($enteredEmail === $adminUser && $password === ADMIN_PASSWORD) {

    session_regenerate_id(true);

    $_SESSION['user_id'] = 0;
    $_SESSION['user_type'] = 'admin';
    $_SESSION['username'] = 'Admin';
    $_SESSION['email'] = ADMIN_USERNAME . '@glownest.com';
    $_SESSION['user_image'] = '';

    header("Location: admin/manage-product.php");
    exit;
}


/* =====================================================
   NORMAL USER LOGIN
===================================================== */

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM users
     WHERE LOWER(email) = LOWER(?) OR LOWER(fullname) = LOWER(?)
     LIMIT 1"
);

mysqli_stmt_bind_param($query, "ss", $loginValue, $loginValue);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($query);


/* =====================================================
   CHECK USER
===================================================== */

if (!$user) {

    header("Location: login.php?msg=" . urlencode("Invalid email or password."));
    exit;
}


/* =====================================================
   CHECK PASSWORD
===================================================== */

if (!password_verify($password, $user['password'])) {

    header("Location: login.php?msg=" . urlencode("Invalid email or password."));
    exit;
}


/* =====================================================
   LOGIN SUCCESS
===================================================== */

session_regenerate_id(true);


/* User ID */

$_SESSION['user_id'] = (int)$user['user_id'];


/* User Type */

$_SESSION['user_type'] = 'user';


/* User Information */

$_SESSION['username'] = $user['fullname'];
$_SESSION['email'] = $user['email'];
$_SESSION['mobile'] = $user['mobile'];
$nameParts = preg_split('/\s+/', trim($user['fullname']), 2);
$_SESSION['first_name'] = $nameParts[0] ?? '';
$_SESSION['last_name'] = $nameParts[1] ?? '';
$_SESSION['city'] = $user['city'];
$_SESSION['dob'] = $user['dob'];
$_SESSION['gender'] = $user['gender'];

$_SESSION['cart'] = sync_user_cart($conn, $_SESSION['user_id'], $_SESSION['cart'] ?? []);


/* Profile Photo */

if (!empty($user['profile_photo'])) {

    $_SESSION['user_image'] = $user['profile_photo'];

} else {

    $_SESSION['user_image'] = '';
}


/* =====================================================
   REDIRECT
===================================================== */

header("Location: myprofile.php");
exit;

?>