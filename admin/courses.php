<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Courses & Pricing Management";

$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $short_name = trim($_POST['short_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $fee = (float)($_POST['fee'] ?? 0);
    $admission_fee = (float)($_POST['admission_fee'] ?? 0);
    $discount = (float)($_POST['discount'] ?? 0);
    $final_fee = max(0, $fee + $admission_fee - $discount);
    $eligibility = trim($_POST['eligibility'] ?? '10th / 12th Pass');
    $certificate_available = isset($_POST['certificate_available']) ? 1 : 0;
    $status = $_POST['status'] ?? 'active';

    if (empty($course_name) || empty($short_name) || empty($duration)) {
        set_flash_message('danger', 'Please enter course name, short name, and duration.');
    } else {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE courses SET course_name = ?, short_name = ?, description = ?, duration = ?, fee = ?, admission_fee = ?, discount = ?, final_fee = ?, eligibility = ?, certificate_available = ?, status = ? WHERE id = ?");
            $stmt->execute([$course_name, $short_name, $description, $duration, $fee, $admission_fee, $discount, $final_fee, $eligibility, $certificate_available, $status, $edit_id]);
            set_flash_message('success', 'Course updated successfully with new pricing!');
        } else {
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, short_name, description, duration, fee, admission_fee, discount, final_fee, eligibility, certificate_available, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$course_name, $short_name, $description, $duration, $fee, $admission_fee, $discount, $final_fee, $eligibility, $certificate_available, $status]);
            set_flash_message('success', 'New course created successfully!');
        }
        header('Location: ' . BASE_URL . '/admin/courses.php');
        exit;
    }
}

// Handle Delete
if ($action === 'delete' && $edit_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$edit_id]);
    set_flash_message('success', 'Course deleted successfully.');
    header('Location: ' . BASE_URL . '/admin/courses.php');
    exit;
}

$editCourse = null;
if ($action === 'edit' && $edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$edit_id]);
    $editCourse = $stmt->fetch();
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Micro Group Admin</title>
    
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
            <h5 class="mb-0 fw-bold text-dark">Courses & Fee Structure Management</h5>
            <button class="btn btn-primary-mgi btn-sm" onclick="document.getElementById('courseFormSection').scrollIntoView({behavior: 'smooth'})">
                <i class="bi bi-plus-circle me-1"></i> Add New Course
            </button>
        </header>

        <main class="portal-content">
            <?php display_flash_message(); ?>

            <!-- Courses Table -->
            <div class="data-card mb-4">
                <div class="data-card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Active Course Catalog</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Code</th>
                                <th>Duration</th>
                                <th>Course Fee</th>
                                <th>Adm. Fee</th>
                                <th>Discount</th>
                                <th>Final Fee</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($c['course_name']) ?></strong>
                                    </td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($c['short_name']) ?></span></td>
                                    <td><?= htmlspecialchars($c['duration']) ?></td>
                                    <td><?= format_currency($c['fee']) ?></td>
                                    <td><?= format_currency($c['admission_fee']) ?></td>
                                    <td class="text-success"><?= format_currency($c['discount']) ?></td>
                                    <td class="fw-bold text-success fs-6"><?= format_currency($c['final_fee']) ?></td>
                                    <td>
                                        <span class="badge <?= ($c['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $c['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/admin/courses.php?action=edit&id=<?= $c['id'] ?>#courseFormSection" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/admin/courses.php?action=delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add / Edit Course Form -->
            <div id="courseFormSection" class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <h5 class="fw-bold text-dark mb-4">
                    <?= ($editCourse) ? '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Course & Update Pricing' : '<i class="bi bi-plus-circle text-success me-2"></i>Add New Course' ?>
                </h5>

                <form method="POST" action="">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Full Course Name *</label>
                            <input type="text" name="course_name" class="form-control" placeholder="e.g. Advanced Diploma in Computer Applications" value="<?= htmlspecialchars($editCourse['course_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Short Code *</label>
                            <input type="text" name="short_name" class="form-control" placeholder="e.g. ADCA" value="<?= htmlspecialchars($editCourse['short_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Course Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Describe the course syllabus, practical lab training, and outcomes..."><?= htmlspecialchars($editCourse['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Dynamic Pricing Calculator Fields -->
                    <div class="p-3 rounded-4 bg-light border mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-currency-rupee me-1"></i>Course Pricing Breakdown (Dynamic Calculation)</h6>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold">Base Course Fee (₹) *</label>
                                <input type="number" step="0.01" name="fee" id="course_fee" class="form-control" value="<?= htmlspecialchars($editCourse['fee'] ?? '10000.00') ?>" required>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold">Admission Fee (₹)</label>
                                <input type="number" step="0.01" name="admission_fee" id="admission_fee" class="form-control" value="<?= htmlspecialchars($editCourse['admission_fee'] ?? '500.00') ?>">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold">Discount (₹)</label>
                                <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="<?= htmlspecialchars($editCourse['discount'] ?? '1000.00') ?>">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold">Net Final Fee (₹)</label>
                                <input type="number" step="0.01" id="final_fee" class="form-control bg-white fw-bold text-success fs-6" value="<?= htmlspecialchars($editCourse['final_fee'] ?? '9500.00') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Duration *</label>
                            <input type="text" name="duration" class="form-control" placeholder="e.g. 12 Months / 3 Months" value="<?= htmlspecialchars($editCourse['duration'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Eligibility</label>
                            <input type="text" name="eligibility" class="form-control" placeholder="e.g. 10th / 12th Pass" value="<?= htmlspecialchars($editCourse['eligibility'] ?? '10th / 12th Pass') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= (($editCourse['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($editCourse['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" name="certificate_available" class="form-check-input" id="cert_chk" value="1" <?= (!isset($editCourse) || $editCourse['certificate_available']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold" for="cert_chk">Provide Course Completion Certificate upon passing exam</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-mgi">
                            <i class="bi bi-save me-1"></i> <?= ($editCourse) ? 'Update Course & Pricing' : 'Save New Course' ?>
                        </button>
                        <?php if ($editCourse): ?>
                            <a href="<?= BASE_URL ?>/admin/courses.php" class="btn btn-outline-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>