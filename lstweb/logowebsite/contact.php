<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config.php");
include_once("includes/order_helpers.php");

ensure_store_tables($conn);

$isLoggedIn = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0 && ($_SESSION['user_type'] ?? '') === 'user';
$userPrefill = [
    'name' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'mobile' => $_SESSION['mobile'] ?? ''
];

$contactError = '';
$showContactModal = false;
$confirmedContact = null;

// Handle Form Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $mobile === '' || $subject === '' || $message === '') {
        $contactError = 'Please fill all required fields before sending your message.';
    } elseif (strlen(preg_replace('/[^0-9]/', '', $mobile)) < 10) {
        $contactError = 'Please enter a valid 10-digit mobile number.';
    } else {
        $insertQuery = mysqli_prepare(
            $conn,
            "INSERT INTO contact_messages (name, email, mobile, subject, message, status)
             VALUES (?, ?, ?, ?, ?, 'New')"
        );

        if ($insertQuery) {
            mysqli_stmt_bind_param($insertQuery, "sssss", $name, $email, $mobile, $subject, $message);
            if (mysqli_stmt_execute($insertQuery)) {
                $newMsgId = mysqli_insert_id($conn);
                mysqli_stmt_close($insertQuery);

                $ticketNumber = 'TKT' . date('Ymd') . '-' . rand(1000, 9999);

                $_SESSION['contact_sent'] = [
                    'id' => $newMsgId,
                    'ticket' => $ticketNumber,
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'subject' => $subject,
                    'message' => $message,
                    'created_at' => date('d M Y, h:i A')
                ];

                header("Location: contact.php?msg_sent=1&ticket=" . urlencode($ticketNumber));
                exit;
            } else {
                $contactError = 'Failed to send message: ' . mysqli_error($conn);
                mysqli_stmt_close($insertQuery);
            }
        } else {
            $contactError = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// Check for recent message sent confirmation
if (isset($_GET['msg_sent']) && $_GET['msg_sent'] == '1') {
    $showContactModal = true;
    if (isset($_SESSION['contact_sent'])) {
        $confirmedContact = $_SESSION['contact_sent'];
    } else {
        $confirmedContact = [
            'ticket' => $_GET['ticket'] ?? ('TKT' . date('Ymd') . '-' . rand(1000, 9999)),
            'name' => 'Valued Customer',
            'email' => 'Your email',
            'mobile' => 'Your mobile',
            'subject' => 'Inquiry / Feedback',
            'created_at' => date('d M Y, h:i A')
        ];
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>

<!-- ================= CONTACT CONFIRMATION CELEBRATION MODAL ================= -->
<?php if ($showContactModal && $confirmedContact): ?>
    <div id="contactSuccessModal" class="order-modal-backdrop active" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title">
        <div class="order-modal-card">
            <!-- Confetti Canvas -->
            <canvas id="contact-confetti-canvas" class="confetti-canvas"></canvas>

            <button type="button" class="modal-close-btn" onclick="closeContactModal()" aria-label="Close modal">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-celebration-head">
                <div class="celebration-badge-ring">
                    <div class="celebration-badge-inner" style="background: linear-gradient(135deg, #ff4f81, #d6336c);">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                </div>
                <span class="celebration-sub"><i class="fa-solid fa-sparkles"></i> Message Sent Successfully</span>
                <h2 id="contact-modal-title">🎉 Your Message Has Been Sent!</h2>
                <p>Thank you, <b><?php echo htmlspecialchars($confirmedContact['name']); ?></b>! Our care team has received your message and will respond within 24 hours.</p>
            </div>

            <div class="modal-receipt-box">
                <div class="receipt-row-top">
                    <div>
                        <span class="receipt-label">Inquiry Ticket #</span>
                        <strong class="receipt-id">#<?php echo htmlspecialchars($confirmedContact['ticket']); ?></strong>
                    </div>
                    <span class="receipt-status-pill"><i class="fa-solid fa-circle-check"></i> Delivered</span>
                </div>

                <div class="receipt-delivery-preview">
                    <div class="r-detail">
                        <i class="fa-solid fa-user-check"></i>
                        <div>
                            <span class="r-label">Contact Details</span>
                            <strong><?php echo htmlspecialchars($confirmedContact['name']); ?></strong>
                            <p><?php echo htmlspecialchars($confirmedContact['email']); ?> · <?php echo htmlspecialchars($confirmedContact['mobile']); ?></p>
                        </div>
                    </div>
                    <div class="r-detail">
                        <i class="fa-solid fa-tag"></i>
                        <div>
                            <span class="r-label">Subject</span>
                            <strong><?php echo htmlspecialchars($confirmedContact['subject']); ?></strong>
                            <p>Sent: <b><?php echo htmlspecialchars($confirmedContact['created_at']); ?></b></p>
                        </div>
                    </div>
                </div>

                <div class="receipt-estimate" style="margin-top: 10px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #ff4f81;"></i>
                    <span>Response Time: <b>Within 24 Hours via Email & Phone</b></span>
                </div>
            </div>

            <div class="modal-actions-grid">
                <button type="button" class="btn modal-btn-primary" onclick="closeContactModal()">
                    <i class="fa-solid fa-check"></i> Great, Got It!
                </button>
                <a href="shop.php" class="outline-btn modal-btn-secondary" style="text-decoration: none;">
                    <i class="fa-solid fa-bag-shopping"></i> Continue to Beauty Shop
                </a>
            </div>
        </div>
    </div>

    <!-- Celebration Confetti Script -->
    <script>
    function closeContactModal() {
        const modal = document.getElementById('contactSuccessModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.remove(), 350);
        }
    }

    (function() {
        const canvas = document.getElementById('contact-confetti-canvas');
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

<!-- ================= CONTACT BANNER ================= -->
<section class="contact-banner">
    <div class="contact-banner-content">
        <h1>Contact GlowNest</h1>
        <p>We'd love to hear from you. Send us your questions, feedback, or booking inquiries.</p>
    </div>
</section>

<!-- ================= CONTACT FORM ================= -->
<section class="contact-section">
    <div class="contact-container">

        <!-- Left Side -->
        <div class="contact-info">
            <h2>Get In Touch</h2>
            <p>If you have any questions regarding our beauty services, products, appointments, or offers, feel free to contact us.</p>

            <div class="info-box">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <h4>Address</h4>
                    <p>123 Beauty Street, Mumbai, Maharashtra, India</p>
                </div>
            </div>

            <div class="info-box">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <h4>Phone</h4>
                    <p>+91 9876543210</p>
                </div>
            </div>

            <div class="info-box">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p>info@glownest.com</p>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="contact-form">
            <h2>Send Message</h2>

            <?php if ($contactError !== ''): ?>
                <div class="checkout-alert checkout-alert-error" style="margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div><strong>Notice:</strong> <?php echo htmlspecialchars($contactError); ?></div>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST" id="contact-form">
                <input type="hidden" name="action" value="send_message">

                <div class="form-group">
                    <label>Full Name <span style="color: #e74c3c;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($userPrefill['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter your full name"
                        required>
                </div>

                <div class="form-group">
                    <label>Email Address <span style="color: #e74c3c;">*</span></label>
                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($userPrefill['email'], ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter your email"
                        required>
                </div>

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

                <!-- Subject -->
                <div class="form-group">
                    <label>Subject <span style="color: #e74c3c;">*</span></label>
                    <input
                        type="text"
                        name="subject"
                        placeholder="What is this inquiry regarding?"
                        required>
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label>Your Message <span style="color: #e74c3c;">*</span></label>
                    <textarea
                        name="message"
                        rows="5"
                        placeholder="Write your message here..."
                        required></textarea>
                </div>

                <!-- Send Button -->
                <div class="contact-button">
                    <button type="submit" class="btn" id="btn-send-message">
                        <i class="fa-solid fa-paper-plane"></i> Send Message
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- ================= WORKING HOURS ================= -->
<section class="working-hours">
    <h2>Salon Working Hours</h2>
    <div class="hours-container">
        <div class="hour-card">
            <h3>Monday - Saturday</h3>
            <p>09:00 AM - 07:00 PM</p>
        </div>
        <div class="hour-card">
            <h3>Sunday</h3>
            <p>10:00 AM - 04:00 PM</p>
        </div>
    </div>
</section>

<!-- ================= SOCIAL MEDIA ================= -->
<section class="social-media">
    <h2>Follow Us</h2>
    <div class="social-icons">
        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-youtube"></i></a>
        <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
    </div>
</section>

<!-- ================= GOOGLE MAP ================= -->
<section class="map-section">
    <h2>Find Us</h2>
    <div class="map-container">
        <iframe
            src="https://www.google.com/maps?q=Mumbai,Maharashtra&output=embed"
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>
</section>

<!-- ================= FAQ SECTION ================= -->
<section class="faq-section">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-container">
        <div class="faq-box">
            <h3>How can I book an appointment?</h3>
            <p>You can easily book an appointment from the Booking page by selecting your preferred service, date and time.</p>
        </div>
        <div class="faq-box">
            <h3>Can I cancel my booking?</h3>
            <p>Yes. You can cancel your appointment before the scheduled time by contacting our salon.</p>
        </div>
        <div class="faq-box">
            <h3>Which payment methods are accepted?</h3>
            <p>Currently we accept Cash, UPI and Card payments at the salon.</p>
        </div>
        <div class="faq-box">
            <h3>Do I need an account?</h3>
            <p>Creating an account helps you manage bookings, orders and future services easily.</p>
        </div>
    </div>
</section>

<!-- ================= CONTACT NOTE ================= -->
<section class="contact-note">
    <h2>We're Always Happy To Help!</h2>
    <p>Thank you for choosing <strong>GlowNest Beautiparlor</strong>. Our team is always ready to answer your questions and provide the best beauty experience.</p>
</section>

<?php
include("includes/footer.php");
?>