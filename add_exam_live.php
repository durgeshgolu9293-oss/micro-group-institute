<?php
require_once __DIR__ . '/config/functions.php';

try {
    // 1. Check if exam already exists to avoid duplicates
    $checkStmt = $pdo->prepare("SELECT id FROM exams WHERE exam_title = '🏆 ADCA Digital Skill Challenge - 2026' LIMIT 1");
    $checkStmt->execute();
    if ($checkStmt->fetch()) {
        die("Exam already exists in live database!");
    }

    // 2. Get Course ID for ADCA
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE short_name = 'ADCA' LIMIT 1");
    $stmt->execute();
    $course = $stmt->fetch();
    $course_id = $course ? $course['id'] : 1;

    // 3. Insert Exam
    $exam_title = '🏆 ADCA Digital Skill Challenge - 2026';
    $stmt = $pdo->prepare("INSERT INTO exams (course_id, exam_title, title, description, total_questions, duration_minutes, max_marks, passing_percentage, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([
        $course_id, 
        $exam_title, 
        'Online Surprise Test', 
        '25 Questions | Level: Easy-Medium | Topic: Basic Computer, Notepad, MS Word, MS Excel.', 
        25, 
        20, 
        100, 
        40
    ]);
    
    // In PostgreSQL, lastInsertId doesn't always work reliably without sequence name, but let's try getting it via SELECT MAX(id) if needed.
    $exam_id = $pdo->lastInsertId();
    if (!$exam_id) {
        $stmt = $pdo->query("SELECT MAX(id) FROM exams");
        $exam_id = $stmt->fetchColumn();
    }

    // 4. Insert Questions
    $questions = [
        ['CPU का पूरा नाम क्या है?', 'Central Processing Unit', 'Computer Processing Unit', 'Central Program Unit', 'Control Processing Unit', 'A'],
        ['इनमें से कौन-सा Output Device है?', 'Keyboard', 'Mouse', 'Monitor', 'Scanner', 'C'],
        ['Ctrl + C का उपयोग किसके लिए किया जाता है?', 'Cut', 'Copy', 'Close', 'Clear', 'B'],
        ['Ctrl + V का उपयोग किसके लिए किया जाता है?', 'Paste', 'Print', 'View', 'Save', 'A'],
        ['कंप्यूटर में अस्थायी रूप से Data Store करने वाली Memory कौन-सी है?', 'Hard Disk', 'RAM', 'Pen Drive', 'CD', 'B'],
        ['किसी File को Rename करने के लिए Windows में सामान्यतः कौन-सी Key उपयोग होती है?', 'F1', 'F2', 'F5', 'F12', 'B'],
        ['Notepad में बनाई गई File का सामान्य Extension क्या होता है?', '.docx', '.xlsx', '.txt', '.pptx', 'C'],
        ['Notepad में File Save करने की Shortcut Key क्या है?', 'Ctrl + S', 'Ctrl + C', 'Ctrl + P', 'Ctrl + X', 'A'],
        ['Notepad में किसी शब्द को खोजने के लिए कौन-सी Shortcut Key है?', 'Ctrl + F', 'Ctrl + H', 'Ctrl + N', 'Ctrl + W', 'A'],
        ['Notepad में नया Document बनाने के लिए कौन-सी Shortcut Key है?', 'Ctrl + O', 'Ctrl + N', 'Ctrl + D', 'Ctrl + E', 'B'],
        ['Notepad में Word Wrap का उपयोग किसलिए किया जाता है?', 'Text को Automatically अगली Line में दिखाने के लिए', 'Text को Delete करने के लिए', 'File को Save करने के लिए', 'Font को Bold करने के लिए', 'A'],
        ['MS Word का मुख्य उपयोग किसके लिए होता है?', 'Document बनाने के लिए', 'Calculation करने के लिए', 'Video बनाने के लिए', 'Internet चलाने के लिए', 'A'],
        ['MS Word की सामान्य File का Extension क्या होता है?', '.txt', '.xlsx', '.docx', '.jpg', 'C'],
        ['Text को Bold करने की Shortcut Key क्या है?', 'Ctrl + I', 'Ctrl + U', 'Ctrl + B', 'Ctrl + P', 'C'],
        ['Text को Italic करने की Shortcut Key क्या है?', 'Ctrl + I', 'Ctrl + B', 'Ctrl + U', 'Ctrl + E', 'A'],
        ['MS Word में Text को Center करने की Shortcut Key क्या है?', 'Ctrl + L', 'Ctrl + R', 'Ctrl + E', 'Ctrl + J', 'C'],
        ['Document को Print करने की Shortcut Key क्या है?', 'Ctrl + S', 'Ctrl + P', 'Ctrl + C', 'Ctrl + T', 'B'],
        ['MS Word में Spelling Check करने के लिए सामान्यतः कौन-सी Key उपयोग होती है?', 'F2', 'F5', 'F7', 'F12', 'C'],
        ['MS Excel का मुख्य उपयोग किसके लिए किया जाता है?', 'Documents लिखने के लिए', 'Data और Calculation के लिए', 'Photo Editing के लिए', 'Video Editing के लिए', 'B'],
        ['Excel की सामान्य File का Extension क्या होता है?', '.docx', '.txt', '.xlsx', '.jpg', 'C'],
        ['Excel में Formula किस चिन्ह से शुरू होता है?', '#', '@', '=', '&', 'C'],
        ['Excel में Row और Column के Intersection को क्या कहा जाता है?', 'Sheet', 'Cell', 'Table', 'Box', 'B'],
        ['Excel में A1 क्या दर्शाता है?', 'Row', 'Column', 'Cell Address', 'Worksheet', 'C'],
        ['Excel में दो या अधिक Numbers को जोड़ने के लिए कौन-सा Function उपयोग होता है?', 'MAX', 'SUM', 'COUNT', 'MIN', 'B'],
        ['Excel में Average निकालने के लिए कौन-सा Function उपयोग होता है?', 'SUM', 'MAX', 'AVERAGE', 'COUNT', 'C']
    ];

    $qStmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, 4)");

    foreach ($questions as $q) {
        $qStmt->execute([$exam_id, $q[0], $q[1], $q[2], $q[3], $q[4], $q[5]]);
    }

    echo "Exam and 25 questions inserted successfully into live database! Exam ID: " . $exam_id;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
