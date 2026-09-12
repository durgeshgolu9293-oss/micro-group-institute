<?php
require_once __DIR__ . '/config/functions.php';

$cert_no = trim($_GET['cert_no'] ?? '');
if (empty($cert_no)) {
    die("Certificate number missing.");
}

$stmt = $pdo->prepare("SELECT * FROM certificates WHERE certificate_number = ? LIMIT 1");
$stmt->execute([$cert_no]);
$cert = $stmt->fetch();

if (!$cert) {
    die("Certificate not found.");
}

$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur / Fukganj, Uttar Pradesh');

// Date formatting
$issueDateFormatted = date('d F, Y', strtotime($cert['issue_date']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($cert['student_name']) ?> (<?= htmlspecialchars($cert['certificate_number']) ?>)</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/certificate.css">
</head>
<body>

<!-- Control Bar for Print/Download -->
<div class="no-print bg-dark py-3 text-center border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Home</a>
        <div class="text-white small">
            Official Course Completion Certificate: <strong class="text-warning"><?= htmlspecialchars($cert['certificate_number']) ?></strong>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Print / Download PDF
            </button>
            <a href="<?= BASE_URL ?>/verify-certificate.php?cert_no=<?= urlencode($cert['certificate_number']) ?>" target="_blank" class="btn btn-info btn-sm text-white fw-bold">
                <i class="bi bi-patch-check-fill me-1"></i> Public Verification Link
            </a>
        </div>
    </div>
</div>

<div class="certificate-outer-wrapper">
    <div class="certificate-container">
        <div class="cert-border-outer">
            <!-- Four Corner Accents -->
            <div class="cert-corner corner-tl"></div>
            <div class="cert-corner corner-tr"></div>
            <div class="cert-corner corner-bl"></div>
            <div class="cert-corner corner-br"></div>

            <div class="cert-border-inner">
                <!-- Watermark Background -->
                <div class="cert-watermark">
                    <svg viewBox="0 0 100 100" width="100%" height="100%">
                        <polygon points="50,15 85,32 50,49 15,32" fill="#0A192F" />
                        <rect x="25" y="52" width="50" height="30" rx="3" fill="#0A192F" />
                    </svg>
                </div>

                <!-- Certificate Header -->
                <div class="cert-header">
                    <div class="d-flex justify-content-center align-items-center gap-3 mb-1">
                        <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="Logo" class="cert-logo-badge">
                    </div>
                    <h1 class="cert-inst-name"><?= htmlspecialchars($site_name) ?></h1>
                    <div class="cert-inst-location">Reg. Center Location: <?= htmlspecialchars($location) ?> / Fukganj</div>
                    <div class="cert-main-title">Certificate of Completion</div>
                </div>

                <!-- Certificate Content -->
                <div class="cert-body">
                    <div class="cert-certify-text">This is to certify that</div>
                    <div class="cert-student-name"><?= htmlspecialchars($cert['student_name']) ?></div>
                    <p class="cert-paragraph">
                        Roll Number <strong><?= htmlspecialchars($cert['roll_number']) ?></strong> has successfully completed the prescribed curriculum and passed the examination for
                    </p>
                    <div class="cert-course-name text-uppercase">
                        <?= htmlspecialchars($cert['course_name']) ?>
                    </div>
                    <p class="cert-paragraph mt-1">
                        at Micro Group Computer Institute with meritorious performance and practical excellence.
                    </p>

                    <!-- Academic Details Grid -->
                    <div class="cert-details-grid mt-2">
                        <div class="cert-detail-item">Course Duration: <span><?= htmlspecialchars($cert['duration']) ?></span></div>
                        <div class="cert-detail-item">Score: <span><?= $cert['percentage'] ?>%</span></div>
                        <div class="cert-detail-item">Grade: <span><?= htmlspecialchars($cert['grade']) ?></span></div>
                        <div class="cert-detail-item">Issue Date: <span><?= $issueDateFormatted ?></span></div>
                    </div>
                </div>

                <!-- Certificate Footer & Signatures -->
                <div class="cert-footer">
                    <!-- Verification / Certificate ID block -->
                    <div class="text-start" style="font-size: 0.75rem; color: #4A5568;">
                        <div class="fw-bold text-dark">Certificate ID: <?= htmlspecialchars($cert['certificate_number']) ?></div>
                        <div>Status: <span class="text-success fw-bold">AUTHENTIC & VERIFIED</span></div>
                        <div class="mt-1" style="font-size: 0.68rem;">Verify online at: <?= BASE_URL ?>/verify-certificate.php</div>
                    </div>

                    <!-- Gold Seal -->
                    <div class="cert-seal-wrap">
                        <div class="cert-gold-seal">
                            <i class="bi bi-award-fill" style="font-size: 1.3rem;"></i>
                            <span>OFFICIAL</span>
                            <span>SEAL</span>
                        </div>
                    </div>

                    <!-- Manager Signature -->
                    <div class="cert-sig-block">
                        <img src="<?= BASE_URL ?>/assets/images/signature.svg" alt="Signature" class="cert-sig-img">
                        <div class="cert-sig-line"><?= htmlspecialchars($manager_name) ?></div>
                        <div class="cert-sig-title">Director & Institute Manager</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>