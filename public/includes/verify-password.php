<?php 
session_start();
include_once 'connect-db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt1 = $pdo->prepare('UPDATE user SET password = SHA2(?, 256) WHERE email = ?');
        $stmt1->execute([$hashed_password, $email]);

        if ($stmt1->rowCount() > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Password updated successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No matching user found or password unchanged.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
