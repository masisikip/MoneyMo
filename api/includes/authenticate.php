<?php
include_once(__DIR__ . '/extract-custom-headers.php');
include_once(__DIR__ . '/connect-db.php');


function authenticate($user_type) {
    global $pdo;
    $custom_headers = extractCustomHeaders();

    if (empty($custom_headers['user_id']) || empty($custom_headers['password'])) {
        handleAuthFailure('Missing user_id or password.', 400);
        return;
    }

    $user_id = (int) $custom_headers['user_id'];
    $user_password = hash('sha256', $custom_headers['password']);

    $stmt = $pdo->prepare("SELECT iduser, password, usertype FROM user WHERE iduser = :iduser");
    $stmt->bindParam(':iduser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        handleAuthFailure('User not found.', 404);
        return;
    }

    if ($user_password !== $user['password']) {
        handleAuthFailure('Invalid password.', 401);
        return;
    }

    if ($user['usertype'] !== $user_type) {
        handleAuthFailure('Access denied for this user type.', 403);
        return;
    }
}

function handleAuthFailure($message, $response_code) {
    if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
        http_response_code($response_code);
        echo json_encode(['error' => $message]);
    } else {
        header('Location: /login');
    }
    exit;
}
