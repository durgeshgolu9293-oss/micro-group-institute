<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');
$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$phone = get_setting('phone', '+91 98765 43210');
$address = get_setting('branch_address', 'Main Market, Bhoopganj Payagpur, Bahraich, Uttar Pradesh');

// Fetch course details
$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$student['course_id']]);
$course = $cStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card - <?= htmlspecialchars($student['name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <style>
        .id-card-wrapper {
            width: 340px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            background: #fff;
            border: 2px solid #0F233E;
            margin: 0 auto;
        }
        .id-card-header {
            background: linear-gradient(135deg, #09172A 0%, #0284C7 100%);
            color: #fff;
            padding: 16px 12px;
            text-align: center;
        }
        .id-card-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid #F59E0B;
            object-fit: cover;
            margin-top: -45px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .id-card-wrapper { box-shadow: none !important; page-break-inside: avoid; }
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
            <h5 class="mb-0 fw-bold text-dark">Official Student Identity Card</h5>
            <div>
                <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                    <i class="bi bi-printer-fill me-1"></i> Print / Download ID Card
                </button>
            </div>
        </header>

        <main class="portal-content">
            <div class="row justify-content-center py-4">
                <div class="col-auto">
                    
                    <!-- ID Card Component -->
                    <div class="id-card-wrapper">
                        <!-- Top Header -->
                        <div class="id-card-header">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <div class="brand-icon-box" style="width: 26px; height: 26px; font-size: 0.8rem;">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-white text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">MICRO GROUP</h6>
                            </div>
                            <div class="text-warning small fw-semibold" style="font-size: 0.7rem; text-transform: uppercase;">Computer Institute &bull; <?= htmlspecialchars($location) ?></div>
                        </div>

                        <!-- Photo & Body -->
                        <div class="text-center px-4 pt-0 pb-4">
                            <img src="<?= BASE_URL ?>/assets/images/developer.jpg" alt="Photo" class="id-card-avatar">
                            
                            <h5 class="fw-bold text-dark mt-2 mb-0"><?= htmlspecialchars($student['name']) ?></h5>
                            <span class="badge bg-primary px-3 py-1 mt-1 mb-3">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>

                            <div class="text-start bg-light p-3 rounded-3 border small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Course:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($course['course_name'] ?? 'ADCA') ?> (<?= htmlspecialchars($course['short_name'] ?? 'ADCA') ?>)</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Father's Name:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($student['father_name'] ?? 'Shri Verma') ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Mobile:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($student['phone'] ?? '9876543210') ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Valid Till:</span>
                                    <strong class="text-success">2026 - 2027</strong>
                                </div>
                            </div>

                            <!-- Footer Seals -->
                            <div class="d-flex justify-content-between align-items-end mt-4 pt-2 border-top">
                                <div class="text-start">
                                    <div class="small text-muted" style="font-size: 0.65rem;">Student Sign</div>
                                    <div style="height: 25px; border-bottom: 1px dotted #999; width: 80px;"></div>
                                </div>
                                <div class="text-center">
                                    <i class="bi bi-qr-code fs-2 text-dark"></i>
                                </div>
                                <div class="text-end">
                                    <img src="<?= BASE_URL ?>/assets/images/signature.svg" alt="Sign" style="height: 20px;">
                                    <div class="small fw-bold text-dark" style="font-size: 0.65rem;">Director Sign</div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom Bar -->
                        <div class="bg-dark text-white p-2 text-center" style="font-size: 0.65rem;">
                            <?= htmlspecialchars($address) ?>
                        </div>
                    </div>

                    <div class="text-center mt-3 no-print">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Standard CR-80 Lanyard Card Size &bull; Print on Photo Glossy Card</small>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>