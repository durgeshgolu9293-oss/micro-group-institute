<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "My Certificate";

$stmt = $pdo->prepare("SELECT * FROM certificates WHERE student_id = ? OR roll_number = ? LIMIT 1");
$stmt->execute([$student['id'], $student['roll_number']]);
$cert = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Certificate - Micro Group Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/student_sidebar.php'; ?>
    <div class="portal-main">
        <header class="portal-topbar">
            <h5 class="mb-0 fw-bold text-dark">Course Completion Certificate</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>
        <main class="portal-content">
            <?php if ($cert && $cert['status'] === 'active'): ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Congratulations, <?= htmlspecialchars($cert['student_name']) ?>!</h3>
                    <p class="text-secondary max-w-600 mx-auto mb-4" style="max-width: 600px;">
                        Your official Course Completion Certificate for <strong><?= htmlspecialchars($cert['course_name']) ?></strong> is active and verified in the Micro Group central registry.
                    </p>

                    <div class="p-3 rounded-3 bg-light border d-inline-block text-start mb-4" style="min-width: 320px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Certificate ID:</span>
                            <strong class="font-monospace text-primary"><?= htmlspecialchars($cert['certificate_number']) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Issue Date:</span>
                            <strong><?= date('d F, Y', strtotime($cert['issue_date'])) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Marks Score:</span>
                            <strong class="text-success"><?= $cert['percentage'] ?>% (Grade: <?= htmlspecialchars($cert['grade']) ?>)</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Verification Status:</span>
                            <span class="badge bg-success">AUTHENTIC</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-gold-mgi btn-lg">
                            <i class="bi bi-printer-fill me-1"></i> View & Print Certificate
                        </a>
                        <a href="<?= BASE_URL ?>/verify-certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-outline-navy btn-lg">
                            <i class="bi bi-shield-check me-1"></i> Public Verification
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white text-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Certificate Not Yet Generated</h4>
                    <p class="text-secondary max-w-600 mx-auto mb-4" style="max-width: 600px;">
                        To receive your official certificate, please pass your course examination with a score of <?= get_setting('default_passing_percentage', '40') ?>% or higher.
                    </p>
                    <div>
                        <a href="<?= BASE_URL ?>/student/exams.php" class="btn btn-cyan-mgi">
                            <i class="bi bi-pencil-square me-1"></i> Take Course Exam Now
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>