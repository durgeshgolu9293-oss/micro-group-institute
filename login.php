<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Student Login";

if (is_student_logged_in()) {
    header('Location: ' . BASE_URL . '/student/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Please enter your Roll Number / Email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE (roll_number = ? OR email = ?) AND status = 'active' LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $student = $stmt->fetch();

        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_roll'] = $student['roll_number'];
            $_SESSION['student_course'] = $student['course_id'];
            
            set_flash_message('success', 'Welcome back, ' . htmlspecialchars($student['name']) . '!');
            header('Location: ' . BASE_URL . '/student/dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials or inactive account. Please check your Roll Number/Password.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-light min-vh-75 d-flex align-items-center">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="p-4 text-center bg-dark text-white" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%);">
                        <div class="brand-icon-box mx-auto mb-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill-lock"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Student Portal Login</h4>
                        <p class="small text-light opacity-75 mb-0">Enter your Roll Number or Email to access your dashboard</p>
                    </div>
                    <div class="p-4 p-md-5 bg-white">
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-exclamation-triangle me-1"></i><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Roll Number or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" name="identifier" class="form-control" placeholder="e.g. MG202601 or email" required autofocus>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-mgi btn-lg">
                                    <i class="bi bi-box-arrow-in-right"></i> Login to Dashboard
                                </button>
                            </div>
                        </form>

                        <div class="text-center pt-3 border-top">
                            <p class="small text-muted mb-2">New student? <a href="<?= BASE_URL ?>/register.php" class="text-primary fw-bold">Register for Admission</a></p>
                            <p class="small text-muted mb-0"><a href="<?= BASE_URL ?>/exam-login.php" class="text-secondary"><i class="bi bi-pencil-square me-1"></i>Direct Exam Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>