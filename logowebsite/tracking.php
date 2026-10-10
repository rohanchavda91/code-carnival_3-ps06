<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

$orderId = (int)($_GET['order'] ?? 0);
$order = null;
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$sessionOrderIds = array_filter(array_map('intval', $_SESSION['placed_orders'] ?? []));

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $actionOrderId = (int)($_POST['order_id'] ?? 0);

    if ($actionOrderId > 0) {
        $allowed = false;
        if ($currentUserId > 0) {
            $checkStmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, "ii", $actionOrderId, $currentUserId);
                mysqli_stmt_execute($checkStmt);
                $res = mysqli_stmt_get_result($checkStmt);
                if ($res && $sRow = mysqli_fetch_assoc($res)) {
                    $allowed = true;
                    $curSt = strtolower(trim((string)$sRow['status']));
                }
                mysqli_stmt_close($checkStmt);
            }
        } elseif (in_array($actionOrderId, $sessionOrderIds, true)) {
            $statusCheck = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = $actionOrderId LIMIT 1");
            if ($statusCheck && $sRow = mysqli_fetch_assoc($statusCheck)) {
                $allowed = true;
                $curSt = strtolower(trim((string)$sRow['status']));
            }
        }

        if ($allowed) {
            if ($postAction === 'cancel' && !in_array($curSt, ['delivered', 'cancelled', 'returned', 'refund successful', 'refund processing'], true)) {
                update_order_tracking_status($conn, $actionOrderId, 'Cancelled');
            } elseif ($postAction === 'return' && $curSt === 'delivered') {
                update_order_tracking_status($conn, $actionOrderId, 'Returned');
            }
        }
    }
    header("Location: tracking.php?order=$actionOrderId");
    exit;
}

if ($orderId > 0) {
    if ($currentUserId > 0) {
        if (!empty($sessionOrderIds)) {
            $ownerCheck = " AND (user_id = $currentUserId OR order_id IN (" . implode(',', $sessionOrderIds) . "))";
        } else {
            $ownerCheck = " AND user_id = $currentUserId";
        }
    } elseif (!empty($sessionOrderIds)) {
        $ownerCheck = " AND order_id IN (" . implode(',', $sessionOrderIds) . ")";
    } else {
        $ownerCheck = ""; // Fallback for direct link testing
    }

    $result = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $orderId$ownerCheck LIMIT 1");
    if ($result) {
        $order = mysqli_fetch_assoc($result);
    }
}

$statusInfo = $order ? get_order_status_data($order) : null;
include("includes/header.php");
include("includes/navbar.php");
?>

<section class="tracking-page-shell">
    <?php if ($order) :
        $image = !empty($order['image']) ? 'uploads/' . basename($order['image']) : 'images/beautytools.jpg';
        $isCancelled = $statusInfo['is_cancelled'];
        $isDelivered = $statusInfo['is_delivered'];
        $isDelayed = $statusInfo['is_delayed'];
        $isRefunded = $statusInfo['is_refunded'];
        $deliveryDate = date('D, d M', strtotime($order['created_at'] . ' +5 days'));
        $qty = isset($order['quantity']) && (int)$order['quantity'] > 0 ? (int)$order['quantity'] : 1;
        $payMethod = !empty($order['payment_method']) ? $order['payment_method'] : 'Cash on Delivery';
        $isCod = stripos($payMethod, 'cash') !== false || stripos($payMethod, 'cod') !== false;

        $bannerIcon = 'fa-truck-fast';
        if ($isDelivered) {
            $bannerIcon = 'fa-circle-check';
        } elseif ($isCancelled) {
            $bannerIcon = 'fa-circle-xmark';
        } elseif ($isRefunded) {
            $bannerIcon = 'fa-rotate-left';
        } elseif ($isDelayed) {
            $bannerIcon = 'fa-triangle-exclamation';
        }
    ?>
        <div class="tracking-topbar">
            <a href="myorder.php" class="tracking-back"><i class="fa-solid fa-chevron-left"></i> My Orders</a>
            <a href="contact.php" class="tracking-help"><i class="fa-solid fa-headset"></i> Need Help?</a>
        </div>

        <div class="tracking-order-hero">
            <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>">
            <div>
                <span class="order-category"><?php echo htmlspecialchars($order['category'] ?: 'Beauty'); ?></span>
                <h1><?php echo htmlspecialchars($order['product_name']); ?></h1>
                <p>Order #<?php echo htmlspecialchars($order['order_number'] ?: $order['order_id']); ?> · Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
            </div>
            <span class="tracking-arrow"><i class="fa-solid fa-box-open"></i></span>
        </div>

        <div class="tracking-status-banner <?php echo htmlspecialchars($statusInfo['chip']); ?>">
            <span class="status-symbol"><i class="fa-solid <?php echo $bannerIcon; ?>"></i></span>
            <div>
                <strong><?php echo htmlspecialchars($statusInfo['label']); ?></strong>
                <p><?php echo htmlspecialchars($statusInfo['message']); ?></p>
            </div>
        </div>

        <!-- TRACKING TIMELINE / STATUS PANEL -->
        <?php if ($isCancelled): ?>
            <!-- CANCELLED STATE: NO TIMELINE, NO TRUCK -->
            <div class="tracking-panel">
                <div class="tracking-panel-heading">
                    <div>
                        <span class="eyebrow">Order Lifecycle</span>
                        <h2>Order Cancelled</h2>
                    </div>
                    <span class="tracking-estimate" style="color: #a83b56; background: #fdecef;">✕ Cancelled</span>
                </div>
                <div class="order-card-divider"></div>
                <div class="order-status-card-section cancelled-section" style="padding: 20px 0 10px; border-top: none;">
                    <div class="status-banner-title text-danger" style="font-size: 18px;">
                        <i class="fa-solid fa-ban"></i> <?php echo htmlspecialchars($statusInfo['cancelled_heading']); ?>
                    </div>
                    <p class="status-banner-date" style="font-size: 14px; margin-top: 6px;"><?php echo htmlspecialchars($statusInfo['cancelled_date_text']); ?></p>
                    <p style="color: #777; font-size: 13px; margin-top: 10px;">This order has been cancelled and will not be processed for delivery.</p>
                </div>
            </div>

        <?php elseif ($isRefunded): ?>
            <!-- REFUNDED / RETURNED STATE: NO DELIVERY TIMELINE, NO TRUCK -->
            <div class="tracking-panel">
                <div class="tracking-panel-heading">
                    <div>
                        <span class="eyebrow">Return &amp; Refund</span>
                        <h2><?php echo htmlspecialchars($statusInfo['refund_heading']); ?></h2>
                    </div>
                    <span class="tracking-estimate" style="color: #0369a1; background: #e0f2fe;">✓ <?php echo htmlspecialchars($statusInfo['refund_heading']); ?></span>
                </div>
                <div class="order-card-divider"></div>
                <div class="order-status-card-section refund-section" style="padding: 20px 0 10px; border-top: none;">
                    <div class="status-banner-title text-info" style="font-size: 18px;">
                        <i class="fa-solid fa-rotate-left"></i> <?php echo htmlspecialchars($statusInfo['refund_heading']); ?>
                    </div>
                    <p class="status-banner-date" style="font-size: 14px; margin-top: 6px;"><?php echo htmlspecialchars($statusInfo['refund_date_text']); ?></p>
                    <p style="color: #777; font-size: 13px; margin-top: 10px;"><?php echo htmlspecialchars($statusInfo['message']); ?></p>
                </div>
            </div>

        <?php else: ?>
            <!-- ACTIVE / DELAYED / DELIVERED ORDER JOURNEY -->
            <div class="tracking-panel">
                <div class="tracking-panel-heading">
                    <div>
                        <span class="eyebrow">Live Order Journey</span>
                        <h2>Track Your Package</h2>
                    </div>
                    <span class="tracking-estimate">
                        <?php
                        if ($isDelivered) {
                            echo htmlspecialchars('✓ ' . $statusInfo['delivered_heading']);
                        } elseif ($isDelayed) {
                            echo '⚠ Delayed';
                        } else {
                            echo 'Expected Delivery by ' . $deliveryDate;
                        }
                        ?>
                    </span>
                </div>

                <?php
                $totalSteps = count($statusInfo['steps']);
                $activeStep = (int)$statusInfo['active_step'];

                if ($totalSteps > 1 && $activeStep >= 1) {
                    $stepWidthPercent = 100 / $totalSteps;
                    $halfStep = $stepWidthPercent / 2;
                    $truckPosition = $halfStep + (($activeStep - 1) * $stepWidthPercent);
                    $progressWidth = ($activeStep > 1) ? (($activeStep - 1) * $stepWidthPercent) : 0;
                } else {
                    $truckPosition = 8.33;
                    $progressWidth = 0;
                }
                ?>

                <div class="tracking-timeline">

                    <!-- Progress Line -->
                    <span class="tracking-progress-line"
                          style="width: <?php echo $progressWidth; ?>%;"></span>

                    <!-- Delivery Truck (Stops on Delivered, active on moving orders) -->
                    <span class="tracking-truck <?php echo $isDelivered ? 'truck-delivered truck-stopped' : ($isDelayed ? 'truck-delayed' : ''); ?>"
                          style="left: <?php echo $truckPosition; ?>%;">
                        <i class="fa-solid <?php echo $isDelivered ? 'fa-circle-check' : 'fa-truck-fast'; ?>"></i>
                    </span>

                    <?php foreach ($statusInfo['steps'] as $index => $step) :
                        $stepNumber = $index + 1;

                        if ($isDelivered) {
                            $complete = true;
                            $current = false;
                        } else {
                            $complete = $stepNumber < $activeStep;
                            $current = $stepNumber === $activeStep;
                        }

                        $stepDate = date(
                            'd M',
                            strtotime($order['created_at'] . " +$index days")
                        );
                    ?>

                        <div class="timeline-step <?php echo $complete ? 'complete' : ($current ? 'current' : ''); ?>">

                            <span class="timeline-icon">
                                <i class="fa-solid <?php
                                    echo $complete
                                        ? 'fa-check'
                                        : ($current
                                            ? ($isDelayed ? 'fa-triangle-exclamation' : 'fa-location-dot')
                                            : 'fa-circle');
                                ?>"></i>
                            </span>

                            <div>
                                <strong><?php echo htmlspecialchars($step); ?></strong>
                                <small><?php echo $stepDate; ?></small>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

        <div class="tracking-info-grid">
            <div class="tracking-info-card">
                <div class="info-card-heading">
                    <i class="fa-solid fa-location-dot"></i>
                    <h3>Delivery Address</h3>
                </div>
                <p>
                    <strong><?php echo htmlspecialchars($order['customer_name'] ?? ($_SESSION['username'] ?? 'GlowNest Customer')); ?></strong><br>
                    <?php if (!empty($order['customer_phone'])): ?>
                        <i class="fa-solid fa-phone" style="font-size: 12px; color: #ff4f81;"></i> <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                    <?php endif; ?>
                    <?php echo nl2br(htmlspecialchars($order['delivery_address'] ?? 'Verified shipping address')); ?>
                </p>
            </div>
            <div class="tracking-info-card">
                <div class="info-card-heading">
                    <i class="fa-solid fa-credit-card"></i>
                    <h3>Payment &amp; Order Details</h3>
                </div>
                <p>
                    <strong>Method: <?php echo htmlspecialchars($payMethod); ?></strong><br>
                    <?php echo $isCod ? '<span style="color: #e67e22;"><i class="fa-solid fa-hand-holding-dollar"></i> Pay in cash upon delivery</span>' : '<span style="color: #27ae60;"><i class="fa-solid fa-circle-check"></i> Secure payment confirmed</span>'; ?><br>
                    Quantity: <b><?php echo $qty; ?> Unit(s)</b><br>
                    <strong>Order Total: ₹<?php echo number_format((float)$order['price'], 2); ?></strong>
                </p>
            </div>
        </div>

        <div class="tracking-actions" id="cancel-order">
            <?php if (!$isCancelled && !$isDelivered && !$isRefunded): ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                    <button type="submit" class="outline-btn">Cancel Order</button>
                </form>
            <?php endif; ?>

            <?php if ($isDelivered && !$isRefunded): ?>
                <form method="POST" onsubmit="return confirm('Do you want to request a return for this delivered item?');">
                    <input type="hidden" name="action" value="return">
                    <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                    <button type="submit" class="outline-btn">
                        <i class="fa-solid fa-rotate-left"></i> Return Order
                    </button>
                </form>
            <?php endif; ?>

            <a href="checkout.php?buy_now=<?php echo (int)$order['product_id']; ?>" class="btn">
                <i class="fa-solid fa-bag-shopping"></i> Buy Again
            </a>
            <a href="contact.php" class="text-action">Need Help?</a>
        </div>

        <?php if ($isDelivered && !$isRefunded): ?>
            <div class="tracking-review">
                <div>
                    <span class="eyebrow">Your Experience Matters</span>
                    <h2>How did you like your beauty order?</h2>
                    <p>Share your review and help us deliver perfection with every GlowNest package.</p>
                </div>
                <a href="contact.php" class="outline-btn">Add a review <span>★★★★★</span></a>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="empty-state tracking-empty">
            <i class="fa-solid fa-box-open"></i>
            <h3>No order selected.</h3>
            <p>Choose an order from My Orders to see its live tracking details.</p>
            <a href="myorder.php" class="btn">Back to My Orders</a>
        </div>
    <?php endif; ?>
</section>

<?php include("includes/footer.php"); ?>