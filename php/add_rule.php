<?php
require 'db.php';

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if 'rule' key is set and not empty
if (isset($data['rule']) && !empty(trim($data['rule']))) {
    $rule = trim($data['rule']);
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO detection_rules (rules_text) VALUES (:rule)");
        $stmt->bindParam(':rule', $data['rule'], PDO::PARAM_STR);
        
        // Execute the query
        $stmt->execute();

        // Send a success response
        echo json_encode(['success' => true, 'message' => 'Rule added successfully']);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle invalid input
    echo json_encode(['success' => false, 'message' => 'Invalid or missing input']);
}
?>
