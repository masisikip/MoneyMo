<?php
session_start();
include_once '../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $l_name = trim($_POST['l_name']);
    $f_name = trim($_POST['f_name']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);

    try {
        $stmt = $pdo->prepare("SELECT usertype FROM user WHERE iduser = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        $usertype = $row['usertype'];

        $stmt = $pdo->prepare("UPDATE user SET l_name = ?, f_name = ?, email = ?, student_id=? WHERE iduser = ?");
        $stmt->execute([$l_name, $f_name, $email,$student_id, $user_id]);

        $_SESSION['message'] = "User updated successfully!";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../user.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request!";
    $_SESSION['message_type'] = "error";
    header("Location: ../../users");
    exit();
}
