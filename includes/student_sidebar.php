<?php
$student = get_logged_student();
$currFile = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="portal-sidebar">
    <a href="<?= BASE_URL ?>/student/dashboard.php" class="sidebar-brand">
        <div class="brand-icon-box" style="width: 38px; height: 38px; font-size: 1.1rem;">
            <i class="bi bi-person-workspace"></i>
        </div>
        <div class="brand-text-wrap">
            <span class="brand-title text-white" style="font-size: 0.95rem;">STUDENT PORTAL</span>
            <span class="brand-sub" style="font-size: 0.68rem;"><?= htmlspecialchars($student['roll_number'] ?? 'MGI') ?></span>
        </div>
    </a>

    <div class="p-3 text-center border-bottom border-secondary border-opacity-25">
        <?php $avatarUrl = get_student_avatar_url($student); ?>
        <img src="<?= $avatarUrl ?>" alt="Student Photo" class="rounded-circle border border-2 border-warning shadow-sm mx-auto mb-2" style="width: 58px; height: 58px; object-fit: cover; background: #fff;">
        <h6 class="text-white mb-0"><?= htmlspecialchars($student['name'] ?? 'Student') ?></h6>
        <span class="badge bg-secondary bg-opacity-50 text-light mt-1"><?= htmlspecialchars($student['course_code'] ?? 'Course') ?></span>
    </div>

    <ul class="sidebar-menu">
        <li class="sidebar-heading">Learning Hub</li>
        <li>
            <a href="<?= BASE_URL ?>/student/dashboard.php" class="sidebar-link <?= ($currFile == 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/student/my-course.php" class="sidebar-link <?= ($currFile == 'my-course') ? 'active' : '' ?>">
                <i class="bi bi-book-half"></i> My Course & Syllabus
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/student/notes.php" class="sidebar-link <?= ($currFile == 'notes') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-pdf-fill"></i> Notes & Handouts
            </a>
        </li>

        <li class="sidebar-heading">Assessments</li>
        <li>
            <a href="<?= BASE_URL ?>/student/exams.php" class="sidebar-link <?= ($currFile == 'exams') ? 'active' : '' ?>">
                <i class="bi bi-pencil-square"></i> Available Exams
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/student/results.php" class="sidebar-link <?= ($currFile == 'results') ? 'active' : '' ?>">
                <i class="bi bi-trophy-fill"></i> My Test Results
            </a>
        </li>

        <li class="sidebar-heading">Certification</li>
        <li>
            <a href="<?= BASE_URL ?>/student/my-certificate.php" class="sidebar-link <?= ($currFile == 'my-certificate') ? 'active' : '' ?>">
                <i class="bi bi-patch-check-fill"></i> Certificate Status
            </a>
        </li>
        <li>
            <?php $idCardUnlocked = is_student_profile_complete($student); ?>
            <a href="<?= BASE_URL ?>/student/id-card.php" class="sidebar-link <?= ($currFile == 'id-card') ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill <?= $idCardUnlocked ? 'text-warning' : 'text-light' ?>"></i> Student ID Card 
                <?php if ($idCardUnlocked): ?>
                    <span class="badge bg-success ms-auto" style="font-size: 0.65rem;">ACTIVE</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.65rem;">LOCKED</span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-heading">Account</li>
        <li>
            <a href="<?= BASE_URL ?>/student/profile.php" class="sidebar-link <?= ($currFile == 'profile') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> My Profile
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/student/logout.php" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>

    <div class="p-3 text-center small text-secondary border-top border-secondary border-opacity-25">
        <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-light opacity-75"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
    </div>
</aside>