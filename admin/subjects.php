<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Course Subjects";

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)$_POST['course_id'];
    $subject_name = trim($_POST['subject_name']);
    $subject_code = trim($_POST['subject_code']);
    $description = trim($_POST['description']);

    if ($edit_id > 0) {
        $stmt = $pdo->prepare("UPDATE subjects SET course_id = ?, subject_name = ?, subject_code = ?, description = ? WHERE id = ?");
        $stmt->execute([$course_id, $subject_name, $subject_code, $description, $edit_id]);
        set_flash_message('success', 'Subject updated.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO subjects (course_id, subject_name, subject_code, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $subject_name, $subject_code, $description]);
        set_flash_message('success', 'Subject added successfully.');
    }
    header('Location: ' . BASE_URL . '/admin/subjects.php');
    exit;
}

if ($action === 'delete' && $edit_id > 0) {
    $pdo->prepare("DELETE FROM subjects WHERE id = ?")->execute([$edit_id]);
    set_flash_message('success', 'Subject deleted.');
    header('Location: ' . BASE_URL . '/admin/subjects.php');
    exit;
}

$editSubject = null;
if ($action === 'edit' && $edit_id > 0) {
    $st = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $st->execute([$edit_id]);
    $editSubject = $st->fetch();
}

$subjects = $pdo->query("SELECT s.*, c.course_name, c.short_name FROM subjects s JOIN courses c ON s.course_id = c.id ORDER BY c.id ASC, s.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Course Subjects & Modules</h5>
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
                                        <th>Course</th>
                                        <th>Code</th>
                                        <th>Subject Name</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $s): ?>
                                        <tr>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($s['short_name']) ?></span></td>
                                            <td class="font-monospace small"><?= htmlspecialchars($s['subject_code']) ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($s['subject_name']) ?></td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/admin/subjects.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                <a href="<?= BASE_URL ?>/admin/subjects.php?action=delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete subject?')"><i class="bi bi-trash"></i></a>
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
                        <h6 class="fw-bold text-dark mb-3"><?= ($editSubject) ? 'Edit Subject' : 'Add New Subject' ?></h6>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Select Course *</label>
                                <select name="course_id" class="form-select" required>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (($editSubject['course_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Subject Code</label>
                                <input type="text" name="subject_code" class="form-control" placeholder="e.g. ADCA-101" value="<?= htmlspecialchars($editSubject['subject_code'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Subject Name *</label>
                                <input type="text" name="subject_name" class="form-control" placeholder="e.g. MS Office Suite" value="<?= htmlspecialchars($editSubject['subject_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Module topics..."><?= htmlspecialchars($editSubject['description'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi w-100"><?= ($editSubject) ? 'Update Subject' : 'Add Subject' ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>