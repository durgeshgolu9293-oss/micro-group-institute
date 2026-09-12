<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();

$page_title = "Admin Dashboard";

// Statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalSubjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$totalNotes = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
$totalExams = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$totalQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$totalResults = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();
$totalCertificates = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'active'")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();

// Recent Exam Attempts
$recentAttempts = $pdo->query("SELECT a.*, e.exam_title, r.pass_status FROM exam_attempts a JOIN exams e ON a.exam_id = e.id LEFT JOIN results r ON a.id = r.attempt_id ORDER BY a.id DESC LIMIT 6")->fetchAll();

// Recent Certificates
$recentCerts = $pdo->query("SELECT * FROM certificates ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Micro Group Control Center</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <div class="portal-main">
        <header class="portal-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" type="button" onclick="document.querySelector('.portal-sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">Micro Group Admin Overview</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>/admin/certificates.php" class="btn btn-warning btn-sm fw-bold">
                    <i class="bi bi-patch-check-fill me-1"></i> Issue Certificate
                </a>
                <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </header>

        <main class="portal-content">
            <?php display_flash_message(); ?>

            <!-- 8 KPI Metrics Grid -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-primary"><?= $totalStudents ?></div>
                            <div class="kpi-label">Total Students</div>
                        </div>
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-success"><?= $totalCourses ?></div>
                            <div class="kpi-label">Active Courses</div>
                        </div>
                        <div class="kpi-icon bg-success bg-opacity-10 text-success"><i class="bi bi-journal-bookmark-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-info"><?= $totalSubjects ?></div>
                            <div class="kpi-label">Course Subjects</div>
                        </div>
                        <div class="kpi-icon bg-info bg-opacity-10 text-info"><i class="bi bi-diagram-3-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-danger"><?= $totalNotes ?></div>
                            <div class="kpi-label">Study Notes (PDF)</div>
                        </div>
                        <div class="kpi-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-warning"><?= $totalExams ?></div>
                            <div class="kpi-label">Active Exams</div>
                        </div>
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-pencil-square"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-dark"><?= $totalQuestions ?></div>
                            <div class="kpi-label">MCQ Questions Bank</div>
                        </div>
                        <div class="kpi-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-question-circle-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-success"><?= $totalResults ?></div>
                            <div class="kpi-label">Evaluated Results</div>
                        </div>
                        <div class="kpi-icon bg-success bg-opacity-10 text-success"><i class="bi bi-trophy-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-warning"><?= $totalCertificates ?></div>
                            <div class="kpi-label">Certificates Issued</div>
                        </div>
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-patch-check-fill"></i></div>
                    </div>
                </div>
            </div>

            <!-- Recent Exam Submissions & Issued Certificates -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="data-card h-100">
                        <div class="data-card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Recent Exam Submissions</h6>
                            <a href="<?= BASE_URL ?>/admin/results.php" class="btn btn-sm btn-outline-primary">View All Results</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Exam</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentAttempts)): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-3">No exam submissions yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentAttempts as $att): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($att['student_name']) ?></strong>
                                                    <div class="small text-muted"><?= htmlspecialchars($att['roll_number']) ?></div>
                                                </td>
                                                <td class="small"><?= htmlspecialchars($att['exam_title']) ?></td>
                                                <td>
                                                    <strong><?= $att['obtained_marks'] ?></strong> / <?= $att['total_marks'] ?>
                                                    <span class="small text-muted">(<?= $att['percentage'] ?>%)</span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= ($att['pass_status'] === 'PASS') ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $att['pass_status'] ?? $att['status'] ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= BASE_URL ?>/exam-result.php?attempt_id=<?= $att['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="data-card h-100">
                        <div class="data-card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-award-fill text-warning me-2"></i>Recently Issued Certificates</h6>
                            <a href="<?= BASE_URL ?>/admin/certificates.php" class="btn btn-sm btn-outline-warning text-dark">Manage All</a>
                        </div>
                        <div class="p-3">
                            <?php if (empty($recentCerts)): ?>
                                <p class="text-muted text-center py-4 mb-0">No certificates issued yet.</p>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($recentCerts as $c): ?>
                                        <div class="p-3 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="text-primary font-monospace"><?= htmlspecialchars($c['certificate_number']) ?></strong>
                                                <div class="small fw-bold text-dark"><?= htmlspecialchars($c['student_name']) ?> (<?= htmlspecialchars($c['roll_number']) ?>)</div>
                                                <div class="small text-muted"><?= htmlspecialchars($c['course_name']) ?> &bull; <?= $c['percentage'] ?>% (Grade: <?= htmlspecialchars($c['grade']) ?>)</div>
                                            </div>
                                            <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($c['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-gold-mgi">
                                                <i class="bi bi-printer"></i> Print
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>