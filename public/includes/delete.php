<?php
include 'CRUD_functions.php';

$user = new User();

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid user ID.");
}

$iduser = $_GET['id'];

// Delete the user
if ($user->deleteUser($iduser)) {
    header("Location: read.php"); // Redirect back to the user list
    exit();
} else {
    die("Failed to delete user.");
}
