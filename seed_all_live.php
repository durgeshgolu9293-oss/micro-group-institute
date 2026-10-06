<?php
require_once __DIR__ . '/config/functions.php';

try {
    // Seed Subjects
    $sCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
    if ($sCount == 0) {
        $subjects = [
            [1, 'Computer Fundamentals', 'CF-101', 'Basic computer architecture, input/output devices, and memory.'],
            [1, 'Operating Systems (Windows)', 'OS-102', 'Windows 10/11 operations, file management, and settings.'],
            [1, 'MS Office (Word, Excel, PowerPoint)', 'MSO-103', 'Complete Microsoft Office Suite training.'],
            [1, 'Internet & Email', 'INT-104', 'Web browsing, email communication, and cyber security basics.'],
            [2, 'Introduction to Internet & WWW', 'CCC-03', 'Browsers, search engines, and web concepts.']
        ];
        $stmt = $pdo->prepare("INSERT INTO subjects (course_id, subject_name, subject_code, description) VALUES (?, ?, ?, ?)");
        foreach ($subjects as $s) {
            $stmt->execute($s);
        }
        echo "Subjects seeded.<br>";
    }

    // Seed Notes
    $nCount = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
    if ($nCount == 0) {
        $notes = [
            [1, 1, 'Chapter 1: Basics', 'Introduction to Computers', 'Sample notes on computer basics.', '<p>A computer is an electronic device...</p>'],
            [1, 3, 'Chapter 3: Excel', 'Advanced Excel Formulas', 'VLOOKUP, HLOOKUP, Pivot Tables.', '<p>Formulas in Excel start with = ...</p>']
        ];
        $stmt = $pdo->prepare("INSERT INTO notes (course_id, subject_id, chapter_name, title, description, content) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($notes as $n) {
            $stmt->execute($n);
        }
        echo "Notes seeded.<br>";
    }

    // Seed Demo Student
    $stCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    if ($stCount == 0) {
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO students (roll_number, name, father_name, email, mobile, password, course_id, admission_date) VALUES ('MG202601', 'Rahul Kumar', 'Ramesh Kumar', 'rahul@example.com', '9876543210', ?, 1, '2026-08-01')")->execute([$hash]);
        echo "Demo Student seeded.<br>";
    }

    // Seed Demo Result & Certificate
    $rCount = $pdo->query("SELECT COUNT(*) FROM results")->fetchColumn();
    if ($rCount == 0) {
        $pdo->prepare("INSERT INTO results (attempt_id, student_id, exam_id, roll_number, total_marks, obtained_marks, percentage, grade, pass_status, date) VALUES (999, 1, 1, 'MG202601', 50, 45, 90.00, 'A', 'PASS', CURRENT_DATE)")->execute();
        
        $certNo = generate_certificate_no();
        $pdo->prepare("INSERT INTO certificates (certificate_number, student_id, course_id, result_id, student_name, roll_number, course_name, duration, percentage, grade, issue_date) VALUES (?, 1, 1, 999, 'Rahul Kumar', 'MG202601', 'Advanced Diploma in Computer Applications', '12 Months', 90.00, 'A', CURRENT_DATE)")->execute([$certNo]);
        echo "Demo Result & Certificate seeded ($certNo).<br>";
    }

    // Seed Typing Result
    $tCount = $pdo->query("SELECT COUNT(*) FROM typing_results")->fetchColumn();
    if ($tCount == 0) {
        $pdo->prepare("INSERT INTO typing_results (candidate_name, phone, duration_mins, net_wpm, gross_wpm, accuracy, mistakes, grade, certificate_no) VALUES ('Rahul Kumar', '9876543210', 1, 45, 48, 95.5, 3, 'A', 'TYP-2026-9999')")->execute();
        echo "Demo Typing Result seeded.<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
