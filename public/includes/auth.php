<?php
include_once(__DIR__ . '/load-env.php');
include_once(__DIR__ . '/connect-db.php');
include_once(__DIR__ . '/token.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function authenticate($privilege, $strict = false): array|null {
    $redirect_path = dirname($_SERVER['PHP_SELF']) . '/../logout/';

    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Retrieve token from session or cookie
    $token = $_SESSION['auth_token'] ?? ($_COOKIE['auth_token'] ?? null);

    // Redirect if no token is found
    if (!$token) {
        header("Location: $redirect_path");
        exit;
    }

    // Sync cookie token to session
    if (!isset($_SESSION['auth_token']) && isset($_COOKIE['auth_token'])) {
        $_SESSION['auth_token'] = $_COOKIE['auth_token'];
    }

    // Decrypt token to extract user info
    $user = decryptToken($token);

    // Invalid user data
    if (!$user || !is_array($user) || !isset($user['user_type'])) {
        header("Location: $redirect_path");
        exit;
    }

    // Check user privilege
    if ($strict) {
        return $user['user_type'] === $privilege ? $user : null;
    }

    return $user['user_type'] >= $privilege ? $user : null;
}
