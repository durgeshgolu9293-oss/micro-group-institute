<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "Student Dashboard";

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_attempts, 
                              SUM(CASE WHEN r.pass_status = 'PASS' THEN 1 ELSE 0 END) as passed_count,
                              SUM(CASE WHEN r.pass_status = 'FAIL' THEN 1 ELSE 0 END) as failed_count,
                              AVG(r.percentage) as avg_score
                       FROM results r 
                       WHERE r.student_id = ? OR r.roll_number = ?");
$stmt->execute([$student['id'], $student['roll_number']]);
$stats = $stmt->fetch();

$totalAttempts = (int)($stats['total_attempts'] ?? 0);
$passedCount = (int)($stats['passed_count'] ?? 0);
$failedCount = (int)($stats['failed_count'] ?? 0);
$avgScore = round((float)($stats['avg_score'] ?? 0), 1);

// Certificate check
$certStmt = $pdo->prepare("SELECT * FROM certificates WHERE student_id = ? OR roll_number = ? LIMIT 1");
$certStmt->execute([$student['id'], $student['roll_number']]);
$cert = $certStmt->fetch();

// Recent Results
$resStmt = $pdo->prepare("SELECT r.*, e.exam_title FROM results r JOIN exams e ON r.exam_id = e.id WHERE r.student_id = ? OR r.roll_number = ? ORDER BY r.id DESC LIMIT 5");
$resStmt->execute([$student['id'], $student['roll_number']]);
$recentResults = $resStmt->fetchAll();

// Available Exams for student's course
$examStmt = $pdo->prepare("SELECT * FROM exams WHERE course_id = ? AND status = 'active'");
$examStmt->execute([$student['course_id']]);
$availableExams = $examStmt->fetchAll();

// Course Details
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$courseStmt->execute([$student['course_id']]);
$course = $courseStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Micro Group Computer Institute</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../includes/student_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="portal-main">
        <header class="portal-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" type="button" onclick="document.querySelector('.portal-sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">Welcome, <?= htmlspecialchars($student['name']) ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
                <a href="<?= BASE_URL ?>/student/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </header>

        <main class="portal-content">
            <?php display_flash_message(); ?>

            <!-- Student Profile Card Banner -->
            <div class="card border-0 rounded-4 shadow-sm p-4 text-white mb-4" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%);">
                <div class="row align-items-center g-4">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-circle bg-white bg-opacity-15 p-3 fs-2 text-info">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-white"><?= htmlspecialchars($student['name']) ?></h4>
                                <span class="text-info small">Roll: <strong><?= htmlspecialchars($student['roll_number']) ?></strong> | Enrolled Course: <strong><?= htmlspecialchars($student['course_name'] ?? 'Computer Course') ?></strong></span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-4 text-light small mt-3">
                            <div><i class="bi bi-envelope text-warning me-1"></i><?= htmlspecialchars($student['email']) ?></div>
                            <div><i class="bi bi-telephone text-success me-1"></i><?= htmlspecialchars($student['mobile']) ?></div>
                            <div><i class="bi bi-calendar-check text-info me-1"></i>Admission: <?= date('d M, Y', strtotime($student['admission_date'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <?php if ($cert && $cert['status'] === 'active'): ?>
                            <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-warning fw-bold px-3 py-2 shadow">
                                <i class="bi bi-award-fill me-1"></i> View Certificate
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/student/exams.php" class="btn btn-info text-white fw-bold px-3 py-2 shadow">
                                <i class="bi bi-pencil-square me-1"></i> Start Course Exam
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val"><?= $totalAttempts ?></div>
                            <div class="kpi-label">Tests Attempted</div>
                        </div>
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-success"><?= $passedCount ?></div>
                            <div class="kpi-label">Tests Passed</div>
                        </div>
                        <div class="kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val text-info"><?= $avgScore ?>%</div>
                            <div class="kpi-label">Average Score</div>
                        </div>
                        <div class="kpi-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="kpi-card">
                        <div>
                            <div class="kpi-val <?= ($cert && $cert['status'] === 'active') ? 'text-warning' : 'text-secondary' ?>" style="font-size: 1.25rem;">
                                <?= ($cert && $cert['status'] === 'active') ? 'Issued' : 'Pending Test' ?>
                            </div>
                            <div class="kpi-label">Certificate Status</div>
                        </div>
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning & Performance Grid -->
            <div class="row g-4 mb-4">
                <!-- Available Exams -->
                <div class="col-lg-7">
                    <div class="data-card h-100">
                        <div class="data-card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Eligible Online Examinations</h6>
                            <a href="<?= BASE_URL ?>/student/exams.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="p-4">
                            <?php if (empty($availableExams)): ?>
                                <p class="text-muted mb-0">No active exams scheduled for your enrolled course currently.</p>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($availableExams as $e): ?>
                                        <div class="p-3 rounded-3 border bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($e['exam_title']) ?></h6>
                                                <div class="small text-secondary">
                                                    Duration: <strong><?= $e['duration_minutes'] ?> Mins</strong> &bull; Total Qs: <strong><?= $e['total_questions'] ?></strong> &bull; Pass: <strong><?= $e['passing_percentage'] ?>%</strong>
                                                </div>
                                            </div>
                                            <a href="<?= BASE_URL ?>/exam-login.php?course_id=<?= $student['course_id'] ?>&exam_id=<?= $e['id'] ?>" class="btn btn-sm btn-cyan-mgi">
                                                <i class="bi bi-play-circle"></i> Start Exam
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="col-lg-5">
                    <div class="data-card h-100">
                        <div class="data-card-header">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill text-info me-2"></i>Test Performance Breakdown</h6>
                        </div>
                        <div class="p-4 d-flex flex-column align-items-center justify-content-center">
                            <div style="width: 220px; height: 220px;">
                                <canvas id="performanceChart"></canvas>
                            </div>
                            <div class="d-flex gap-4 mt-3 small">
                                <div><span class="badge bg-success rounded-circle p-1 me-1"> </span> Passed: <strong><?= $passedCount ?></strong></div>
                                <div><span class="badge bg-danger rounded-circle p-1 me-1"> </span> Failed: <strong><?= $failedCount ?></strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Results Table -->
            <div class="data-card">
                <div class="data-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>My Recent Test Attempts</h6>
                    <a href="<?= BASE_URL ?>/student/results.php" class="btn btn-sm btn-outline-secondary">Complete Result History</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Exam Title</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentResults)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No completed tests yet. Start an exam to test your skills!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentResults as $r): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($r['exam_title']) ?></td>
                                        <td><?= date('d M, Y', strtotime($r['date'])) ?></td>
                                        <td><?= $r['obtained_marks'] ?> / <?= $r['total_marks'] ?></td>
                                        <td class="fw-bold text-primary"><?= $r['percentage'] ?>%</td>
                                        <td><span class="badge bg-secondary bg-opacity-25 text-dark"><?= $r['grade'] ?></span></td>
                                        <td>
                                            <span class="badge <?= ($r['pass_status'] === 'PASS') ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $r['pass_status'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/exam-result.php?attempt_id=<?= $r['attempt_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-text"></i> Scorecard
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const passed = <?= $passedCount ?>;
    const failed = <?= $failedCount ?>;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed'],
            datasets: [{
                data: [passed || (failed ? 0 : 1), failed],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '75%'
        }
    });
});
</script>

</body>
</html>