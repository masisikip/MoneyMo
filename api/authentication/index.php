<?php
include_once(__DIR__ . '/../includes/authenticate.php');

$user = authenticate();
http_response_code(200);
echo json_encode(['user' => $user]);
