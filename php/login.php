<?php
require 'db.php';

// Start session at the beginning of the script
session_start();

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (!$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        // Verify database connection
        if (!isset($conn)) {
            throw new Exception('Database connection not initialized.');
        }

        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    } catch (Exception $e) {
        // Return a generic error message
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        // Optionally log the actual error message for debugging
        error_log($e->getMessage());
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
