<?php
require '../../includes/connect-db.php'; 

$f_name = 'Masikip';
$l_name = 'Doe';
$usertype = 1;  // 0 = normal user; 1 = admin
$email = 'admin';
$password = 'admin';

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO user (f_name, l_name, usertype, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$f_name, $l_name, $usertype, $email, $hashed_password]);

    echo "User added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>