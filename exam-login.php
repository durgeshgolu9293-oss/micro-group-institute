<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Online Examination Portal";

$courses = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY id ASC")->fetchAll();
$exams = $pdo->query("SELECT e.*, c.course_name, c.short_name as course_code FROM exams e JOIN courses c ON e.course_id = c.id WHERE e.status = 'active' ORDER BY e.id ASC")->fetchAll();

$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$selected_exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

$loggedStudent = get_logged_student();
$default_name = $loggedStudent ? $loggedStudent['name'] : '';
$default_roll = $loggedStudent ? $loggedStudent['roll_number'] : '';
if ($loggedStudent && !$selected_course_id) {
    $selected_course_id = $loggedStudent['course_id'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $roll_number = trim($_POST['roll_number'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);
    $exam_id = (int)($_POST['exam_id'] ?? 0);

    if (empty($student_name) || empty($roll_number) || empty($course_id) || empty($exam_id)) {
        $error = 'Please fill in all candidate details and select an exam.';
    } else {
        // Fetch exam details
        $exStmt = $pdo->prepare("SELECT e.*, c.course_name FROM exams e JOIN courses c ON e.course_id = c.id WHERE e.id = ? AND e.status = 'active'");
        $exStmt->execute([$exam_id]);
        $examData = $exStmt->fetch();

        if (!$examData) {
            $error = 'The chosen examination is not available or inactive.';
        } else {
            // Count available questions
            $qStmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE exam_id = ?");
            $qStmt->execute([$exam_id]);
            $qCount = $qStmt->fetchColumn();

            if ($qCount == 0) {
                $error = 'Questions for this examination are currently being updated. Please choose another exam.';
            } else {
                // Find or associate student ID
                $stStmt = $pdo->prepare("SELECT id FROM students WHERE roll_number = ? LIMIT 1");
                $stStmt->execute([$roll_number]);
                $stRow = $stStmt->fetch();
                $student_id = $stRow ? $stRow['id'] : ($loggedStudent ? $loggedStudent['id'] : null);

                // Create a fresh exam attempt
                $insStmt = $pdo->prepare("INSERT INTO exam_attempts (student_id, exam_id, roll_number, student_name, course_name, start_time, total_questions, status) VALUES (?, ?, ?, ?, ?, NOW(), ?, 'in_progress')");
                $insStmt->execute([$student_id, $exam_id, $roll_number, $student_name, $examData['course_name'], $qCount]);
                $attempt_id = $pdo->lastInsertId();

                header('Location: ' . BASE_URL . '/take-exam.php?attempt_id=' . $attempt_id);
                exit;
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">Computer Assessment System</span>
        <h1 class="display-5 fw-bold text-white mb-2">Online Examination & Certification Portal</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto" style="max-width: 700px;">
            Timed MCQ tests with instant evaluation, scorecards, and verifiable Course Completion Certificates.
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Instructions Checklist -->
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold text-dark mb-4"><i class="bi bi-info-circle-fill text-primary me-2"></i>Examination Rules & Guidelines</h4>
                    
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 fs-5"><i class="bi bi-stopwatch"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Automatic Timer</h6>
                                <p class="small text-muted mb-0">The test duration is strictly enforced. The exam will automatically submit when the timer hits zero.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 fs-5"><i class="bi bi-ui-checks"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">MCQ Single Choice</h6>
                                <p class="small text-muted mb-0">Select Option A, B, C, or D. You can navigate between questions freely and change answers prior to final submission.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 fs-5"><i class="bi bi-award-fill"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Passing Threshold (<?= get_setting('default_passing_percentage', '40') ?>%)</h6>
                                <p class="small text-muted mb-0">Score <?= get_setting('default_passing_percentage', '40') ?>% or higher to instantly receive your official Course Completion Certificate.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 fs-5"><i class="bi bi-shield-lock-fill"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Single Submission Security</h6>
                                <p class="small text-muted mb-0">Do not refresh or close the browser tab during an active test to avoid premature termination.</p>
                            </div>
                        </li>
                    </ul>

                    <div class="p-3 rounded-3 bg-light border mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Authorized by:</span>
                            <span class="small fw-bold text-dark"><i class="bi bi-person-check-fill text-success me-1"></i>Manager: <?= htmlspecialchars(get_setting('manager_name', 'DK Singh')) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Candidate Entry Form -->
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-lg p-4 p-md-5 bg-white">
                    <h4 class="fw-bold text-dark mb-4"><i class="bi bi-person-vcard text-info me-2"></i>Candidate Registration For Exam</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small mb-4"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Candidate Full Name *</label>
                            <input type="text" name="student_name" class="form-control" placeholder="e.g. Rahul Kumar" value="<?= htmlspecialchars($default_name ?: ($_POST['student_name'] ?? '')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Student Roll Number *</label>
                            <input type="text" name="roll_number" class="form-control" placeholder="e.g. MG202601" value="<?= htmlspecialchars($default_roll ?: ($_POST['roll_number'] ?? '')) ?>" required>
                            <div class="form-text small">Enter your assigned Roll Number or a unique registration identifier.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Enrolled Course *</label>
                            <select name="course_id" id="course-select" class="form-select" required onchange="filterExamsByCourse(this.value)">
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($selected_course_id == $c['id'] || (isset($_POST['course_id']) && $_POST['course_id'] == $c['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['course_name']) ?> (<?= htmlspecialchars($c['short_name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Select Online Examination *</label>
                            <select name="exam_id" id="exam-select" class="form-select" required onchange="updateExamDetailsCard(this)">
                                <option value="">-- Choose Exam --</option>
                                <?php foreach ($exams as $e): ?>
                                    <option value="<?= $e['id'] ?>" 
                                            data-course="<?= $e['course_id'] ?>" 
                                            data-duration="<?= $e['duration_minutes'] ?>"
                                            data-questions="<?= $e['total_questions'] ?>"
                                            data-marks="<?= $e['max_marks'] ?>"
                                            data-pass="<?= $e['passing_percentage'] ?>"
                                            <?= ($selected_exam_id == $e['id'] || (isset($_POST['exam_id']) && $_POST['exam_id'] == $e['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e['exam_title']) ?> [<?= htmlspecialchars($e['course_code']) ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dynamic Exam Preview Badge -->
                        <div id="exam-preview-card" class="p-3 rounded-3 bg-light border mb-4" style="display: none;">
                            <h6 class="fw-bold text-primary mb-2" id="prev-exam-title">Exam Overview</h6>
                            <div class="row g-2 text-center small">
                                <div class="col-3">
                                    <div class="bg-white p-2 rounded border">
                                        <span class="text-muted d-block">Duration</span>
                                        <strong id="prev-duration" class="text-dark">15 Mins</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white p-2 rounded border">
                                        <span class="text-muted d-block">Questions</span>
                                        <strong id="prev-questions" class="text-dark">10</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white p-2 rounded border">
                                        <span class="text-muted d-block">Max Marks</span>
                                        <strong id="prev-marks" class="text-dark">50</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-white p-2 rounded border">
                                        <span class="text-muted d-block">Pass %</span>
                                        <strong id="prev-pass" class="text-success">40%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-gold-mgi btn-lg py-3">
                                <i class="bi bi-play-circle-fill"></i> Start Online Examination Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function filterExamsByCourse(courseId) {
    const examSelect = document.getElementById('exam-select');
    const options = examSelect.querySelectorAll('option');
    let firstValid = null;
    
    options.forEach(opt => {
        if (!opt.value) return;
        const optCourse = opt.getAttribute('data-course');
        if (!courseId || optCourse === courseId) {
            opt.style.display = 'block';
            if (!firstValid) firstValid = opt.value;
        } else {
            opt.style.display = 'none';
        }
    });
    
    if (firstValid && (!examSelect.value || examSelect.selectedOptions[0].style.display === 'none')) {
        examSelect.value = firstValid;
    }
    updateExamDetailsCard(examSelect);
}

function updateExamDetailsCard(selectEl) {
    const card = document.getElementById('exam-preview-card');
    const opt = selectEl.selectedOptions[0];
    if (opt && opt.value) {
        card.style.display = 'block';
        document.getElementById('prev-exam-title').innerText = opt.text;
        document.getElementById('prev-duration').innerText = opt.getAttribute('data-duration') + ' Mins';
        document.getElementById('prev-questions').innerText = opt.getAttribute('data-questions');
        document.getElementById('prev-marks').innerText = opt.getAttribute('data-marks');
        document.getElementById('prev-pass').innerText = opt.getAttribute('data-pass') + '%';
    } else {
        card.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const courseSel = document.getElementById('course-select');
    if (courseSel.value) {
        filterExamsByCourse(courseSel.value);
    }
    const examSel = document.getElementById('exam-select');
    if (examSel.value) {
        updateExamDetailsCard(examSel);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>