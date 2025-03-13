<?php
session_start();
include_once '../../../includes/connect-db.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM user WHERE iduser = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to delete user.";
            $_SESSION['message_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
} else {
    $_SESSION['message'] = "Invalid user ID.";
    $_SESSION['message_type'] = "error";
}

header("Location: ../../users");
exit();

