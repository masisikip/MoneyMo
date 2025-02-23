<?php
include_once(__DIR__ . '/connect-db.php');


function authenticate($user_type=null, $user_id=null, $user_password=null, $autostop=true) {
    global $pdo;
    $headers = getallheaders();
    
    if ($user_id === null || $user_password === null) {
        if (empty($headers['user_id']) || empty($headers['password'])) {
            handleAuthFailure('Missing user_id or password.', 400, $autostop);
            return;
        }

        $user_id = (int) $headers['user_id'];
        $user_password = hash('sha256', $headers['password']);
    }
    

    $stmt = $pdo->prepare("SELECT iduser, password, usertype FROM user WHERE iduser = :iduser");
    $stmt->bindParam(':iduser', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        handleAuthFailure('User not found.', 404, $autostop);
        return null;
    }

    if ($user_password !== $user['password']) {
        handleAuthFailure('Invalid password.', 401, $autostop);
        return null;
    }

    if ($user['usertype'] !== $user_type && $user_type !== null) {
        handleAuthFailure('Access denied for this user type.', 403, $autostop);
        return null;
    }

    return ["id" => $user_id, "password" => $user_password, "type" => $user_type];
}

function handleAuthFailure($message, $response_code, $autostop) {
    if ($autostop) {
        http_response_code($response_code);
        echo json_encode(['error' => $message]);
        exit;
    }    
}
