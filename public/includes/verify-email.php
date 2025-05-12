<?php
include_once 'connect-db.php';
include_once 'token.php';
require 'php/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    try {
        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt1->execute([$email]);
        $result = $stmt1->fetch();

        if ($result) {
            $cur_exp = $result['otp_expiry'];
            $time_diff = $pdo->prepare('SELECT TIMESTAMPDIFF(MINUTE, ?, NOW())');
            $time_diff->execute([$cur_exp]);
            $diff = $time_diff->fetchColumn();

            if ($diff < 0 && $diff !== null) {
                echo json_encode(['status' => 'expired', 'message' => 'OTP expired ' . abs($diff) . ' minutes ago.']);
                exit;
            }

            $otp = strval(rand(1000, 9999));

            $stmt2 = $pdo->prepare('UPDATE user SET otp = SHA2(?, 256), otp_expiry = NOW() + INTERVAL 5 MINUTE WHERE email = ?');
            $stmt2->execute([$otp, $email]);

            $body = "
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification</title>
            <style>
                @media only screen and (max-width: 600px) {
                    .container { padding: 20px !important; }
                    .otp-box { width: 50px !important; height: 50px !important; font-size: 20px !important; }
                }
            </style>
            </head>
            <body style='font-family: Segoe UI, Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 20px;'>
            <div class='container' style='max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; border: 1px solid #e0e0e0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>

                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='cid:logoimg' alt='MoneyMo Logo' style='width: 80px; height: 80px; border-radius: 50%; border: 2px solid #ddd; margin-bottom: 10px;'>
                    <h2 style='margin: 0; color: #333;'>MoneyMo</h2>
                </div>

                <h3 style='text-align: center; color: #222;'>OTP Verification</h3>
                <p style='text-align: center; font-size: 15px; color: #555; margin-bottom: 25px;'>
                    Use the following One-Time Password (OTP) to reset your password. This code is valid for only <strong>5 minutes</strong>.
                </p>

                <div style='display: flex; justify-content: center; gap: 12px; margin: 20px 0;'>
                    <div style='display: flex; justify-content: center; gap: 12px; margin: 20px 0;'>
                        <div class='otp-box' style='width: 60px; height: 60px; font-size: 24px; background: #f0f0f0; border: 2px solid #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;'>" . $otp[0] . "</div>
                        <div class='otp-box' style='width: 60px; height: 60px; font-size: 24px; background: #f0f0f0; border: 2px solid #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;'>" . $otp[1] . "</div>
                        <div class='otp-box' style='width: 60px; height: 60px; font-size: 24px; background: #f0f0f0; border: 2px solid #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;'>" . $otp[2] . "</div>
                        <div class='otp-box' style='width: 60px; height: 60px; font-size: 24px; background: #f0f0f0; border: 2px solid #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;'>" . $otp[3] . "</div>
                    </div>
                </div>

                <p style='text-align: center; font-size: 13px; color: #888;'>
                    If you didn’t request this, you can safely ignore this email.
                </p>

                <p style='text-align: center; font-size: 13px; color: #aaa; margin-top: 30px;'>&copy; 2025 MoneyMo. All rights reserved.</p>
            </div>
            </body>
            </html>
            ";

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.hostinger.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'moneymo@miceff.com';         // Your SMTP username
                $mail->Password = 'man1M0M@sarap';          // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('moneymo@miceff.com', 'MoneyMo');
                $mail->addAddress($result['email'], ($result['f_name'] ?? '') . ' ' . ($result['l_name'] ?? ''));

                $mail->AddEmbeddedImage('../assets/logo-circle.png', 'logoimg', 'logo-circle.png');
                $mail->isHTML(true);
                $mail->Subject = 'Your One-Time Password (OTP)';
                $mail->Body = $body;

                $mail->send();
            } catch (Exception $e) {
                error_log('Mailer Error: ' . $mail->ErrorInfo);
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP email.']);
                exit;
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully.']);
        } else {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Email not found. Invalid user.']);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
