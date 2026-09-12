<?php
$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur, Bahraich, Uttar Pradesh');
$phone = get_setting('phone', '+91 98765 43210');
$email = get_setting('email', 'info@microgroupinstitute.com');
$tagline = get_setting('tagline', 'Learn • Practice • Test • Achieve');
$dev_name = get_setting('developer_name', 'Durgesh Pratap Singh');

// Clean phone check
$wa_phone = preg_replace('/[^0-9]/', '', $phone);
$is_dummy_phone = (strpos($wa_phone, '9876543210') !== false || empty($wa_phone) || strlen($wa_phone) < 10);
$wa_link = $is_dummy_phone ? BASE_URL . '/contact.php' : 'https://wa.me/' . (strlen($wa_phone) === 10 ? '91' . $wa_phone : $wa_phone) . '?text=' . urlencode('Hello Micro Group Institute, I want information regarding computer courses.');
?>
<footer class="footer-mgi">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon-box" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="text-white mb-0"><?= htmlspecialchars($site_name) ?></h5>
                </div>
                <p class="small text-secondary mb-3">
                    <strong><?= htmlspecialchars($tagline) ?></strong><br>
                    Empowering students in <strong><?= htmlspecialchars($location) ?></strong> with practical IT education, recognized diplomas, digital notes, and certified skills for government and private employment.
                </p>
                <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= BASE_URL ?>/assets/images/manager.jpg" alt="<?= htmlspecialchars($manager_name) ?>" class="rounded-circle border border-2 border-warning" style="width: 44px; height: 44px; object-fit: cover; object-position: top;">
                        <div>
                            <div class="small text-secondary text-uppercase fw-semibold">Institute Director & Manager</div>
                            <strong class="text-white"><?= htmlspecialchars($manager_name) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/index.php"><i class="bi bi-chevron-right"></i>Home</a></li>
                    <li><a href="<?= BASE_URL ?>/about.php"><i class="bi bi-chevron-right"></i>About Us</a></li>
                    <li><a href="<?= BASE_URL ?>/courses.php"><i class="bi bi-chevron-right"></i>Courses & Fees</a></li>
                    <li><a href="<?= BASE_URL ?>/notes.php"><i class="bi bi-chevron-right"></i>Study Material</a></li>
                    <li><a href="<?= BASE_URL ?>/contact.php"><i class="bi bi-chevron-right"></i>Contact Support</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Student Services</h6>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/exam-login.php"><i class="bi bi-chevron-right"></i>Start Online Exam</a></li>
                    <li><a href="<?= BASE_URL ?>/result.php"><i class="bi bi-chevron-right"></i>Check Exam Result</a></li>
                    <li><a href="<?= BASE_URL ?>/verify-certificate.php"><i class="bi bi-chevron-right"></i>Verify Certificate</a></li>
                    <li><a href="<?= BASE_URL ?>/login.php"><i class="bi bi-chevron-right"></i>Student Portal Login</a></li>
                    <li><a href="<?= BASE_URL ?>/register.php"><i class="bi bi-chevron-right"></i>New Admission Registration</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="footer-title">Campus & Location</h6>
                <ul class="footer-links">
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-warning mt-1"></i>
                        <span><?= htmlspecialchars($address) ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-info"></i>
                        <?php if ($is_dummy_phone): ?>
                            <a href="<?= BASE_URL ?>/contact.php" class="text-light opacity-75">Contact Institute Office</a>
                        <?php else: ?>
                            <a href="tel:<?= htmlspecialchars($phone) ?>"><?= htmlspecialchars($phone) ?></a>
                        <?php endif; ?>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-danger"></i>
                        <a href="<?= BASE_URL ?>/contact.php"><?= htmlspecialchars($email) ?></a>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-fill text-success"></i>
                        <span>Mon - Sat: 8:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center g-3 text-center text-md-start">
                <div class="col-md-6">
                    <div>
                        &copy; <?= date('Y') ?> <strong><?= htmlspecialchars($site_name) ?></strong> (<?= htmlspecialchars($location) ?>). All Rights Reserved.
                    </div>
                    <div class="small text-secondary mt-1">
                        Director & Manager: <strong><?= htmlspecialchars($manager_name) ?></strong>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <img src="<?= BASE_URL ?>/assets/images/developer.jpg" 
                             alt="<?= htmlspecialchars($dev_name) ?>" 
                             class="rounded-circle border border-warning" 
                             style="width: 28px; height: 28px; object-fit: cover; object-position: top;">
                        <span class="text-light small">
                            Designed & Developed by <strong class="text-warning text-decoration-none"><?= htmlspecialchars($dev_name) ?></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Support Widget -->
<a href="<?= $wa_link ?>" 
   <?= $is_dummy_phone ? '' : 'target="_blank"' ?>
   class="position-fixed shadow-lg d-flex align-items-center justify-content-center text-white text-decoration-none rounded-circle" 
   style="bottom: 24px; right: 24px; width: 56px; height: 56px; background: #25D366; z-index: 9999; font-size: 1.8rem; transition: transform 0.3s ease;"
   onmouseover="this.style.transform='scale(1.1)'"
   onmouseout="this.style.transform='scale(1)'"
   title="<?= $is_dummy_phone ? 'Direct Institute Support' : 'Chat on WhatsApp' ?>">
    <i class="bi <?= $is_dummy_phone ? 'bi-chat-dots-fill' : 'bi-whatsapp' ?>"></i>
</a>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>