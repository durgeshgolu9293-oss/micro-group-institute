<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "Online Examinations";

$exams = $pdo->prepare("SELECT * FROM exams WHERE course_id = ? AND status = 'active'");
$exams->execute([$student['course_id']]);
$examList = $exams->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams - Micro Group Student Portal</title>
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
            <h5 class="mb-0 fw-bold text-dark">Available Online Examinations</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>
        <main class="portal-content">
            <div class="row g-4">
                <?php foreach ($examList as $e): ?>
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold">Active Assessment</span>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= $e['duration_minutes'] ?> Mins</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($e['exam_title']) ?></h5>
                                <p class="small text-secondary mb-4"><?= htmlspecialchars($e['description'] ?? 'Official examination covering course syllabus.') ?></p>
                                
                                <div class="row g-2 text-center small bg-light p-3 rounded-3 mb-4">
                                    <div class="col-4">
                                        <span class="text-muted d-block">Questions</span>
                                        <strong><?= $e['total_questions'] ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted d-block">Max Marks</span>
                                        <strong><?= $e['max_marks'] ?></strong>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted d-block">Passing %</span>
                                        <strong class="text-success"><?= $e['passing_percentage'] ?>%</strong>
                                    </div>
                                </div>
                            </div>

                            <a href="<?= BASE_URL ?>/exam-login.php?course_id=<?= $student['course_id'] ?>&exam_id=<?= $e['id'] ?>" class="btn btn-gold-mgi w-100">
                                <i class="bi bi-pencil-square me-1"></i> Start This Examination
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>