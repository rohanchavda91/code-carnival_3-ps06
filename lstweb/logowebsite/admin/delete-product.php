<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config.php");

$product_id = intval($_GET['id'] ?? 0);

if ($product_id <= 0) {
    $_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Invalid product ID specified for deletion.'];
    header("Location: manage-product.php");
    exit;
}

// Fetch image filename first
$stmt = mysqli_prepare($conn, "SELECT product_name, image FROM products WHERE product_id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($product) {
        $product_name = $product['product_name'];
        $image_name   = $product['image'];

        // Perform Delete with Prepared Statement
        $del_stmt = mysqli_prepare($conn, "DELETE FROM products WHERE product_id = ?");
        if ($del_stmt) {
            mysqli_stmt_bind_param($del_stmt, "i", $product_id);
            if (mysqli_stmt_execute($del_stmt)) {
                mysqli_stmt_close($del_stmt);

                // Safely remove associated image file
                $upload_folder = "../uploads/";
                if (!empty($image_name) && file_exists($upload_folder . $image_name)) {
                    @unlink($upload_folder . $image_name);
                }

                $_SESSION['flash_msg'] = [
                    'type' => 'success',
                    'msg'  => "Product '" . htmlspecialchars($product_name) . "' (#" . $product_id . ") was deleted successfully!"
                ];
                header("Location: manage-product.php");
                exit;
            }
            mysqli_stmt_close($del_stmt);
        }
    }
}

$_SESSION['flash_msg'] = ['type' => 'danger', 'msg' => 'Product could not be deleted.'];
header("Location: manage-product.php");
exit;
?>