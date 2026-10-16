<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$completion = get_student_profile_completion($student);
$is_unlocked = $completion['is_complete'];

$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$phone = get_setting('phone', '+91 9792686570');
$email = get_setting('email', 'Deep2180411008@gmail.com');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur / Fukganj, Bahraich, Uttar Pradesh');

// Course details
$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$student['course_id']]);
$course = $cStmt->fetch();

$course_name = $course['course_name'] ?? 'Computer Application';
$course_code = $course['short_name'] ?? 'MGI';
$course_duration = $course['duration'] ?? '1 Year';

// Calculate session year
$admYear = !empty($student['admission_date']) ? date('Y', strtotime($student['admission_date'])) : date('Y');
$validSession = $admYear . ' - ' . ((int)$admYear + 1);

// Student photo URL
$avatarUrl = get_student_avatar_url($student);

// Public verification URL
$verifyUrl = BASE_URL . '/verify-certificate.php?cert_no=' . urlencode($student['roll_number']);

$page_title = "Official Student ID Card - " . htmlspecialchars($student['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">

    <style>
        /* ===================================================
           ID CARD MODERN STYLING (CR-80 Standard Format)
           =================================================== */
        :root {
            --id-navy-dark: #08162B;
            --id-navy-deep: #0F233E;
            --id-blue-accent: #0284C7;
            --id-cyan: #38BDF8;
            --id-gold: #F59E0B;
            --id-gold-dark: #D97706;
            --id-card-w: 340px;
            --id-card-h: 530px;
        }

        .id-cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 28px;
            margin: 0 auto;
        }

        .id-card-badge {
            width: var(--id-card-w);
            min-height: var(--id-card-h);
            background: #FFFFFF;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(8, 22, 43, 0.15), 0 3px 10px rgba(0,0,0,0.06);
            border: 1px solid #CBD5E1;
            position: relative;
            display: flex;
            flex-direction: column;
            user-select: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .id-card-badge:hover {
            box-shadow: 0 20px 40px rgba(8, 22, 43, 0.2);
        }

        /* Lanyard Hole Cutout Indicator */
        .lanyard-slot {
            width: 44px;
            height: 9px;
            background: #E2E8F0;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            margin: 8px auto 4px auto;
            position: relative;
            z-index: 5;
        }

        /* Front Card Header */
        .id-header {
            background: linear-gradient(135deg, var(--id-navy-dark) 0%, #0F2D59 55%, var(--id-blue-accent) 100%);
            color: #FFFFFF;
            padding: 10px 14px 12px 14px;
            text-align: center;
            position: relative;
        }

        .id-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #F59E0B, #FCD34D, #F59E0B);
        }

        .inst-title {
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 1px;
            line-height: 1.2;
            color: #FFFFFF;
        }

        .inst-tag {
            font-size: 0.65rem;
            color: #E2E8F0;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .inst-loc-bar {
            background: rgba(0, 0, 0, 0.35);
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.62rem;
            color: #FDE68A;
            margin-top: 4px;
            display: inline-block;
            font-weight: 600;
        }

        /* Avatar Container */
        .id-avatar-wrap {
            margin-top: -36px;
            position: relative;
            z-index: 4;
            display: inline-block;
        }

        .id-avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #FFFFFF;
            outline: 2px solid var(--id-gold);
            background: #F8FAFC;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .id-status-badge {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            background: #10B981;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Identity Body */
        .id-body {
            padding: 8px 18px 12px 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .student-name-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .roll-pill {
            display: inline-block;
            background: #0F233E;
            color: #38BDF8;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border: 1px solid #0284C7;
        }

        .course-badge-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 8px;
            padding: 4px 8px;
            margin-bottom: 10px;
        }

        .course-badge-title {
            font-size: 0.76rem;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .course-badge-sub {
            font-size: 0.65rem;
            color: #3B82F6;
        }

        /* Student Specs Grid */
        .id-specs-table {
            width: 100%;
            font-size: 0.72rem;
            margin-bottom: 8px;
            text-align: left;
            border-collapse: separate;
            border-spacing: 0 3px;
        }

        .id-specs-table td {
            padding: 2px 4px;
            vertical-align: middle;
        }

        .spec-label {
            color: #64748B;
            font-weight: 600;
            width: 38%;
        }

        .spec-val {
            color: #0F172A;
            font-weight: 700;
        }

        /* Signatures and QR Code row */
        .id-signatures-row {
            margin-top: auto;
            padding-top: 8px;
            border-top: 1px dashed #CBD5E1;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .sign-box {
            text-align: center;
            width: 82px;
        }

        .sign-line {
            height: 22px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            border-bottom: 1px solid #94A3B8;
            margin-bottom: 3px;
        }

        .sign-label {
            font-size: 0.62rem;
            color: #475569;
            font-weight: 600;
            line-height: 1;
        }

        .qr-center-box {
            width: 72px;
            height: 72px;
            background: #FFFFFF;
            padding: 3px;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        /* Bottom Band */
        .id-card-footer {
            background: var(--id-navy-dark);
            color: #CBD5E1;
            padding: 5px 8px;
            font-size: 0.6rem;
            text-align: center;
            line-height: 1.3;
        }

        /* ===================================================
           BACK SIDE OF ID CARD
           =================================================== */
        .id-back-header {
            background: linear-gradient(135deg, var(--id-navy-dark) 0%, #1E293B 100%);
            color: #FFFFFF;
            padding: 10px 14px;
            text-align: center;
            border-bottom: 3px solid var(--id-gold);
        }

        .id-back-body {
            padding: 12px 16px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
            font-size: 0.68rem;
            background: #FAFAFA;
        }

        .rules-list {
            padding-left: 14px;
            margin-bottom: 10px;
            color: #334155;
            line-height: 1.45;
        }

        .rules-list li {
            margin-bottom: 5px;
        }

        .inst-contact-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 0.65rem;
            color: #1E293B;
            margin-top: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .inst-contact-card div {
            margin-bottom: 2px;
        }

        .barcode-simulation {
            text-align: center;
            padding: 6px 0;
            margin-top: 6px;
            background: #FFFFFF;
            border-radius: 6px;
            border: 1px solid #E2E8F0;
        }

        .barcode-svg {
            height: 28px;
            width: 170px;
        }

        .barcode-text {
            font-family: monospace;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #0F172A;
            margin-top: 2px;
        }

        /* Locked / Blurred Overlay */
        .locked-wrapper {
            position: relative;
        }

        .locked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
            border-radius: 18px;
        }

        /* ===================================================
           PRINT MEDIA OPTIMIZATIONS (@media print)
           =================================================== */
        @media print {
            .no-print, .portal-sidebar, .portal-topbar, .dashboard-wrapper > aside, nav, footer {
                display: none !important;
            }

            body, html {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .portal-main, .portal-content {
                padding: 0 !important;
                margin: 0 !important;
            }

            .id-cards-container {
                display: flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: flex-start !important;
                gap: 12mm !important;
                padding-top: 15mm !important;
                page-break-inside: avoid !important;
            }

            .id-card-badge {
                box-shadow: none !important;
                border: 1px solid #94A3B8 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Cutting Guides / Crop Marks */
            .id-card-badge::before {
                content: '';
                position: absolute;
                top: -4px;
                left: -4px;
                right: -4px;
                bottom: -4px;
                border: 1px dashed #94A3B8;
                border-radius: 20px;
                pointer-events: none;
            }
        }
    </style>
</head>
<body style="background: #F1F5F9; min-height: 100vh;">

<div class="dashboard-wrapper">
    <div class="no-print">
        <?php require_once __DIR__ . '/../includes/student_sidebar.php'; ?>
    </div>

    <div class="portal-main">
        <header class="portal-topbar no-print">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" type="button" onclick="document.querySelector('.portal-sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">Official Student Identity Card (CR-80)</h5>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/student/profile.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person-gear me-1"></i> Edit Profile Info
                </a>
                <?php if ($is_unlocked): ?>
                    <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold shadow-sm">
                        <i class="bi bi-printer-fill me-1"></i> Print / Download PDF
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <main class="portal-content">
            <?php display_flash_message(); ?>

            <!-- IF PROFILE IS INCOMPLETE (CARD LOCKED) -->
            <?php if (!$is_unlocked): ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-4 text-center mx-auto" style="max-width: 780px;">
                    <div class="rounded-circle bg-warning bg-opacity-15 text-warning d-inline-flex p-4 fs-1 mb-3">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">आपका स्टूडेंट आईडी कार्ड अभी लॉक है</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                        संस्थान के नियमों के अनुसार, पहचान पत्र जारी करने के लिए विद्यार्थी की <strong>पासपोर्ट साइज फोटो, पिता का नाम, जन्मतिथि और स्थायी पता</strong> अनिवार्य है।
                    </p>

                    <div class="card bg-light border-0 rounded-3 p-4 text-start mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark"><i class="bi bi-list-check text-primary me-1"></i> प्रोफ़ाइल पूर्णता स्थिति (Profile Status):</span>
                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold"><?= $completion['percentage'] ?>% Completed</span>
                        </div>
                        <div class="progress progress-thin mb-3" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: <?= $completion['percentage'] ?>%"></div>
                        </div>

                        <div class="small fw-semibold text-secondary mb-2">शेष अनिवार्य विवरण जिन्हें भरना आवश्यक है:</div>
                        <ul class="mb-0 small text-danger ps-3">
                            <?php foreach ($completion['missing'] as $missLabel): ?>
                                <li class="mb-1"><strong><?= htmlspecialchars($missLabel) ?></strong></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div>
                        <a href="<?= BASE_URL ?>/student/profile.php" class="btn btn-primary-mgi btn-lg px-5 py-3 fw-bold shadow">
                            <i class="bi bi-pencil-square me-2"></i> अभी प्रोफ़ाइल पूरी करें और आईडी कार्ड अनलॉक करें
                        </a>
                    </div>
                </div>

                <!-- Blurred Card Preview for preview motivation -->
                <div class="text-center mb-3">
                    <span class="badge bg-secondary px-3 py-2"><i class="bi bi-eye-fill me-1"></i> आईडी कार्ड पूर्वावलोकन (Preview Template)</span>
                </div>
            <?php else: ?>
                <!-- Card View Controls -->
                <div class="no-print d-flex flex-wrap justify-content-between align-items-center gap-3 bg-white p-3 rounded-4 shadow-sm mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Official ID Card Active</span>
                        <span class="text-muted small">Standard CR-80 Lanyard Card</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-dark active" id="btn-show-both" onclick="setViewMode('both')">
                                <i class="bi bi-layout-split me-1"></i> Both Sides
                            </button>
                            <button type="button" class="btn btn-outline-dark" id="btn-show-front" onclick="setViewMode('front')">
                                Front Only
                            </button>
                            <button type="button" class="btn btn-outline-dark" id="btn-show-back" onclick="setViewMode('back')">
                                Back Only
                            </button>
                        </div>

                        <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-printer-fill me-1"></i> Print ID Card
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ==============================================
                 ID CARDS CONTAINER (FRONT & BACK)
                 ============================================== -->
            <div class="id-cards-container mb-5 <?= !$is_unlocked ? 'locked-wrapper' : '' ?>">

                <?php if (!$is_unlocked): ?>
                    <div class="locked-overlay">
                        <i class="bi bi-lock-fill text-warning" style="font-size: 3.5rem;"></i>
                        <h4 class="fw-bold text-dark mt-2">Card Locked</h4>
                        <p class="text-muted small mb-3">Complete your profile to unlock and print your high-definition card.</p>
                        <a href="<?= BASE_URL ?>/student/profile.php" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-unlock-fill me-1"></i> Fill Profile Details
                        </a>
                    </div>
                <?php endif; ?>

                <!-- ----------------------------------------------------
                     FRONT SIDE OF ID CARD
                     ---------------------------------------------------- -->
                <div class="id-card-badge" id="card-front">
                    <!-- Lanyard Hole -->
                    <div class="lanyard-slot"></div>

                    <!-- Header -->
                    <div class="id-header">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="MGI Logo" style="width: 28px; height: 28px;">
                            <div class="text-start">
                                <div class="inst-title">MICRO GROUP</div>
                                <div class="inst-tag">OF COMPUTER INSTITUTE</div>
                            </div>
                        </div>
                        <div class="inst-loc-bar">
                            <i class="bi bi-patch-check-fill text-warning me-1"></i> ISO 9001:2015 &bull; <?= htmlspecialchars($location) ?>
                        </div>
                    </div>

                    <!-- Photo & Body -->
                    <div class="id-body">
                        <div class="id-avatar-wrap">
                            <img src="<?= $avatarUrl ?>" alt="Student Photo" class="id-avatar-img">
                            <span class="id-status-badge" title="Active Student"></span>
                        </div>

                        <div class="student-name-title mt-2"><?= htmlspecialchars($student['name']) ?></div>
                        <div>
                            <span class="roll-pill">ROLL: <?= htmlspecialchars($student['roll_number']) ?></span>
                        </div>

                        <div class="course-badge-box">
                            <div class="course-badge-title"><?= htmlspecialchars($course_name) ?></div>
                            <div class="course-badge-sub"><?= htmlspecialchars($course_code) ?> &bull; Duration: <?= htmlspecialchars($course_duration) ?></div>
                        </div>

                        <!-- Details Table -->
                        <table class="id-specs-table">
                            <tr>
                                <td class="spec-label">Father's Name:</td>
                                <td class="spec-val"><?= htmlspecialchars($student['father_name'] ?: 'Not Specified') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Date of Birth:</td>
                                <td class="spec-val"><?= !empty($student['dob']) ? date('d/m/Y', strtotime($student['dob'])) : 'Not Specified' ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Blood Group:</td>
                                <td class="spec-val"><?= htmlspecialchars($student['blood_group'] ?: 'N/A') ?> &bull; <?= htmlspecialchars($student['gender'] ?: 'Male') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Mobile No:</td>
                                <td class="spec-val"><?= htmlspecialchars($student['mobile'] ?: $student['phone'] ?: 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td class="spec-label">Valid Session:</td>
                                <td class="spec-val text-success"><?= $validSession ?></td>
                            </tr>
                        </table>

                        <!-- Signatures & Dynamic QR Code -->
                        <div class="id-signatures-row">
                            <div class="sign-box">
                                <div class="sign-line"></div>
                                <div class="sign-label">Holder Sign</div>
                            </div>

                            <div class="qr-center-box" id="qrcode-front" title="Scan to Verify Student Online"></div>

                            <div class="sign-box">
                                <div class="sign-line">
                                    <img src="<?= BASE_URL ?>/assets/images/signature.svg" alt="Director Signature" style="height: 18px; max-width: 70px;">
                                </div>
                                <div class="sign-label fw-bold text-dark">Director</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Bar -->
                    <div class="id-card-footer">
                        <i class="bi bi-geo-alt-fill text-warning me-1"></i> Main Market, Bhoopganj Payagpur / Fukganj, UP
                    </div>
                </div>

                <!-- ----------------------------------------------------
                     BACK SIDE OF ID CARD
                     ---------------------------------------------------- -->
                <div class="id-card-badge" id="card-back">
                    <!-- Lanyard Hole -->
                    <div class="lanyard-slot"></div>

                    <!-- Back Header -->
                    <div class="id-back-header">
                        <div class="fw-bold text-uppercase small letter-spacing-1">Terms & Campus Regulations</div>
                        <div class="text-warning small" style="font-size: 0.62rem;">नियम एवं महत्वपूर्ण निर्देश</div>
                    </div>

                    <!-- Back Body -->
                    <div class="id-back-body">
                        <ol class="rules-list">
                            <li>यह पहचान पत्र केवल अधिकृत विद्यार्थी के लिए है एवं गैर-हस्तांतरणीय (Non-transferable) है।</li>
                            <li>कक्षा, प्रैक्टिकल कंप्यूटर लैब और ऑनलाइन परीक्षा के दौरान यह कार्ड साथ रखना अनिवार्य है।</li>
                            <li>आईडी कार्ड खो जाने पर तत्काल संस्थान कार्यालय को सूचित कर डुप्लीकेट कार्ड प्राप्त करें।</li>
                            <li>कार्ड के आगे दिए गए क्यूआर कोड को स्कैन करके प्रमाणिकता ऑनलाइन सत्यापित की जा सकती है।</li>
                        </ol>

                        <!-- Student Address Record -->
                        <div class="p-2 mb-2 bg-white rounded border" style="font-size: 0.63rem;">
                            <div class="text-muted fw-bold mb-1"><i class="bi bi-house-door-fill text-primary me-1"></i> विद्यार्थी का स्थायी पता (Address):</div>
                            <div class="text-dark fw-semibold" style="line-height: 1.3;">
                                <?= nl2br(htmlspecialchars($student['address'] ?: 'Bhoopganj Payagpur, Bahraich, Uttar Pradesh')) ?>
                            </div>
                            <?php if (!empty($student['emergency_contact'])): ?>
                                <div class="text-danger mt-1"><strong>Emergency:</strong> <?= htmlspecialchars($student['emergency_contact']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Barcode Section -->
                        <div class="barcode-simulation">
                            <svg class="barcode-svg" viewBox="0 0 200 40">
                                <!-- Real Code 128 / 39 SVG Barcode pattern representing Roll Number -->
                                <rect x="5" y="2" width="2" height="34" fill="#000" />
                                <rect x="9" y="2" width="4" height="34" fill="#000" />
                                <rect x="16" y="2" width="1" height="34" fill="#000" />
                                <rect x="20" y="2" width="3" height="34" fill="#000" />
                                <rect x="26" y="2" width="5" height="34" fill="#000" />
                                <rect x="34" y="2" width="2" height="34" fill="#000" />
                                <rect x="39" y="2" width="4" height="34" fill="#000" />
                                <rect x="46" y="2" width="1" height="34" fill="#000" />
                                <rect x="50" y="2" width="3" height="34" fill="#000" />
                                <rect x="56" y="2" width="4" height="34" fill="#000" />
                                <rect x="63" y="2" width="2" height="34" fill="#000" />
                                <rect x="68" y="2" width="5" height="34" fill="#000" />
                                <rect x="76" y="2" width="1" height="34" fill="#000" />
                                <rect x="80" y="2" width="3" height="34" fill="#000" />
                                <rect x="86" y="2" width="2" height="34" fill="#000" />
                                <rect x="91" y="2" width="4" height="34" fill="#000" />
                                <rect x="98" y="2" width="3" height="34" fill="#000" />
                                <rect x="104" y="2" width="1" height="34" fill="#000" />
                                <rect x="108" y="2" width="5" height="34" fill="#000" />
                                <rect x="116" y="2" width="2" height="34" fill="#000" />
                                <rect x="121" y="2" width="4" height="34" fill="#000" />
                                <rect x="128" y="2" width="2" height="34" fill="#000" />
                                <rect x="133" y="2" width="3" height="34" fill="#000" />
                                <rect x="139" y="2" width="5" height="34" fill="#000" />
                                <rect x="147" y="2" width="1" height="34" fill="#000" />
                                <rect x="151" y="2" width="3" height="34" fill="#000" />
                                <rect x="157" y="2" width="4" height="34" fill="#000" />
                                <rect x="164" y="2" width="2" height="34" fill="#000" />
                                <rect x="169" y="2" width="4" height="34" fill="#000" />
                                <rect x="176" y="2" width="2" height="34" fill="#000" />
                                <rect x="181" y="2" width="5" height="34" fill="#000" />
                                <rect x="189" y="2" width="2" height="34" fill="#000" />
                                <rect x="194" y="2" width="3" height="34" fill="#000" />
                            </svg>
                            <div class="barcode-text"><?= htmlspecialchars($student['roll_number']) ?></div>
                        </div>

                        <!-- Institute Contact Details -->
                        <div class="inst-contact-card mt-2">
                            <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($site_name) ?></div>
                            <div><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($address) ?></div>
                            <div><i class="bi bi-telephone-fill text-success me-1"></i> Helpline: <?= htmlspecialchars($phone) ?></div>
                            <div><i class="bi bi-envelope-fill text-warning me-1"></i> <?= htmlspecialchars($email) ?></div>
                        </div>
                    </div>

                    <!-- Back Footer -->
                    <div class="id-card-footer">
                        FOUND THIS CARD? PLEASE RETURN TO INSTITUTE OFFICE
                    </div>
                </div>

            </div>

            <!-- Print Guide Helper (Hidden on Print) -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mx-auto no-print text-center mb-5" style="max-width: 720px;">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-printer text-primary me-2"></i> प्रिंट व लेमिनेशन गाइड (Printing & PVC Instructions)</h6>
                <p class="text-muted small mb-3">
                    यह आईडी कार्ड आधिकारिक <strong>CR-80 (54mm × 86mm)</strong> मानक माप पर तैयार किया गया है। प्रिंट करते समय ब्राउज़र प्रिंटर डायलॉग में <strong>"More Settings"</strong> पर जाकर <strong>"Background Graphics"</strong> अवश्य ऑन रखें।
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button onclick="window.print()" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-printer-fill me-1"></i> अभी प्रिंट करें (Print Both Sides)
                    </button>
                    <a href="<?= BASE_URL ?>/student/profile.php" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-pencil-square me-1"></i> फोटो या जानकारी बदलें
                    </a>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/qrcode.min.js"></script>
<script>
// Dynamic Scannable QR Code Generation
document.addEventListener("DOMContentLoaded", function() {
    const qrTarget = document.getElementById("qrcode-front");
    if (qrTarget) {
        new QRCode(qrTarget, {
            text: "<?= $verifyUrl ?>",
            width: 66,
            height: 66,
            colorDark : "#08162B",
            colorLight : "#FFFFFF",
            correctLevel : QRCode.CorrectLevel.M
        });
    }
});

// View mode switcher: both, front, back
function setViewMode(mode) {
    const front = document.getElementById('card-front');
    const back = document.getElementById('card-back');
    const btnBoth = document.getElementById('btn-show-both');
    const btnFront = document.getElementById('btn-show-front');
    const btnBack = document.getElementById('btn-show-back');

    btnBoth.classList.remove('active');
    btnFront.classList.remove('active');
    btnBack.classList.remove('active');

    if (mode === 'front') {
        front.style.display = 'flex';
        back.style.display = 'none';
        btnFront.classList.add('active');
    } else if (mode === 'back') {
        front.style.display = 'none';
        back.style.display = 'flex';
        btnBack.classList.add('active');
    } else {
        front.style.display = 'flex';
        back.style.display = 'flex';
        btnBoth.classList.add('active');
    }
}
</script>
</body>
</html>