<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "My Test Results";

$stmt = $pdo->prepare("SELECT r.*, e.exam_title, c.certificate_number 
                       FROM results r 
                       JOIN exams e ON r.exam_id = e.id 
                       LEFT JOIN certificates c ON r.id = c.result_id 
                       WHERE r.student_id = ? OR r.roll_number = ? 
                       ORDER BY r.id DESC");
$stmt->execute([$student['id'], $student['roll_number']]);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results - Micro Group Student Portal</title>
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
            <h5 class="mb-0 fw-bold text-dark">My Examination Results & Scorecards</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>
        <main class="portal-content">
            <div class="data-card">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Date</th>
                                <th>Marks Obtained</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Pass / Fail</th>
                                <th>Certificate</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($results)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No test results found. Take an online exam to see your scorecard here.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($results as $r): ?>
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
                                        <td>
                                            <?php if (!empty($r['certificate_number'])): ?>
                                                <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($r['certificate_number']) ?>" target="_blank" class="badge bg-warning text-dark text-decoration-none">
                                                    <i class="bi bi-award-fill me-1"></i><?= htmlspecialchars($r['certificate_number']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/exam-result.php?attempt_id=<?= $r['attempt_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-text"></i> View Scorecard
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
</body>
</html>