<?php
require_once '../includes/connect-db.php';
require_once '../includes/token.php';
session_start();

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Create payload with user info
        $payload = [
            'user_id' => $user['iduser'],
            'user_email' => $user['email'],
            'user_type' => $user['usertype'],
            'iat' => time(),              // Issued At
        ];

        // Generate encrypted token
        $token = encryptToken($payload);

        // Store token in a session or secure cookie
        $_SESSION['auth_token'] = $token;

        // Alternatively, use an HttpOnly cookie for better security
        setcookie('auth_token', $token, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict'
        ]);

        header("Location: ../user_dashboard.html");
        exit();
    } 
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    href="../css/styles.css"
    rel="stylesheet"
  />
  <title>MoneyMo - Login</title>
</head>
<body class="bg-gray-50">
  <!-- Container -->
  <div class="flex min-h-screen">
    <!-- Left side: Login form -->
    <div class="flex flex-col justify-center w-full px-8 py-12 lg:px-24">
      <div class="max-w-md mx-auto w-full">
        <h1 class="text-3xl font-bold text-gray-800 mb-1">Login to Your Account</h1>
        <p class="text-gray-500 mb-8">Enter your credentials to continue</p>

        <form class="space-y-6 flex flex-col items-center" method='post' action="">
          <!-- Email Field -->
          <div class="w-full">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-700"
              >Email</label
            >
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              required
              class="block w-full px-4 py-2 text-gray-700 bg-gray-200 rounded-full focus:outline-none focus:ring-1 focus:ring-gray-500"
            />
          </div>

          <!-- Password Field -->
          <div class="w-full">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700"
              >Password</label
            >
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              required
              class="block w-full px-4 py-2 text-gray-700 bg-gray-200 rounded-full focus:outline-none focus:ring-1 focus:ring-gray-500"
            />
          </div>

          <!-- Sign In Button -->
          <button
            type="submit"
            class="w-40 py-2 bg-black text-white font-semibold rounded-full hover:bg-gray-500 transition-colors"
          >
            Sign In
          </button>
        </form>
      </div>
    </div>

    <!-- Right side: Branding (hidden on small screens) -->
    <div class="hidden md:flex md:w-1/4 items-center justify-center bg-black text-white p-8">
      <div class="text-center">
        <!-- You could add a logo here if desired -->
        <!-- <img src="your_logo.png" alt="MoneyMo Logo" class="mx-auto mb-4 w-20 h-20"> -->
        <h2 class="text-3xl font-bold mb-2">MoneyMo</h2>
        <p class="text-gray-300">effortless transaction</p>
      </div>
    </div>
  </div>
</body>
</html>
