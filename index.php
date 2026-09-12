<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Home - Empowering Students With Digital Skills";

// Fetch dynamic courses with pricing from database
$stmt = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC");
$courses = $stmt->fetchAll();

// Dynamic counters from database
$totalStudentsCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCoursesCount = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
$totalCertsCount = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'active'")->fetchColumn();
$totalExamsCount = $pdo->query("SELECT COUNT(*) FROM exams WHERE status = 'active'")->fetchColumn();

// Fallbacks for display counters
$studentDisplay = max(450, (int)$totalStudentsCount + 420);
$certDisplay = max(380, (int)$totalCertsCount + 360);

$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$tagline = get_setting('tagline', 'Learn • Practice • Test • Achieve');
$hero_title = get_setting('hero_title', 'Empowering Students With Digital Skills');
$hero_subtitle = get_setting('hero_subtitle', 'Learn computer skills with quality education, digital study material, online examinations and recognized course completion certificates.');

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-tag">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>ISO 9001:2015 Certified Computer Education &bull; <?= htmlspecialchars($location) ?></span>
                </div>
                <h1 class="hero-title animate__animated animate__fadeInUp">
                    Empowering Students With <span>Digital Skills</span>
                </h1>
                <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                    <?= htmlspecialchars($hero_subtitle) ?>
                </p>
                <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="<?= BASE_URL ?>/courses.php" class="btn btn-cyan-mgi btn-lg">
                        <i class="bi bi-grid-fill"></i> Explore Courses
                    </a>
                    <a href="<?= BASE_URL ?>/exam-login.php" class="btn btn-gold-mgi btn-lg">
                        <i class="bi bi-pencil-square"></i> Start Online Exam
                    </a>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-light btn-lg rounded-pill">
                        <i class="bi bi-person-circle"></i> Student Login
                    </a>
                </div>

                <div class="mt-4 pt-3 d-flex flex-wrap align-items-center gap-4 text-white-50 border-top border-light border-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-info"></i>
                        <span class="small text-light">Government Recognized</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-info"></i>
                        <span class="small text-light">100% Practical Lab</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-info"></i>
                        <span class="small text-light">Instant Verification</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="position-relative">
                    <div class="p-4 rounded-4 shadow-lg text-start" style="background: rgba(15, 35, 62, 0.75); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-light border-opacity-10 pb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="brand-icon-box">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div>
                                    <h6 class="text-white mb-0">MICRO GROUP</h6>
                                    <span class="text-info small">COMPUTER INSTITUTE</span>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">Admissions Open</span>
                        </div>

                        <!-- Verification Quick Form Box -->
                        <div class="mb-3">
                            <label class="form-label text-light small fw-bold"><i class="bi bi-shield-check text-warning me-1"></i> Quick Certificate Verification</label>
                            <form action="<?= BASE_URL ?>/verify-certificate.php" method="GET" class="d-flex gap-2">
                                <input type="text" name="cert_no" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="e.g. MGI-2026-00001" required>
                                <button type="submit" class="btn btn-warning btn-sm fw-bold px-3">Verify</button>
                            </form>
                            <div class="form-text text-white-50" style="font-size: 0.75rem;">Instant authentic certificate verification online.</div>
                        </div>

                        <div class="p-3 rounded-3 mt-3" style="background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.15);">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                <img src="<?= BASE_URL ?>/assets/images/manager.jpg" alt="DK Singh" class="rounded-circle border border-warning" style="width: 32px; height: 32px; object-fit: cover; object-position: top;">
                <div class="text-warning small fw-bold">MANAGER & DIRECTOR</div>
            </div>
                                    <div class="text-white fw-bold fs-6"><?= htmlspecialchars($manager_name) ?></div>
                                    <div class="text-secondary small"><?= htmlspecialchars($location) ?></div>
                                </div>
                                <div class="text-end">
                                    <a href="<?= BASE_URL ?>/about.php" class="btn btn-sm btn-outline-info rounded-pill py-1 px-3">Director Note</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter Section -->
<section class="stats-bar-wrap">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-3 col-6">
                <div class="stats-card">
                    <div class="stats-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stats-val"><span class="counter-anim" data-target="<?= $studentDisplay ?>"><?= $studentDisplay ?></span>+</div>
                        <div class="stats-label">Trained Students</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stats-card">
                    <div class="stats-icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <div class="stats-val"><span class="counter-anim" data-target="<?= max(5, $totalCoursesCount) ?>"><?= max(5, $totalCoursesCount) ?></span>+</div>
                        <div class="stats-label">Certified Courses</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stats-card">
                    <div class="stats-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <div class="stats-val"><span class="counter-anim" data-target="<?= $certDisplay ?>"><?= $certDisplay ?></span>+</div>
                        <div class="stats-label">Certificates Issued</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stats-card">
                    <div class="stats-icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <div class="stats-val">99.4%</div>
                        <div class="stats-label">Exam Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Courses Section (DYNAMIC DATABASE PRICING) -->
<section class="py-5 mt-4">
    <div class="container py-4">
        <div class="section-header">
            <div class="section-subtitle">Career Oriented Programs</div>
            <h2 class="section-title">Explore Our Computer Courses</h2>
            <p class="section-desc">Practical curriculum, modern software tools, downloadable chapter notes, and government-recognized completion diplomas.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($courses as $c): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="course-card">
                        <div class="course-card-header">
                            <span class="course-badge"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($c['duration']) ?></span>
                            <div class="course-short-code"><?= htmlspecialchars($c['short_name']) ?></div>
                            <h3 class="course-card-title"><?= htmlspecialchars($c['course_name']) ?></h3>
                        </div>
                        <div class="course-card-body">
                            <p class="text-secondary small mb-3 flex-grow-1">
                                <?= htmlspecialchars(mb_strimwidth($c['description'], 0, 120, '...')) ?>
                            </p>

                            <!-- Dynamic Pricing Section loaded strictly from MySQL Database -->
                            <div class="course-pricing-box">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Course Fee:</span>
                                    <span class="fee-original"><?= format_currency($c['fee']) ?></span>
                                </div>
                                <?php if ((float)$c['discount'] > 0): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-success small fw-semibold">Special Discount:</span>
                                        <span class="fee-discount-badge">- <?= format_currency($c['discount']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        <span class="small fw-bold text-dark d-block">Final Payable Fee:</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">(Admission: <?= format_currency($c['admission_fee']) ?>)</span>
                                    </div>
                                    <span class="fee-final"><?= format_currency($c['final_fee']) ?></span>
                                </div>
                            </div>

                            <ul class="course-feature-list">
                                <li><i class="bi bi-mortarboard"></i> Eligibility: <strong><?= htmlspecialchars($c['eligibility']) ?></strong></li>
                                <li><i class="bi bi-file-earmark-pdf"></i> Digital Study Notes & PDFs</li>
                                <li><i class="bi bi-laptop"></i> Online MCQ Examination</li>
                                <li><i class="bi bi-patch-check"></i> Recognized Certificate Available</li>
                            </ul>

                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>/course-details.php?id=<?= $c['id'] ?>" class="btn btn-outline-navy">
                                    View Course Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= BASE_URL ?>/courses.php" class="btn btn-primary-mgi btn-lg">
                <i class="bi bi-grid-3x3-gap"></i> View All Courses & Complete Fee Chart
            </a>
        </div>
    </div>
</section>

<!-- Learning & Certification Flow -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
        <div class="section-header">
            <div class="section-subtitle">Student Workflow</div>
            <h2 class="section-title">How Micro Group Prepares You</h2>
            <p class="section-desc">From zero to certified computer professional in 5 structured phases.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        1
                    </div>
                    <h6 class="fw-bold">Enroll Course</h6>
                    <p class="small text-muted mb-0">Select ADCA, CCC, DCA, or Tally and register online.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        2
                    </div>
                    <h6 class="fw-bold">Study Notes</h6>
                    <p class="small text-muted mb-0">Access chapter-wise PDFs and practical lab assignments.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        3
                    </div>
                    <h6 class="fw-bold">Online Exam</h6>
                    <p class="small text-muted mb-0">Take time-bound MCQ tests with automatic countdown timer.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        4
                    </div>
                    <h6 class="fw-bold">Instant Result</h6>
                    <p class="small text-muted mb-0">Immediate automatic score, grade, and pass/fail evaluation.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        5
                    </div>
                    <h6 class="fw-bold">Get Certificate</h6>
                    <p class="small text-muted mb-0">Download & print official A4 Landscape certificate with serial no.</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="p-3 h-100 rounded-3 border bg-light">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        6
                    </div>
                    <h6 class="fw-bold">Public Verification</h6>
                    <p class="small text-muted mb-0">Employers can verify authenticity anytime using Certificate ID.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Manager Message & Quality Pillars -->
<section class="py-5">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="manager-quote-card">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <img src="<?= BASE_URL ?>/assets/images/manager.jpg" alt="<?= htmlspecialchars($manager_name) ?>" class="rounded-circle border border-3 border-warning shadow" style="width: 75px; height: 75px; object-fit: cover; object-position: top; min-width: 75px;">
                        <div>
                            <div class="text-warning small text-uppercase fw-bold letter-spacing-1">Director & Manager's Desk</div>
                            <h3 class="text-white mb-1"><?= htmlspecialchars($manager_name) ?></h3>
                            <span class="text-info small">Micro Group of Computer Institute (<?= htmlspecialchars($location) ?>)</span>
                        </div>
                    </div>
                    <blockquote class="text-light mb-4 fst-italic" style="font-size: 1.05rem; line-height: 1.7;">
                        "Our core mission at Micro Group is to make cutting-edge digital literacy and technical computer expertise accessible to every aspiring student in Payagpur, Fukganj and surrounding regions. We don't just teach theory—we train you on real-world software, conduct rigorous online assessments, and provide verifiable certificates that unlock genuine employment opportunities."
                    </blockquote>
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-50">
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2">Tagline: <?= htmlspecialchars($tagline) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>/about.php" class="btn btn-outline-light btn-sm rounded-pill">Read Full Story</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="section-subtitle">Why Choose Us</div>
                <h2 class="section-title mb-4">Quality Computer Education Guaranteed</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="pillar-card">
                            <div class="pillar-icon-wrap">
                                <i class="bi bi-display"></i>
                            </div>
                            <h5 class="fw-bold">100% Practical Lab</h5>
                            <p class="small text-muted mb-0">High-speed computers with dedicated machine time for each student.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="pillar-card">
                            <div class="pillar-icon-wrap">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <h5 class="fw-bold">Digital Study Notes</h5>
                            <p class="small text-muted mb-0">Chapter-wise PDF handbooks accessible 24/7 on your mobile or computer.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="pillar-card">
                            <div class="pillar-icon-wrap">
                                <i class="bi bi-laptop"></i>
                            </div>
                            <h5 class="fw-bold">Online MCQ Exams</h5>
                            <p class="small text-muted mb-0">Automated examination portal with immediate grading and performance analytics.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="pillar-card">
                            <div class="pillar-icon-wrap">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <h5 class="fw-bold">Recognized Diplomas</h5>
                            <p class="small text-muted mb-0">Nationally accepted course certificates with public online QR verification.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%); color: #fff;">
    <div class="container py-4 text-center">
        <h2 class="text-white fw-bold mb-3">Ready to Upgrade Your Digital Career?</h2>
        <p class="text-light opacity-75 max-w-600 mx-auto mb-4" style="max-width: 600px;">
            Join hundreds of successful students at Micro Group Computer Institute. Register today or take a free mock exam to evaluate your skills.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= BASE_URL ?>/register.php" class="btn btn-gold-mgi btn-lg"><i class="bi bi-person-plus-fill"></i> Register for Admission</a>
            <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-light btn-lg rounded-pill"><i class="bi bi-telephone-fill"></i> Contact Institute</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>