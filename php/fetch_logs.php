<?php
require 'db.php'; // Include database connection

// Ensure the response is JSON
header('Content-Type: application/json');

// Start output buffering to prevent unwanted output
ob_start();

// Validate database connection
if (!isset($conn)) {
    echo json_encode(['success' => false, 'error' => 'Database connection not initialized.']);
    ob_end_flush();
    exit;
}

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM logs ORDER BY created_at DESC");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the fetched logs as JSON
    if ($logs) {
        echo json_encode(['success' => true, 'data' => $logs]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No logs found.']);
    }
} catch (Exception $e) {
    // Handle errors and return a generic error message
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
} finally {
    ob_end_flush(); // Clear the buffer to ensure clean output
}
