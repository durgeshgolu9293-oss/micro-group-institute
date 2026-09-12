<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "MCQ Question Bank";

$exams = $pdo->query("SELECT * FROM exams ORDER BY id ASC")->fetchAll();
$filter_exam_id = (int)($_GET['exam_id'] ?? 0);
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = (int)$_POST['exam_id'];
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = $_POST['correct_option'];
    $marks = (int)$_POST['marks'];

    if ($edit_id > 0) {
        $stmt = $pdo->prepare("UPDATE questions SET exam_id = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, marks = ? WHERE id = ?");
        $stmt->execute([$exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks, $edit_id]);
        set_flash_message('success', 'Question updated.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks]);
        set_flash_message('success', 'MCQ question added to bank.');
    }
    header('Location: ' . BASE_URL . '/admin/questions.php?exam_id=' . $exam_id);
    exit;
}

if ($action === 'delete' && $edit_id > 0) {
    $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$edit_id]);
    set_flash_message('success', 'Question deleted.');
    header('Location: ' . BASE_URL . '/admin/questions.php' . ($filter_exam_id ? "?exam_id=$filter_exam_id" : ''));
    exit;
}

$editQ = null;
if ($action === 'edit' && $edit_id > 0) {
    $st = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $st->execute([$edit_id]);
    $editQ = $st->fetch();
}

$qQuery = "SELECT q.*, e.exam_title FROM questions q JOIN exams e ON q.exam_id = e.id";
$qParams = [];
if ($filter_exam_id > 0) {
    $qQuery .= " WHERE q.exam_id = ?";
    $qParams[] = $filter_exam_id;
}
$qQuery .= " ORDER BY q.exam_id ASC, q.id ASC";
$stmt = $pdo->prepare($qQuery);
$stmt->execute($qParams);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage MCQs - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">MCQ Question Bank Management</h5>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="data-card mb-4">
                        <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                            <form method="GET" action="" class="d-flex gap-2 align-items-center">
                                <select name="exam_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="0">-- All Exams --</option>
                                    <?php foreach ($exams as $e): ?>
                                        <option value="<?= $e['id'] ?>" <?= ($filter_exam_id == $e['id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['exam_title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <span class="badge bg-secondary small"><?= count($questions) ?> Questions</span>
                        </div>
                        <div class="p-3">
                            <?php foreach ($questions as $idx => $q): ?>
                                <div class="p-3 rounded-3 border bg-white mb-3 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Q<?= ($idx + 1) ?> &bull; <?= htmlspecialchars($q['exam_title']) ?></span>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">Correct: Option <?= $q['correct_option'] ?></span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2"><?= htmlspecialchars($q['question_text']) ?></h6>
                                    <div class="row g-2 small text-secondary mb-3">
                                        <div class="col-6">A: <?= htmlspecialchars($q['option_a']) ?></div>
                                        <div class="col-6">B: <?= htmlspecialchars($q['option_b']) ?></div>
                                        <div class="col-6">C: <?= htmlspecialchars($q['option_c']) ?></div>
                                        <div class="col-6">D: <?= htmlspecialchars($q['option_d']) ?></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="small text-muted">Marks: <?= $q['marks'] ?></span>
                                        <div>
                                            <a href="<?= BASE_URL ?>/admin/questions.php?action=edit&id=<?= $q['id'] ?>&exam_id=<?= $q['exam_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                                            <a href="<?= BASE_URL ?>/admin/questions.php?action=delete&id=<?= $q['id'] ?>&exam_id=<?= $q['exam_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete question?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white sticky-top" style="top: 90px;">
                        <h6 class="fw-bold text-dark mb-3"><?= ($editQ) ? 'Edit MCQ Question' : 'Add MCQ Question' ?></h6>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Assign to Exam *</label>
                                <select name="exam_id" class="form-select" required>
                                    <?php foreach ($exams as $e): ?>
                                        <option value="<?= $e['id'] ?>" <?= (($editQ['exam_id'] ?? $filter_exam_id) == $e['id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['exam_title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Question Text *</label>
                                <textarea name="question_text" rows="3" class="form-control" placeholder="Enter question here..." required><?= htmlspecialchars($editQ['question_text'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Option A *</label>
                                <input type="text" name="option_a" class="form-control" value="<?= htmlspecialchars($editQ['option_a'] ?? '') ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Option B *</label>
                                <input type="text" name="option_b" class="form-control" value="<?= htmlspecialchars($editQ['option_b'] ?? '') ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Option C *</label>
                                <input type="text" name="option_c" class="form-control" value="<?= htmlspecialchars($editQ['option_c'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Option D *</label>
                                <input type="text" name="option_d" class="form-control" value="<?= htmlspecialchars($editQ['option_d'] ?? '') ?>" required>
                            </div>
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Correct Option *</label>
                                    <select name="correct_option" class="form-select" required>
                                        <option value="A" <?= (($editQ['correct_option'] ?? '') === 'A') ? 'selected' : '' ?>>Option A</option>
                                        <option value="B" <?= (($editQ['correct_option'] ?? '') === 'B') ? 'selected' : '' ?>>Option B</option>
                                        <option value="C" <?= (($editQ['correct_option'] ?? '') === 'C') ? 'selected' : '' ?>>Option C</option>
                                        <option value="D" <?= (($editQ['correct_option'] ?? '') === 'D') ? 'selected' : '' ?>>Option D</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Marks *</label>
                                    <input type="number" name="marks" class="form-control" value="<?= htmlspecialchars($editQ['marks'] ?? '5') ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi w-100"><?= ($editQ) ? 'Update Question' : 'Save Question' ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>