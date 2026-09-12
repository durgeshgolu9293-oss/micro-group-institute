<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Certificate Management";

$action = $_GET['action'] ?? 'list';
$cert_id = (int)($_GET['id'] ?? 0);

// Revoke Certificate
if ($action === 'revoke' && $cert_id > 0) {
    $pdo->prepare("UPDATE certificates SET status = 'revoked' WHERE id = ?")->execute([$cert_id]);
    set_flash_message('warning', 'Certificate has been revoked.');
    header('Location: ' . BASE_URL . '/admin/certificates.php');
    exit;
}

// Restore Certificate
if ($action === 'restore' && $cert_id > 0) {
    $pdo->prepare("UPDATE certificates SET status = 'active' WHERE id = ?")->execute([$cert_id]);
    set_flash_message('success', 'Certificate restored and verified.');
    header('Location: ' . BASE_URL . '/admin/certificates.php');
    exit;
}

// Manual Certificate Generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_cert'])) {
    $student_name = trim($_POST['student_name']);
    $roll_number = trim($_POST['roll_number']);
    $course_id = (int)$_POST['course_id'];
    $percentage = (float)$_POST['percentage'];
    $grade = calculate_grade($percentage);
    $cert_number = generate_certificate_no();

    $cStmt = $pdo->prepare("SELECT course_name, duration FROM courses WHERE id = ?");
    $cStmt->execute([$course_id]);
    $cRow = $cStmt->fetch();

    $ins = $pdo->prepare("INSERT INTO certificates (certificate_number, student_id, course_id, result_id, student_name, roll_number, course_name, duration, percentage, grade, issue_date, status) VALUES (?, 0, ?, 0, ?, ?, ?, ?, ?, ?, CURDATE(), 'active')");
    $ins->execute([$cert_number, $course_id, $student_name, $roll_number, $cRow['course_name'], $cRow['duration'], $percentage, $grade]);
    set_flash_message('success', "Certificate {$cert_number} generated successfully!");
    header('Location: ' . BASE_URL . '/admin/certificates.php');
    exit;
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();
$certificates = $pdo->query("SELECT * FROM certificates ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Management - Micro Group Admin</title>
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
            <h5 class="mb-0 fw-bold text-dark">Course Completion Certificate Management</h5>
            <button class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#genCertModal">
                <i class="bi bi-plus-circle me-1"></i> Manual Certificate Issuance
            </button>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="data-card">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Certificate No</th>
                                <th>Student</th>
                                <th>Roll No</th>
                                <th>Course</th>
                                <th>Score</th>
                                <th>Grade</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($certificates)): ?>
                                <tr><td colspan="9" class="text-center py-4 text-muted">No certificates issued yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($certificates as $cert): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($cert['certificate_number']) ?></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($cert['student_name']) ?></td>
                                        <td><?= htmlspecialchars($cert['roll_number']) ?></td>
                                        <td><?= htmlspecialchars($cert['course_name']) ?></td>
                                        <td><?= $cert['percentage'] ?>%</td>
                                        <td><span class="badge bg-secondary bg-opacity-25 text-dark"><?= htmlspecialchars($cert['grade']) ?></span></td>
                                        <td><?= date('d M, Y', strtotime($cert['issue_date'])) ?></td>
                                        <td>
                                            <span class="badge <?= ($cert['status'] === 'active') ? 'bg-success' : 'bg-danger' ?>">
                                                <?= ($cert['status'] === 'active') ? 'VALID' : 'REVOKED' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-gold-mgi" title="Print Certificate"><i class="bi bi-printer"></i></a>
                                            <a href="<?= BASE_URL ?>/verify-certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Public Verify"><i class="bi bi-patch-check"></i></a>
                                            <?php if ($cert['status'] === 'active'): ?>
                                                <a href="<?= BASE_URL ?>/admin/certificates.php?action=revoke&id=<?= $cert['id'] ?>" class="btn btn-sm btn-outline-danger" title="Revoke Certificate" onclick="return confirm('Revoke this certificate?')"><i class="bi bi-slash-circle"></i></a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>/admin/certificates.php?action=restore&id=<?= $cert['id'] ?>" class="btn btn-sm btn-outline-success" title="Restore Certificate"><i class="bi bi-arrow-counterclockwise"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Manual Cert Modal -->
<div class="modal fade" id="genCertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Manual Certificate Generator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <input type="hidden" name="generate_cert" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Student Name *</label>
                        <input type="text" name="student_name" class="form-control" placeholder="Candidate Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Roll Number *</label>
                        <input type="text" name="roll_number" class="form-control" placeholder="MG202601" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Course *</label>
                        <select name="course_id" class="form-select" required>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Marks Percentage (%) *</label>
                        <input type="number" step="0.01" name="percentage" class="form-control" placeholder="85.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-mgi">Generate & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>