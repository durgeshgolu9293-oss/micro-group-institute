<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Results Management";

$results = $pdo->query("SELECT r.*, a.student_name, a.course_name, a.total_questions, a.attempted, a.correct_answers, a.wrong_answers, e.exam_title, c.certificate_number 
                       FROM results r 
                       JOIN exam_attempts a ON r.attempt_id = a.id 
                       JOIN exams e ON r.exam_id = e.id 
                       LEFT JOIN certificates c ON r.id = c.result_id 
                       ORDER BY r.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Student Results & Scorecards</h5>
        </header>
        <main class="portal-content">
            <div class="data-card">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll No</th>
                                <th>Course</th>
                                <th>Exam</th>
                                <th>Marks</th>
                                <th>%</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Scorecard</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($r['student_name']) ?></td>
                                    <td class="font-monospace text-primary"><?= htmlspecialchars($r['roll_number']) ?></td>
                                    <td><?= htmlspecialchars($r['course_name']) ?></td>
                                    <td class="small"><?= htmlspecialchars($r['exam_title']) ?></td>
                                    <td><?= $r['obtained_marks'] ?> / <?= $r['total_marks'] ?></td>
                                    <td class="fw-bold text-primary"><?= $r['percentage'] ?>%</td>
                                    <td><span class="badge bg-secondary bg-opacity-25 text-dark"><?= $r['grade'] ?></span></td>
                                    <td><span class="badge <?= ($r['pass_status'] === 'PASS') ? 'bg-success' : 'bg-danger' ?>"><?= $r['pass_status'] ?></span></td>
                                    <td><?= date('d M, Y', strtotime($r['date'])) ?></td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/exam-result.php?attempt_id=<?= $r['attempt_id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>