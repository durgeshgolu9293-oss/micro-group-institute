<?php
require_once __DIR__ . '/../config/functions.php';
unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email'], $_SESSION['admin_role']);
set_flash_message('info', 'Logged out of admin panel.');
header('Location: ' . BASE_URL . '/admin/login.php');
exit;