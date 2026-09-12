<?php
require_once __DIR__ . '/../config/functions.php';
unset($_SESSION['student_id'], $_SESSION['student_name'], $_SESSION['student_roll'], $_SESSION['student_course']);
set_flash_message('info', 'You have been logged out safely.');
header('Location: ' . BASE_URL . '/login.php');
exit;