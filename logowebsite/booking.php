<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

$isLoggedIn = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0 && ($_SESSION['user_type'] ?? '') === 'user';
$currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : 0;

$userPrefill = [
    'fullname' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'mobile' => $_SESSION['mobile'] ?? '',
    'gender' => $_SESSION['gender'] ?? ''
];

$bookingError = '';
$showBookingModal = false;
$confirmedBooking = null;

// Handle Form Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_booking') {
    $fullname = trim($_POST['fullname'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $staff = trim($_POST['staff'] ?? 'Any Available Staff');
    if ($staff === '') {
        $staff = 'Any Available Staff';
    }
    $bookingDate = trim($_POST['booking_date'] ?? '');
    $bookingTime = trim($_POST['booking_time'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Pay at Salon');
    if ($paymentMethod === '') {
        $paymentMethod = 'Pay at Salon';
    }

    if ($fullname === '' || $mobile === '' || $email === '' || $service === '' || $bookingDate === '' || $bookingTime === '') {
        $bookingError = 'Please fill all required appointment details.';
    } elseif (strlen(preg_replace('/[^0-9]/', '', $mobile)) < 10) {
        $bookingError = 'Please enter a valid 10-digit mobile number.';
    } else {
        $referenceImage = '';
        if (isset($_FILES['reference_image']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed, true)) {
                $newFilename = 'booking_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $targetPath = __DIR__ . '/uploads/' . $newFilename;
                if (!is_dir(__DIR__ . '/uploads')) {
                    mkdir(__DIR__ . '/uploads', 0777, true);
                }
                if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $targetPath)) {
                    $referenceImage = $newFilename;
                }
            }
        }

        $userIdParam = $currentUserId > 0 ? $currentUserId : null;
        $insertQuery = mysqli_prepare(
            $conn,
            "INSERT INTO bookings (user_id, fullname, mobile, email, gender, service, staff, booking_date, booking_time, reference_image, message, payment_method, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')"
        );

        if ($insertQuery) {
            mysqli_stmt_bind_param(
                $insertQuery,
                "isssssssssss",
                $userIdParam,
                $fullname,
                $mobile,
                $email,
                $gender,
                $service,
                $staff,
                $bookingDate,
                $bookingTime,
                $referenceImage,
                $message,
                $paymentMethod
            );

            if (mysqli_stmt_execute($insertQuery)) {
                $newBookingId = mysqli_insert_id($conn);
                mysqli_stmt_close($insertQuery);

                $bookingNumber = 'BK' . date('Ymd') . '-' . rand(1000, 9999);

                $_SESSION['booking_confirmed'] = [
                    'id' => $newBookingId,
                    'booking_number' => $bookingNumber,
                    'fullname' => $fullname,
                    'mobile' => $mobile,
                    'email' => $email,
                    'service' => $service,
                    'staff' => $staff,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'payment_method' => $paymentMethod,
                    'message' => $message,
                    'created_at' => date('d M Y, h:i A')
                ];

                header("Location: booking.php?booked=1&id=" . $newBookingId);
                exit;
            } else {
                $bookingError = 'Booking submission failed: ' . mysqli_error($conn);
                mysqli_stmt_close($insertQuery);
            }
        } else {
            $bookingError = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// Check for recent booking confirmation
if (isset($_GET['booked']) && $_GET['booked'] == '1') {
    $showBookingModal = true;
    if (isset($_SESSION['booking_confirmed'])) {
        $confirmedBooking = $_SESSION['booking_confirmed'];
    } elseif (isset($_GET['id'])) {
        $bId = (int)$_GET['id'];
        $bRes = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $bId LIMIT 1");
        if ($bRes && $bRow = mysqli_fetch_assoc($bRes)) {
            $confirmedBooking = [
                'id' => $bRow['booking_id'],
                'booking_number' => 'BK' . date('Ymd', strtotime($bRow['created_at'])) . '-' . $bRow['booking_id'],
                'fullname' => $bRow['fullname'],
                'mobile' => $bRow['mobile'],
                'email' => $bRow['email'],
                'service' => $bRow['service'],
                'staff' => $bRow['staff'] ?: 'Any Available Specialist',
                'booking_date' => $bRow['booking_date'],
                'booking_time' => $bRow['booking_time'],
                'payment_method' => $bRow['payment_method'] ?: 'Pay at Salon',
                'message' => $bRow['message'],
                'created_at' => date('d M Y, h:i A', strtotime($bRow['created_at']))
            ];
        }
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= BOOKING CONFIRMATION CELEBRATION MODAL ================= -->
<?php if ($showBookingModal && $confirmedBooking): ?>
    <div id="bookingSuccessModal" class="order-modal-backdrop active" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title">
        <div class="order-modal-card">
            <!-- Confetti Canvas -->
            <canvas id="booking-confetti-canvas" class="confetti-canvas"></canvas>

            <button type="button" class="modal-close-btn" onclick="closeBookingModal()" aria-label="Close modal">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-celebration-head">
                <div class="celebration-badge-ring">
                    <div class="celebration-badge-inner" style="background: linear-gradient(135deg, #ff4f81, #d6336c);">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <span class="celebration-sub"><i class="fa-solid fa-sparkles"></i> Appointment Reserved</span>
                <h2 id="booking-modal-title">🎉 Your Booking is Confirmed!</h2>
                <p>Thank you, <b><?php echo htmlspecialchars($confirmedBooking['fullname']); ?></b>! Your beauty session has been reserved with our professional experts.</p>
            </div>

            <div class="modal-receipt-box">
                <div class="receipt-row-top">
                    <div>
                        <span class="receipt-label">Booking Reference</span>
                        <strong class="receipt-id">#<?php echo htmlspecialchars($confirmedBooking['booking_number']); ?></strong>
                    </div>
                    <span class="receipt-status-pill"><i class="fa-solid fa-circle-check"></i> Confirmed</span>
                </div>

                <div class="receipt-delivery-preview">
                    <div class="r-detail">
                        <i class="fa-solid fa-spa"></i>
                        <div>
                            <span class="r-label">Selected Service</span>
                            <strong><?php echo htmlspecialchars($confirmedBooking['service']); ?></strong>
                            <p>Staff: <?php echo htmlspecialchars($confirmedBooking['staff']); ?></p>
                        </div>
                    </div>
                    <div class="r-detail">
                        <i class="fa-regular fa-clock"></i>
                        <div>
                            <span class="r-label">Date & Time</span>
                            <strong><?php echo date('d M Y (D)', strtotime($confirmedBooking['booking_date'])); ?></strong>
                            <p>Slot: <b><?php echo htmlspecialchars($confirmedBooking['booking_time']); ?></b></p>
                        </div>
                    </div>
                </div>

                <div class="receipt-estimate" style="margin-top: 10px;">
                    <i class="fa-solid fa-location-dot" style="color: #ff4f81;"></i>
                    <span>Salon: <b>GlowNest Beautiparlor, 123 Beauty Street, Mumbai</b></span>
                </div>
            </div>

            <div class="modal-actions-grid">
                <button type="button" class="btn modal-btn-primary" onclick="closeBookingModal()">
                    <i class="fa-solid fa-check"></i> Done & View Details
                </button>
                <a href="shop.php" class="outline-btn modal-btn-secondary" style="text-decoration: none;">
                    <i class="fa-solid fa-bag-shopping"></i> Explore Beauty Products
                </a>
            </div>
        </div>
    </div>
    <script>
    function closeBookingModal() {
        const modal = document.getElementById('bookingSuccessModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.remove(), 350);
        }
    }

    (function() {
        const canvas = document.getElementById('booking-confetti-canvas');
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

<!-- ================= BOOKING BANNER ================= -->
<section class="booking-banner">
    <div class="booking-banner-content">
        <h1>Book Your Appointment</h1>
        <p>Schedule your beauty appointment with our professional experts.</p>
    </div>
</section>

<!-- ================= BOOKING FORM ================= -->
<section class="booking-section booking-page-section">
    <div class="booking-container">
        <h2>Appointment Form</h2>
        <p class="booking-text">Fill in your details and choose your preferred service.</p>

        <?php if ($bookingError !== ''): ?>
            <div class="checkout-alert checkout-alert-error" style="max-width: 600px; margin: 0 auto 25px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><strong>Notice:</strong> <?php echo htmlspecialchars($bookingError); ?></div>
            </div>
        <?php endif; ?>

        <form action="booking.php" method="POST" enctype="multipart/form-data" class="booking-form" id="booking-form">
            <input type="hidden" name="action" value="confirm_booking">

            <!-- Full Name -->
            <div class="form-group">
                <label>Full Name <span style="color: #e74c3c;">*</span></label>
                <input
                    type="text"
                    name="fullname"
                    value="<?php echo htmlspecialchars($userPrefill['fullname'], ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Enter your full name"
                    required>
            </div>

            <!-- Mobile -->
            <div class="form-group">
                <label>Mobile Number <span style="color: #e74c3c;">*</span></label>
                <input
                    type="tel"
                    name="mobile"
                    value="<?php echo htmlspecialchars($userPrefill['mobile'], ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="10-digit mobile number"
                    pattern="[0-9]{10}"
                    maxlength="15"
                    required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email Address <span style="color: #e74c3c;">*</span></label>
                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($userPrefill['email'], ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Enter email address"
                    required>
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label>Gender <span style="color: #e74c3c;">*</span></label>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option <?php echo $userPrefill['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option <?php echo $userPrefill['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option <?php echo $userPrefill['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <!-- Service -->
            <div class="form-group">
                <label>Select Service <span style="color: #e74c3c;">*</span></label>
                <select name="service" required>
                    <option value="">Choose Service</option>
                    <option>Hair Cut</option>
                    <option>Hair Spa</option>
                    <option>Hair Coloring</option>
                    <option>Facial</option>
                    <option>Cleanup</option>
                    <option>Makeup</option>
                    <option>Bridal Makeup</option>
                    <option>Manicure</option>
                    <option>Pedicure</option>
                    <option>Waxing</option>
                    <option>Threading</option>
                </select>
            </div>

            <!-- Staff -->
            <div class="form-group">
                <label>Select Staff</label>
                <select name="staff">
                    <option value="Any Available Staff">Any Available Staff</option>
                    <option>Priya Sharma (Senior Stylist)</option>
                    <option>Neha Patel (Skincare Specialist)</option>
                    <option>Anjali Verma (Makeup Artist)</option>
                    <option>Pooja Singh (Hair Specialist)</option>
                </select>
            </div>

            <!-- Booking Date -->
            <div class="form-group">
                <label>Appointment Date <span style="color: #e74c3c;">*</span></label>
                <input
                    type="date"
                    name="booking_date"
                    min="<?php echo date('Y-m-d'); ?>"
                    required>
            </div>

            <!-- Booking Time -->
            <div class="form-group">
                <label>Appointment Time <span style="color: #e74c3c;">*</span></label>
                <select name="booking_time" required>
                    <option value="">Select Time</option>
                    <option>09:00 AM</option>
                    <option>10:00 AM</option>
                    <option>11:00 AM</option>
                    <option>12:00 PM</option>
                    <option>01:00 PM</option>
                    <option>02:00 PM</option>
                    <option>03:00 PM</option>
                    <option>04:00 PM</option>
                    <option>05:00 PM</option>
                    <option>06:00 PM</option>
                </select>
            </div>

            <!-- Reference Image -->
            <div class="form-group">
                <label>Upload Reference Image (Optional)</label>
                <input
                    type="file"
                    name="reference_image"
                    accept="image/*">
            </div>

            <!-- Special Request -->
            <div class="form-group">
                <label>Special Request</label>
                <textarea
                    name="message"
                    rows="4"
                    placeholder="Write any special requests or notes here..."></textarea>
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method">
                    <option value="Pay at Salon">Pay at Salon (Cash / Card / UPI)</option>
                    <option value="UPI / QR Code">Instant UPI / QR Code</option>
                    <option value="Online Card">Credit / Debit Card</option>
                </select>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <ul>
                    <li><strong>Salon:</strong> GlowNest Beautiparlor</li>
                    <li><strong>Working Hours:</strong> 9:00 AM - 7:00 PM</li>
                    <li><strong>Booking Confirmation:</strong> Instant</li>
                    <li><strong>Cancellation:</strong> Free before 24 Hours</li>
                </ul>
            </div>

        <!-- Terms -->
        <div class="terms-box">
            <label>
                <input type="checkbox" required>
                I agree to the Terms & Conditions and Privacy Policy.
            </label>
        </div>
        
            <!-- Submit Button -->
            <div class="booking-button">
                <button type="submit" class="btn" id="btn-confirm-booking">
                    <i class="fa-solid fa-calendar-check"></i> Confirm Booking
                </button>
            </div>
        </form>
    </div>
</section>

<!-- ================= CONTACT INFORMATION ================= -->
<section class="booking-contact">
    <div class="contact-card">
        <i class="fa-solid fa-phone"></i>
        <h3>Call Us</h3>
        <p>+91 9876543210</p>
    </div>
    <div class="contact-card">
        <i class="fa-solid fa-envelope"></i>
        <h3>Email</h3>
        <p>info@glownest.com</p>
    </div>
    <div class="contact-card">
        <i class="fa-solid fa-location-dot"></i>
        <h3>Visit Us</h3>
        <p>123 Beauty Street, Mumbai, India</p>
    </div>
</section>

<?php
include("includes/footer.php");
?>