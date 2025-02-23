<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        die('Email and password are required!');
    }

    $hostname = $_SERVER['HTTP_HOST']; 

    $postData = http_build_query([
        'email' => $email,
        'password' => $password
    ]);

    $ch = curl_init("$hostname/api/authentication/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        echo "Login successful!";
    } else {
        echo "Login failed: " . htmlspecialchars($response);
    }
} else {
    echo "Invalid request method.";
}
?>

