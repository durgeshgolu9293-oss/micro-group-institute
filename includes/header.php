<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/functions.php';
}
$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$phone = get_setting('phone', '+91 98765 43210');
$email = get_setting('email', 'info@microgroupinstitute.com');

$currPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?><?= htmlspecialchars($site_name) ?></title>
    <meta name="description" content="Micro Group of Computer Institute - Quality digital education, ADCA, CCC, DCA, Tally Prime courses in <?= htmlspecialchars($location) ?>. Manager: <?= htmlspecialchars($manager_name) ?>">
    
    <!-- Google Fonts & Bootstrap Icons & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Institute Design System CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

<!-- Announcement / Breaking News Bar -->
<div class="bg-primary text-white py-1 px-3 small d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, #0284C7, #06B6D4) !important;">
    <div class="container d-flex align-items-center gap-2 overflow-hidden text-nowrap">
        <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="bi bi-megaphone-fill me-1"></i>NOTICE</span>
        <marquee behavior="scroll" direction="left" scrollamount="5" class="fw-semibold text-white">
            🎓 Admissions Open for 2026 Batch (ADCA, CCC, DCA, Tally Prime + GST) &bull; 💻 Online Examination Portal Active &bull; 📜 Instant Verifiable Course Completion Certificates &bull; Manager: <?= htmlspecialchars($manager_name) ?> (<?= htmlspecialchars($location) ?>)
        </marquee>
    </div>
</div>

<!-- Top Quick Info Bar -->
<div class="top-bar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-geo-alt-fill text-warning me-1"></i><?= htmlspecialchars($location) ?></span>
            <span class="d-none d-md-inline"><i class="bi bi-person-badge text-info me-1"></i>Manager: <strong><?= htmlspecialchars($manager_name) ?></strong></span>
            <span class="d-none d-lg-inline"><i class="bi bi-telephone-fill text-success me-1"></i><a href="tel:<?= htmlspecialchars($phone) ?>"><?= htmlspecialchars($phone) ?></a></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>/verify-certificate.php" class="text-warning fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Verify Certificate</a>
            <span class="text-secondary">|</span>
            <a href="<?= BASE_URL ?>/result.php"><i class="bi bi-trophy-fill me-1"></i>Check Result</a>
            <span class="text-secondary">|</span>
            <a href="<?= BASE_URL ?>/admin/login.php" class="text-light opacity-75"><i class="bi bi-shield-lock me-1"></i>Admin</a>
        </div>
    </div>
</div>

<!-- Main Sticky Navbar -->
<nav class="navbar navbar-expand-lg navbar-mgi">
    <div class="container">
        <a class="navbar-brand-logo" href="<?= BASE_URL ?>/index.php">
            <div class="brand-icon-box">
                <i class="bi bi-laptop"></i>
            </div>
            <div class="brand-text-wrap">
                <span class="brand-title">MICRO GROUP</span>
                <span class="brand-sub">Computer Institute</span>
            </div>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 my-3 my-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currPage == 'index') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currPage == 'about') ? 'active' : '' ?>" href="<?= BASE_URL ?>/about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currPage == 'courses' || $currPage == 'course-details') ? 'active' : '' ?>" href="<?= BASE_URL ?>/courses.php">Courses & Fees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currPage == 'notes') ? 'active' : '' ?>" href="<?= BASE_URL ?>/notes.php">Study Material</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= (strpos($currPage, 'exam') !== false) ? 'active' : '' ?>" href="<?= BASE_URL ?>/exam-login.php">Online Exam</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom text-warning fw-bold <?= ($currPage == 'typing-test') ? 'active' : '' ?>" href="<?= BASE_URL ?>/typing-test.php"><i class="bi bi-keyboard me-1"></i>Typing Test <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">LIVE</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currPage == 'contact') ? 'active' : '' ?>" href="<?= BASE_URL ?>/contact.php">Contact</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 ms-lg-3">
                <?php if (is_student_logged_in()): ?>
                    <a href="<?= BASE_URL ?>/student/dashboard.php" class="btn btn-primary-mgi btn-sm">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-navy btn-sm">
                        <i class="bi bi-person"></i> Student Login
                    </a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-cyan-mgi btn-sm">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <?php display_flash_message(); ?>
</div>