<?php
include_once(__DIR__ . '/load-env.php');

function encryptToken(array $data): string {
    $ENCRYPTION_KEY = getenv('ENCRYPTION_KEY'); 
    $plaintext = json_encode($data);
    $iv = random_bytes(16); // 16 bytes for AES-256-CBC
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $ciphertext);
}

function decryptToken(string $token): ?array {
    $ENCRYPTION_KEY = getenv('ENCRYPTION_KEY'); 
    $data = base64_decode($token);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $ENCRYPTION_KEY, 0, $iv);

    return $plaintext ? json_decode($plaintext, true) : null;
}
