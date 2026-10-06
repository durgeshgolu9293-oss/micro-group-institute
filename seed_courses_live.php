<?php
require_once __DIR__ . '/config/functions.php';

try {
    $cCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    if ($cCount == 0) {
        $courses = [
            ['Advanced Diploma in Computer Applications', 'ADCA', '12 Months', 6000, 500, 1000],
            ['Course on Computer Concepts', 'CCC', '3 Months', 2500, 200, 300],
            ['Diploma in Computer Applications', 'DCA', '6 Months', 4000, 300, 500],
            ['Post Graduate Diploma in Computer Applications', 'PGDCA', '15 Months', 12000, 1000, 2000],
            ['Tally Prime with GST & E-Way Bill', 'Tally Prime', '3 Months', 3500, 300, 500]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO courses (course_name, short_name, duration, fee, admission_fee, discount, final_fee) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($courses as $c) {
            $final = ($c[3] + $c[4]) - $c[5];
            $stmt->execute([$c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $final]);
        }
        echo "Courses seeded successfully!<br>";
    } else {
        echo "Courses already exist. Count: $cCount<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
