<?php
include_once(__DIR__ . '/../includes/connect-db.php');
include_once(__DIR__ . '/../includes/authenticate.php');

session_start();

global $pdo;
$headers = getallheaders();

unset($_SESSION['user_id']);
unset($_SESSION['user_password']);
unset($_SESSION['user_type']);

$email = $headers['x-email'];
$stmt = $pdo->prepare("SELECT iduser FROM user WHERE email = :email");
$stmt->bindParam(":email", $email, PDO::PARAM_STR);
$stmt->execute();

$user_id = $stmt->fetch(PDO::FETCH_ASSOC)['iduser'];
    
if (!$user_id) {
  http_response_code(404);
  echo json_encode(['error' => 'User not found']);
  exit;
} 

$user = authenticate(user_id:$user_id, user_password:$headers['x-password']);
http_response_code(200);
echo json_encode(['user' => $user]);

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_password'] = $user['password'];
$_SESSION['user_type'] = $user['type'];
