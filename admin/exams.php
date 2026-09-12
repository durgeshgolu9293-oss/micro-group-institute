<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Online Exams Management";

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)$_POST['course_id'];
    $exam_title = trim($_POST['exam_title']);
    $description = trim($_POST['description']);
    $duration_minutes = (int)$_POST['duration_minutes'];
    $total_questions = (int)$_POST['total_questions'];
    $max_marks = (int)$_POST['max_marks'];
    $passing_percentage = (int)$_POST['passing_percentage'];
    $status = $_POST['status'] ?? 'active';

    if ($edit_id > 0) {
        $stmt = $pdo->prepare("UPDATE exams SET course_id = ?, exam_title = ?, description = ?, duration_minutes = ?, total_questions = ?, max_marks = ?, passing_percentage = ?, status = ? WHERE id = ?");
        $stmt->execute([$course_id, $exam_title, $description, $duration_minutes, $total_questions, $max_marks, $passing_percentage, $status, $edit_id]);
        set_flash_message('success', 'Exam updated successfully.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO exams (course_id, exam_title, description, duration_minutes, total_questions, max_marks, passing_percentage, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $exam_title, $description, $duration_minutes, $total_questions, $max_marks, $passing_percentage, $status]);
        set_flash_message('success', 'New exam created successfully.');
    }
    header('Location: ' . BASE_URL . '/admin/exams.php');
    exit;
}

if ($action === 'delete' && $edit_id > 0) {
    $pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([$edit_id]);
    set_flash_message('success', 'Exam deleted.');
    header('Location: ' . BASE_URL . '/admin/exams.php');
    exit;
}

$editExam = null;
if ($action === 'edit' && $edit_id > 0) {
    $st = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $st->execute([$edit_id]);
    $editExam = $st->fetch();
}

$exams = $pdo->query("SELECT e.*, c.course_name, c.short_name FROM exams e JOIN courses c ON e.course_id = c.id ORDER BY e.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Online Exams & Assessment Config</h5>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="data-card">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Exam Title</th>
                                        <th>Course</th>
                                        <th>Duration</th>
                                        <th>Qs / Marks</th>
                                        <th>Pass %</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exams as $e): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($e['exam_title']) ?></td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($e['short_name']) ?></span></td>
                                            <td><?= $e['duration_minutes'] ?> Mins</td>
                                            <td><?= $e['total_questions'] ?> Qs / <?= $e['max_marks'] ?> M</td>
                                            <td class="fw-bold text-success"><?= $e['passing_percentage'] ?>%</td>
                                            <td><span class="badge <?= ($e['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>"><?= $e['status'] ?></span></td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/admin/questions.php?exam_id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-info" title="Manage MCQs"><i class="bi bi-question-circle"></i></a>
                                                <a href="<?= BASE_URL ?>/admin/exams.php?action=edit&id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                <a href="<?= BASE_URL ?>/admin/exams.php?action=delete&id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete exam?')"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                        <h6 class="fw-bold text-dark mb-3"><?= ($editExam) ? 'Edit Exam' : 'Create New Exam' ?></h6>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Course *</label>
                                <select name="course_id" class="form-select" required>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (($editExam['course_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Exam Title *</label>
                                <input type="text" name="exam_title" class="form-control" placeholder="ADCA Semester Final Exam" value="<?= htmlspecialchars($editExam['exam_title'] ?? '') ?>" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Duration (Mins) *</label>
                                    <input type="number" name="duration_minutes" class="form-control" value="<?= htmlspecialchars($editExam['duration_minutes'] ?? '15') ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Total Qs *</label>
                                    <input type="number" name="total_questions" class="form-control" value="<?= htmlspecialchars($editExam['total_questions'] ?? '10') ?>" required>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Max Marks *</label>
                                    <input type="number" name="max_marks" class="form-control" value="<?= htmlspecialchars($editExam['max_marks'] ?? '50') ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Passing % *</label>
                                    <input type="number" name="passing_percentage" class="form-control" value="<?= htmlspecialchars($editExam['passing_percentage'] ?? '40') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= (($editExam['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= (($editExam['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="description" rows="2" class="form-control"><?= htmlspecialchars($editExam['description'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi w-100"><?= ($editExam) ? 'Update Exam' : 'Create Exam' ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>