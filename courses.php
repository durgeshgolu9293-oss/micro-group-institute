<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Courses & Fee Structure - All Diploma Programs";

$courses = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white text-center" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-3">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">Academic Curriculum</span>
        <h1 class="display-6 fw-bold text-white mb-2">Our Computer Diploma Courses</h1>
        <p class="text-light opacity-75 mx-auto" style="max-width: 650px;">
            Recognized computer training programs with 100% practical lab sessions, digital notes, and government/private job eligibility.
        </p>
    </div>
</div>

<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <?php foreach ($courses as $course): ?>
                <?php
                $courseFee = (float)$course['fee'];
                $admFee = (float)($course['admission_fee'] ?? 0);
                $discount = (float)($course['discount'] ?? 0);
                $finalFee = (float)$course['final_fee'];

                // Fetch subjects count
                $subStmt = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE course_id = ?");
                $subStmt->execute([$course['id']]);
                $subjectCount = $subStmt->fetchColumn();
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white hover-elevate d-flex flex-column justify-content-between p-4">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary px-3 py-2 fw-bold fs-6"><?= htmlspecialchars($course['short_name']) ?></span>
                                <span class="badge bg-light text-dark border px-2 py-1 small"><i class="bi bi-clock me-1 text-primary"></i><?= htmlspecialchars($course['duration']) ?></span>
                            </div>

                            <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($course['course_name']) ?></h4>
                            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                                <?= htmlspecialchars($course['description']) ?>
                            </p>

                            <div class="p-3 rounded-3 bg-light mb-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Eligibility:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($course['eligibility'] ?? '10th / 12th Pass') ?></strong>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Curriculum:</span>
                                    <strong class="text-dark"><?= $subjectCount ?> Core Subjects + Practical Labs</strong>
                                </div>
                            </div>
                        </div>

                        <div>
                            <!-- Dynamic Fee Box -->
                            <div class="p-3 rounded-3 mb-3 border" style="background: #F8FAFC;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Course Fee:</span>
                                    <span class="small text-muted"><?= format_currency($courseFee) ?></span>
                                </div>
                                <?php if ($admFee > 0): ?>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Admission Fee:</span>
                                    <span class="small text-muted">+ <?= format_currency($admFee) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($discount > 0): ?>
                                <div class="d-flex justify-content-between align-items-center mb-1 text-success">
                                    <span class="small fw-semibold">Special Discount:</span>
                                    <span class="small fw-bold">- <?= format_currency($discount) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-1">
                                    <strong class="text-dark">Total Fee:</strong>
                                    <h4 class="fw-bold text-primary mb-0"><?= format_currency($finalFee) ?></h4>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>/course-details.php?id=<?= $course['id'] ?>" class="btn btn-outline-primary flex-grow-1">
                                    <i class="bi bi-info-circle me-1"></i> Details
                                </a>
                                <a href="<?= BASE_URL ?>/register.php?course_id=<?= $course['id'] ?>" class="btn btn-primary-mgi flex-grow-1">
                                    <i class="bi bi-person-plus me-1"></i> Enroll Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>