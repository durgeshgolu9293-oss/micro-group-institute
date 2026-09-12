<?php
require_once __DIR__ . '/../config/functions.php';
$page_title = "Admin Login";

if (is_admin_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your administrator email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];

            set_flash_message('success', 'Logged in successfully.');
            header('Location: ' . BASE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'Invalid admin email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Login - Micro Group</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%); min-height: 100vh; display: flex; align-items: center;">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="p-4 text-center bg-dark text-white" style="background: linear-gradient(135deg, #0284C7 0%, #09172A 100%);">
                    <div class="brand-icon-box mx-auto mb-2" style="width: 52px; height: 52px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Admin Control Portal</h5>
                    <span class="text-info small">Micro Group Computer Institute</span>
                </div>
                <div class="p-4 p-md-5 bg-white">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-exclamation-triangle me-1"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Admin Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Enter admin email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Admin Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter secure password" required>
                            </div>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary-mgi btn-lg">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Secure Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/index.php" class="text-secondary small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>