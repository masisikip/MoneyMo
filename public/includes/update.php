<?php
include 'CRUD_functions.php';

$user = new User();
$message = "";

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid user ID.");
}

$iduser = $_GET['id'];

// Fetch existing user data
$existingUser = $user->getUserById($iduser);
if (!$existingUser) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = trim($_POST['f_name']);
    $l_name = trim($_POST['l_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($f_name) && !empty($l_name) && !empty($email)) {
        if (!empty($password) && $password !== $confirm_password) {
            $message = "Passwords do not match!";
        } else {
            // Update user (password is optional)
            if ($user->updateUser($iduser, $f_name, $l_name, $email, $password)) {
                $message = "User updated successfully!";
                header("Location: read.php"); // Redirect to read.php after successful update
                exit();
            } else {
                $message = "Failed to update user.";
            }
        }
    } else {
        $message = "All fields except password are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>

<h2>Update User</h2>

<?php if (!empty($message)) echo "<p>$message</p>"; ?>

<form method="post" action="">
    <label for="f_name">First Name:</label>
    <input type="text" id="f_name" name="f_name" value="<?php echo htmlspecialchars($existingUser['f_name']); ?>" required><br><br>

    <label for="l_name">Last Name:</label>
    <input type="text" id="l_name" name="l_name" value="<?php echo htmlspecialchars($existingUser['l_name']); ?>" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($existingUser['email']); ?>" required><br><br>

    <label for="password">New Password (optional):</label>
    <input type="password" id="password" name="password"><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password"><br><br>

    <button type="submit">Update</button>
</form>

<a href="read.php"><button>Back to User List</button></a>

</body>
</html>
