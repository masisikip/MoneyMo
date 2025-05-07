<?php
session_start();
include_once '../../../includes/connect-db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $l_name = trim($_POST['l_name']);
    $f_name = trim($_POST['f_name']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);
    $password = trim($_POST['student_id']); 
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($l_name) || empty($f_name) || empty($email) || empty($student_id) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit();
    }

    $stmt1 = $pdo->prepare('SELECT COUNT(*) FROM user WHERE student_id = ? OR email = ?');
    $stmt1->execute([$student_id, $email]);
    $users = $stmt1->fetchColumn();

    if ($users != 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User already exists.'
        ]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO user (l_name, f_name, email, student_id, password, usertype) 
                               VALUES (:l_name, :f_name, :email, :student_id, :password, :usertype)");
        $stmt->bindParam(':l_name', $l_name);
        $stmt->bindParam(':f_name', $f_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':usertype', $is_admin);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'User added successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add user.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }

    exit();
}
?>
