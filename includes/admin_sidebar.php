<?php
$admin = get_logged_admin();
$currFile = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="portal-sidebar">
    <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-brand">
        <div class="brand-icon-box" style="width: 38px; height: 38px; font-size: 1.1rem; background: linear-gradient(135deg, #0284C7, #06B6D4);">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="brand-text-wrap">
            <span class="brand-title text-white" style="font-size: 0.95rem;">ADMIN CONTROL</span>
            <span class="brand-sub" style="font-size: 0.68rem; color: #38BDF8;">Manager Portal</span>
        </div>
    </a>

    <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
        <div class="rounded-circle bg-info bg-opacity-25 text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.2rem;">
            <i class="bi bi-person-fill-gear"></i>
        </div>
        <div>
            <div class="text-white small fw-bold"><?= htmlspecialchars($admin['name'] ?? 'DK Singh') ?></div>
            <div class="text-secondary" style="font-size: 0.72rem;"><?= htmlspecialchars($admin['email'] ?? 'admin@microgroup.com') ?></div>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="sidebar-heading">Main Overview</li>
        <li>
            <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-link <?= ($currFile == 'index') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard Stats
            </a>
        </li>

        <li class="sidebar-heading">Academic Management</li>
        <li>
            <a href="<?= BASE_URL ?>/admin/courses.php" class="sidebar-link <?= ($currFile == 'courses') ? 'active' : '' ?>">
                <i class="bi bi-journal-bookmark-fill"></i> Courses & Pricing
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/subjects.php" class="sidebar-link <?= ($currFile == 'subjects') ? 'active' : '' ?>">
                <i class="bi bi-diagram-3-fill"></i> Course Subjects
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/notes.php" class="sidebar-link <?= ($currFile == 'notes') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-pdf-fill"></i> Notes & PDFs
            </a>
        </li>

        <li class="sidebar-heading">Examinations & MCQs</li>
        <li>
            <a href="<?= BASE_URL ?>/admin/exams.php" class="sidebar-link <?= ($currFile == 'exams') ? 'active' : '' ?>">
                <i class="bi bi-card-checklist"></i> Exams Setup
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/questions.php" class="sidebar-link <?= ($currFile == 'questions') ? 'active' : '' ?>">
                <i class="bi bi-question-circle-fill"></i> Question Bank (MCQ)
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/results.php" class="sidebar-link <?= ($currFile == 'results') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill"></i> Student Results
            </a>
        </li>

        <li class="sidebar-heading">Certification & Students</li>
        <li>
            <a href="<?= BASE_URL ?>/admin/certificates.php" class="sidebar-link <?= ($currFile == 'certificates') ? 'active' : '' ?>">
                <i class="bi bi-patch-check-fill text-warning"></i> Certificate Management
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/students.php" class="sidebar-link <?= ($currFile == 'students') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Student Database
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/messages.php" class="sidebar-link <?= ($currFile == 'messages') ? 'active' : '' ?>">
                <i class="bi bi-chat-left-text-fill"></i> Contact Inquiries
            </a>
        </li>

        <li class="sidebar-heading">Configuration</li>
        <li>
            <a href="<?= BASE_URL ?>/admin/settings.php" class="sidebar-link <?= ($currFile == 'settings') ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i> Institute Settings
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/logout.php" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>

    <div class="p-3 text-center small text-secondary border-top border-secondary border-opacity-25">
        <a href="<?= BASE_URL ?>/index.php" target="_blank" class="text-decoration-none text-light opacity-75"><i class="bi bi-box-arrow-up-right me-1"></i>View Live Site</a>
    </div>
</aside>