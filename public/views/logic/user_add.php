<?php
session_start();
include_once 'C:\xampp\htdocs\masisikip\MoneyMo\api\includes\connect-db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $l_name = trim($_POST['l_name']);
    $f_name = trim($_POST['f_name']);
    $email = trim($_POST['email']);

    if (empty($l_name) || empty($f_name) || empty($email)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "error";
        header("Location: ../../user.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO user (l_name, f_name, email, usertype) VALUES (:l_name, :f_name, :email, 0)");
        $stmt->bindParam(':l_name', $l_name);
        $stmt->bindParam(':f_name', $f_name);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            $_SESSION['message'] = "User added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to add user.";
            $_SESSION['message_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: ../../user.php");
    exit();
}
?>