<?php
session_start();
include_once '../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $l_name = trim($_POST['l_name']);
    $f_name = trim($_POST['f_name']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);
    $password = trim($_POST['password']);
    $usertype = isset($_POST['usertype']) ? 1 : 0; // Admin (1) or User (0)

    $stmt1 = $pdo->prepare('SELECT COUNT(*) FROM user WHERE (student_id = ? OR email = ?) AND iduser != ?');
    $stmt1->execute([$student_id, $email, $user_id]);
    $users = $stmt1->fetchColumn();

    if ($users != 0) {
        echo json_encode(['status' => 'error', 'message' => 'Update failed 1.']);
        exit();
    }

    try {
        // If password is provided, update with hashing
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET l_name = ?, f_name = ?, email = ?, student_id = ?, password = ?, usertype = ? WHERE iduser = ?");
            $stmt->execute([$l_name, $f_name, $email, $student_id, $hashed_password, $usertype, $user_id]);

            echo json_encode(['status' => 'success']);
            exit();
        } else {
            // No password update
            $stmt = $pdo->prepare("UPDATE user SET l_name = ?, f_name = ?, email = ?, student_id = ?, usertype = ? WHERE iduser = ?");
            $stmt->execute([$l_name, $f_name, $email, $student_id, $usertype, $user_id]);

            echo json_encode(['status' => 'success']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
    exit();
}
