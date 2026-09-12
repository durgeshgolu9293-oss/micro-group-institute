<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Notes Management";

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY id ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)$_POST['course_id'];
    $subject_id = (int)$_POST['subject_id'];
    $chapter_name = trim($_POST['chapter_name']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $file_path = 'adca_ch1_fundamentals.pdf';

    // File Upload handling
    if (!empty($_FILES['pdf_file']['name'])) {
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['pdf_file']['name']);
        $target = __DIR__ . '/../assets/uploads/notes/' . $fileName;
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target)) {
            $file_path = $fileName;
        }
    }

    if ($edit_id > 0) {
        $stmt = $pdo->prepare("UPDATE notes SET course_id = ?, subject_id = ?, chapter_name = ?, title = ?, description = ?, file_path = ? WHERE id = ?");
        $stmt->execute([$course_id, $subject_id, $chapter_name, $title, $description, $file_path, $edit_id]);
        set_flash_message('success', 'Notes updated.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO notes (course_id, subject_id, chapter_name, title, description, file_path, file_size) VALUES (?, ?, ?, ?, ?, ?, '2.0 MB')");
        $stmt->execute([$course_id, $subject_id, $chapter_name, $title, $description, $file_path]);
        set_flash_message('success', 'Notes handout added.');
    }
    header('Location: ' . BASE_URL . '/admin/notes.php');
    exit;
}

if ($action === 'delete' && $edit_id > 0) {
    $pdo->prepare("DELETE FROM notes WHERE id = ?")->execute([$edit_id]);
    set_flash_message('success', 'Notes deleted.');
    header('Location: ' . BASE_URL . '/admin/notes.php');
    exit;
}

$notes = $pdo->query("SELECT n.*, c.short_name, s.subject_name FROM notes n JOIN courses c ON n.course_id = c.id JOIN subjects s ON n.subject_id = s.id ORDER BY n.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notes - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Study Material & Notes (PDF)</h5>
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
                                        <th>Subject & Chapter</th>
                                        <th>Title</th>
                                        <th>PDF</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notes as $n): ?>
                                        <tr>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($n['short_name']) ?></span></td>
                                            <td>
                                                <div class="small fw-bold text-dark"><?= htmlspecialchars($n['subject_name']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($n['chapter_name']) ?></div>
                                            </td>
                                            <td class="fw-semibold"><?= htmlspecialchars($n['title']) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($n['file_path']) ?>" download class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i></a>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/admin/notes.php?action=delete&id=<?= $n['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete notes?')"><i class="bi bi-trash"></i></a>
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
                        <h6 class="fw-bold text-dark mb-3">Upload Study Handout</h6>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Course *</label>
                                <select name="course_id" class="form-select" required>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Subject *</label>
                                <select name="subject_id" class="form-select" required>
                                    <?php foreach ($subjects as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Chapter Name *</label>
                                <input type="text" name="chapter_name" class="form-control" placeholder="e.g. Chapter 1: Fundamentals" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Handout Title *</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Complete Excel Guide" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">PDF Document File</label>
                                <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="description" rows="2" class="form-control" placeholder="Key topics covered..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi w-100">Upload Notes</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>