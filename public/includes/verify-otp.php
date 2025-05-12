<?php
include_once 'connect-db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $u_otp = hash('sha256', trim($_POST['code']));

    try {
        $stmt1 = $pdo->prepare('SELECT otp, otp_expiry FROM user WHERE email = ?');
        $stmt1->execute([$email]);
        $user = $stmt1->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if OTP has expired
            $time_diff = $pdo->prepare('SELECT TIMESTAMPDIFF(MINUTE, ?, NOW())');
            $time_diff->execute([$user['otp_expiry']]);
            $diff = $time_diff->fetchColumn();

            if ($diff > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'OTP has expired.'
                ]);
            } else {
                // Check if OTP is correct
                if ($u_otp === $user['otp']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'OTP verified successfully.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Incorrect OTP.'
                    ]);
                }
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'User not found.'
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
