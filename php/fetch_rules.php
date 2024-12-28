<?php
require 'db.php';

// Ensure the response is JSON
header('Content-Type: application/json');
ob_start();
// Validate database connection
if (!isset($conn)) {
    echo json_encode(['error' => 'Database connection not initialized.']);
    ob_end_flush();
    exit;
}

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT rules_text FROM detection_rules");
    $stmt->execute();
    $rules = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Return the fetched rules as JSON
    echo json_encode( $rules);

    $jsonData = json_encode($rules,JSON_PRETTY_PRINT);
    $filePath= 'data.json';
    file_put_contents($filePath,$jsonData);
        
    
} catch (Exception $e) {
    // Return a generic error message and log the actual error
    echo json_encode(['success' => false, 'error' => 'An error occurred while fetching detection rules.']);
    error_log($e->getMessage());
    
}finally{
    ob_end_flush(); // Clear the buffer to ensure clean output

}
?>
