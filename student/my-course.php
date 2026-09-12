<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "My Course & Syllabus";

$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$student['course_id']]);
$course = $cStmt->fetch();

$subStmt = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? ORDER BY id ASC");
$subStmt->execute([$student['course_id']]);
$subjects = $subStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Course - Micro Group Student Portal</title>
    
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
            <h5 class="mb-0 fw-bold text-dark">My Enrolled Course</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>

        <main class="portal-content">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 mb-2"><?= htmlspecialchars($course['short_name']) ?></span>
                        <h3 class="fw-bold text-dark mb-2"><?= htmlspecialchars($course['course_name']) ?></h3>
                        <p class="text-secondary mb-0"><?= htmlspecialchars($course['description']) ?></p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small d-block">Duration</span>
                            <strong class="text-dark"><?= htmlspecialchars($course['duration']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small d-block">Eligibility</span>
                            <strong class="text-dark"><?= htmlspecialchars($course['eligibility']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small d-block">Course Fee</span>
                            <strong class="text-success"><?= format_currency($course['final_fee']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small d-block">Certificate</span>
                            <strong class="text-primary"><?= $course['certificate_available'] ? 'Included' : 'N/A' ?></strong>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Curriculum Modules & Subjects</h5>
                <div class="row g-3">
                    <?php foreach ($subjects as $idx => $s): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($s['subject_code'] ?? ('Module ' . ($idx+1))) ?></span>
                                    <a href="<?= BASE_URL ?>/student/notes.php" class="small text-primary text-decoration-none"><i class="bi bi-file-earmark-pdf"></i> View Notes</a>
                                </div>
                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($s['subject_name']) ?></h6>
                                <p class="small text-secondary mb-0"><?= htmlspecialchars($s['description'] ?? 'Practical laboratory sessions and theoretical foundational training.') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>