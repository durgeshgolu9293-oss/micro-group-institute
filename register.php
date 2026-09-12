<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Student Admission Registration";

$courses = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC")->fetchAll();
$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($mobile) || empty($course_id) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $chk = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'A student with this email address already exists.';
        } else {
            $roll_number = generate_roll_number();
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $admission_date = date('Y-m-d');

            $stmt = $pdo->prepare("INSERT INTO students (roll_number, name, email, mobile, password, course_id, admission_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
            if ($stmt->execute([$roll_number, $name, $email, $mobile, $hash, $course_id, $admission_date])) {
                $newId = $pdo->lastInsertId();
                $_SESSION['student_id'] = $newId;
                $_SESSION['student_name'] = $name;
                $_SESSION['student_roll'] = $roll_number;
                $_SESSION['student_course'] = $course_id;
                
                set_flash_message('success', "Registration successful! Your official Roll Number is {$roll_number}.");
                header('Location: ' . BASE_URL . '/student/dashboard.php');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-light min-vh-75 d-flex align-items-center">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="p-4 text-center bg-dark text-white" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%);">
                        <div class="brand-icon-box mx-auto mb-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Student Admission Registration</h4>
                        <p class="small text-light opacity-75 mb-0">Enroll in Micro Group Computer Institute to start learning</p>
                    </div>
                    <div class="p-4 p-md-5 bg-white">
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-exclamation-triangle me-1"></i><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Student Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Kumar" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Address *</label>
                                    <input type="email" name="email" class="form-control" placeholder="student@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mobile Number *</label>
                                    <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Select Enrolled Course *</label>
                                <select name="course_id" class="form-select" required>
                                    <option value="">-- Choose Your Course --</option>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($selected_course_id == $c['id'] || (isset($_POST['course_id']) && $_POST['course_id'] == $c['id'])) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>) - Fee: <?= format_currency($c['final_fee']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Create Password *</label>
                                    <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Confirm Password *</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                                </div>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-cyan-mgi btn-lg">
                                    <i class="bi bi-check-circle-fill"></i> Complete Registration & Get Roll Number
                                </button>
                            </div>
                        </form>

                        <div class="text-center pt-3 border-top">
                            <p class="small text-muted mb-0">Already registered? <a href="<?= BASE_URL ?>/login.php" class="text-primary fw-bold">Student Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>