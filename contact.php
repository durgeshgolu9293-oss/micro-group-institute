<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Contact Us";

$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur / Fukganj, Uttar Pradesh');
$phone = get_setting('phone', '+91 98765 43210');
$email = get_setting('email', 'info@microgroupinstitute.com');

$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $user_email = trim($_POST['email'] ?? '');
    $user_phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($user_email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'unread')");
        if ($stmt->execute([$name, $user_email, $user_phone, $subject, $message])) {
            $sent = true;
        } else {
            $error = 'Could not send message. Please try again or call us directly.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-info text-dark px-3 py-2 fw-bold text-uppercase mb-3">Support & Admissions</span>
        <h1 class="display-5 fw-bold text-white mb-3">Contact Micro Group Computer Institute</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto" style="max-width: 700px;">
            Have questions regarding new admissions, fees, syllabus or exams? Reach out to Director DK Singh & team.
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                    <h4 class="fw-bold text-dark mb-4">Institute Information</h4>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-4">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Campus Location</h6>
                            <p class="text-secondary small mb-0"><?= htmlspecialchars($address) ?></p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 fs-4">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Telephone / WhatsApp</h6>
                            <p class="text-secondary small mb-0"><a href="tel:<?= htmlspecialchars($phone) ?>" class="text-decoration-none text-dark fw-semibold"><?= htmlspecialchars($phone) ?></a></p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 fs-4">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Official Email</h6>
                            <p class="text-secondary small mb-0"><a href="mailto:<?= htmlspecialchars($email) ?>" class="text-decoration-none text-dark fw-semibold"><?= htmlspecialchars($email) ?></a></p>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-light border mt-auto">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-info text-white p-2 fs-5">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Manager & Director</small>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($manager_name) ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                    <h4 class="fw-bold text-dark mb-4">Send Us a Message</h4>

                    <?php if ($sent): ?>
                        <div class="alert alert-success py-3 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Thank you! Your message has been sent.</h6>
                                <p class="small mb-0">Manager DK Singh and our admissions staff will contact you shortly.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small mb-3"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Your Name *</label>
                                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Address *</label>
                                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mobile Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="10-digit number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Inquiry Subject *</label>
                                    <input type="text" name="subject" class="form-control" placeholder="e.g. ADCA Course Admission" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Your Message *</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Please write your questions or course queries here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi btn-lg">
                                <i class="bi bi-send-fill"></i> Send Message
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>