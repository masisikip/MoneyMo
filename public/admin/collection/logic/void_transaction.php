<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and token functions
include_once __DIR__ . '/../../../includes/connect-db.php';
include_once __DIR__ . '/../../../includes/token.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header
header('Content-Type: application/json');

// DEBUG: Log the request
error_log("Void transaction request received: inventory_id=" . ($_POST['inventory_id'] ?? 'empty') . ", method=" . $_SERVER['REQUEST_METHOD']);

// Check if user is logged in using token
if (!isset($_SESSION['auth_token'])) {
    $response = ['success' => false, 'message' => 'Unauthorized access - Please log in'];
    error_log("Unauthorized access - auth_token not set in session");
    echo json_encode($response);
    exit;
}

// Verify the token
$payload = decryptToken($_SESSION['auth_token']);
if (!$payload || !isset($payload['user_id'])) {
    $response = ['success' => false, 'message' => 'Invalid session - Please log in again'];
    error_log("Invalid token in session");
    echo json_encode($response);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['success' => false, 'message' => 'Invalid request method'];
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode($response);
    exit;
}

// Get POST data
$inventory_id = $_POST['inventory_id'] ?? '';
$admin_password = $_POST['admin_password'] ?? '';

// DEBUG: Log received data
error_log("Received data - inventory_id: $inventory_id, admin_password length: " . strlen($admin_password));
error_log("User ID from token: " . $payload['user_id']);

// Validate input
if (empty($inventory_id) || empty($admin_password)) {
    $response = ['success' => false, 'message' => 'Missing required fields'];
    error_log("Missing required fields - inventory_id: " . (empty($inventory_id) ? 'empty' : 'set') . ", admin_password: " . (empty($admin_password) ? 'empty' : 'set'));
    echo json_encode($response);
    exit;
}

try {
    // Get user ID from token payload
    $user_id = $payload['user_id'];
    
    // Verify admin password for the current logged-in user
    $stmt = $pdo->prepare("SELECT password, usertype FROM user WHERE iduser = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $response = ['success' => false, 'message' => 'User not found'];
        error_log("User not found for id: " . $user_id);
        echo json_encode($response);
        exit;
    }

    // Check if user has admin privileges (usertype = 1)
    if ($user['usertype'] != 1) {
        $response = ['success' => false, 'message' => 'Insufficient privileges - Admin access required'];
        error_log("User does not have admin privileges. User type: " . $user['usertype']);
        echo json_encode($response);
        exit;
    }

    // Verify password
    if (!password_verify($admin_password, $user['password'])) {
        $response = ['success' => false, 'message' => 'Incorrect password'];
        error_log("Password verification failed for user: " . $user_id);
        echo json_encode($response);
        exit;
    }

    // Check if transaction is already voided
    $checkStmt = $pdo->prepare("SELECT is_void FROM inventory WHERE idinventory = ?");
    $checkStmt->execute([$inventory_id]);
    $inventory = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$inventory) {
        $response = ['success' => false, 'message' => 'Transaction not found'];
        error_log("Transaction not found: " . $inventory_id);
        echo json_encode($response);
        exit;
    }

    if ($inventory['is_void'] == 1) {
        $response = ['success' => false, 'message' => 'Transaction already voided'];
        error_log("Transaction already voided: " . $inventory_id);
        echo json_encode($response);
        exit;
    }

    // Update the inventory record to set is_void = 1
    $updateStmt = $pdo->prepare("UPDATE inventory SET is_void = 1 WHERE idinventory = ?");
    $updateStmt->execute([$inventory_id]);

    if ($updateStmt->rowCount() > 0) {
        $response = ['success' => true, 'message' => 'Transaction voided successfully'];
        error_log("Transaction voided successfully: " . $inventory_id . " by user: " . $user_id);
        echo json_encode($response);
    } else {
        $response = ['success' => false, 'message' => 'Failed to void transaction'];
        error_log("Failed to void transaction: " . $inventory_id);
        echo json_encode($response);
    }

} catch (PDOException $e) {
    // Log the error but don't expose database details to user
    error_log("Void transaction error: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Database error occurred'];
    echo json_encode($response);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'An error occurred'];
    echo json_encode($response);
}

// Ensure no extra output
exit;
?>