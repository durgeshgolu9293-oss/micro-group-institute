<?php
require_once __DIR__ . '/config/functions.php';

$attempt_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attempt_id = (int)($_POST['attempt_id'] ?? 0);
    $userAnswers = $_POST['answers'] ?? [];
    
    // Evaluate submission
    $attStmt = $pdo->prepare("SELECT a.*, e.course_id, e.max_marks, e.passing_percentage FROM exam_attempts a JOIN exams e ON a.exam_id = e.id WHERE a.id = ?");
    $attStmt->execute([$attempt_id]);
    $attempt = $attStmt->fetch();

    if ($attempt && $attempt['status'] !== 'completed') {
        // Fetch all questions with correct answers
        $qStmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC");
        $qStmt->execute([$attempt['exam_id']]);
        $questions = $qStmt->fetchAll();

        $totalQuestions = count($questions);
        $attempted = 0;
        $correct = 0;
        $wrong = 0;
        $obtainedMarks = 0;
        $totalMarks = 0;

        foreach ($questions as $q) {
            $qid = $q['id'];
            $totalMarks += (int)$q['marks'];
            $selected = isset($userAnswers[$qid]) ? $userAnswers[$qid] : null;

            if ($selected !== null) {
                $attempted++;
                $isCorrect = ($selected === $q['correct_option']) ? 1 : 0;
                if ($isCorrect) {
                    $correct++;
                    $obtainedMarks += (int)$q['marks'];
                } else {
                    $wrong++;
                }
            } else {
                $isCorrect = 0;
            }

            // Record answer
            $ansStmt = $pdo->prepare("INSERT INTO exam_answers (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
            $ansStmt->execute([$attempt_id, $qid, $selected, $isCorrect]);
        }

        $unattempted = $totalQuestions - $attempted;
        $percentage = ($totalMarks > 0) ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $grade = calculate_grade($percentage);
        $passStatus = ($percentage >= (float)$attempt['passing_percentage']) ? 'PASS' : 'FAIL';

        // Update exam_attempts record
        $updStmt = $pdo->prepare("UPDATE exam_attempts SET end_time = NOW(), attempted = ?, correct_answers = ?, wrong_answers = ?, unattempted = ?, total_marks = ?, obtained_marks = ?, percentage = ?, grade = ?, status = 'completed' WHERE id = ?");
        $updStmt->execute([$attempted, $correct, $wrong, $unattempted, $totalMarks, $obtainedMarks, $percentage, $grade, $attempt_id]);

        // Insert into results table
        $resStmt = $pdo->prepare("INSERT INTO results (attempt_id, student_id, exam_id, roll_number, total_marks, obtained_marks, percentage, grade, pass_status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE obtained_marks=VALUES(obtained_marks), percentage=VALUES(percentage), grade=VALUES(grade), pass_status=VALUES(pass_status)");
        $resStmt->execute([$attempt_id, $attempt['student_id'], $attempt['exam_id'], $attempt['roll_number'], $totalMarks, $obtainedMarks, $percentage, $grade, $passStatus]);
        $result_id = $pdo->lastInsertId() ?: 1;

        // Auto-generate Certificate if PASS (>= passing percentage)
        if ($passStatus === 'PASS') {
            // Check if certificate already exists for this result or student+course
            $certChk = $pdo->prepare("SELECT id, certificate_number FROM certificates WHERE result_id = ? OR (student_id = ? AND course_id = ? AND student_id IS NOT NULL)");
            $certChk->execute([$result_id, $attempt['student_id'], $attempt['course_id']]);
            $existingCert = $certChk->fetch();

            if (!$existingCert) {
                $certNumber = generate_certificate_no();
                $cDetails = $pdo->prepare("SELECT duration FROM courses WHERE id = ?");
                $cDetails->execute([$attempt['course_id']]);
                $cRow = $cDetails->fetch();
                $courseDuration = $cRow ? $cRow['duration'] : '12 Months';

                $cIns = $pdo->prepare("INSERT INTO certificates (certificate_number, student_id, course_id, result_id, student_name, roll_number, course_name, duration, percentage, grade, issue_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'active')");
                $cIns->execute([
                    $certNumber,
                    $attempt['student_id'] ?: 0,
                    $attempt['course_id'],
                    $result_id,
                    $attempt['student_name'],
                    $attempt['roll_number'],
                    $attempt['course_name'],
                    $courseDuration,
                    $percentage,
                    $grade
                ]);
            }
        }

        header('Location: ' . BASE_URL . '/exam-result.php?attempt_id=' . $attempt_id);
        exit;
    }
} else {
    $attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
}

if (!$attempt_id) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Fetch completed result details
$stmt = $pdo->prepare("SELECT a.*, r.id as result_id, r.pass_status, e.exam_title, e.passing_percentage, c.id as course_id, c.course_name 
                      FROM exam_attempts a 
                      JOIN exams e ON a.exam_id = e.id 
                      JOIN courses c ON e.course_id = c.id 
                      LEFT JOIN results r ON a.id = r.attempt_id 
                      WHERE a.id = ?");
$stmt->execute([$attempt_id]);
$result = $stmt->fetch();

if (!$result) {
    set_flash_message('danger', 'Result scorecard not found.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Check certificate availability
$certStmt = $pdo->prepare("SELECT * FROM certificates WHERE result_id = ? OR (roll_number = ? AND course_id = ?)");
$certStmt->execute([$result['result_id'], $result['roll_number'], $result['course_id']]);
$certificate = $certStmt->fetch();

$page_title = "Exam Result: " . $result['student_name'] . " (" . $result['roll_number'] . ")";
$manager_name = get_setting('manager_name', 'DK Singh');

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge <?= ($result['pass_status'] === 'PASS') ? 'bg-success' : 'bg-danger' ?> px-3 py-2 fw-bold text-uppercase mb-2">
            Status: <?= $result['pass_status'] ?>
        </span>
        <h1 class="display-6 fw-bold text-white mb-2">Official Examination Scorecard</h1>
        <p class="text-light opacity-75 small">Candidate: <strong><?= htmlspecialchars($result['student_name']) ?></strong> | Roll No: <strong><?= htmlspecialchars($result['roll_number']) ?></strong></p>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Result Scorecard Box -->
                <div class="card border-0 rounded-4 shadow-lg overflow-hidden bg-white mb-4">
                    <div class="p-4 p-md-5">
                        <div class="row g-4 align-items-center border-bottom pb-4 mb-4">
                            <div class="col-md-8">
                                <div class="text-primary small fw-bold text-uppercase">Micro Group of Computer Institute</div>
                                <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($result['exam_title']) ?></h3>
                                <div class="text-secondary small">Course: <strong><?= htmlspecialchars($result['course_name']) ?></strong></div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="p-3 rounded-3 text-center d-inline-block <?= ($result['pass_status'] === 'PASS') ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-danger bg-opacity-10 text-danger border border-danger' ?>" style="min-width: 140px;">
                                    <div class="fs-2 fw-bold"><?= $result['percentage'] ?>%</div>
                                    <div class="small fw-bold">Grade: <?= $result['grade'] ?> (<?= $result['pass_status'] ?>)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Grid -->
                        <div class="row g-3 text-center mb-4">
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Total Qs</span>
                                    <h5 class="fw-bold text-dark mb-0"><?= $result['total_questions'] ?></h5>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Attempted</span>
                                    <h5 class="fw-bold text-primary mb-0"><?= $result['attempted'] ?></h5>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Correct</span>
                                    <h5 class="fw-bold text-success mb-0"><?= $result['correct_answers'] ?></h5>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Wrong</span>
                                    <h5 class="fw-bold text-danger mb-0"><?= $result['wrong_answers'] ?></h5>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Unattempted</span>
                                    <h5 class="fw-bold text-secondary mb-0"><?= $result['unattempted'] ?></h5>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted small d-block">Marks</span>
                                    <h5 class="fw-bold text-dark mb-0"><?= $result['obtained_marks'] ?> / <?= $result['total_marks'] ?></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Eligibility Banner -->
                        <?php if ($result['pass_status'] === 'PASS' && $certificate && $certificate['status'] === 'active'): ?>
                            <div class="p-4 rounded-4 bg-warning bg-opacity-10 border border-warning mb-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-warning text-dark p-3 fs-3">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">Course Completion Certificate Available!</h5>
                                            <p class="small text-secondary mb-0">Official Certificate Number: <strong class="text-dark font-monospace"><?= htmlspecialchars($certificate['certificate_number']) ?></strong></p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($certificate['certificate_number']) ?>" target="_blank" class="btn btn-gold-mgi">
                                            <i class="bi bi-award-fill"></i> View & Print Certificate
                                        </a>
                                        <a href="<?= BASE_URL ?>/verify-certificate.php?cert_no=<?= urlencode($certificate['certificate_number']) ?>" class="btn btn-outline-navy">
                                            <i class="bi bi-shield-check"></i> Verify Online
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($result['pass_status'] === 'FAIL'): ?>
                            <div class="p-4 rounded-4 bg-danger bg-opacity-10 border border-danger mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-exclamation-octagon-fill text-danger fs-2"></i>
                                    <div>
                                        <h6 class="fw-bold text-danger mb-1">Minimum Passing Percentage Not Met (Required: <?= $result['passing_percentage'] ?>%)</h6>
                                        <p class="small text-secondary mb-0">You scored <?= $result['percentage'] ?>%. You can review the study material notes and retake the online assessment.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                            <button onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="bi bi-printer-fill me-1"></i> Print Scorecard
                            </button>
                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>/exam-login.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Take Another Test
                                </a>
                                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary-mgi">
                                    <i class="bi bi-house-fill me-1"></i> Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>