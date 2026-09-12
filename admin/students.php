<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Student Directory";

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $course_id = (int)$_POST['course_id'];
    $roll_number = trim($_POST['roll_number']);
    $password = $_POST['password'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if ($edit_id > 0) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course_id = ?, roll_number = ?, password = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $email, $mobile, $course_id, $roll_number, $hash, $status, $edit_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course_id = ?, roll_number = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $email, $mobile, $course_id, $roll_number, $status, $edit_id]);
        }
        set_flash_message('success', 'Student record updated.');
    } else {
        $hash = password_hash($password ?: 'student123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO students (name, email, mobile, course_id, roll_number, password, admission_date, status) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)");
        $stmt->execute([$name, $email, $mobile, $course_id, $roll_number, $hash, $status]);
        set_flash_message('success', 'Student enrolled successfully.');
    }
    header('Location: ' . BASE_URL . '/admin/students.php');
    exit;
}

if ($action === 'delete' && $edit_id > 0) {
    $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$edit_id]);
    set_flash_message('success', 'Student deleted.');
    header('Location: ' . BASE_URL . '/admin/students.php');
    exit;
}

$editStudent = null;
if ($action === 'edit' && $edit_id > 0) {
    $st = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $st->execute([$edit_id]);
    $editStudent = $st->fetch();
}

$query = "SELECT s.*, c.course_name, c.short_name as course_code FROM students s JOIN courses c ON s.course_id = c.id";
$params = [];
if (!empty($search)) {
    $query .= " WHERE s.name LIKE ? OR s.roll_number LIKE ? OR s.email LIKE ? OR s.mobile LIKE ?";
    $term = "%$search%";
    $params = [$term, $term, $term, $term];
}
$query .= " ORDER BY s.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Student Management Directory</h5>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="data-card mb-4">
                        <div class="p-3 bg-light border-bottom">
                            <form method="GET" action="" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, roll no, mobile..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                                <?php if ($search): ?><a href="<?= BASE_URL ?>/admin/students.php" class="btn btn-sm btn-outline-secondary">Reset</a><?php endif; ?>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Name & Email</th>
                                        <th>Course</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $st): ?>
                                        <tr>
                                            <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($st['roll_number']) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($st['name']) ?></strong>
                                                <div class="small text-muted"><?= htmlspecialchars($st['email']) ?></div>
                                            </td>
                                            <td><span class="badge bg-secondary bg-opacity-25 text-dark"><?= htmlspecialchars($st['course_code']) ?></span></td>
                                            <td><?= htmlspecialchars($st['mobile']) ?></td>
                                            <td><span class="badge <?= ($st['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>"><?= $st['status'] ?></span></td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/admin/students.php?action=edit&id=<?= $st['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                <a href="<?= BASE_URL ?>/admin/students.php?action=delete&id=<?= $st['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete student?')"><i class="bi bi-trash"></i></a>
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
                        <h6 class="fw-bold text-dark mb-3"><?= ($editStudent) ? 'Edit Student' : 'Add New Student' ?></h6>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Roll Number *</label>
                                <input type="text" name="roll_number" class="form-control" value="<?= htmlspecialchars($editStudent['roll_number'] ?? generate_roll_number()) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Rahul Kumar" value="<?= htmlspecialchars($editStudent['name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="student@gmail.com" value="<?= htmlspecialchars($editStudent['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mobile *</label>
                                <input type="tel" name="mobile" class="form-control" placeholder="9876543210" value="<?= htmlspecialchars($editStudent['mobile'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Enrolled Course *</label>
                                <select name="course_id" class="form-select" required>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (($editStudent['course_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password <?= ($editStudent) ? '(leave blank to keep current)' : '' ?></label>
                                <input type="password" name="password" class="form-control" placeholder="student123">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= (($editStudent['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= (($editStudent['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi w-100"><?= ($editStudent) ? 'Update Student' : 'Add Student' ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>