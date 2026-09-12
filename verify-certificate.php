<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Verify Student Certificate - Official Verification Portal";

$cert_no = trim($_GET['cert_no'] ?? '');
$certificate = null;
$searched = false;

if (!empty($cert_no)) {
    $searched = true;
    $stmt = $pdo->prepare("SELECT c.*, s.name as student_name, s.father_name, s.roll_number, s.photo,
                                  co.course_name, co.short_name as course_code, co.duration
                           FROM certificates c
                           JOIN students s ON c.student_id = s.id
                           JOIN courses co ON c.course_id = co.id
                           WHERE c.certificate_number = ? LIMIT 1");
    $stmt->execute([$cert_no]);
    $certificate = $stmt->fetch();
}

$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white text-center" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-3">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">Official Verification Portal</span>
        <h1 class="display-6 fw-bold text-white mb-2">Student Certificate Verification</h1>
        <p class="text-light opacity-75 mx-auto" style="max-width: 650px;">
            Verify the authenticity of Course Completion Certificates issued by <strong><?= htmlspecialchars($site_name) ?></strong> (<?= htmlspecialchars($location) ?>).
        </p>
    </div>
</div>

<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <h5 class="fw-bold text-dark text-center mb-3">Enter Certificate Serial Number</h5>
                    <form method="GET" action="" class="mb-2">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-warning"><i class="bi bi-patch-check-fill fs-4"></i></span>
                            <input type="text" name="cert_no" class="form-control border-start-0 text-uppercase fw-bold" 
                                   placeholder="e.g. MGI-2026-00001" 
                                   value="<?= htmlspecialchars($cert_no) ?>" required autofocus>
                            <button class="btn btn-primary-mgi px-4 fw-bold" type="submit">
                                <i class="bi bi-search me-1"></i> Verify Now
                            </button>
                        </div>
                    </form>
                    <div class="text-center text-muted small mt-2">
                        Example demo certificate ID: <a href="?cert_no=MGI-2026-00001" class="text-decoration-none fw-bold text-primary">MGI-2026-00001</a>
                    </div>
                </div>

                <!-- Verification Result Display -->
                <?php if ($searched): ?>
                    <?php if ($certificate && $certificate['status'] === 'active'): ?>
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white animate__animated animate__fadeInUp">
                            <div class="p-4 bg-success text-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-white text-success p-2 fs-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-white">Certificate Verified & Valid</h5>
                                        <small class="text-white-50">Official Record Found in Institute Registry</small>
                                    </div>
                                </div>
                                <span class="badge bg-white text-success fw-bold px-3 py-2 fs-6">AUTHENTIC</span>
                            </div>

                            <div class="p-4 p-md-5">
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="text-secondary small fw-semibold text-uppercase">Student Full Name</div>
                                        <h5 class="fw-bold text-dark mt-1 mb-0"><?= htmlspecialchars($certificate['student_name']) ?></h5>
                                        <?php if (!empty($certificate['father_name'])): ?>
                                            <div class="text-muted small">S/O: <?= htmlspecialchars($certificate['father_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-secondary small fw-semibold text-uppercase">Roll Number</div>
                                        <h5 class="fw-bold text-primary mt-1 mb-0"><?= htmlspecialchars($certificate['roll_number']) ?></h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-secondary small fw-semibold text-uppercase">Course Completed</div>
                                        <h5 class="fw-bold text-dark mt-1 mb-0"><?= htmlspecialchars($certificate['course_name']) ?> (<?= htmlspecialchars($certificate['course_code']) ?>)</h5>
                                        <div class="text-muted small">Duration: <?= htmlspecialchars($certificate['duration'] ?? '12 Months') ?></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-secondary small fw-semibold text-uppercase">Certificate Serial No.</div>
                                        <h5 class="fw-bold text-dark mt-1 mb-0"><?= htmlspecialchars($certificate['certificate_number']) ?></h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-secondary small fw-semibold text-uppercase">Issue Date</div>
                                        <h6 class="fw-bold text-dark mt-1 mb-0"><?= date('d M, Y', strtotime($certificate['issue_date'])) ?></h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-secondary small fw-semibold text-uppercase">Grade Awarded</div>
                                        <h6 class="fw-bold text-success mt-1 mb-0"><?= htmlspecialchars($certificate['grade'] ?? 'A') ?></h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-secondary small fw-semibold text-uppercase">Score Percentage</div>
                                        <h6 class="fw-bold text-dark mt-1 mb-0"><?= number_format($certificate['percentage'] ?? 85, 1) ?>%</h6>
                                    </div>
                                </div>

                                <div class="p-3 rounded-3 bg-light border mt-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <small class="text-muted d-block">Issuing Authority:</small>
                                        <strong class="text-dark"><?= htmlspecialchars($site_name) ?> (<?= htmlspecialchars($location) ?>)</strong> &bull; Manager: <strong><?= htmlspecialchars($manager_name) ?></strong>
                                    </div>
                                    <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($certificate['certificate_number']) ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3">
                                        <i class="bi bi-printer me-1"></i> View / Print Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($certificate && $certificate['status'] === 'revoked'): ?>
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white p-4 p-md-5 text-center animate__animated animate__shakeX">
                            <div class="text-danger fs-1 mb-2"><i class="bi bi-x-circle-fill"></i></div>
                            <h4 class="fw-bold text-danger">Certificate Revoked / Suspended</h4>
                            <p class="text-muted mb-0">Certificate <strong><?= htmlspecialchars($cert_no) ?></strong> has been revoked or cancelled by institute administration.</p>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white p-4 p-md-5 text-center animate__animated animate__fadeIn">
                            <div class="text-warning fs-1 mb-2"><i class="bi bi-exclamation-triangle-fill"></i></div>
                            <h4 class="fw-bold text-dark">Certificate Record Not Found</h4>
                            <p class="text-muted mb-0">No official certificate record found for <strong><?= htmlspecialchars($cert_no) ?></strong>. Please double-check the certificate number.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>