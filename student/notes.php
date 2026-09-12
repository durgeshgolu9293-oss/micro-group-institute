<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "Study Material & Notes";

$notesStmt = $pdo->prepare("SELECT n.*, s.subject_name FROM notes n JOIN subjects s ON n.subject_id = s.id WHERE n.course_id = ? ORDER BY n.id ASC");
$notesStmt->execute([$student['course_id']]);
$notes = $notesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes - Micro Group Student Portal</title>
    
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
            <h5 class="mb-0 fw-bold text-dark">Course Study Notes & Handouts</h5>
            <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
        </header>

        <main class="portal-content">
            <div class="data-card">
                <div class="data-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Study Handouts for <?= htmlspecialchars($student['course_code'] ?? 'Enrolled Course') ?></h6>
                    <span class="badge bg-secondary"><?= count($notes) ?> Chapters</span>
                </div>
                <div class="p-4">
                    <?php if (empty($notes)): ?>
                        <p class="text-muted text-center py-4">No notes have been uploaded for your course yet.</p>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($notes as $idx => $n): ?>
                                <div class="col-md-6">
                                    <div class="p-4 rounded-4 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold"><i class="bi bi-file-pdf me-1"></i>PDF Note</span>
                                                <span class="text-muted small"><?= htmlspecialchars($n['file_size'] ?? '2 MB') ?></span>
                                            </div>
                                            <div class="text-info small fw-bold text-uppercase mb-1"><?= htmlspecialchars($n['subject_name']) ?></div>
                                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($n['title']) ?></h5>
                                            <div class="text-secondary small fw-medium mb-3"><?= htmlspecialchars($n['chapter_name']) ?></div>
                                            <p class="small text-muted mb-4"><?= htmlspecialchars($n['description'] ?? 'Official study material prepared by Micro Group faculty.') ?></p>
                                        </div>

                                        <div class="pt-3 border-top d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-cyan-mgi flex-grow-1" onclick="openStudentNoteModal(<?= $idx ?>)">
                                                <i class="bi bi-book-half me-1"></i> Read Online
                                            </button>
                                            <a href="<?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($n['file_path']) ?>" download class="btn btn-sm btn-outline-danger" title="Download PDF">
                                                <i class="bi bi-download me-1"></i> PDF
                                            </a>
                                        </div>

                                        <div id="st-modal-title-<?= $idx ?>" class="d-none"><?= htmlspecialchars($n['title']) ?></div>
                                        <div id="st-modal-meta-<?= $idx ?>" class="d-none"><?= htmlspecialchars($n['chapter_name']) ?> &bull; <?= htmlspecialchars($n['subject_name']) ?></div>
                                        <div id="st-modal-file-<?= $idx ?>" class="d-none"><?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($n['file_path']) ?></div>
                                        <div id="st-modal-body-<?= $idx ?>" class="d-none">
                                            <?= $n['content'] ?: ('<p>' . htmlspecialchars($n['description']) . '</p>') ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="stNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="stNoteTitle">Chapter Note</h5>
                    <small class="text-info" id="stNoteMeta">Subject & Chapter</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="stNoteBody"></div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="stNoteDownloadBtn" download class="btn btn-primary-mgi">
                    <i class="bi bi-download me-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openStudentNoteModal(idx) {
    document.getElementById('stNoteTitle').innerText = document.getElementById(`st-modal-title-${idx}`).innerText;
    document.getElementById('stNoteMeta').innerText = document.getElementById(`st-modal-meta-${idx}`).innerText;
    document.getElementById('stNoteBody').innerHTML = document.getElementById(`st-modal-body-${idx}`).innerHTML;
    document.getElementById('stNoteDownloadBtn').href = document.getElementById(`st-modal-file-${idx}`).innerText;

    new bootstrap.Modal(document.getElementById('stNoteModal')).show();
}
</script>
</body>
</html>