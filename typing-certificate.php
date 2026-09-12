<?php
require_once __DIR__ . '/config/functions.php';

$cert_no = trim($_GET['cert_no'] ?? '');
$result = null;

if (!empty($cert_no)) {
    $stmt = $pdo->prepare("SELECT * FROM typing_results WHERE certificate_no = ? LIMIT 1");
    $stmt->execute([$cert_no]);
    $result = $stmt->fetch();
}

$candidate_name = $result['candidate_name'] ?? ($_GET['name'] ?? 'Rahul Kumar');
$net_wpm = $result['net_wpm'] ?? (int)($_GET['wpm'] ?? 35);
$accuracy = $result['accuracy'] ?? (float)($_GET['acc'] ?? 98.0);
$grade = $result['grade'] ?? ($_GET['grade'] ?? 'A+');
$duration = $result['duration_mins'] ?? 1;
$date = $result ? date('d F, Y', strtotime($result['created_at'])) : date('d F, Y');
$certNoDisplay = $cert_no ?: 'TYP-2026-00001';

$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Certificate - <?= htmlspecialchars($candidate_name) ?> (<?= htmlspecialchars($certNoDisplay) ?>)</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Alex+Brush&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            background-color: #0F172A;
            margin: 0;
            padding: 30px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .cert-toolbar {
            width: 1040px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .premium-cert-frame {
            width: 1040px;
            height: 735px;
            background: #FFFFFF;
            position: relative;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            border-radius: 4px;
            overflow: hidden;
            padding: 16px;
        }
        .cert-border-outer {
            width: 100%;
            height: 100%;
            border: 4px double #C59B27;
            padding: 10px;
            position: relative;
            background: #FCFBF7;
        }
        .cert-border-inner {
            width: 100%;
            height: 100%;
            border: 1.5px solid #0F233E;
            padding: 30px 45px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background: radial-gradient(circle at center, #FFFFFF 0%, #FAFAF7 100%);
        }
        
        /* Security Watermark */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Cinzel', serif;
            font-size: 7rem;
            color: rgba(15, 35, 62, 0.025);
            font-weight: 900;
            letter-spacing: 12px;
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        /* Header Elements */
        .cert-top-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 14px;
        }
        .cert-institute-title {
            font-family: 'Cinzel', serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: #09172A;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1.2;
        }
        .cert-institute-sub {
            font-size: 0.85rem;
            color: #C59B27;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 3px;
        }
        .cert-badge-serial {
            background: #09172A;
            color: #F8FAFC;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 1px;
            border: 1px solid #C59B27;
        }

        /* Main Content */
        .cert-center-body {
            text-align: center;
            position: relative;
            z-index: 2;
            margin: auto 0;
        }
        .cert-award-heading {
            font-family: 'Cinzel', serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #0284C7;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .cert-tag-sub {
            font-size: 0.95rem;
            color: #64748B;
            font-style: italic;
            margin-bottom: 12px;
        }
        .cert-student-hero-name {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #09172A;
            border-bottom: 2px solid #C59B27;
            display: inline-block;
            padding: 0 40px 6px;
            margin-bottom: 14px;
            letter-spacing: 0.5px;
        }
        .cert-statement-p {
            font-size: 1rem;
            color: #334155;
            max-width: 820px;
            margin: 0 auto 20px;
            line-height: 1.65;
        }

        /* Stats Pill */
        .typing-stats-pill {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            border: 1.5px solid #C59B27;
            border-radius: 50px;
            padding: 10px 32px;
            box-shadow: 0 8px 20px rgba(197, 155, 39, 0.15);
            gap: 28px;
        }
        .stat-col {
            text-align: center;
        }
        .stat-col .stat-title {
            font-size: 0.72rem;
            color: #64748B;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .stat-col .stat-data {
            font-size: 1.35rem;
            font-weight: 800;
            color: #09172A;
            line-height: 1;
        }
        .stat-col .stat-data.speed {
            color: #0284C7;
        }
        .stat-col .stat-data.grade {
            color: #C59B27;
        }

        /* Footer & Signatures */
        .cert-bottom-footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            padding-top: 15px;
            border-top: 1.5px solid #E2E8F0;
        }
        .cert-foot-meta {
            text-align: left;
        }
        .cert-foot-meta .meta-lbl {
            font-size: 0.75rem;
            color: #64748B;
            font-weight: 600;
        }
        .cert-foot-meta .meta-val {
            font-size: 0.9rem;
            color: #09172A;
            font-weight: 700;
        }

        /* Gold Seal */
        .gold-seal-badge {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: radial-gradient(circle at center, #F59E0B 0%, #D97706 70%, #B45309 100%);
            border: 3px double #FFFFFF;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            text-align: center;
        }
        .gold-seal-badge i {
            font-size: 1.4rem;
            line-height: 1;
        }
        .gold-seal-badge span {
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Signature */
        .cert-sign-wrapper {
            text-align: right;
            width: 200px;
        }
        .cert-sign-img {
            height: 48px;
            margin-bottom: -6px;
        }
        .cert-sign-divider {
            height: 1.5px;
            background: #09172A;
            margin-bottom: 4px;
        }
        .cert-sign-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: #09172A;
            margin: 0;
        }
        .cert-sign-role {
            font-size: 0.75rem;
            color: #64748B;
            font-weight: 600;
            margin: 0;
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            .cert-toolbar {
                display: none !important;
            }
            .premium-cert-frame {
                width: 100vw !important;
                height: 100vh !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 10mm !important;
            }
        }
    </style>
</head>
<body>

<div class="cert-toolbar">
    <a href="<?= BASE_URL ?>/typing-test.php" class="btn btn-outline-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Typing Test
    </a>
    <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold px-4 py-2">
        <i class="bi bi-printer-fill me-1"></i> Print / Download PDF
    </button>
</div>

<!-- Main Certificate Container -->
<div class="premium-cert-frame">
    <div class="cert-border-outer">
        <div class="cert-border-inner">

            <div class="cert-watermark">MICRO GROUP</div>

            <!-- Header -->
            <div class="cert-top-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-dark text-warning p-2 fs-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border: 2px solid #C59B27;">
                        <i class="bi bi-keyboard-fill"></i>
                    </div>
                    <div>
                        <h1 class="cert-institute-title"><?= htmlspecialchars($site_name) ?></h1>
                        <div class="cert-institute-sub"><?= htmlspecialchars($location) ?> &bull; Center of Digital Excellence</div>
                    </div>
                </div>
                <div>
                    <div class="cert-badge-serial">CERTIFICATE NO: <?= htmlspecialchars($certNoDisplay) ?></div>
                </div>
            </div>

            <!-- Body -->
            <div class="cert-center-body">
                <div class="cert-award-heading">CERTIFICATE OF TYPING PROFICIENCY</div>
                <div class="cert-tag-sub">This is to certify that</div>

                <div class="cert-student-hero-name"><?= htmlspecialchars($candidate_name) ?></div>

                <p class="cert-statement-p">
                    has successfully qualified the <strong>National Speed Keyboarding & Typing Assessment (English)</strong> conducted under standardized examination protocol at <strong><?= htmlspecialchars($site_name) ?> (<?= htmlspecialchars($location) ?>)</strong>.
                </p>

                <!-- Performance Metrics Pill -->
                <div class="typing-stats-pill">
                    <div class="stat-col">
                        <div class="stat-title">Net Speed</div>
                        <div class="stat-data speed"><?= $net_wpm ?> <span style="font-size: 0.85rem; font-weight: 600;">WPM</span></div>
                    </div>
                    <div class="border-start border-secondary opacity-25" style="height: 28px;"></div>
                    <div class="stat-col">
                        <div class="stat-title">Accuracy</div>
                        <div class="stat-data"><?= $accuracy ?>%</div>
                    </div>
                    <div class="border-start border-secondary opacity-25" style="height: 28px;"></div>
                    <div class="stat-col">
                        <div class="stat-title">Duration</div>
                        <div class="stat-data"><?= $duration ?> Min</div>
                    </div>
                    <div class="border-start border-secondary opacity-25" style="height: 28px;"></div>
                    <div class="stat-col">
                        <div class="stat-title">Grade Awarded</div>
                        <div class="stat-data grade"><?= htmlspecialchars($grade) ?></div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="cert-bottom-footer">
                <div class="cert-foot-meta">
                    <div class="meta-lbl">Date of Assessment:</div>
                    <div class="meta-val"><?= $date ?></div>
                    <div class="text-muted small mt-1">Verification: Online Authenticated</div>
                </div>

                <!-- Gold Verified Seal -->
                <div class="gold-seal-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>VERIFIED</span>
                </div>

                <!-- Signature of DK Singh -->
                <div class="cert-sign-wrapper">
                    <img src="<?= BASE_URL ?>/assets/images/signature.svg" alt="DK Singh Signature" class="cert-sign-img">
                    <div class="cert-sign-divider"></div>
                    <div class="cert-sign-name">DK Singh</div>
                    <div class="cert-sign-role">Director & Authorized Manager</div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>