<?php
require_once __DIR__ . '/config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    set_flash_message('danger', 'Selected course could not be found.');
    header('Location: ' . BASE_URL . '/courses.php');
    exit;
}

$page_title = $course['course_name'] . " (" . $course['short_name'] . ")";

// Fetch subjects for this course
$subStmt = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? ORDER BY id ASC");
$subStmt->execute([$course['id']]);
$subjects = $subStmt->fetchAll();

// Fetch notes for this course
$notesStmt = $pdo->prepare("SELECT n.*, s.subject_name FROM notes n LEFT JOIN subjects s ON n.subject_id = s.id WHERE n.course_id = ? ORDER BY n.id ASC");
$notesStmt->execute([$course['id']]);
$notes = $notesStmt->fetchAll();

// Fetch active exams for this course
$examStmt = $pdo->prepare("SELECT * FROM exams WHERE course_id = ? AND status = 'active'");
$examStmt->execute([$course['id']]);
$exams = $examStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-2"><?= htmlspecialchars($course['short_name']) ?></span>
                <h1 class="display-6 fw-bold text-white mb-2"><?= htmlspecialchars($course['course_name']) ?></h1>
                <p class="text-light opacity-75 lead mb-3"><?= htmlspecialchars($course['description']) ?></p>
                
                <div class="d-flex flex-wrap gap-4 text-light small">
                    <div><i class="bi bi-clock-history text-info me-1"></i> Duration: <strong><?= htmlspecialchars($course['duration']) ?></strong></div>
                    <div><i class="bi bi-award text-warning me-1"></i> Eligibility: <strong><?= htmlspecialchars($course['eligibility']) ?></strong></div>
                    <div><i class="bi bi-patch-check-fill text-success me-1"></i> Certificate: <strong><?= $course['certificate_available'] ? 'Available' : 'N/A' ?></strong></div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= BASE_URL ?>/register.php?course_id=<?= $course['id'] ?>" class="btn btn-gold-mgi btn-lg px-4">
                    <i class="bi bi-check-circle-fill"></i> Enroll in Course
                </a>
            </div>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- Syllabus & Subjects -->
                <div class="data-card mb-4">
                    <div class="data-card-header">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text text-primary me-2"></i>Curriculum & Subjects Covered</h5>
                    </div>
                    <div class="p-4">
                        <?php if (empty($subjects)): ?>
                            <p class="text-muted mb-0">Detailed subjects syllabus will be updated shortly.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($subjects as $idx => $sub): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 border bg-light h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($sub['subject_code'] ?? ('Module ' . ($idx+1))) ?></span>
                                                <span class="text-muted small"><i class="bi bi-check2"></i> Core Subject</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($sub['subject_name']) ?></h6>
                                            <p class="small text-secondary mb-0"><?= htmlspecialchars($sub['description'] ?? 'Comprehensive theoretical and lab exercises.') ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Study Material & Notes Preview -->
                <div class="data-card mb-4">
                    <div class="data-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Digital Study Materials</h5>
                        <a href="<?= BASE_URL ?>/notes.php?course_id=<?= $course['id'] ?>" class="btn btn-sm btn-outline-primary">View All Notes</a>
                    </div>
                    <div class="p-4">
                        <?php if (empty($notes)): ?>
                            <p class="text-muted mb-0">Study materials for this course are available to enrolled students.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($notes as $note): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded bg-danger bg-opacity-10 text-danger p-2 fs-4">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($note['title']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($note['chapter_name']) ?> &bull; <?= htmlspecialchars($note['subject_name'] ?? 'General') ?> &bull; Size: <?= htmlspecialchars($note['file_size'] ?? '1.5 MB') ?></small>
                                            </div>
                                        </div>
                                        <a href="<?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($note['file_path']) ?>" download class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Available Online Exams -->
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-card-checklist text-success me-2"></i>Online Examinations for this Course</h5>
                    </div>
                    <div class="p-4">
                        <?php if (empty($exams)): ?>
                            <p class="text-muted mb-0">No active examinations currently scheduled for this course.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($exams as $ex): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 border bg-white shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">Active Test</span>
                                                <span class="text-muted small"><i class="bi bi-stopwatch me-1"></i><?= $ex['duration_minutes'] ?> Mins</span>
                                            </div>
                                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($ex['exam_title']) ?></h6>
                                            <p class="small text-secondary mb-3">Questions: <strong><?= $ex['total_questions'] ?></strong> | Max Marks: <strong><?= $ex['max_marks'] ?></strong> | Pass: <strong><?= $ex['passing_percentage'] ?>%</strong></p>
                                            <a href="<?= BASE_URL ?>/exam-login.php?course_id=<?= $course['id'] ?>&exam_id=<?= $ex['id'] ?>" class="btn btn-sm btn-cyan-mgi w-100">
                                                <i class="bi bi-pencil-square"></i> Take This Exam
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Fee Breakdown & Quick Actions -->
            <div class="col-lg-4">
                <div class="p-4 rounded-4 bg-white shadow-md border mb-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Fee Breakdown & Pricing</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Standard Course Fee:</span>
                            <span class="text-dark fw-semibold"><?= format_currency($course['fee']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Admission Fee:</span>
                            <span class="text-dark fw-semibold">+ <?= format_currency($course['admission_fee']) ?></span>
                        </div>
                        <?php if ((float)$course['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Special Discount:</span>
                                <span>- <?= format_currency($course['discount']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between pt-3 border-top mt-3">
                            <span class="h6 fw-bold mb-0 text-dark">Total Net Fee:</span>
                            <span class="h4 fw-bold mb-0 text-success"><?= format_currency($course['final_fee']) ?></span>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-4">
                        <i class="bi bi-info-circle-fill me-1"></i> Fee covers digital notes, computer lab access, online exams, and diploma certificate.
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>/register.php?course_id=<?= $course['id'] ?>" class="btn btn-gold-mgi">
                            <i class="bi bi-pencil-fill"></i> Enroll Now
                        </a>
                        <a href="<?= BASE_URL ?>/notes.php?course_id=<?= $course['id'] ?>" class="btn btn-outline-navy">
                            <i class="bi bi-file-earmark-text"></i> View Notes
                        </a>
                        <a href="<?= BASE_URL ?>/exam-login.php?course_id=<?= $course['id'] ?>" class="btn btn-cyan-mgi">
                            <i class="bi bi-laptop"></i> Take Test
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>