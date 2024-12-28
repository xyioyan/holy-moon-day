<?php
require 'db.php';

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if 'log_message' key is set and not empty
if (isset($data['log_message']) && !empty($data['log_message'])) {
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO logs (log_message) VALUES (:log_message)");
        $stmt->bindParam(':log_message', $data['log_message'], PDO::PARAM_STR);
        
        // Execute the query
        $stmt->execute();

        // Send a success response
        echo json_encode(['success' => true, 'message' => 'Log message added successfully']);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle invalid input
    echo json_encode(['success' => false, 'message' => 'Invalid or missing log_message']);
}
?>
