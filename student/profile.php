<?php
require_once __DIR__ . '/../config/functions.php';
require_student_login();

$student = get_logged_student();
$page_title = "My Profile & ID Card Setup";

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? $student['name']);
    $father_name = trim($_POST['father_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? 'Male');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Password change fields
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name)) {
        $err = 'कृपया अपना पूरा नाम दर्ज करें। (Full name cannot be empty)';
    } elseif (empty($father_name)) {
        $err = 'कृपया पिता का नाम दर्ज करें। (Father\'s name is required for ID Card)';
    } elseif (empty($mobile)) {
        $err = 'कृपया वैध मोबाइल नंबर दर्ज करें। (Mobile number is required)';
    } elseif (empty($dob)) {
        $err = 'कृपया अपनी जन्मतिथि (Date of Birth) चुनें।';
    } elseif (empty($address)) {
        $err = 'कृपया अपना पूरा स्थायी पता दर्ज करें। (Address is required for ID Card)';
    } else {
        // Photo upload handling
        $profile_image = $student['profile_image'] ?? 'default_avatar.svg';

        if (!empty($_FILES['photo']['name'])) {
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            $file_info = pathinfo($_FILES['photo']['name']);
            $ext = strtolower($file_info['extension'] ?? '');

            if (!in_array($ext, $allowed_exts)) {
                $err = 'अमान्य फोटो फॉर्मेट! कृपया केवल JPG, JPEG, PNG या WEBP फोटो ही अपलोड करें।';
            } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $err = 'फोटो का साइज 5MB से कम होना चाहिए।';
            } else {
                $upload_dir = __DIR__ . '/../assets/uploads/students';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_filename = 'student_' . preg_replace('/[^a-zA-Z0-9]/', '', $student['roll_number']) . '_' . time() . '.' . $ext;
                $target_file = $upload_dir . '/' . $new_filename;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $profile_image = $new_filename;
                } else {
                    $err = 'फोटो अपलोड करने में समस्या हुई। कृपया पुनः प्रयास करें।';
                }
            }
        }

        // Handle optional password change
        $password_update = false;
        $new_hash = '';
        if (!$err && !empty($new_password)) {
            if (empty($current_password)) {
                $err = 'नया पासवर्ड सेट करने के लिए कृपया अपना वर्तमान (Current) पासवर्ड दर्ज करें।';
            } elseif (!password_verify($current_password, $student['password'])) {
                $err = 'आपका वर्तमान पासवर्ड गलत है! (Incorrect current password)';
            } elseif (strlen($new_password) < 6) {
                $err = 'नया पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।';
            } elseif ($new_password !== $confirm_password) {
                $err = 'नया पासवर्ड और कन्फर्म पासवर्ड मेल नहीं खा रहे हैं।';
            } else {
                $password_update = true;
                $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            }
        }

        // If no errors, update database
        if (!$err) {
            try {
                if ($password_update) {
                    $stmt = $pdo->prepare("UPDATE students SET name = ?, father_name = ?, dob = ?, gender = ?, blood_group = ?, mobile = ?, emergency_contact = ?, address = ?, profile_image = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $father_name, $dob, $gender, $blood_group, $mobile, $emergency_contact, $address, $profile_image, $new_hash, $student['id']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE students SET name = ?, father_name = ?, dob = ?, gender = ?, blood_group = ?, mobile = ?, emergency_contact = ?, address = ?, profile_image = ? WHERE id = ?");
                    $stmt->execute([$name, $father_name, $dob, $gender, $blood_group, $mobile, $emergency_contact, $address, $profile_image, $student['id']]);
                }

                // Refresh student data
                $student = get_logged_student();
                $completion = get_student_profile_completion($student);

                if ($completion['is_complete']) {
                    $msg = '🎉 बधाई हो! आपकी प्रोफाइल 100% पूरी हो गई है और आपका आधिकारिक आईडी कार्ड जनरेट हो चुका है!';
                } else {
                    $msg = 'प्रोफ़ाइल सफलतापूर्वक अपडेट हो गई है।';
                }
            } catch (Exception $e) {
                $err = 'डेटाबेस अपडेट में त्रुटि: ' . $e->getMessage();
            }
        }
    }
}

$completion = get_student_profile_completion($student);
$avatarUrl = get_student_avatar_url($student);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile & ID Card Setup - Micro Group</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <style>
        .photo-preview-box {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0284C7;
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25);
            background: #F8FAFC;
        }
        .progress-thin {
            height: 10px;
            border-radius: 10px;
        }
        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0F172A;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/student_sidebar.php'; ?>
    <div class="portal-main">
        <header class="portal-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" type="button" onclick="document.querySelector('.portal-sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">Student Profile & ID Card Details</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2">Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
                <?php if ($completion['is_complete']): ?>
                    <a href="<?= BASE_URL ?>/student/id-card.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
                        <i class="bi bi-person-badge-fill me-1"></i> View ID Card
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <main class="portal-content">
            <?php display_flash_message(); ?>

            <!-- Profile Completion Status Banner -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 text-white" style="background: linear-gradient(135deg, #09172A 0%, #0369A1 100%);">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="<?= $avatarUrl ?>" alt="Student Photo" class="rounded-circle border border-3 border-warning" style="width: 68px; height: 68px; object-fit: cover; background: #fff;">
                            <div>
                                <h5 class="fw-bold mb-1 text-white"><?= htmlspecialchars($student['name']) ?></h5>
                                <div class="text-light opacity-75 small">
                                    Course: <strong class="text-warning"><?= htmlspecialchars($student['course_name'] ?? 'Computer Application') ?></strong> &bull; Roll: <strong><?= htmlspecialchars($student['roll_number']) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="fw-semibold">ID Card Profile Completion Status:</span>
                                <span class="fw-bold <?= ($completion['percentage'] === 100) ? 'text-success' : 'text-warning' ?>"><?= $completion['percentage'] ?>% Complete</span>
                            </div>
                            <div class="progress progress-thin bg-dark bg-opacity-50">
                                <div class="progress-bar <?= ($completion['percentage'] === 100) ? 'bg-success' : 'bg-warning' ?>" role="progressbar" style="width: <?= $completion['percentage'] ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-md-end">
                        <?php if ($completion['is_complete']): ?>
                            <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-light border-opacity-25 text-center">
                                <i class="bi bi-patch-check-fill text-warning fs-1 d-block mb-1"></i>
                                <span class="badge bg-success mb-2 px-3 py-1">ID Card Active</span>
                                <a href="<?= BASE_URL ?>/student/id-card.php" class="btn btn-warning w-100 fw-bold btn-sm">
                                    <i class="bi bi-printer-fill me-1"></i> Print / Download ID Card
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-light border-opacity-25 text-center">
                                <i class="bi bi-lock-fill text-warning fs-2 d-block mb-1"></i>
                                <div class="small text-light mb-2">आईडी कार्ड पाने के लिए नीचे दी गई सभी अनिवार्य जानकारी भरें</div>
                                <span class="badge bg-danger">Pending Details (<?= count($completion['missing']) ?>)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($msg): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 py-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <strong><?= $msg ?></strong>
                    <?php if ($completion['is_complete']): ?>
                        <div class="mt-2">
                            <a href="<?= BASE_URL ?>/student/id-card.php" class="btn btn-success btn-sm fw-bold">
                                <i class="bi bi-person-badge-fill me-1"></i> अभी अपना डिजिटल आईडी कार्ड देखें (View ID Card)
                            </a>
                        </div>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($err): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 py-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <strong>त्रुटि: </strong><?= $err ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Missing Fields Reminder if incomplete -->
            <?php if (!$completion['is_complete']): ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> आईडी कार्ड जनरेट करने हेतु आवश्यक शेष जानकारी:</div>
                    <ul class="mb-0 small ps-3">
                        <?php foreach ($completion['missing'] as $missKey => $missLabel): ?>
                            <li><?= htmlspecialchars($missLabel) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Main Profile Form -->
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-5">
                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <!-- Section 1: Photo & Academic Info -->
                    <div class="form-section-title">
                        <i class="bi bi-camera-fill text-primary me-1"></i> 1. पासपोर्ट साइज फोटो व शैक्षणिक विवरण (Photo & Academic Info)
                    </div>

                    <div class="row align-items-center mb-4 g-4">
                        <div class="col-md-3 text-center">
                            <img id="photo-preview" src="<?= $avatarUrl ?>" alt="Student Photo" class="photo-preview-box mb-2">
                            <div class="small text-muted">Passport Photo Preview</div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small">
                                अपनी पासपोर्ट साइज फोटो चुनें <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="photo" id="photo-input" class="form-control form-control-lg mb-2" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small text-muted">
                                <i class="bi bi-info-circle me-1"></i> पासपोर्ट साइज फोटो, साफ चेहरा, सफेद या हल्के बैकग्राउंड के साथ। (अधिकतम साइज 5MB, फॉर्मेट: JPG, PNG, WEBP)
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">विद्यार्थी का पूरा नाम (Full Name) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">रोल नंबर (Roll Number)</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['roll_number']) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">कोर्स (Enrolled Course)</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['course_name'] ?? 'ADCA') ?>" readonly>
                        </div>
                    </div>

                    <!-- Section 2: Personal Details Required on ID Card -->
                    <div class="form-section-title mt-4">
                        <i class="bi bi-person-lines-fill text-primary me-1"></i> 2. व्यक्तिगत विवरण (Personal Details for ID Card)
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">पिता का नाम (Father's Name) <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" placeholder="उदा. Shri Ramesh Kumar" value="<?= htmlspecialchars($student['father_name'] ?? '') ?>" required>
                            <div class="form-text small">यह नाम आईडी कार्ड पर प्रिंट होगा।</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">जन्मतिथि (Date of Birth) <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">लिंग (Gender)</label>
                            <select name="gender" class="form-select">
                                <option value="Male" <?= (($student['gender'] ?? 'Male') === 'Male') ? 'selected' : '' ?>>पुरुष (Male)</option>
                                <option value="Female" <?= (($student['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>महिला (Female)</option>
                                <option value="Other" <?= (($student['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>अन्य (Other)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ब्लड ग्रुप (Blood Group)</label>
                            <select name="blood_group" class="form-select">
                                <option value="" <?= empty($student['blood_group']) ? 'selected' : '' ?>>-- Not Known / पता नहीं --</option>
                                <?php 
                                $bgs = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach ($bgs as $bg): ?>
                                    <option value="<?= $bg ?>" <?= (($student['blood_group'] ?? '') === $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ईमेल पता (Registered Email)</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($student['email']) ?>" readonly>
                        </div>
                    </div>

                    <!-- Section 3: Contact & Address Details -->
                    <div class="form-section-title mt-4">
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i> 3. संपर्क व स्थायी पता (Contact & Address Details)
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">मोबाइल नंबर (Primary Mobile) <span class="text-danger">*</span></label>
                            <input type="tel" name="mobile" class="form-control" placeholder="उदा. 9876543210" value="<?= htmlspecialchars($student['mobile'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">आपातकालीन संपर्क नंबर (Emergency Contact)</label>
                            <input type="tel" name="emergency_contact" class="form-control" placeholder="माता/पिता या अभिभावक का नंबर" value="<?= htmlspecialchars($student['emergency_contact'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">स्थायी पता (Full Communication Address) <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control" placeholder="गांव/मोहल्ला, पोस्ट ऑफिस, तहसील/ब्लॉक, जिला, पिनकोड" required><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                            <div class="form-text small">यह पता आईडी कार्ड के पीछे संपर्क हेतु सुरक्षित रहेगा।</div>
                        </div>
                    </div>

                    <!-- Section 4: Security (Password Change) -->
                    <div class="form-section-title mt-4">
                        <i class="bi bi-shield-lock-fill text-primary me-1"></i> 4. पोर्टल सुरक्षा व पासवर्ड परिवर्तन (Security & Password)
                    </div>
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="small text-muted mb-3"><i class="bi bi-info-circle me-1"></i> यदि आप पासवर्ड नहीं बदलना चाहते, तो इसे खाली छोड़ दें।</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">वर्तमान पासवर्ड (Current Password)</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">नया पासवर्ड (New Password)</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">नया पासवर्ड कन्फर्म करें (Confirm)</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                        <div class="small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> आपकी जानकारी सुरक्षित है और केवल पहचान व संस्थान प्रमाणन के लिए उपयोग होगी।
                        </div>
                        <button type="submit" class="btn btn-primary-mgi px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> सुरक्षित करें और आईडी कार्ड अनलॉक करें (Save & Activate ID Card)
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script>
// Real-time photo preview
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('photo-preview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
</body>
</html>