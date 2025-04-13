<?php
require_once 'includes/connect-db.php';
require_once 'includes/token.php';
session_start();

global $pdo;

if (isset($_SESSION['auth_token'])) {
  $payload = decryptToken($_SESSION['auth_token']);
  if ($payload && isset($payload['user_type'])) {
    $usertype = $payload['user_type'];

    if ($usertype == 0) {
      header('location: ./user');
    } elseif ($usertype == 1) {
      header('location: ./admin');
    }
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (is_array($user) && isset($user['password'])) {
    $password_is_verified = password_verify($password, $user['password']);
  } else {
    $password_is_verified = false;
  }

  if ($user && $password_is_verified) {
    // Create payload with user info
    $payload = [
      'user_id' => $user['iduser'],
      'user_email' => $user['email'],
      'user_type' => $user['usertype'],
      'user_name' => $user['f_name'],
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

    if ($user['usertype'] == 0) {
      header("Location: ./user");
      exit();
    } else {
      header("Location: ./admin");
      exit();
    }
  }
  // header("Location: " . $_SERVER['PHP_SELF']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="css/styles.css" rel="stylesheet" />
  <title>MoneyMo - Login</title>
</head>

<body class="bg-white">
  <!-- Container -->
  <div class="flex min-h-screen">
    <!-- Left side: Login form -->
    <div class="flex flex-col relative justify-center w-full px-8 py-12 lg:px-24">
      <!-- LOGO -->
      <div class="absolute top-0 left-0 flex justify-center items-center gap-4 px-6 py-4">
        <img src="assets/logo.png" alt="MoneyMo Logo" class="max-h-16 w-auto">
        <span class="text-3xl font-bold">MoneyMo</span>
      </div>

      <div class="flex flex-col justify-center items-center mx-auto w-full">
        <h1 class="text-4xl font-bold mb-1">Login to Your Account</h1>
        <p class="text-lg text-[#545454] mb-8">Enter your credentials to continue</p>

        <form class="space-y-4 flex flex-col items-center w-lg" method='post' action="">
          <!-- Email Field -->
          <div class="w-full">
            <!-- <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label> -->
            <input type="text" id="email" name="email" placeholder="Enter your email..." required
              class="block w-full px-6 py-4 text-[#262626] bg-[#d9d9d9] rounded-full focus:outline-none focus:ring-1 focus:ring-[#545454]" value="<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') echo $email ?>" />
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              if (!$user) {
            ?>
                <span class="block text-red-500 font-medium text-xs ml-6 mt-1">Unrecognized email. Please use your corporate student email.</span>
            <?php
              }
            }
            ?>
          </div>

          <!-- Password Field -->
          <div class="w-full">
            <!-- <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label> -->
            <input type="password" id="password" name="password" placeholder="Enter your password..." required
              class="block w-full px-6 py-4 text-[#262626] bg-[#d9d9d9] rounded-full focus:outline-none focus:ring-1 focus:ring-[#545454]" />
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              if ($user && !$password_is_verified) {
            ?>
                <span class="block text-red-500 font-medium text-xs ml-6 mt-1">Incorrect password. Please try again.</span>
            <?php
              }
            }
            ?>
          </div>

          <!-- Sign In Button -->
          <button type="submit"
            class="w-40 mt-2 px-5 py-2.5 bg-black text-white font-bold rounded-2xl hover:bg-gray-500 transition-colors">
            Sign In
          </button>
        </form>
      </div>
    </div>

    <!-- Right side: Branding (hidden on small screens) -->
    <div class="hidden md:flex md:w-1/3 items-center justify-center bg-black text-white p-8">
      <div class="text-left pl-4">
        <!-- You could add a logo here if desired -->
        <!-- <img src="your_logo.png" alt="MoneyMo Logo" class="mx-auto mb-4 w-20 h-20"> -->
        <h2 class="text-4xl font-bold mb-2">Welcome back!</h2>
        <p class="text-2xl pl-4">You can sign in to access
          your existing account.</p>
      </div>
    </div>
  </div>
</body>

</html>