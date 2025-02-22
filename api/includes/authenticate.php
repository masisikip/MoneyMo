<?php
include_once(__DIR__ . '/extract-custom-headers.php');
include_once(__DIR__ . '/connect-db.php');


function authenticate($user_type) {
    global $pdo;
    $custom_headers = extractCustomHeaders();

    if (empty($custom_headers['user_id']) || empty($custom_headers['password'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing user_id or password.']);
        return;
    }

    $user_id = (int) $custom_headers['user_id'];
    $user_password = hash('sha256', $custom_headers['password']);

    $stmt = $pdo->prepare("SELECT iduser, password, usertype FROM user WHERE iduser = :iduser");
    $stmt->bindParam(':iduser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'User not found.']);
        return;
    }

    if ($user_password !== $user['password']) {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Invalid password.']);
        return;
    }

    if ($user['usertype'] !== $user_type) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'Access denied for this user type.']);
        return;
    }
}
