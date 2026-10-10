<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

$orderSearch = trim($_GET['search'] ?? '');
$orderFilter = strtolower(trim($_GET['status'] ?? 'all'));
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$sessionOrderIds = array_filter(array_map('intval', $_SESSION['placed_orders'] ?? []));

$orderConditions = [];
if ($currentUserId > 0) {
    if (!empty($sessionOrderIds)) {
        $orderConditions[] = "(user_id = $currentUserId OR order_id IN (" . implode(',', $sessionOrderIds) . "))";
    } else {
        $orderConditions[] = "user_id = $currentUserId";
    }
} elseif (!empty($sessionOrderIds)) {
    $orderConditions[] = "order_id IN (" . implode(',', $sessionOrderIds) . ")";
} else {
    $orderConditions[] = "1 = 0";
}

// Handle Order Actions from My Orders (Cancel / Return)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $actionOrderId = (int)($_POST['order_id'] ?? 0);

    if ($actionOrderId > 0) {
        $allowed = false;
        if ($currentUserId > 0) {
            $chk = mysqli_prepare($conn, "SELECT status FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
            if ($chk) {
                mysqli_stmt_bind_param($chk, "ii", $actionOrderId, $currentUserId);
                mysqli_stmt_execute($chk);
                $cRes = mysqli_stmt_get_result($chk);
                if ($cRow = mysqli_fetch_assoc($cRes)) {
                    $allowed = true;
                    $currentSt = strtolower(trim((string)$cRow['status']));
                }
                mysqli_stmt_close($chk);
            }
        } elseif (in_array($actionOrderId, $sessionOrderIds, true)) {
            $cRes = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = $actionOrderId LIMIT 1");
            if ($cRes && $cRow = mysqli_fetch_assoc($cRes)) {
                $allowed = true;
                $currentSt = strtolower(trim((string)$cRow['status']));
            }
        }

        if ($allowed) {
            if ($postAction === 'cancel' && !in_array($currentSt, ['delivered', 'cancelled', 'returned', 'refund successful', 'refund processing'], true)) {
                update_order_tracking_status($conn, $actionOrderId, 'Cancelled');
            } elseif ($postAction === 'return_order' && $currentSt === 'delivered') {
                update_order_tracking_status($conn, $actionOrderId, 'Returned');
            }
        }
    }

    header("Location: myorder.php");
    exit;
}

$ordersQuery = "SELECT * FROM orders WHERE " . implode(' AND ', $orderConditions) . " ORDER BY order_id DESC";
$result = mysqli_query($conn, $ordersQuery);
$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status = strtolower($row['status'] ?? '');
        $matchesSearch = $orderSearch === ''
            || stripos($row['product_name'] ?? '', $orderSearch) !== false
            || stripos($row['order_number'] ?? '', $orderSearch) !== false
            || stripos($row['category'] ?? '', $orderSearch) !== false;
        $matchesFilter = $orderFilter === 'all'
            || ($orderFilter === 'active' && !in_array($status, ['delivered', 'cancelled', 'returned', 'refund successful', 'refund processing'], true))
            || ($orderFilter === 'delivered' && $status === 'delivered')
            || ($orderFilter === 'cancelled' && $status === 'cancelled')
            || ($orderFilter === 'delayed' && $status === 'delayed')
            || ($orderFilter === 'returned' && in_array($status, ['returned', 'refund successful', 'refund processing'], true))
            || $status === $orderFilter;
        if ($matchesSearch && $matchesFilter) {
            $orders[] = $row;
        }
    }
}

// Check for recent order confirmation modal
$showSuccessModal = false;
$confirmedOrderData = null;

if (isset($_GET['order_success']) && $_GET['order_success'] == '1') {
    $showSuccessModal = true;
    if (isset($_SESSION['new_order_success'])) {
        $confirmedOrderData = $_SESSION['new_order_success'];
    } elseif (!empty($_GET['order_number'])) {
        $oNum = trim($_GET['order_number']);
        $sQuery = mysqli_prepare($conn, "SELECT * FROM orders WHERE order_number = ? LIMIT 1");
        if ($sQuery) {
            mysqli_stmt_bind_param($sQuery, "s", $oNum);
            mysqli_stmt_execute($sQuery);
            $sRes = mysqli_stmt_get_result($sQuery);
            if ($sRow = mysqli_fetch_assoc($sRes)) {
                $confirmedOrderData = [
                    'order_ids' => [(int)$sRow['order_id']],
                    'order_number' => $sRow['order_number'],
                    'customer_name' => $sRow['customer_name'] ?? ($_SESSION['username'] ?? 'GlowNest Customer'),
                    'customer_phone' => $sRow['customer_phone'] ?? '',
                    'delivery_address' => $sRow['delivery_address'] ?? 'Your delivery address',
                    'payment_method' => $sRow['payment_method'] ?? 'Cash on Delivery',
                    'total_amount' => (float)$sRow['price'],
                    'item_count' => 1,
                    'items' => [
                        [
                            'name' => $sRow['product_name'],
                            'qty' => (int)($sRow['quantity'] ?? 1),
                            'price' => (float)$sRow['price'],
                            'image' => !empty($sRow['image']) ? 'uploads/' . basename($sRow['image']) : 'images/beautytools.jpg'
                        ]
                    ],
                    'created_at' => date('d M Y, h:i A', strtotime($sRow['created_at']))
                ];
            }
            mysqli_stmt_close($sQuery);
        }
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= ORDER CONFIRMATION CELEBRATION MODAL ================= -->
<?php if ($showSuccessModal && $confirmedOrderData): ?>
    <div id="orderSuccessModal" class="order-modal-backdrop active" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="order-modal-card">
            <!-- Confetti Canvas -->
            <canvas id="confetti-canvas" class="confetti-canvas"></canvas>

            <button type="button" class="modal-close-btn" onclick="closeSuccessModal()" aria-label="Close modal">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-celebration-head">
                <div class="celebration-badge-ring">
                    <div class="celebration-badge-inner">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>
                <span class="celebration-sub"><i class="fa-solid fa-sparkles"></i> Order Successfully Placed</span>
                <h2 id="modal-title">🎉 Your Order is Confirmed!</h2>
                <p>Thank you for shopping with GlowNest. We are preparing your beauty essentials with love and care.</p>
            </div>

            <div class="modal-receipt-box">
                <div class="receipt-row-top">
                    <div>
                        <span class="receipt-label">Order Number</span>
                        <strong class="receipt-id">#<?php echo htmlspecialchars($confirmedOrderData['order_number']); ?></strong>
                    </div>
                    <span class="receipt-status-pill"><i class="fa-solid fa-circle-check"></i> Confirmed</span>
                </div>

                <div class="receipt-delivery-preview">
                    <div class="r-detail">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <span class="r-label">Deliver To</span>
                            <strong><?php echo htmlspecialchars($confirmedOrderData['customer_name'] ?? 'Customer'); ?></strong>
                            <p><?php echo htmlspecialchars($confirmedOrderData['delivery_address'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="r-detail">
                        <i class="fa-solid fa-credit-card"></i>
                        <div>
                            <span class="r-label">Payment Method</span>
                            <strong><?php echo htmlspecialchars($confirmedOrderData['payment_method'] ?? 'Cash on Delivery'); ?></strong>
                            <p>Total: <b>₹<?php echo number_format((float)$confirmedOrderData['total_amount'], 2); ?></b></p>
                        </div>
                    </div>
                </div>

                <div class="receipt-estimate">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Estimated Delivery: <b>Within 3 to 5 Business Days</b></span>
                </div>
            </div>

            <div class="modal-actions-grid">
                <?php
                $firstTrackId = !empty($confirmedOrderData['order_ids'][0]) ? (int)$confirmedOrderData['order_ids'][0] : (!empty($orders[0]['order_id']) ? (int)$orders[0]['order_id'] : 0);
                if ($firstTrackId > 0):
                ?>
                    <a href="tracking.php?order=<?php echo $firstTrackId; ?>" class="btn modal-btn-primary">
                        <i class="fa-solid fa-map-location-dot"></i> Track Order Live
                    </a>
                <?php endif; ?>
                <button type="button" class="outline-btn modal-btn-secondary" onclick="closeSuccessModal()">
                    <i class="fa-solid fa-box-open"></i> View My Orders
                </button>
                <a href="shop.php" class="text-action-link">
                    <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <!-- Celebration Confetti Script -->
    <script>
    function closeSuccessModal() {
        const modal = document.getElementById('orderSuccessModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.remove(), 350);
        }
    }

    // Confetti particles generator
    (function() {
        const canvas = document.getElementById('confetti-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        const colors = ['#ff4f81', '#d4af37', '#ff85a2', '#4cd137', '#9b59b6', '#ffd700'];
        const particles = [];
        for (let i = 0; i < 70; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * (height / 2),
                r: Math.random() * 6 + 3,
                d: Math.random() * 70,
                color: colors[Math.floor(Math.random() * colors.length)],
                tilt: Math.floor(Math.random() * 10) - 10,
                tiltAngleIncremental: (Math.random() * 0.07) + 0.05,
                tiltAngle: 0
            });
        }

        let animationFrame;
        function render() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.tiltAngle += p.tiltAngleIncremental;
                p.y += (Math.cos(p.d) + 1 + p.r / 2) * 0.9;
                p.x += Math.sin(p.d);
                p.tilt = Math.sin(p.tiltAngle) * 15;

                ctx.beginPath();
                ctx.lineWidth = p.r;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + p.r / 4, p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 4);
                ctx.stroke();

                if (p.y > height) {
                    particles[i] = {
                        x: Math.random() * width,
                        y: -10,
                        r: p.r,
                        d: p.d,
                        color: p.color,
                        tilt: p.tilt,
                        tiltAngleIncremental: p.tiltAngleIncremental,
                        tiltAngle: p.tiltAngle
                    };
                }
            }
            animationFrame = requestAnimationFrame(render);
        }
        render();

        setTimeout(() => {
            cancelAnimationFrame(animationFrame);
        }, 6000);
    })();
    </script>
<?php endif; ?>

<!-- ================= MY ORDERS MAIN PAGE ================= -->
<section class="orders-page-shell">
    <div class="orders-page-heading">
        <div>
            <p class="eyebrow">Your GlowNest account</p>
            <h1>My Orders</h1>
            <p>Track deliveries, review purchases, and shop your favourite beauty essentials again.</p>
        </div>
        <a href="shop.php" class="btn"><i class="fa-solid fa-bag-shopping"></i> Continue Shopping</a>
    </div>

    <div class="orders-toolbar">
        <form action="myorder.php" method="GET" class="orders-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="search" value="<?php echo htmlspecialchars($orderSearch, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search orders or products">
            <button type="submit" aria-label="Search orders"><i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <div class="order-filter-tabs" role="navigation" aria-label="Order filters">
            <?php foreach (['all' => 'All', 'active' => 'Active', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $value => $label): ?>
                <a href="myorder.php?status=<?php echo $value; ?>" class="<?php echo $orderFilter === $value ? 'active' : ''; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="orders-grid">
        <div class="orders-main">
            <?php if (!empty($orders)) : ?>
                <?php foreach ($orders as $order) :
                    $statusInfo = get_order_status_data($order);
                    $image = !empty($order['image']) ? 'uploads/' . basename($order['image']) : 'images/beautytools.jpg';
                    $rawStatus = strtolower($order['status'] ?? 'Order Placed');
                    $isCancelled = $statusInfo['is_cancelled'];
                    $isDelivered = $statusInfo['is_delivered'];
                    $isDelayed = $statusInfo['is_delayed'];
                    $isRefunded = $statusInfo['is_refunded'];
                    $qty = isset($order['quantity']) && (int)$order['quantity'] > 0 ? (int)$order['quantity'] : 1;
                    $payMethod = !empty($order['payment_method']) ? $order['payment_method'] : 'Cash on Delivery';
                ?>
                    <article class="order-card order-history-card">
                        <div class="order-card-topline">
                            <div class="order-status-wrap">
                                <span class="order-status-dot <?php echo htmlspecialchars($statusInfo['chip']); ?>"></span>
                                <strong><?php echo htmlspecialchars($statusInfo['label']); ?></strong>
                                <span class="order-num-tag">#<?php echo htmlspecialchars($order['order_number'] ?: $order['order_id']); ?></span>
                            </div>
                            <span class="order-date"><i class="fa-regular fa-calendar"></i> Placed <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                        </div>

                        <!-- Product Row -->
                        <a class="order-product-row" href="tracking.php?order=<?php echo (int)$order['order_id']; ?>">
                            <div class="order-thumb">
                                <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>">
                            </div>
                            <div class="order-product-copy">
                                <span class="order-category"><?php echo htmlspecialchars($order['category'] ?: 'Beauty'); ?></span>
                                <h2><?php echo htmlspecialchars($order['product_name']); ?></h2>
                                <p><?php echo htmlspecialchars($statusInfo['message']); ?></p>

                                <div class="order-item-meta-pills">
                                    <span class="pill-qty"><i class="fa-solid fa-box"></i> Qty: <b><?php echo $qty; ?></b></span>
                                    <span class="pill-price"><b>₹<?php echo number_format((float)$order['price'], 2); ?></b></span>
                                    <span class="pill-pay"><i class="fa-solid fa-wallet"></i> <?php echo htmlspecialchars($payMethod); ?></span>
                                </div>

                                <?php if (!empty($order['delivery_address'])): ?>
                                    <div class="order-address-preview">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span><?php echo htmlspecialchars($order['delivery_address']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <i class="fa-solid fa-chevron-right order-chevron"></i>
                        </a>

                        <!-- ================= ORDER STATUS INFORMATION SECTION ================= -->
                        <?php if ($isCancelled): ?>
                            <!-- CANCELLED ORDER SECTION (With visual separation line) -->
                            <div class="order-card-divider"></div>
                            <div class="order-status-card-section cancelled-section">
                                <div class="status-banner-title text-danger">
                                    <i class="fa-solid fa-ban"></i> <?php echo htmlspecialchars($statusInfo['cancelled_heading']); ?>
                                </div>
                                <p class="status-banner-date"><?php echo htmlspecialchars($statusInfo['cancelled_date_text']); ?></p>
                            </div>

                        <?php elseif ($isRefunded): ?>
                            <!-- REFUND / RETURN SECTION (With visual separation line) -->
                            <div class="order-card-divider"></div>
                            <div class="order-status-card-section refund-section">
                                <div class="status-banner-title text-info">
                                    <i class="fa-solid fa-rotate-left"></i> <?php echo htmlspecialchars($statusInfo['refund_heading']); ?>
                                </div>
                                <p class="status-banner-date"><?php echo htmlspecialchars($statusInfo['refund_date_text']); ?></p>
                            </div>

                        <?php elseif ($isDelivered): ?>
                            <!-- DELIVERED / DELIVERY EARLY SECTION (With visual separation line) -->
                            <div class="order-card-divider"></div>
                            <div class="order-status-card-section delivered-section">
                                <div class="status-banner-title text-success">
                                    <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($statusInfo['delivered_heading']); ?>
                                </div>
                                <p class="status-banner-date"><?php echo htmlspecialchars($statusInfo['delivered_date_text']); ?></p>
                            </div>

                            <div class="order-feedback">
                                <strong>We are glad you loved your GlowNest beauty item!</strong>
                                <span class="feedback-stars">★★★★★</span>
                                <a href="contact.php">Add a review</a>
                            </div>

                        <?php elseif ($isDelayed): ?>
                            <!-- DELAYED ORDER SECTION -->
                            <div class="order-delayed-note">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span><strong>⚠ Order Delayed</strong> · Your delivery has been delayed. We are working to deliver your order as soon as possible.</span>
                            </div>

                        <?php else: ?>
                            <!-- NORMAL ACTIVE DELIVERY NOTE -->
                            <div class="order-progress-note">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span><?php echo htmlspecialchars($statusInfo['tracking_label']); ?> · <?php echo htmlspecialchars($statusInfo['message']); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Card Action Buttons -->
                        <div class="order-actions compact-actions">
                            <a href="tracking.php?order=<?php echo (int)$order['order_id']; ?>" class="btn">
                                <i class="fa-solid fa-location-dot"></i> Track Order
                            </a>

                            <?php if (!$isDelivered && !$isCancelled && !$isRefunded): ?>
                                <form method="POST" action="myorder.php" style="margin: 0;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                    <input type="hidden" name="action" value="cancel">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                                    <button type="submit" class="outline-btn" style="cursor: pointer;">
                                        Cancel Order
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($isDelivered && !$isRefunded): ?>
                                <form method="POST" action="myorder.php" style="margin: 0;" onsubmit="return confirm('Do you want to request a return for this delivered item?');">
                                    <input type="hidden" name="action" value="return_order">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                                    <button type="submit" class="outline-btn" style="cursor: pointer;">
                                        <i class="fa-solid fa-rotate-left"></i> Return Order
                                    </button>
                                </form>
                            <?php endif; ?>

                            <a href="checkout.php?buy_now=<?php echo (int)$order['product_id']; ?>" class="text-action">
                                <i class="fa-solid fa-rotate-right"></i> Buy Again
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <h3><?php echo empty($orderSearch) && $orderFilter === 'all' ? 'No orders yet.' : 'No matching orders.'; ?></h3>
                    <p><?php echo empty($orderSearch) && $orderFilter === 'all' ? 'Browse the shop and tap Buy Now to place your first order.' : 'Try another search or select a different filter.'; ?></p>
                    <a href="shop.php" class="btn" style="margin-top: 15px;"><i class="fa-solid fa-bag-shopping"></i> Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <aside class="order-sidebar">
            <div class="sidebar-card premium-card order-summary-card">
                <span class="summary-icon"><i class="fa-solid fa-receipt"></i></span>
                <h3>Orders Summary</h3>
                <div class="summary-row"><span>Orders shown</span><span><?php echo count($orders); ?></span></div>
                <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format(!empty($orders) ? array_sum(array_column($orders, 'price')) : 0, 2); ?></span></div>
                <div class="summary-row"><span>Delivery</span><span style="color: #28a745; font-weight: 600;">FREE</span></div>
                <div class="summary-row total-row"><span>Total value</span><span>₹<?php echo number_format(!empty($orders) ? array_sum(array_column($orders, 'price')) : 0, 2); ?></span></div>
            </div>
            <div class="order-help-card">
                <i class="fa-solid fa-headset"></i>
                <h3>Need help with an order?</h3>
                <p>Our care team is here for delivery, return, and payment questions.</p>
                <a href="contact.php">Contact support <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </aside>
    </div>
</section>

<?php
include("includes/footer.php");
?>