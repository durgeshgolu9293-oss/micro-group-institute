<?php
require_once __DIR__ . '/config/functions.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['candidate_name'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$name = trim($data['candidate_name']);
$phone = trim($data['phone'] ?? '');
$duration = (int)($data['duration_mins'] ?? 1);
$netWpm = (int)($data['net_wpm'] ?? 0);
$grossWpm = (int)($data['gross_wpm'] ?? 0);
$accuracy = (float)($data['accuracy'] ?? 100);
$mistakes = (int)($data['mistakes'] ?? 0);
$grade = $data['grade'] ?? 'A';

// Generate unique typing certificate number: TYP-2026-XXXXX
$count = $pdo->query("SELECT COUNT(*) FROM typing_results")->fetchColumn();
$certNo = 'TYP-2026-' . str_pad((int)$count + 1, 5, '0', STR_PAD_LEFT);

$stmt = $pdo->prepare("INSERT INTO typing_results (candidate_name, phone, duration_mins, net_wpm, gross_wpm, accuracy, mistakes, grade, certificate_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $phone, $duration, $netWpm, $grossWpm, $accuracy, $mistakes, $grade, $certNo]);

echo json_encode(['success' => true, 'certificate_no' => $certNo]);