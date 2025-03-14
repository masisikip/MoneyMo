<?php
include 'CRUD_functions.php';

$user = new User();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = trim($_POST['f_name']);
    $l_name = trim($_POST['l_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($f_name) && !empty($l_name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if ($user->createUser($f_name, $l_name, $email, $password)) {
                $message = "User registered successfully!";
            } else {
                $message = "Failed to register user.";
            }
        } else {
            $message = "Passwords do not match!";
        }
    } else {
        $message = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>

<h2>Sign Up</h2>

<?php if (!empty($message)) echo "<p>$message</p>"; ?>

<form method="post" action="">
    <label for="f_name">First Name:</label>
    <input type="text" id="f_name" name="f_name" required><br><br>

    <label for="l_name">Last Name:</label>
    <input type="text" id="l_name" name="l_name" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <button type="submit">Sign Up</button>
    
</form>
<a href="Read.php">
    <button>Back</button>
</a>
</body>
</html>
