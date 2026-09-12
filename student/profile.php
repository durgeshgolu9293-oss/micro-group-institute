<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "My Profile";

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (empty($mobile)) {
        $err = 'Mobile number cannot be empty.';
    } else {
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $err = 'New password must be at least 6 characters.';
            } else {
                $hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE students SET mobile = ?, password = ? WHERE id = ?");
                $stmt->execute([$mobile, $hash, $student['id']]);
                $msg = 'Profile & Password updated successfully!';
            }
        } else {
            $stmt = $pdo->prepare("UPDATE students SET mobile = ? WHERE id = ?");
            $stmt->execute([$mobile, $student['id']]);
            $msg = 'Mobile number updated successfully!';
        }
        $student = get_logged_student();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Micro Group Student Portal</title>
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
            <h5 class="mb-0 fw-bold text-dark">Student Profile & Settings</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>
        <main class="portal-content">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                        <h5 class="fw-bold text-dark mb-4">Edit Profile Information</h5>

                        <?php if ($msg): ?>
                            <div class="alert alert-success py-2 small mb-3"><?= $msg ?></div>
                        <?php endif; ?>
                        <?php if ($err): ?>
                            <div class="alert alert-danger py-2 small mb-3"><?= $err ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Roll Number</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($student['roll_number']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mobile Number</label>
                                <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($student['mobile']) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Change Password (leave blank to keep current)</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                            </div>
                            <button type="submit" class="btn btn-primary-mgi">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>