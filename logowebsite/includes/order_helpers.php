<?php
function ensure_store_tables($conn)
{
    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(255) NOT NULL,
            category VARCHAR(100) DEFAULT 'general',
            description TEXT,
            price DECIMAL(10,2) DEFAULT 0,
            stock INT DEFAULT 0,
            image VARCHAR(255) DEFAULT '',
            status VARCHAR(50) DEFAULT 'Available',
            view_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS orders (
            order_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED DEFAULT NULL,
            order_number VARCHAR(50) UNIQUE,
            product_id INT,
            product_name VARCHAR(255),
            category VARCHAR(100),
            description TEXT,
            price DECIMAL(10,2) DEFAULT 0,
            image VARCHAR(255) DEFAULT '',
            status VARCHAR(50) DEFAULT 'Order Placed',
            tracking_label VARCHAR(50) DEFAULT 'Order Placed',
            tracking_stage INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS wishlist (
            wishlist_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_wishlist_user_product (user_id, product_id)
        )
    ");

    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS cart (
            cart_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_cart_user_product (user_id, product_id)
        )
    ");

    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS bookings (
            booking_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED DEFAULT NULL,
            fullname VARCHAR(150) NOT NULL,
            mobile VARCHAR(30) NOT NULL,
            email VARCHAR(150) NOT NULL,
            gender VARCHAR(20) DEFAULT NULL,
            service VARCHAR(100) NOT NULL,
            staff VARCHAR(100) DEFAULT NULL,
            booking_date DATE NOT NULL,
            booking_time VARCHAR(20) NOT NULL,
            reference_image VARCHAR(255) DEFAULT NULL,
            message TEXT DEFAULT NULL,
            payment_method VARCHAR(50) DEFAULT 'Pay at Salon',
            status VARCHAR(50) NOT NULL DEFAULT 'Confirmed',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS contact_messages (
            message_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            mobile VARCHAR(30) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'New',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $viewCheck = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'view_count'");
    if (mysqli_num_rows($viewCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE products ADD COLUMN view_count INT DEFAULT 0");
    }

    $brandCheck = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'brand'");
    if (mysqli_num_rows($brandCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE products ADD COLUMN brand VARCHAR(150) DEFAULT NULL AFTER product_name");
    }

    $trackingCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'tracking_stage'");
    if (mysqli_num_rows($trackingCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN tracking_stage INT DEFAULT 1");
    }

    $orderUserCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'user_id'");
    if (mysqli_num_rows($orderUserCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN user_id INT UNSIGNED DEFAULT NULL AFTER order_id");
    }

    $qtyCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'quantity'");
    if (mysqli_num_rows($qtyCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN quantity INT NOT NULL DEFAULT 1 AFTER description");
    }

    $paymentCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'payment_method'");
    if (mysqli_num_rows($paymentCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(100) NOT NULL DEFAULT 'Cash on Delivery' AFTER price");
    }

    $addressCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'delivery_address'");
    if (mysqli_num_rows($addressCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN delivery_address TEXT DEFAULT NULL AFTER payment_method");
    }

    $custNameCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'customer_name'");
    if (mysqli_num_rows($custNameCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN customer_name VARCHAR(150) DEFAULT NULL AFTER delivery_address");
    }

    $custPhoneCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'customer_phone'");
    if (mysqli_num_rows($custPhoneCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(50) DEFAULT NULL AFTER customer_name");
    }

    $custEmailCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'customer_email'");
    if (mysqli_num_rows($custEmailCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN customer_email VARCHAR(150) DEFAULT NULL AFTER customer_phone");
    }

    $deliveredAtCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'delivered_at'");
    if (mysqli_num_rows($deliveredAtCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN delivered_at DATETIME DEFAULT NULL AFTER tracking_stage");
    }

    $cancelledAtCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'cancelled_at'");
    if (mysqli_num_rows($cancelledAtCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN cancelled_at DATETIME DEFAULT NULL AFTER delivered_at");
    }

    $delayedAtCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'delayed_at'");
    if (mysqli_num_rows($delayedAtCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN delayed_at DATETIME DEFAULT NULL AFTER cancelled_at");
    }

    $returnedAtCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'returned_at'");
    if (mysqli_num_rows($returnedAtCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN returned_at DATETIME DEFAULT NULL AFTER delayed_at");
    }

    $refundAtCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'refund_at'");
    if (mysqli_num_rows($refundAtCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN refund_at DATETIME DEFAULT NULL AFTER returned_at");
    }

    $refundStatusCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'refund_status'");
    if (mysqli_num_rows($refundStatusCheck) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN refund_status VARCHAR(50) DEFAULT NULL AFTER refund_at");
    }
}

function normalize_product_status($status, $stock)
{
    $stock = max(0, (int)$stock);
    if ($stock > 0) {
        return 'Available';
    }

    $status = trim((string)($status ?? ''));
    $normalized = strtolower($status);

    if ($normalized === '' || $normalized === 'available' || $normalized === 'in stock') {
        return 'Out of Stock';
    }

    if (in_array($normalized, ['out of stock', 'out-of-stock', 'sold out'], true)) {
        return 'Out of Stock';
    }

    return 'Out of Stock';
}

function get_default_category_settings()
{
    return [
        'makeup' => ['title' => 'Makeup', 'image' => 'images/image copy 13.png', 'description' => 'Lipstick, foundation, primer, compact, kajal and more.'],
        'skincare' => ['title' => 'Skin Care', 'image' => 'images/image copy 5.png', 'description' => 'Face wash, serum, moisturizer and sunscreen.'],
        'haircare' => ['title' => 'Hair Care', 'image' => 'images/image copy 6.png', 'description' => 'Shampoo, conditioner, hair oil and hair masks.'],
        'nailcare' => ['title' => 'Nail Care', 'image' => 'images/image copy 4.png', 'description' => 'Nail polish, nail art, remover and nail kits.'],
        'fragrance' => ['title' => 'Fragrances', 'image' => 'images/perfume.jpg', 'description' => 'Perfumes, body mist, deodorants and gift sets.'],
        'tools' => ['title' => 'Beauty Tools', 'image' => 'images/beautytools.jpg', 'description' => 'Makeup brushes, blenders, dryers and trimmers.'],
    ];
}

function ensure_category_settings_table($conn)
{
    if (!$conn) {
        return false;
    }

    $sql = "
        CREATE TABLE IF NOT EXISTS category_settings (
            slug VARCHAR(100) NOT NULL PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            image VARCHAR(255) DEFAULT '',
            description TEXT DEFAULT NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";

    if (!mysqli_query($conn, $sql)) {
        return false;
    }

    mysqli_query($conn, "ALTER TABLE category_settings ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0");

    $defaults = get_default_category_settings();
    foreach ($defaults as $slug => $setting) {
        $slugValue = strtolower(trim((string)$slug));
        $titleValue = trim((string)($setting['title'] ?? ucfirst($slugValue)));
        $imageValue = trim((string)($setting['image'] ?? ''));
        $descriptionValue = trim((string)($setting['description'] ?? ''));

        $insertSql = mysqli_prepare($conn, "INSERT INTO category_settings (slug, title, image, description, is_deleted) VALUES (?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE title = VALUES(title), image = VALUES(image), description = VALUES(description)");
        if ($insertSql) {
            mysqli_stmt_bind_param($insertSql, 'ssss', $slugValue, $titleValue, $imageValue, $descriptionValue);
            mysqli_stmt_execute($insertSql);
            mysqli_stmt_close($insertSql);
        }
    }

    return true;
}

function get_category_settings_map($conn)
{
    $settings = get_default_category_settings();

    if (!$conn) {
        return $settings;
    }

    ensure_category_settings_table($conn);

    $result = mysqli_query($conn, "SELECT slug, title, image, description, is_deleted FROM category_settings");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $slug = strtolower(trim((string)($row['slug'] ?? '')));
            if ($slug === '') {
                continue;
            }

            if ((int)($row['is_deleted'] ?? 0) === 1) {
                unset($settings[$slug]);
                continue;
            }

            $settings[$slug] = [
                'title' => trim((string)($row['title'] ?? ucfirst($slug))),
                'image' => trim((string)($row['image'] ?? '')),
                'description' => trim((string)($row['description'] ?? '')),
            ];
        }
    }

    return $settings;
}

function update_product_stock_after_order($conn, $productId, $quantity = 1)
{
    $productId = (int)$productId;
    $quantity = max(1, (int)$quantity);

    if ($productId <= 0) {
        return false;
    }

    $check = mysqli_prepare($conn, "SELECT stock FROM products WHERE product_id = ? LIMIT 1");
    if (!$check) {
        return false;
    }

    mysqli_stmt_bind_param($check, "i", $productId);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($check);

    if (!$product) {
        return false;
    }

    $currentStock = (int)($product['stock'] ?? 0);
    if ($currentStock < $quantity) {
        return false;
    }

    $newStock = max(0, $currentStock - $quantity);
    $newStatus = ($newStock <= 0) ? 'Out of Stock' : 'Available';

    $update = mysqli_prepare($conn, "UPDATE products SET stock = ?, status = ? WHERE product_id = ? AND stock >= ?");
    if (!$update) {
        return false;
    }

    mysqli_stmt_bind_param($update, "isii", $newStock, $newStatus, $productId, $quantity);
    $updated = mysqli_stmt_execute($update);
    mysqli_stmt_close($update);

    return $updated;
}

function get_user_product_ids($conn, $table, $userId)
{
    $allowedTables = ['wishlist', 'cart'];
    if (!in_array($table, $allowedTables, true) || $userId < 1) {
        return [];
    }

    $userId = (int)$userId;
    $ids = [];
    $result = mysqli_query($conn, "SELECT product_id FROM $table WHERE user_id = $userId");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ids[] = (int)$row['product_id'];
        }
    }

    return $ids;
}

function sync_user_cart($conn, $userId, $sessionCart = [])
{
    $userId = (int)$userId;
    if ($userId < 1) {
        return [];
    }

    $insert = mysqli_prepare($conn, "INSERT IGNORE INTO cart (user_id, product_id) VALUES (?, ?)");
    if ($insert) {
        foreach (array_unique(array_map('intval', (array)$sessionCart)) as $productId) {
            if ($productId > 0) {
                mysqli_stmt_bind_param($insert, "ii", $userId, $productId);
                mysqli_stmt_execute($insert);
            }
        }
        mysqli_stmt_close($insert);
    }

    return get_user_product_ids($conn, 'cart', $userId);
}

function remove_user_product($conn, $table, $userId, $productId)
{
    $allowedTables = ['wishlist', 'cart'];
    if (!in_array($table, $allowedTables, true)) {
        return;
    }

    $statement = mysqli_prepare($conn, "DELETE FROM $table WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($statement, "ii", $userId, $productId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function create_order($conn, $product)
{
    ensure_store_tables($conn);

    $productId = (int)($product['product_id'] ?? $product['id'] ?? 0);
    $userId = (int)($_SESSION['user_id'] ?? $product['user_id'] ?? 0);
    $quantity = max(1, (int)($product['quantity'] ?? $product['qty'] ?? 1));
    $paymentMethod = trim($product['payment_method'] ?? 'Cash on Delivery');
    if ($paymentMethod === '') {
        $paymentMethod = 'Cash on Delivery';
    }
    $deliveryAddress = trim($product['delivery_address'] ?? '');
    $customerName = trim($product['customer_name'] ?? $_SESSION['username'] ?? '');
    $customerPhone = trim($product['customer_phone'] ?? $_SESSION['mobile'] ?? '');
    $customerEmail = trim($product['customer_email'] ?? $_SESSION['email'] ?? '');

    if ($productId <= 0) {
        return 0;
    }

    /*
     * Product details
     */
    $productQuery = mysqli_prepare(
        $conn,
        "SELECT product_id, product_name, category, description, price, image, stock, status
         FROM products
         WHERE product_id = ?
         LIMIT 1"
    );

    if (!$productQuery) {
        return 0;
    }

    mysqli_stmt_bind_param($productQuery, "i", $productId);
    mysqli_stmt_execute($productQuery);

    $productResult = mysqli_stmt_get_result($productQuery);
    $productRow = mysqli_fetch_assoc($productResult);

    mysqli_stmt_close($productQuery);

    if (!$productRow) {
        return 0;
    }

    /*
     * Check stock
     */
    $stock = (int)$productRow['stock'];
    if ($stock < $quantity) {
        return 0;
    }

    /*
     * Start transaction
     */
    mysqli_begin_transaction($conn);

    try {
        /*
         * Reduce stock
         */
        $newStock = max(0, $stock - $quantity);
        $newStatus = $newStock > 0 ? 'Available' : 'Out of Stock';

        $stockQuery = mysqli_prepare(
            $conn,
            "UPDATE products
             SET stock = ?, status = ?
             WHERE product_id = ? AND stock >= ?"
        );

        if (!$stockQuery) {
            throw new Exception("Stock update prepare failed");
        }

        mysqli_stmt_bind_param(
            $stockQuery,
            "isii",
            $newStock,
            $newStatus,
            $productId,
            $quantity
        );

        if (!mysqli_stmt_execute($stockQuery)) {
            mysqli_stmt_close($stockQuery);
            throw new Exception("Stock update failed");
        }

        mysqli_stmt_close($stockQuery);

        /*
         * Generate order number
         */
        $orderNumber = 'GN' . date('Ymd') . '-' . rand(1000, 9999);

        $name = $productRow['product_name'];
        $category = $productRow['category'] ?? 'general';
        $description = $productRow['description'] ?? '';
        $unitPrice = (float)$productRow['price'];
        $totalPrice = isset($product['price']) ? (float)$product['price'] : ($unitPrice * $quantity);
        $image = basename($productRow['image'] ?? '');

        /*
         * Insert order
         */
        $orderQuery = mysqli_prepare(
            $conn,
            "INSERT INTO orders
            (
                user_id,
                order_number,
                product_id,
                product_name,
                category,
                description,
                quantity,
                price,
                payment_method,
                delivery_address,
                customer_name,
                customer_phone,
                customer_email,
                image,
                status,
                tracking_label,
                tracking_stage
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Order Placed', 'Order Placed', 1)"
        );

        if (!$orderQuery) {
            throw new Exception("Order prepare failed: " . mysqli_error($conn));
        }

        $userIdParam = $userId > 0 ? $userId : null;

        mysqli_stmt_bind_param(
            $orderQuery,
            "isisssidssssss",
            $userIdParam,
            $orderNumber,
            $productId,
            $name,
            $category,
            $description,
            $quantity,
            $totalPrice,
            $paymentMethod,
            $deliveryAddress,
            $customerName,
            $customerPhone,
            $customerEmail,
            $image
        );

        if (!mysqli_stmt_execute($orderQuery)) {
            $error = mysqli_stmt_error($orderQuery);
            mysqli_stmt_close($orderQuery);
            throw new Exception("Order insert failed: " . $error);
        }

        $orderId = mysqli_insert_id($conn);
        mysqli_stmt_close($orderQuery);
        if ($orderId <= 0) {
            throw new Exception("Order ID was not generated");
        }

        /*
         * Everything successful
         */
        mysqli_commit($conn);

        if (!isset($_SESSION['placed_orders'])) {
            $_SESSION['placed_orders'] = [];
        }
        if (!in_array($orderId, $_SESSION['placed_orders'], true)) {
            $_SESSION['placed_orders'][] = $orderId;
        }

        $_SESSION['last_confirmed_order'] = [
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'product_name' => $name,
            'quantity' => $quantity,
            'price' => $totalPrice,
            'payment_method' => $paymentMethod,
            'delivery_address' => $deliveryAddress,
            'image' => $image,
            'customer_name' => $customerName,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $orderId;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("GlowNest create_order error: " . $e->getMessage());
        return 0;
    }
}

function get_standard_order_stages()
{
    return [
        1 => [
            'name' => 'Order Placed',
            'label' => 'Order Placed',
            'chip' => 'placed',
            'tracking_label' => 'Order Placed',
            'message' => 'Your order has been placed successfully.'
        ],
        2 => [
            'name' => 'Confirmed',
            'label' => 'Order Confirmed',
            'chip' => 'confirmed',
            'tracking_label' => 'Confirmed',
            'message' => 'Your order has been verified and confirmed.'
        ],
        3 => [
            'name' => 'Packed',
            'label' => 'Order Packed',
            'chip' => 'packed',
            'tracking_label' => 'Packed',
            'message' => 'Your beauty essentials are packed and ready to dispatch.'
        ],
        4 => [
            'name' => 'Shipped',
            'label' => 'Order Shipped',
            'chip' => 'shipped',
            'tracking_label' => 'Shipped',
            'message' => 'Your package has been shipped and is on its way.'
        ],
        5 => [
            'name' => 'Out for Delivery',
            'label' => 'Out for Delivery',
            'chip' => 'out-for-delivery',
            'tracking_label' => 'Out for Delivery',
            'message' => 'Your package is out for delivery and will arrive soon.'
        ],
        6 => [
            'name' => 'Delivered',
            'label' => 'Delivered',
            'chip' => 'delivered',
            'tracking_label' => 'Delivered',
            'message' => 'Your package has been successfully delivered. Enjoy your beauty essentials!'
        ]
    ];
}

function get_order_status_data($order)
{
    $rawStatus = is_array($order) ? ($order['status'] ?? 'Order Placed') : (is_string($order) ? $order : 'Order Placed');
    $normalizedStatus = strtolower(trim((string)$rawStatus));
    $trackingStage = is_array($order) && isset($order['tracking_stage']) ? (int)$order['tracking_stage'] : 0;

    $standardSteps = ['Order Placed', 'Confirmed', 'Packed', 'Shipped', 'Out for Delivery', 'Delivered'];

    $createdAtTime = !empty($order['created_at']) ? strtotime($order['created_at']) : time();
    $expectedDeliveryTime = strtotime(date('Y-m-d', $createdAtTime) . ' +5 days');

    // 1. CANCELLED STATE
    if ($normalizedStatus === 'cancelled' || ($trackingStage === 0 && !in_array($normalizedStatus, ['order placed', 'returned', 'refund processing', 'refund successful'], true))) {
        $cancelledTime = !empty($order['cancelled_at']) ? strtotime($order['cancelled_at']) : (!empty($order['updated_at']) ? strtotime($order['updated_at']) : $createdAtTime);
        return [
            'status' => 'Cancelled',
            'label' => 'Cancelled Order',
            'chip' => 'cancelled',
            'tracking_label' => 'Cancelled',
            'steps' => $standardSteps,
            'active_step' => 0,
            'is_cancelled' => true,
            'is_delayed' => false,
            'is_delivered' => false,
            'is_delivery_early' => false,
            'is_refunded' => false,
            'cancelled_heading' => 'CANCELLED ORDER',
            'cancelled_date_text' => 'Cancelled on: ' . date('d M Y', $cancelledTime),
            'cancelled_date_formatted' => date('d M Y', $cancelledTime),
            'message' => 'This order has been cancelled.'
        ];
    }

    // 2. RETURNED & REFUND STATES
    if (in_array($normalizedStatus, ['returned', 'refund processing', 'refund successful'], true) || (!empty($order['refund_status']) && strtolower($order['refund_status']) === 'completed')) {
        $isRefundSuccess = $normalizedStatus === 'refund successful' || (!empty($order['refund_status']) && strtolower($order['refund_status']) === 'completed');
        $isRefundProcessing = $normalizedStatus === 'refund processing' || (!empty($order['refund_status']) && strtolower($order['refund_status']) === 'processing');

        $refundTime = !empty($order['refund_at']) ? strtotime($order['refund_at']) : (!empty($order['returned_at']) ? strtotime($order['returned_at']) : (!empty($order['updated_at']) ? strtotime($order['updated_at']) : $createdAtTime));

        if ($isRefundSuccess) {
            $rHeading = 'Refund Successful';
            $rDateText = 'Refund processed on ' . date('d M Y', $refundTime);
            $rChip = 'refund-successful';
        } elseif ($isRefundProcessing) {
            $rHeading = 'Refund Processing';
            $rDateText = 'Refund initiated on ' . date('d M Y', $refundTime);
            $rChip = 'refund-processing';
        } else {
            $rHeading = 'Order Returned';
            $rDateText = 'Returned on ' . date('d M Y', $refundTime);
            $rChip = 'returned';
        }

        return [
            'status' => $rHeading,
            'label' => $rHeading,
            'chip' => $rChip,
            'tracking_label' => $rHeading,
            'steps' => $standardSteps,
            'active_step' => 0,
            'is_cancelled' => false,
            'is_delayed' => false,
            'is_delivered' => false,
            'is_delivery_early' => false,
            'is_refunded' => true,
            'refund_heading' => $rHeading,
            'refund_date_text' => $rDateText,
            'message' => $isRefundSuccess ? 'Your return has been processed and refund was credited successfully.' : 'Your return is being processed by our team.'
        ];
    }

    // 3. DELIVERED STATE
    if ($normalizedStatus === 'delivered' || $trackingStage === 6) {
        $deliveredTime = !empty($order['delivered_at']) ? strtotime($order['delivered_at']) : (!empty($order['updated_at']) ? strtotime($order['updated_at']) : time());
        $deliveredDateOnly = date('Y-m-d', $deliveredTime);
        $expectedDateOnly = date('Y-m-d', $expectedDeliveryTime);

        $isDeliveryEarly = $deliveredDateOnly < $expectedDateOnly;
        $heading = $isDeliveryEarly ? 'Delivery Early' : 'Delivered';
        $deliveredDateText = 'Delivered on ' . date('d M Y', $deliveredTime);

        return [
            'status' => 'Delivered',
            'label' => $heading,
            'chip' => 'delivered',
            'tracking_label' => 'Delivered',
            'steps' => $standardSteps,
            'active_step' => 6,
            'is_cancelled' => false,
            'is_delayed' => false,
            'is_delivered' => true,
            'is_delivery_early' => $isDeliveryEarly,
            'is_refunded' => false,
            'delivered_heading' => $heading,
            'delivered_date_text' => $deliveredDateText,
            'message' => $isDeliveryEarly
                ? 'Your package arrived earlier than expected on ' . date('d M Y', $deliveredTime) . '. Enjoy your beauty essentials!'
                : 'Your package was safely delivered on ' . date('d M Y', $deliveredTime) . '. Enjoy your beauty essentials!'
        ];
    }

    // 4. DELAYED STATE
    if ($normalizedStatus === 'delayed' || $normalizedStatus === 'order delayed') {
        $stageNumber = ($trackingStage >= 1 && $trackingStage <= 5) ? $trackingStage : 4;
        $delayedTime = !empty($order['delayed_at']) ? strtotime($order['delayed_at']) : (!empty($order['updated_at']) ? strtotime($order['updated_at']) : time());

        return [
            'status' => 'Delayed',
            'label' => 'Order Delayed',
            'chip' => 'delayed',
            'tracking_label' => 'Delayed',
            'steps' => $standardSteps,
            'active_step' => $stageNumber,
            'is_cancelled' => false,
            'is_delayed' => true,
            'is_delivered' => false,
            'is_delivery_early' => false,
            'is_refunded' => false,
            'delayed_heading' => 'Order Delayed',
            'delayed_date_text' => 'Delay noticed on ' . date('d M Y', $delayedTime),
            'message' => 'Your delivery has been delayed. We are working to deliver your order as soon as possible.'
        ];
    }

    // 5. STANDARD ACTIVE STAGES (Order Placed, Confirmed, Packed, Shipped, Out for Delivery)
    $stages = get_standard_order_stages();

    $stageNumber = 1;
    if ($normalizedStatus === 'confirmed' || $trackingStage === 2) {
        $stageNumber = 2;
    } elseif ($normalizedStatus === 'packed' || $trackingStage === 3) {
        $stageNumber = 3;
    } elseif ($normalizedStatus === 'shipped' || $trackingStage === 4) {
        $stageNumber = 4;
    } elseif ($normalizedStatus === 'out for delivery' || $normalizedStatus === 'out_for_delivery' || $trackingStage === 5) {
        $stageNumber = 5;
    } elseif ($trackingStage >= 1 && $trackingStage <= 5) {
        $stageNumber = $trackingStage;
    }

    $currentStage = $stages[$stageNumber] ?? $stages[1];

    return [
        'status' => $currentStage['name'],
        'label' => $currentStage['label'],
        'chip' => $currentStage['chip'],
        'tracking_label' => $currentStage['tracking_label'],
        'steps' => $standardSteps,
        'active_step' => $stageNumber,
        'is_cancelled' => false,
        'is_delayed' => false,
        'is_delivered' => false,
        'is_delivery_early' => false,
        'is_refunded' => false,
        'message' => $currentStage['message']
    ];
}

function update_order_tracking_status($conn, $orderId, $newStatus)
{
    $orderId = (int)$orderId;
    if ($orderId <= 0 || !$conn) {
        return false;
    }

    $normalized = strtolower(trim((string)$newStatus));

    // Fetch current order status for priority guards
    $curQuery = mysqli_query($conn, "SELECT status, tracking_stage, delivered_at, cancelled_at, delayed_at, returned_at, refund_at FROM orders WHERE order_id = $orderId LIMIT 1");
    if (!$curQuery || mysqli_num_rows($curQuery) === 0) {
        return false;
    }
    $currentOrder = mysqli_fetch_assoc($curQuery);
    $currentStatus = strtolower(trim((string)$currentOrder['status']));

    // State Guards
    if ($currentStatus === 'cancelled' && !in_array($normalized, ['cancelled'], true)) {
        // Cancelled order cannot become Delivered or active
        return false;
    }
    if (in_array($currentStatus, ['returned', 'refund processing', 'refund successful'], true) && in_array($normalized, ['order placed', 'confirmed', 'packed', 'shipped', 'out for delivery', 'delivered', 'delayed'], true)) {
        // Returned order cannot become Delivered or active
        return false;
    }
    if ($currentStatus === 'delivered' && in_array($normalized, ['delayed'], true)) {
        // Delivered order cannot become Delayed
        return false;
    }

    $statusMap = [
        'order placed' => ['status' => 'Order Placed', 'stage' => 1, 'label' => 'Order Placed'],
        'confirmed' => ['status' => 'Confirmed', 'stage' => 2, 'label' => 'Confirmed'],
        'packed' => ['status' => 'Packed', 'stage' => 3, 'label' => 'Packed'],
        'shipped' => ['status' => 'Shipped', 'stage' => 4, 'label' => 'Shipped'],
        'out for delivery' => ['status' => 'Out for Delivery', 'stage' => 5, 'label' => 'Out for Delivery'],
        'delivered' => ['status' => 'Delivered', 'stage' => 6, 'label' => 'Delivered'],
        'delayed' => ['status' => 'Delayed', 'stage' => 4, 'label' => 'Delayed'],
        'order delayed' => ['status' => 'Delayed', 'stage' => 4, 'label' => 'Delayed'],
        'cancelled' => ['status' => 'Cancelled', 'stage' => 0, 'label' => 'Cancelled'],
        'returned' => ['status' => 'Returned', 'stage' => 0, 'label' => 'Returned'],
        'refund processing' => ['status' => 'Refund Processing', 'stage' => 0, 'label' => 'Refund Processing'],
        'refund successful' => ['status' => 'Refund Successful', 'stage' => 0, 'label' => 'Refund Successful']
    ];

    if (!isset($statusMap[$normalized])) {
        return false;
    }

    $target = $statusMap[$normalized];
    $targetStatus = $target['status'];
    $targetStage = $target['stage'];
    $targetLabel = $target['label'];

    // Preserve tracking_stage if marking Delayed on an active order
    if ($targetStatus === 'Delayed' && (int)$currentOrder['tracking_stage'] >= 1 && (int)$currentOrder['tracking_stage'] <= 5) {
        $targetStage = (int)$currentOrder['tracking_stage'];
    }

    $setParts = [
        "status = ?",
        "tracking_stage = ?",
        "tracking_label = ?",
        "updated_at = NOW()"
    ];
    $params = [$targetStatus, $targetStage, $targetLabel];
    $types = "sis";

    if ($targetStatus === 'Delivered') {
        $setParts[] = "delivered_at = COALESCE(delivered_at, NOW())";
    } elseif ($targetStatus === 'Cancelled') {
        $setParts[] = "cancelled_at = COALESCE(cancelled_at, NOW())";
    } elseif ($targetStatus === 'Delayed') {
        $setParts[] = "delayed_at = COALESCE(delayed_at, NOW())";
    } elseif ($targetStatus === 'Returned') {
        $setParts[] = "returned_at = COALESCE(returned_at, NOW())";
    } elseif ($targetStatus === 'Refund Processing') {
        $setParts[] = "returned_at = COALESCE(returned_at, NOW())";
        $setParts[] = "refund_status = 'Processing'";
    } elseif ($targetStatus === 'Refund Successful') {
        $setParts[] = "refund_at = COALESCE(refund_at, NOW())";
        $setParts[] = "refund_status = 'Completed'";
    }

    $params[] = $orderId;
    $types .= "i";

    $sql = "UPDATE orders SET " . implode(', ', $setParts) . " WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    $executed = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $executed;
}
?>