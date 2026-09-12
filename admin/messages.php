<?php
require_once __DIR__ . '/../config/functions.php';
require_admin_login();
$page_title = "Contact Messages";

$action = $_GET['action'] ?? 'list';
$msg_id = (int)($_GET['id'] ?? 0);

if ($action === 'mark_read' && $msg_id > 0) {
    $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$msg_id]);
    header('Location: ' . BASE_URL . '/admin/messages.php');
    exit;
}

if ($action === 'delete' && $msg_id > 0) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msg_id]);
    set_flash_message('success', 'Message deleted.');
    header('Location: ' . BASE_URL . '/admin/messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Micro Group Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="portal-main">
        <header class="portal-topbar">
            <h5 class="mb-0 fw-bold text-dark">Contact Inquiries & Admissions Requests</h5>
        </header>
        <main class="portal-content">
            <?php display_flash_message(); ?>

            <div class="data-card">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No messages received yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($messages as $m): ?>
                                    <tr class="<?= ($m['status'] === 'unread') ? 'table-warning' : '' ?>">
                                        <td class="fw-bold"><?= htmlspecialchars($m['name']) ?></td>
                                        <td>
                                            <div class="small"><?= htmlspecialchars($m['email']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($m['phone'] ?? '') ?></div>
                                        </td>
                                        <td class="fw-semibold"><?= htmlspecialchars($m['subject']) ?></td>
                                        <td class="small" style="max-width: 300px;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                                        <td class="small"><?= date('d M, Y', strtotime($m['created_at'])) ?></td>
                                        <td><span class="badge <?= ($m['status'] === 'unread') ? 'bg-danger' : 'bg-success' ?>"><?= $m['status'] ?></span></td>
                                        <td class="text-end">
                                            <?php if ($m['status'] === 'unread'): ?>
                                                <a href="<?= BASE_URL ?>/admin/messages.php?action=mark_read&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-check2"></i> Mark Read</a>
                                            <?php endif; ?>
                                            <a href="<?= BASE_URL ?>/admin/messages.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete message?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>