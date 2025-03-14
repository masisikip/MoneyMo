<?php
include 'CRUD_functions.php'; // Include the User class

$user = new User();
$users = $user->readUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>
<body>

<h2>User List</h2>
<a href="sign_up_form.php">
    <button>Sign Up</button>
</a>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>User Type</th>
            <th>Email</th>
            <th>Password</th>
            <th>OTP</th>
            <th>OTP Expiry</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['iduser']); ?></td>
                    <td><?php echo htmlspecialchars($user['f_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['l_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['usertype']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['password']); ?></td>
                    <td><?php echo htmlspecialchars($user['otp']); ?></td>
                    <td><?php echo htmlspecialchars($user['otp_expiry']); ?></td>
                    <td>
                        <!-- Update Button -->
                        <a href="update.php?id=<?php echo $user['iduser']; ?>">
                            <button>Update</button>
                        </a>
                        <!-- Delete Button -->
                        <a href="delete.php?id=<?php echo $user['iduser']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">
                            <button>Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
