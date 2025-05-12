<?php
include_once '../../../includes/connect-db.php';
include_once '../../../includes/token.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['auth_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$payload = decryptToken($_SESSION['auth_token']);
if (!$payload || !isset($payload['user_type'], $payload['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

$userId = $payload['user_id'];

if (empty($_POST['old_password']) || empty($_POST['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

$oldPassword = $_POST['old_password'];
$newPassword = $_POST['new_password'];

$stmt = $pdo->prepare("SELECT password FROM user WHERE iduser = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

if (!password_verify($oldPassword, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
    exit;
}

$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE user SET password = ? WHERE iduser = ?");
if ($stmt->execute([$newPasswordHash, $userId])) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
}
