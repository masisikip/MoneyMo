<?php
require 'includes/connect-db.php'; 

$f_name = 'John';
$l_name = 'Doe';
$usertype = 0;  // 0 = normal user; 1 = admin
$email = '202280110@psu.palawan.edu.ph';
$password = '12345678';

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO user (f_name, l_name, usertype, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$f_name, $l_name, $usertype, $email, $hashed_password]);

    echo "User added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>