<?php
require_once __DIR__ . '/config/functions.php';

$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
if (!$attempt_id) {
    header('Location: ' . BASE_URL . '/exam-login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, e.exam_title, e.duration_minutes, e.max_marks, e.passing_percentage FROM exam_attempts a JOIN exams e ON a.exam_id = e.id WHERE a.id = ?");
$stmt->execute([$attempt_id]);
$attempt = $stmt->fetch();

if (!$attempt) {
    set_flash_message('danger', 'Exam session not found.');
    header('Location: ' . BASE_URL . '/exam-login.php');
    exit;
}

if ($attempt['status'] === 'completed') {
    // Already submitted
    header('Location: ' . BASE_URL . '/exam-result.php?attempt_id=' . $attempt_id);
    exit;
}

// Fetch questions for this exam
$qStmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC");
$qStmt->execute([$attempt['exam_id']]);
$questions = $qStmt->fetchAll();
$totalQuestions = count($questions);

if ($totalQuestions === 0) {
    die("No questions found for this exam.");
}

$page_title = "Live Exam: " . $attempt['exam_title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($attempt['exam_title']) ?> - Micro Group Examination</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <style>
        body { background-color: #F8FAFC; }
        .exam-header-bar {
            background: #09172A;
            color: #fff;
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .timer-box {
            background: #0F233E;
            border: 2px solid #0284C7;
            border-radius: 12px;
            padding: 6px 18px;
            font-family: 'Outfit', monospace;
            font-size: 1.5rem;
            font-weight: 800;
            color: #38BDF8;
            letter-spacing: 0.05em;
        }
        .question-card {
            background: #FFFFFF;
            border-radius: 18px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);
            padding: 32px;
        }
        .option-label {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            background: #FAFBFD;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 12px;
            font-weight: 500;
        }
        .option-label:hover {
            border-color: #0284C7;
            background: #F0F9FF;
        }
        .option-input:checked + .option-label {
            border-color: #0284C7;
            background: #E0F2FE;
            color: #0369A1;
            font-weight: 700;
        }
        .option-bullet {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #E2E8F0;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .option-input:checked + .option-label .option-bullet {
            background: #0284C7;
            color: #FFFFFF;
        }
        .palette-btn {
            width: 40px;
            height: 40px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0;
        }
    </style>
</head>
<body>

<!-- Sticky Header Bar with Live Countdown -->
<header class="exam-header-bar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-icon-box" style="width: 42px; height: 42px;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <h6 class="text-white mb-0 fw-bold"><?= htmlspecialchars($attempt['exam_title']) ?></h6>
                <span class="small text-info"><i class="bi bi-person me-1"></i><?= htmlspecialchars($attempt['student_name']) ?> (Roll: <?= htmlspecialchars($attempt['roll_number']) ?>)</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="small text-secondary text-uppercase fw-bold d-none d-sm-inline">Time Remaining:</span>
                <div class="timer-box" id="exam-timer-display">
                    --:--
                </div>
            </div>
        </div>
    </div>
    <!-- Progress Bar -->
    <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.1);">
        <div id="exam-progress-timer" class="progress-bar bg-info" role="progressbar" style="width: 100%;"></div>
    </div>
</header>

<main class="py-4">
    <div class="container">
        <form id="exam-form" method="POST" action="<?= BASE_URL ?>/exam-result.php">
            <input type="hidden" name="attempt_id" value="<?= $attempt_id ?>">
            
            <div class="row g-4">
                <!-- Question Display Area -->
                <div class="col-lg-8">
                    <?php foreach ($questions as $idx => $q): ?>
                        <div class="question-card question-card-item" id="question-card-<?= $idx ?>" style="<?= ($idx === 0) ? 'display: block;' : 'display: none;' ?>">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 fw-bold">
                                    Question <?= ($idx + 1) ?> of <?= $totalQuestions ?>
                                </span>
                                <span class="badge bg-secondary bg-opacity-25 text-secondary fw-semibold">
                                    Marks: <?= $q['marks'] ?>
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-4" style="line-height: 1.6;">
                                <?= htmlspecialchars($q['question_text']) ?>
                            </h5>

                            <!-- Options -->
                            <div class="options-container">
                                <?php foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $key => $val): ?>
                                    <div>
                                        <input type="radio" 
                                               class="d-none option-input" 
                                               name="answers[<?= $q['id'] ?>]" 
                                               id="opt_<?= $idx ?>_<?= $key ?>" 
                                               value="<?= $key ?>"
                                               onchange="selectOption(<?= $idx ?>, '<?= $key ?>')">
                                        <label for="opt_<?= $idx ?>_<?= $key ?>" class="option-label">
                                            <span class="option-bullet"><?= $key ?></span>
                                            <span><?= htmlspecialchars($val) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Question Navigation Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" onclick="prevQuestion()" <?= ($idx === 0) ? 'disabled' : '' ?>>
                                    <i class="bi bi-chevron-left me-1"></i> Previous
                                </button>
                                
                                <?php if ($idx === $totalQuestions - 1): ?>
                                    <button type="button" class="btn btn-success btn-lg px-4 rounded-pill" onclick="confirmSubmission()">
                                        <i class="bi bi-check2-circle me-1"></i> Submit Examination
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary-mgi px-4 rounded-pill" onclick="nextQuestion()">
                                        Next Question <i class="bi bi-chevron-right ms-1"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Question Palette & Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4 sticky-top" style="top: 100px;">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Question Palette
                        </h6>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                                <button type="button" 
                                        id="palette-badge-<?= $i ?>" 
                                        class="btn btn-outline-secondary palette-btn" 
                                        onclick="showQuestion(<?= $i ?>)">
                                    <?= ($i + 1) ?>
                                </button>
                            <?php endfor; ?>
                        </div>

                        <div class="p-3 rounded-3 bg-light border mb-4 small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Questions:</span>
                                <strong class="text-dark"><?= $totalQuestions ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Questions Attempted:</span>
                                <strong class="text-success" id="attempted-count-display">0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Passing Percentage:</span>
                                <strong class="text-primary"><?= $attempt['passing_percentage'] ?>%</strong>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-danger btn-lg rounded-pill" onclick="confirmSubmission()">
                                <i class="bi bi-box-arrow-right me-1"></i> Finish & Submit Exam
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                    <i class="bi bi-question-lg"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Submit Examination?</h5>
                <p class="text-muted small mb-4">Are you sure you want to finish your test? Your answers will be automatically scored immediately.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">Keep Reviewing</button>
                    <button type="button" class="btn btn-success px-4 rounded-pill" onclick="document.getElementById('exam-form').submit()">Yes, Submit Now</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.EXAM_DURATION_MINS = <?= (int)$attempt['duration_minutes'] ?>;
window.TOTAL_QUESTIONS = <?= $totalQuestions ?>;

function confirmSubmission() {
    const modal = new bootstrap.Modal(document.getElementById('submitModal'));
    modal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/exam.js"></script>
</body>
</html>