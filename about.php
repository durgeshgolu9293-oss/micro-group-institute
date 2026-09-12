<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "About Us - Micro Group of Computer Institute";

$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur / Fukganj, Uttar Pradesh');
$tagline = get_setting('tagline', 'Learn • Practice • Test • Achieve');
$dev_name = get_setting('developer_name', 'Durgesh Pratap Singh');

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-info text-dark px-3 py-2 fw-bold text-uppercase mb-3">About Our Institute</span>
        <h1 class="display-5 fw-bold text-white mb-3">Micro Group of Computer Institute</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto" style="max-width: 700px;">
            "To provide quality computer education and practical digital skills to students and learners."
        </p>
        <div class="text-warning fw-semibold"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($location) ?> | Manager: <?= htmlspecialchars($manager_name) ?></div>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="section-subtitle">Our Vision & Mission</div>
                <h2 class="section-title mb-4">Building Digital Proficiency in Payagpur & Beyond</h2>
                <p class="text-secondary mb-3">
                    <strong>Micro Group of Computer Institute</strong> was established under the visionary leadership of <strong>Manager DK Singh</strong> with a singular objective: bridging the digital divide for students, job seekers, and professionals across Bhoopganj Payagpur, Fukganj, and neighboring districts.
                </p>
                <p class="text-secondary mb-4">
                    In today's competitive job market, computer proficiency is no longer optional—it is fundamental. We provide career-oriented computer diploma courses including <strong>ADCA (1 Year)</strong>, <strong>CCC (3 Months)</strong>, <strong>DCA (6 Months)</strong>, <strong>PGDCA</strong>, and <strong>Tally Prime with GST</strong>. Every course is backed by extensive practical lab work, digital study material, online mock tests, and verifiable completion certificates.
                </p>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                            <i class="bi bi-check2-circle text-primary fs-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Certified Training</h6>
                                <small class="text-muted">ISO 9001:2015 Standards</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                            <i class="bi bi-mortarboard text-success fs-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Verifiable Diplomas</h6>
                                <small class="text-muted">Online Serial Number Tracking</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="manager-quote-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= BASE_URL ?>/assets/images/manager.jpg" alt="<?= htmlspecialchars($manager_name) ?>" class="rounded-circle border border-3 border-warning shadow" style="width: 80px; height: 80px; object-fit: cover; object-position: top; min-width: 80px;">
                        <div>
                            <div class="text-warning small fw-bold">INSTITUTE MANAGER & DIRECTOR</div>
                            <h3 class="text-white mb-0"><?= htmlspecialchars($manager_name) ?></h3>
                            <span class="text-info small">Micro Group Computer Institute</span>
                        </div>
                    </div>
                    <p class="text-light mb-4" style="line-height: 1.7;">
                        "Welcome to Micro Group Computer Institute. We believe true learning occurs when theoretical concepts transform into practical application. Every student in our institute receives personal attention, a dedicated workstation in our modern laboratory, digital notes on their mobile devices, and periodic online assessments. Our goal is to see our students excel in government examinations, private sector jobs, and entrepreneurship."
                    </p>
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="small text-white">
                            <i class="bi bi-geo-alt text-warning me-1"></i><strong>Location:</strong> <?= htmlspecialchars($address) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7 Core Pillars -->
        <div class="mt-5 pt-5 border-top">
            <div class="section-header">
                <div class="section-subtitle">Academic Excellence</div>
                <h2 class="section-title">The 7 Pillars of Micro Group</h2>
                <p class="section-desc">What sets our educational institute apart from ordinary training centers.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-award-fill"></i></div>
                        <h5 class="fw-bold">1. Quality Education</h5>
                        <p class="text-muted small mb-0">Systematic, industry-relevant curriculum regularly updated with latest IT developments.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-laptop"></i></div>
                        <h5 class="fw-bold">2. Practical Training</h5>
                        <p class="text-muted small mb-0">Daily 1-on-1 machine practice on real software projects, office tools, and accounting sheets.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-phone"></i></div>
                        <h5 class="fw-bold">3. Digital Learning</h5>
                        <p class="text-muted small mb-0">24/7 web portal access to course curriculum, student dashboard, and progress tracking.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-card-checklist"></i></div>
                        <h5 class="fw-bold">4. Online Examinations</h5>
                        <p class="text-muted small mb-0">Modern MCQ testing system with live timers, instant evaluation, and detailed scorecards.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                        <h5 class="fw-bold">5. Comprehensive Notes</h5>
                        <p class="text-muted small mb-0">Chapter-wise downloadable PDF notes, shortcut keys reference, and interview question sets.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="pillar-card">
                        <div class="pillar-icon-wrap"><i class="bi bi-patch-check-fill"></i></div>
                        <h5 class="fw-bold">6. Recognized Certificates</h5>
                        <p class="text-muted small mb-0">Official Course Completion Certificates with unique serial numbers and online QR verification.</p>
                    </div>
                </div>
            </div>

            <!-- Developer Profile Card with Real Photo -->
            <div class="card border-0 rounded-4 shadow-lg p-4 text-white" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%); border: 1px solid rgba(255, 255, 255, 0.15);">
                <div class="row align-items-center g-4">
                    <div class="col-md-auto text-center">
                        <img src="<?= BASE_URL ?>/assets/images/developer.jpg" 
                             alt="<?= htmlspecialchars($dev_name) ?>" 
                             class="rounded-circle border border-3 border-warning shadow" 
                             style="width: 100px; height: 100px; object-fit: cover; object-position: top; box-shadow: 0 8px 25px rgba(0,0,0,0.4) !important;">
                    </div>
                    <div class="col-md">
                        <span class="badge bg-warning text-dark fw-bold text-uppercase mb-1">Lead Software Architect & Developer</span>
                        <h4 class="fw-bold mb-1 text-white"><?= htmlspecialchars($dev_name) ?></h4>
                        <p class="small text-light opacity-90 mb-0" style="max-width: 700px;">
                            Designed and engineered the complete full-stack web application, online timed examination engine, automatic certificate generation & verification registry, and administrative portal for <strong>Micro Group of Computer Institute</strong> (Bhoopganj Payagpur).
                        </p>
                    </div>
                    <div class="col-md-auto text-md-end">
                        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-light rounded-pill px-4">
                            <i class="bi bi-envelope-fill me-1"></i> Contact Developer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>