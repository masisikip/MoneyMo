<?php
include '../../../includes/connect-db.php';

if (!isset($_POST['students_json'])) {
    die("No students data received.");
}

$students = json_decode($_POST['students_json'], true);
if (!$students) {
    die("Invalid JSON data.");
}

$count = 0;

foreach ($students as $stu) {
    $password_hashed = password_hash($stu['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO user (f_name, l_name, year, email, student_id, password) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $stu['f_name'],
        $stu['l_name'],
        $stu['year'],
        $stu['email'],
        $stu['student_id'],
        $password_hashed
    ]);
    $count++;
}

echo "$count students inserted successfully!";
?>