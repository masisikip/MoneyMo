<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data and destroy the session
$_SESSION = [];
session_unset();
session_destroy();

// Delete the persistent auth_token cookie
setcookie('auth_token', '', [
    'expires' => time() - 3600, // Expire in the past to delete
    'path' => '/',              // Ensure site-wide deletion
    'httponly' => true,         // Secure against XSS
    'secure' => true,           // Only over HTTPS
    'samesite' => 'Strict'      // Mitigate CSRF
]);

// Redirect to the login page
header("Location: ../login/");
exit();
