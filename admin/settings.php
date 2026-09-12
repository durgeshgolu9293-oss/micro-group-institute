<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Institute & Admin Settings";

$admin = get_logged_admin();
$passMsg = '';
$passErr = '';

// Handle Admin Account / Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin_account'])) {
    $new_name = trim($_POST['admin_name'] ?? '');
    $new_email = trim($_POST['admin_email'] ?? '');
    $new_pass = $_POST['new_password'] ?? '';
    $conf_pass = $_POST['confirm_password'] ?? '';

    if (empty($new_name) || empty($new_email)) {
        $passErr = 'Admin name and email are required.';
    } else {
        if (!empty($new_pass)) {
            if (strlen($new_pass) < 6) {
                $passErr = 'New password must be at least 6 characters.';
            } elseif ($new_pass !== $conf_pass) {
                $passErr = 'Passwords do not match.';
            } else {
                $hash = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$new_name, $new_email, $hash, $admin['id']]);
                $_SESSION['admin_name'] = $new_name;
                $_SESSION['admin_email'] = $new_email;
                $passMsg = 'Admin Email & Password changed successfully!';
            }
        } else {
            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$new_name, $new_email, $admin['id']]);
            $_SESSION['admin_name'] = $new_name;
            $_SESSION['admin_email'] = $new_email;
            $passMsg = 'Admin profile updated successfully!';
        }
        $admin = get_logged_admin();
    }
}

// Handle Institute Global Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_institute_settings'])) {
    foreach ($_POST['settings'] as $key => $val) {
        update_setting($key, trim($val));
    }
    set_flash_message('success', 'Institute configurations updated successfully.');
    header('Location: ' . BASE_URL . '/admin/settings.php');
    exit;
}

$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$branch_address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur, Bahraich, Uttar Pradesh');
$phone = get_setting('phone', '+91 98765 43210');
$email = get_setting('email', 'info@microgroupinstitute.com');
$tagline = get_setting('tagline', 'Learn • Practice • Test • Achieve');
$default_passing_percentage = get_setting('default_passing_percentage', '40');
$certificate_prefix = get_setting('certificate_prefix', 'MGI-2026-');
$dev_name = get_setting('developer_name', 'Durgesh Pratap Singh');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Settings & Security Management</h5>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="row g-4">
                <!-- Change Admin Password & Email Box -->
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 fs-5">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Change Admin Password</h5>
                        </div>

                        <?php if ($passMsg): ?>
                            <div class="alert alert-success py-2 small mb-3"><i class="bi bi-check-circle me-1"></i><?= $passMsg ?></div>
                        <?php endif; ?>
                        <?php if ($passErr): ?>
                            <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-exclamation-triangle me-1"></i><?= $passErr ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="update_admin_account" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Admin Name *</label>
                                <input type="text" name="admin_name" class="form-control" value="<?= htmlspecialchars($admin['name'] ?? 'DK Singh') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Admin Login Email *</label>
                                <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? 'admin@microgroup.com') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">New Secret Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min. 6 chars)">
                                <div class="form-text small">Leave blank if you don't want to change the password.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password">
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-key-fill me-1"></i> Update Admin Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Institute Global Configurations -->
                <div class="col-lg-7">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 fs-5">
                                <i class="bi bi-building"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Institute Details & Branding</h5>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="update_institute_settings" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Institute Center Name *</label>
                                <input type="text" name="settings[institute_name]" class="form-control" value="<?= htmlspecialchars($site_name) ?>" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Manager / Director *</label>
                                    <input type="text" name="settings[manager_name]" class="form-control" value="<?= htmlspecialchars($manager_name) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Center Location *</label>
                                    <input type="text" name="settings[location]" class="form-control" value="<?= htmlspecialchars($location) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Branch Address</label>
                                <input type="text" name="settings[branch_address]" class="form-control" value="<?= htmlspecialchars($branch_address) ?>">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Phone Number</label>
                                    <input type="text" name="settings[phone]" class="form-control" value="<?= htmlspecialchars($phone) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Official Email</label>
                                    <input type="email" name="settings[email]" class="form-control" value="<?= htmlspecialchars($email) ?>">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Default Passing %</label>
                                    <input type="number" name="settings[default_passing_percentage]" class="form-control" value="<?= htmlspecialchars($default_passing_percentage) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Certificate Prefix</label>
                                    <input type="text" name="settings[certificate_prefix]" class="form-control" value="<?= htmlspecialchars($certificate_prefix) ?>">
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded-3 border mb-4">
                                <label class="form-label small fw-bold text-primary"><i class="bi bi-code-slash me-1"></i>Website Developer Credit</label>
                                <input type="text" name="settings[developer_name]" class="form-control" value="<?= htmlspecialchars($dev_name) ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary-mgi">Save Institute Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>