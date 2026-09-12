<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Check Examination Result";

$roll_number = trim($_GET['roll_number'] ?? '');
$searched = !empty($roll_number);
$results = [];

if ($searched) {
    $stmt = $pdo->prepare("SELECT r.*, a.student_name, a.course_name, a.total_questions, a.attempted, a.correct_answers, a.wrong_answers, e.exam_title, c.certificate_number 
                          FROM results r 
                          JOIN exam_attempts a ON r.attempt_id = a.id 
                          JOIN exams e ON r.exam_id = e.id 
                          LEFT JOIN certificates c ON r.id = c.result_id 
                          WHERE r.roll_number = ? 
                          ORDER BY r.id DESC");
    $stmt->execute([$roll_number]);
    $results = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-info text-dark px-3 py-2 fw-bold text-uppercase mb-3">Student Scorecards</span>
        <h1 class="display-5 fw-bold text-white mb-2">Check Online Examination Result</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto" style="max-width: 700px;">
            Enter your student Roll Number to view and print your completed exam results and scorecards.
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-5">
                    <form method="GET" action="" class="row g-3 align-items-center">
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">Enter Student Roll Number *</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="roll_number" class="form-control" placeholder="e.g. MG202601" value="<?= htmlspecialchars($roll_number) ?>" required autofocus>
                            </div>
                        </div>
                        <div class="col-md-3 mt-md-auto">
                            <button type="submit" class="btn btn-primary-mgi btn-lg w-100 py-3">
                                Search Result
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Results Output -->
                <?php if ($searched): ?>
                    <?php if (empty($results)): ?>
                        <div class="alert alert-warning p-4 rounded-4 shadow-sm text-center">
                            <i class="bi bi-file-earmark-x fs-1 text-warning mb-2 d-block"></i>
                            <h5 class="fw-bold">No exam results found for Roll Number: <?= htmlspecialchars($roll_number) ?></h5>
                            <p class="small text-muted mb-0">Please verify your Roll Number or complete an exam first.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($results as $res): ?>
                            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($res['exam_title']) ?></h5>
                                        <span class="text-secondary small">Course: <strong><?= htmlspecialchars($res['course_name']) ?></strong> &bull; Date: <?= date('d M, Y', strtotime($res['date'])) ?></span>
                                    </div>
                                    <span class="badge <?= ($res['pass_status'] === 'PASS') ? 'bg-success' : 'bg-danger' ?> px-3 py-2 fs-6">
                                        <?= $res['pass_status'] ?>
                                    </span>
                                </div>

                                <div class="row g-3 text-center mb-3">
                                    <div class="col-3">
                                        <div class="bg-light p-2 rounded border">
                                            <span class="text-muted small d-block">Score</span>
                                            <strong class="text-dark"><?= $res['obtained_marks'] ?> / <?= $res['total_marks'] ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-light p-2 rounded border">
                                            <span class="text-muted small d-block">Percentage</span>
                                            <strong class="text-primary"><?= $res['percentage'] ?>%</strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-light p-2 rounded border">
                                            <span class="text-muted small d-block">Grade</span>
                                            <strong class="text-success"><?= $res['grade'] ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-light p-2 rounded border">
                                            <span class="text-muted small d-block">Correct Qs</span>
                                            <strong class="text-success"><?= $res['correct_answers'] ?> / <?= $res['total_questions'] ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-end pt-2 border-top">
                                    <a href="<?= BASE_URL ?>/exam-result.php?attempt_id=<?= $res['attempt_id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-printer me-1"></i> Print Result
                                    </a>
                                    <?php if (!empty($res['certificate_number'])): ?>
                                        <a href="<?= BASE_URL ?>/certificate.php?cert_no=<?= urlencode($res['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-gold-mgi">
                                            <i class="bi bi-award-fill me-1"></i> View Certificate
                                        </a>
                                        <a href="<?= BASE_URL ?>/verify-certificate.php?cert_no=<?= urlencode($res['certificate_number']) ?>" class="btn btn-sm btn-outline-navy">
                                            <i class="bi bi-shield-check me-1"></i> Verify Certificate
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>