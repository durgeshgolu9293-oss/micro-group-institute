<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Digital Study Material & Notes Library";

$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$courses = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC")->fetchAll();

$query = "SELECT n.*, c.course_name, c.short_name as course_code, s.subject_name 
          FROM notes n 
          JOIN courses c ON n.course_id = c.id 
          JOIN subjects s ON n.subject_id = s.id 
          WHERE 1=1";
$params = [];
if ($selected_course > 0) {
    $query .= " AND n.course_id = ?";
    $params[] = $selected_course;
}
$query .= " ORDER BY c.id ASC, s.id ASC, n.id ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notesList = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">Online Learning & Handouts</span>
        <h1 class="display-5 fw-bold text-white mb-2">Digital Study Material & Chapter Notes</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto" style="max-width: 700px;">
            Read chapter notes online right inside your browser or download high-quality PDF handouts for ADCA, CCC, and IT diploma courses.
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <!-- Live Search & Course Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-5 p-3 p-md-4 bg-white">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-funnel-fill text-primary me-1"></i>Filter by Course:</label>
                    <select class="form-select" onchange="location.href='<?= BASE_URL ?>/notes.php' + (this.value != '0' ? '?course_id=' + this.value : '')">
                        <option value="0">-- All Computer Courses --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($selected_course == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-search text-info me-1"></i>Search Notes by Topic / Chapter:</label>
                    <input type="text" id="notesSearchInput" class="form-control" placeholder="Type to search (e.g. Excel, HTML, Hardware, UPI)..." onkeyup="filterNotesLive()">
                </div>
                <div class="col-md-2 mt-md-auto">
                    <a href="<?= BASE_URL ?>/notes.php" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </div>

        <!-- Notes Grid -->
        <div class="row g-4" id="notesGridContainer">
            <?php if (empty($notesList)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted fs-1 mb-3"><i class="bi bi-file-earmark-x"></i></div>
                    <h5 class="text-secondary">No notes found for this selection.</h5>
                </div>
            <?php else: ?>
                <?php foreach ($notesList as $idx => $note): ?>
                    <div class="col-lg-4 col-md-6 note-card-wrapper" 
                         data-title="<?= strtolower(htmlspecialchars($note['title'])) ?>" 
                         data-chapter="<?= strtolower(htmlspecialchars($note['chapter_name'])) ?>" 
                         data-subject="<?= strtolower(htmlspecialchars($note['subject_name'])) ?>">
                        <div class="card h-100 border rounded-4 shadow-sm p-4 d-flex flex-column justify-content-between bg-white hover-elevate">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= htmlspecialchars($note['course_code']) ?></span>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold"><i class="bi bi-filetype-pdf me-1"></i>PDF Note</span>
                                </div>
                                <div class="text-info small fw-bold text-uppercase mb-1"><?= htmlspecialchars($note['subject_name']) ?></div>
                                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($note['title']) ?></h5>
                                <div class="text-secondary small fw-medium mb-3"><?= htmlspecialchars($note['chapter_name']) ?></div>
                                <p class="small text-muted mb-4"><?= htmlspecialchars($note['description'] ?? 'Download or read official chapter notes prepared by Micro Group faculty.') ?></p>
                            </div>

                            <div class="pt-3 border-top d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-cyan-mgi flex-grow-1" onclick="openNoteReaderModal(<?= $idx ?>)">
                                    <i class="bi bi-book-half me-1"></i> Read Online
                                </button>
                                <a href="<?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($note['file_path']) ?>" download class="btn btn-sm btn-outline-danger" title="Download PDF">
                                    <i class="bi bi-download"></i> PDF
                                </a>
                            </div>

                            <!-- Hidden note content for modal reader -->
                            <div id="note-modal-title-<?= $idx ?>" class="d-none"><?= htmlspecialchars($note['title']) ?></div>
                            <div id="note-modal-meta-<?= $idx ?>" class="d-none"><?= htmlspecialchars($note['course_name']) ?> &bull; <?= htmlspecialchars($note['chapter_name']) ?> &bull; <?= htmlspecialchars($note['subject_name']) ?></div>
                            <div id="note-modal-file-<?= $idx ?>" class="d-none"><?= BASE_URL ?>/assets/uploads/notes/<?= htmlspecialchars($note['file_path']) ?></div>
                            <div id="note-modal-body-<?= $idx ?>" class="d-none">
                                <?= $note['content'] ?: ('<p>' . htmlspecialchars($note['description']) . '</p>') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Interactive Note Reader Modal -->
<div class="modal fade" id="noteReaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="modalNoteTitle">Chapter Note</h5>
                    <small class="text-info" id="modalNoteMeta">Course & Subject</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalNoteBody">
                <!-- Rich Content dynamically rendered here -->
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="modalNoteDownloadBtn" download class="btn btn-primary-mgi">
                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download Official PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function filterNotesLive() {
    const query = document.getElementById('notesSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.note-card-wrapper');
    cards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const chap = card.getAttribute('data-chapter') || '';
        const subj = card.getAttribute('data-subject') || '';
        if (title.includes(query) || chap.includes(query) || subj.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function openNoteReaderModal(idx) {
    const title = document.getElementById(`note-modal-title-${idx}`).innerText;
    const meta = document.getElementById(`note-modal-meta-${idx}`).innerText;
    const body = document.getElementById(`note-modal-body-${idx}`).innerHTML;
    const fileUrl = document.getElementById(`note-modal-file-${idx}`).innerText;

    document.getElementById('modalNoteTitle').innerText = title;
    document.getElementById('modalNoteMeta').innerText = meta;
    document.getElementById('modalNoteBody').innerHTML = body;
    document.getElementById('modalNoteDownloadBtn').href = fileUrl;

    const modal = new bootstrap.Modal(document.getElementById('noteReaderModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>