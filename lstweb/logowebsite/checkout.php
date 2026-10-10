<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['placed_orders'])) {
    $_SESSION['placed_orders'] = [];
}

$isLoggedIn = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0 && ($_SESSION['user_type'] ?? '') === 'user';
$currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : 0;

// Fetch user profile if logged in
$userData = [
    'fullname' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'mobile' => $_SESSION['mobile'] ?? '',
    'city' => $_SESSION['city'] ?? '',
    'address' => '',
    'state' => 'Maharashtra',
    'pincode' => ''
];

if ($isLoggedIn) {
    $uStmt = mysqli_prepare($conn, "SELECT fullname, email, mobile, city, address FROM users WHERE user_id = ? LIMIT 1");
    if ($uStmt) {
        mysqli_stmt_bind_param($uStmt, "i", $currentUserId);
        mysqli_stmt_execute($uStmt);
        $uRes = mysqli_stmt_get_result($uStmt);
        if ($uRow = mysqli_fetch_assoc($uRes)) {
            $userData['fullname'] = $uRow['fullname'] ?: $userData['fullname'];
            $userData['email'] = $uRow['email'] ?: $userData['email'];
            $userData['mobile'] = $uRow['mobile'] ?: $userData['mobile'];
            $userData['city'] = $uRow['city'] ?: $userData['city'];
            $userData['address'] = $uRow['address'] ?: '';
        }
        mysqli_stmt_close($uStmt);
    }
}

// Determine checkout mode and fetch checkout products
$buyNowId = isset($_GET['buy_now']) ? (int)$_GET['buy_now'] : (isset($_POST['buy_now']) ? (int)$_POST['buy_now'] : 0);
$fromCart = isset($_GET['from_cart']) || isset($_GET['cart']) || (isset($_POST['from_cart']) && (int)$_POST['from_cart'] === 1);
$items = [];
$errorMsg = '';

if ($buyNowId > 0) {
    $pStmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ? LIMIT 1");
    if ($pStmt) {
        mysqli_stmt_bind_param($pStmt, "i", $buyNowId);
        mysqli_stmt_execute($pStmt);
        $pRes = mysqli_stmt_get_result($pStmt);
        if ($prod = mysqli_fetch_assoc($pRes)) {
            $statusNorm = normalize_product_status($prod['status'] ?? '', (int)$prod['stock']);
            if ($statusNorm === 'Out of Stock' || (int)$prod['stock'] <= 0) {
                $errorMsg = "Sorry, '" . htmlspecialchars($prod['product_name']) . "' is currently out of stock.";
            } else {
                $items[] = [
                    'id' => (int)$prod['product_id'],
                    'name' => $prod['product_name'],
                    'category' => $prod['category'] ?? 'Beauty',
                    'description' => $prod['description'] ?? '',
                    'price' => (float)$prod['price'],
                    'image' => !empty($prod['image']) ? 'uploads/' . basename($prod['image']) : 'images/beautytools.jpg',
                    'stock' => (int)$prod['stock'],
                    'requested_qty' => 1
                ];
            }
        } else {
            $errorMsg = "The requested product was not found.";
        }
        mysqli_stmt_close($pStmt);
    }
} elseif ($fromCart || empty($buyNowId)) {
    $fromCart = true;
    $cartIds = $isLoggedIn
        ? get_user_product_ids($conn, 'cart', $currentUserId)
        : ($_SESSION['cart'] ?? []);

    if (!empty($cartIds)) {
        $cartIdsList = implode(',', array_map('intval', $cartIds));
        $cQuery = "SELECT * FROM products WHERE product_id IN ($cartIdsList) AND LOWER(TRIM(COALESCE(status, ''))) <> 'hidden' AND LOWER(TRIM(COALESCE(status, ''))) <> 'out of stock' AND stock > 0 ORDER BY product_id DESC";
        $cRes = mysqli_query($conn, $cQuery);
        if ($cRes) {
            while ($prod = mysqli_fetch_assoc($cRes)) {
                $items[] = [
                    'id' => (int)$prod['product_id'],
                    'name' => $prod['product_name'],
                    'category' => $prod['category'] ?? 'Beauty',
                    'description' => $prod['description'] ?? '',
                    'price' => (float)$prod['price'],
                    'image' => !empty($prod['image']) ? 'uploads/' . basename($prod['image']) : 'images/beautytools.jpg',
                    'stock' => (int)$prod['stock'],
                    'requested_qty' => 1
                ];
            }
        }
    }
}

// ----------------------------------------------------
// PROCESS ORDER SUBMISSION (POST)
// ----------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_order') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $addressLine = trim($_POST['address_line'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? 'Maharashtra');
    $pincode = trim($_POST['pincode'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Cash on Delivery');
    $quantities = isset($_POST['qty']) && is_array($_POST['qty']) ? $_POST['qty'] : [];

    // Validation
    if ($customerName === '' || $customerPhone === '' || $addressLine === '' || $city === '' || $pincode === '') {
        $errorMsg = "Please fill in all required delivery details.";
    } elseif (strlen(preg_replace('/[^0-9]/', '', $customerPhone)) < 10) {
        $errorMsg = "Please enter a valid 10-digit mobile number.";
    } elseif (empty($items)) {
        $errorMsg = "No products available in this order.";
    } else {
        // Construct full delivery address
        $fullAddress = $addressLine . ', ' . $city;
        if ($state !== '') {
            $fullAddress .= ', ' . $state;
        }
        $fullAddress .= ' - ' . $pincode;

        $createdOrderIds = [];
        $lastOrderNumber = '';
        $orderedItemsSummary = [];
        $grandTotal = 0;

        foreach ($items as $item) {
            $pId = $item['id'];
            $qty = isset($quantities[$pId]) ? max(1, (int)$quantities[$pId]) : 1;
            // Cap at available stock
            if ($qty > $item['stock']) {
                $qty = $item['stock'];
            }

            $itemTotalPrice = $item['price'] * $qty;
            $grandTotal += $itemTotalPrice;

            $orderId = create_order($conn, [
                'product_id' => $pId,
                'product_name' => $item['name'],
                'category' => $item['category'],
                'description' => $item['description'],
                'quantity' => $qty,
                'price' => $itemTotalPrice,
                'payment_method' => $paymentMethod,
                'delivery_address' => $fullAddress,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'image' => basename($item['image']),
                'user_id' => $currentUserId
            ]);

            if ($orderId > 0) {
                $createdOrderIds[] = $orderId;
                $orderedItemsSummary[] = [
                    'name' => $item['name'],
                    'qty' => $qty,
                    'price' => $itemTotalPrice,
                    'image' => $item['image']
                ];

                // Remove from cart if from cart
                if ($fromCart || isset($_GET['from']) && $_GET['from'] === 'cart') {
                    if ($isLoggedIn) {
                        remove_user_product($conn, 'cart', $currentUserId, $pId);
                    } else {
                        $cIdx = array_search($pId, $_SESSION['cart'], true);
                        if ($cIdx !== false) {
                            unset($_SESSION['cart'][$cIdx]);
                            $_SESSION['cart'] = array_values($_SESSION['cart']);
                        }
                    }
                }
            }
        }

        if (!empty($createdOrderIds)) {
            // Save address to user profile if user is logged in and had no address
            if ($isLoggedIn && empty($userData['address'])) {
                $updUser = mysqli_prepare($conn, "UPDATE users SET address = ?, city = ? WHERE user_id = ? AND (address IS NULL OR address = '')");
                if ($updUser) {
                    mysqli_stmt_bind_param($updUser, "ssi", $fullAddress, $city, $currentUserId);
                    mysqli_stmt_execute($updUser);
                    mysqli_stmt_close($updUser);
                }
            }

            // Fetch the primary order number
            $firstId = $createdOrderIds[0];
            $oCheck = mysqli_query($conn, "SELECT order_number FROM orders WHERE order_id = $firstId LIMIT 1");
            if ($oCheck && $oRow = mysqli_fetch_assoc($oCheck)) {
                $lastOrderNumber = $oRow['order_number'];
            }

            $_SESSION['new_order_success'] = [
                'order_ids' => $createdOrderIds,
                'order_number' => $lastOrderNumber,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'delivery_address' => $fullAddress,
                'payment_method' => $paymentMethod,
                'total_amount' => $grandTotal,
                'item_count' => count($orderedItemsSummary),
                'items' => $orderedItemsSummary,
                'created_at' => date('d M Y, h:i A')
            ];

            header("Location: myorder.php?order_success=1&order_number=" . urlencode($lastOrderNumber));
            exit;
        } else {
            $errorMsg = "Could not place the order. Please check item stock and try again.";
        }
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= CHECKOUT PAGE ================= -->
<section class="checkout-page-shell">
    <div class="checkout-header-banner">
        <div class="checkout-breadcrumbs">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <span>/</span>
            <a href="shop.php">Shop</a>
            <span>/</span>
            <span class="current">Secure Checkout</span>
        </div>
        <div class="checkout-hero-text">
            <h1><i class="fa-solid fa-lock"></i> GlowNest Express Checkout</h1>
            <p>Fast, secure, and luxury beauty delivery directly to your doorstep.</p>
        </div>

        <!-- Checkout Progress Stepper -->
        <div class="checkout-stepper">
            <div class="step-badge active"><span class="step-num">1</span> <span class="step-label">Select Items & Qty</span></div>
            <div class="step-line active"></div>
            <div class="step-badge active"><span class="step-num">2</span> <span class="step-label">Delivery Address</span></div>
            <div class="step-line active"></div>
            <div class="step-badge active"><span class="step-num">3</span> <span class="step-label">Payment Method</span></div>
            <div class="step-line"></div>
            <div class="step-badge"><span class="step-num">4</span> <span class="step-label">Confirmation</span></div>
        </div>
    </div>

    <?php if ($errorMsg !== ''): ?>
        <div class="checkout-alert checkout-alert-error" role="alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Order Alert:</strong> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="checkout-empty-state">
            <div class="empty-icon"><i class="fa-solid fa-bag-shopping"></i></div>
            <h2>Your Checkout Bag is Empty</h2>
            <p>You have not selected any products to purchase. Explore our collection of premium beauty essentials.</p>
            <div class="empty-actions">
                <a href="shop.php" class="btn"><i class="fa-solid fa-arrow-left"></i> Explore Shop</a>
                <a href="cart.php" class="btn btn-secondary"><i class="fa-solid fa-cart-shopping"></i> View Cart</a>
            </div>
        </div>
    <?php else: ?>

        <form action="checkout.php" method="POST" id="checkout-form" class="checkout-layout-grid">
            <input type="hidden" name="action" value="confirm_order">
            <?php if ($buyNowId > 0): ?>
                <input type="hidden" name="buy_now" value="<?php echo $buyNowId; ?>">
            <?php else: ?>
                <input type="hidden" name="from_cart" value="1">
            <?php endif; ?>

            <!-- LEFT COLUMN: Items, Address, Payment -->
            <div class="checkout-main-content">

                <!-- 1. ORDER ITEMS & QUANTITY SELECTOR -->
                <div class="checkout-card">
                    <div class="card-heading">
                        <span class="heading-badge">1</span>
                        <div>
                            <h2>Review Products & Quantity</h2>
                            <p>Select how many units you would like to order.</p>
                        </div>
                    </div>

                    <div class="checkout-items-list">
                        <?php
                        $subtotal = 0;
                        foreach ($items as $item):
                            $lineTotal = $item['price'] * $item['requested_qty'];
                            $subtotal += $lineTotal;
                        ?>
                            <div class="checkout-item-row" data-id="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" data-max="<?php echo $item['stock']; ?>">
                                <div class="item-thumb-wrap">
                                    <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="item-details">
                                    <span class="item-category"><?php echo htmlspecialchars($item['category']); ?></span>
                                    <h3 class="item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <div class="item-stock-tag <?php echo $item['stock'] < 5 ? 'low-stock' : 'in-stock'; ?>">
                                        <i class="fa-solid <?php echo $item['stock'] < 5 ? 'fa-fire' : 'fa-circle-check'; ?>"></i>
                                        <?php echo $item['stock'] < 5 ? "Only {$item['stock']} left in stock!" : "In Stock ({$item['stock']} available)"; ?>
                                    </div>
                                    <div class="item-price-unit">Unit Price: <strong>₹<?php echo number_format($item['price'], 2); ?></strong></div>
                                </div>

                                <!-- Interactive Quantity Stepper -->
                                <div class="item-quantity-control">
                                    <label>Quantity:</label>
                                    <div class="qty-stepper">
                                        <button type="button" class="qty-btn qty-minus" aria-label="Decrease quantity"><i class="fa-solid fa-minus"></i></button>
                                        <input
                                            type="number"
                                            name="qty[<?php echo $item['id']; ?>]"
                                            class="qty-input"
                                            value="<?php echo $item['requested_qty']; ?>"
                                            min="1"
                                            max="<?php echo $item['stock']; ?>"
                                            readonly
                                        >
                                        <button type="button" class="qty-btn qty-plus" aria-label="Increase quantity"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <span class="qty-line-total">Item Total: <b class="line-total-val">₹<?php echo number_format($lineTotal, 2); ?></b></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. DELIVERY ADDRESS FORM -->
                <div class="checkout-card">
                    <div class="card-heading">
                        <span class="heading-badge">2</span>
                        <div>
                            <h2>Delivery & Shipping Address</h2>
                            <p>Where should we deliver your luxury beauty order?</p>
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <span class="user-status-pill"><i class="fa-solid fa-circle-check"></i> Logged In</span>
                        <?php else: ?>
                            <a href="login.php" class="quick-login-link"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login for saved address</a>
                        <?php endif; ?>
                    </div>

                    <div class="checkout-form-fields">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="customer_name"><i class="fa-regular fa-user"></i> Full Name <span class="req">*</span></label>
                                <input
                                    type="text"
                                    id="customer_name"
                                    name="customer_name"
                                    value="<?php echo htmlspecialchars($userData['fullname'], ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="Enter your full name"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="customer_phone"><i class="fa-solid fa-phone"></i> Mobile Number <span class="req">*</span></label>
                                <input
                                    type="tel"
                                    id="customer_phone"
                                    name="customer_phone"
                                    value="<?php echo htmlspecialchars($userData['mobile'], ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="10-digit mobile number (e.g. 9876543210)"
                                    pattern="[0-9]{10}"
                                    maxlength="15"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="customer_email"><i class="fa-regular fa-envelope"></i> Email Address (for order tracking receipt)</label>
                            <input
                                type="email"
                                id="customer_email"
                                name="customer_email"
                                value="<?php echo htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="name@example.com"
                            >
                        </div>

                        <div class="form-group">
                            <label for="address_line"><i class="fa-solid fa-house"></i> Flat / House No. / Building / Street Address <span class="req">*</span></label>
                            <textarea
                                id="address_line"
                                name="address_line"
                                rows="2"
                                placeholder="e.g. Flat 402, Rosewood Heights, MG Road, Landmark near Metro Station"
                                required
                            ><?php echo htmlspecialchars($userData['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="city"><i class="fa-solid fa-city"></i> City / Town <span class="req">*</span></label>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    value="<?php echo htmlspecialchars($userData['city'], ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="e.g. Mumbai, Delhi, Ahmedabad"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="state"><i class="fa-solid fa-map-location-dot"></i> State</label>
                                <select id="state" name="state" class="state-select">
                                    <option value="Maharashtra" selected>Maharashtra</option>
                                    <option value="Gujarat">Gujarat</option>
                                    <option value="Delhi">Delhi</option>
                                    <option value="Karnataka">Karnataka</option>
                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                                    <option value="Rajasthan">Rajasthan</option>
                                    <option value="West Bengal">West Bengal</option>
                                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                                    <option value="Punjab">Punjab</option>
                                    <option value="Other">Other State</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pincode"><i class="fa-solid fa-location-pin"></i> Pincode <span class="req">*</span></label>
                                <input
                                    type="text"
                                    id="pincode"
                                    name="pincode"
                                    placeholder="6-digit Pincode"
                                    pattern="[0-9]{6}"
                                    maxlength="6"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PAYMENT METHOD SELECTION -->
                <div class="checkout-card">
                    <div class="card-heading">
                        <span class="heading-badge">3</span>
                        <div>
                            <h2>Choose Payment Method</h2>
                            <p>All payment methods are 100% safe, verified, and encrypted.</p>
                        </div>
                    </div>

                    <div class="payment-methods-grid">

                        <!-- Cash on Delivery -->
                        <label class="payment-option-card selected" for="pay-cod">
                            <input type="radio" id="pay-cod" name="payment_method" value="Cash on Delivery" checked>
                            <div class="payment-option-content">
                                <div class="option-header">
                                    <div class="icon-circle"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                                    <div class="option-text">
                                        <strong>Cash on Delivery (COD)</strong>
                                        <span class="badge-tag popular">Most Popular</span>
                                        <p>Pay easily with cash when your beauty package arrives at your doorstep.</p>
                                    </div>
                                </div>
                                <div class="payment-subpanel cod-panel show">
                                    <i class="fa-solid fa-circle-check"></i> No advance online payment needed. Pay ₹<span class="dyn-total"><?php echo number_format($subtotal, 2); ?></span> in cash on delivery.
                                </div>
                            </div>
                        </label>

                        <!-- UPI / QR Code -->
                        <label class="payment-option-card" for="pay-upi">
                            <input type="radio" id="pay-upi" name="payment_method" value="UPI / QR Code">
                            <div class="payment-option-content">
                                <div class="option-header">
                                    <div class="icon-circle upi-icon"><i class="fa-solid fa-qrcode"></i></div>
                                    <div class="option-text">
                                        <strong>Instant UPI / QR Code</strong>
                                        <span class="badge-tag instant">Instant & Zero Fee</span>
                                        <p>Google Pay, PhonePe, Paytm, BHIM & all UPI apps.</p>
                                    </div>
                                </div>
                                <div class="payment-subpanel upi-panel">
                                    <div class="upi-box">
                                        <div class="upi-apps-icons">
                                            <span class="app-chip">GPay</span>
                                            <span class="app-chip">PhonePe</span>
                                            <span class="app-chip">Paytm</span>
                                            <span class="app-chip">BHIM</span>
                                        </div>
                                        <div class="upi-input-wrap">
                                            <input type="text" placeholder="Enter UPI ID (e.g. mobile@upi or name@okhdfcbank)" class="upi-field">
                                            <button type="button" class="btn small-btn upi-verify-btn">Verify UPI</button>
                                        </div>
                                        <small class="upi-note">✦ You will receive a payment request on your UPI app after clicking Confirm Order.</small>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Credit / Debit Card -->
                        <label class="payment-option-card" for="pay-card">
                            <input type="radio" id="pay-card" name="payment_method" value="Credit / Debit Card">
                            <div class="payment-option-content">
                                <div class="option-header">
                                    <div class="icon-circle"><i class="fa-regular fa-credit-card"></i></div>
                                    <div class="option-text">
                                        <strong>Credit / Debit Card</strong>
                                        <span class="badge-tag">Visa, Master, RuPay</span>
                                        <p>Safe 256-bit encrypted card checkout.</p>
                                    </div>
                                </div>
                                <div class="payment-subpanel card-panel">
                                    <div class="mock-card-fields">
                                        <div class="card-input-row">
                                            <input type="text" placeholder="Card Number (16 Digits)" maxlength="19" class="card-num-field">
                                        </div>
                                        <div class="card-input-row-2">
                                            <input type="text" placeholder="MM / YY" maxlength="5">
                                            <input type="password" placeholder="CVV" maxlength="3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Net Banking -->
                        <label class="payment-option-card" for="pay-netbanking">
                            <input type="radio" id="pay-netbanking" name="payment_method" value="Net Banking">
                            <div class="payment-option-content">
                                <div class="option-header">
                                    <div class="icon-circle"><i class="fa-solid fa-building-columns"></i></div>
                                    <div class="option-text">
                                        <strong>Net Banking</strong>
                                        <p>Direct bank transfer via SBI, HDFC, ICICI, Axis, and 50+ banks.</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: ORDER SUMMARY SIDEBAR -->
            <aside class="checkout-sidebar">
                <div class="summary-card-luxury sticky-card">
                    <div class="summary-header">
                        <i class="fa-solid fa-receipt"></i>
                        <h3>Order Summary</h3>
                    </div>

                    <div class="summary-items-preview">
                        <?php foreach ($items as $item): ?>
                            <div class="preview-mini-item" id="preview-item-<?php echo $item['id']; ?>">
                                <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <div class="preview-info">
                                    <span class="p-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="p-meta"><span class="dyn-qty" id="dyn-qty-<?php echo $item['id']; ?>">1</span> × ₹<?php echo number_format($item['price']); ?></span>
                                </div>
                                <strong class="p-total" id="dyn-line-<?php echo $item['id']; ?>">₹<?php echo number_format($item['price'], 2); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="summary-divider">

                    <div class="price-breakdown">
                        <div class="breakdown-row">
                            <span>Subtotal (<span id="total-qty-count"><?php echo count($items); ?></span> items)</span>
                            <span id="summary-subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="breakdown-row">
                            <span>Delivery & Shipping</span>
                            <span class="free-shipping-tag"><i class="fa-solid fa-truck-fast"></i> FREE</span>
                        </div>
                        <div class="breakdown-row discount-row">
                            <span>Special Promo Discount</span>
                            <span class="discount-val">-₹200.00</span>
                        </div>
                        <div class="breakdown-row total-row">
                            <strong>Total Amount Payable</strong>
                            <strong class="grand-total-val" id="summary-grandtotal">₹<?php echo number_format(max(0, $subtotal - 200), 2); ?></strong>
                        </div>
                    </div>

                    <div class="savings-alert">
                        <i class="fa-solid fa-sparkles"></i> You are saving ₹200.00 on this order!
                    </div>

                    <!-- SUBMIT / CONFIRM ORDER BUTTON -->
                    <button type="submit" id="btn-confirm-order" class="btn btn-confirm-order">
                        <span><i class="fa-solid fa-circle-check"></i> Confirm Order</span>
                        <b class="btn-price-tag" id="btn-price-display">₹<?php echo number_format(max(0, $subtotal - 200), 2); ?></b>
                    </button>

                    <p class="terms-note">
                        <i class="fa-solid fa-shield-halved"></i> By placing this order, you agree to GlowNest's <a href="about.php">Terms of Service</a> & <a href="contact.php">Return Policy</a>.
                    </p>

                    <div class="trust-badges-grid">
                        <div class="trust-item">
                            <i class="fa-solid fa-shield-check"></i>
                            <span>100% Genuine</span>
                        </div>
                        <div class="trust-item">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>7-Day Return</span>
                        </div>
                        <div class="trust-item">
                            <i class="fa-solid fa-lock"></i>
                            <span>256-Bit SSL</span>
                        </div>
                    </div>
                </div>
            </aside>

        </form>

    <?php endif; ?>
</section>

<!-- Interactive Dynamic Javascript for Quantity, Pricing & Payment Selection -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const DISCOUNT = 200;
    const itemRows = document.querySelectorAll('.checkout-item-row');
    const subtotalEl = document.getElementById('summary-subtotal');
    const grandtotalEl = document.getElementById('summary-grandtotal');
    const btnPriceEl = document.getElementById('btn-price-display');
    const totalQtyCountEl = document.getElementById('total-qty-count');
    const dynTotalEls = document.querySelectorAll('.dyn-total');
    const form = document.getElementById('checkout-form');
    const confirmBtn = document.getElementById('btn-confirm-order');

    function calculateTotals() {
        let subtotal = 0;
        let totalItemsCount = 0;

        itemRows.forEach(row => {
            const pId = row.getAttribute('data-id');
            const unitPrice = parseFloat(row.getAttribute('data-price')) || 0;
            const maxStock = parseInt(row.getAttribute('data-max')) || 1;
            const qtyInput = row.querySelector('.qty-input');
            let qty = parseInt(qtyInput.value) || 1;

            if (qty < 1) qty = 1;
            if (qty > maxStock) qty = maxStock;
            qtyInput.value = qty;

            const lineTotal = unitPrice * qty;
            subtotal += lineTotal;
            totalItemsCount += qty;

            // Update line total text in row
            const lineValEl = row.querySelector('.line-total-val');
            if (lineValEl) {
                lineValEl.textContent = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Update sidebar mini item
            const previewQty = document.getElementById('dyn-qty-' + pId);
            const previewLine = document.getElementById('dyn-line-' + pId);
            if (previewQty) previewQty.textContent = qty;
            if (previewLine) previewLine.textContent = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });

        const grandTotal = Math.max(0, subtotal - DISCOUNT);
        const subtotalStr = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const grandTotalStr = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (subtotalEl) subtotalEl.textContent = subtotalStr;
        if (grandtotalEl) grandtotalEl.textContent = grandTotalStr;
        if (btnPriceEl) btnPriceEl.textContent = grandTotalStr;
        if (totalQtyCountEl) totalQtyCountEl.textContent = totalItemsCount;

        dynTotalEls.forEach(el => {
            el.textContent = grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    }

    // Attach stepper button events
    itemRows.forEach(row => {
        const minusBtn = row.querySelector('.qty-minus');
        const plusBtn = row.querySelector('.qty-plus');
        const qtyInput = row.querySelector('.qty-input');
        const maxStock = parseInt(row.getAttribute('data-max')) || 1;

        if (minusBtn && plusBtn && qtyInput) {
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let currentVal = parseInt(qtyInput.value) || 1;
                if (currentVal > 1) {
                    qtyInput.value = currentVal - 1;
                    calculateTotals();
                }
            });

            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let currentVal = parseInt(qtyInput.value) || 1;
                if (currentVal < maxStock) {
                    qtyInput.value = currentVal + 1;
                    calculateTotals();
                } else {
                    alert('Only ' + maxStock + ' units are currently available in stock.');
                }
            });
        }
    });

    // Payment Option Cards Interactivity
    const paymentCards = document.querySelectorAll('.payment-option-card');
    paymentCards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                paymentCards.forEach(c => {
                    c.classList.remove('selected');
                    const subpanel = c.querySelector('.payment-subpanel');
                    if (subpanel) subpanel.classList.remove('show');
                });
                if (this.checked) {
                    card.classList.add('selected');
                    const subpanel = card.querySelector('.payment-subpanel');
                    if (subpanel) subpanel.classList.add('show');
                }
            });
        }
    });

    // Form submission feedback
    if (form && confirmBtn) {
        form.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('customer_phone');
            const cleanPhone = phoneInput.value.replace(/[^0-9]/g, '');
            if (cleanPhone.length < 10) {
                e.preventDefault();
                alert('Please enter a valid 10-digit mobile number for order delivery.');
                phoneInput.focus();
                return false;
            }

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Your Order...';
        });
    }

    // Initial calculation
    calculateTotals();
});
</script>

<?php
include("includes/footer.php");
?>
