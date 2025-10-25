<?php
include_once '../../../includes/connect-db.php';
include_once '../../../includes/token.php';
require '../../../includes/php/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload && isset($payload['user_type'])) {
        $officer = $payload['user_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $stmt1 = $pdo->prepare('SELECT * FROM user WHERE student_id = ?');
    $stmt1->execute([$student_id]);
    $user = $stmt1->fetch();

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Valid user.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid user ID.'
        ]);
    }
}