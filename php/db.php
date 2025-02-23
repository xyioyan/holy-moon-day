<?php
// db.php - Database connection file

// Database configuration
define('DB_HOST', 'localhost');   // Database host
define('DB_NAME', 'ids_system'); // Database name
define('DB_USER', 'root');       // Database username
define('DB_PASS', '');           // Database password

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the error message and handle it gracefully
    error_log('Database Connection Error: ' . $e->getMessage());

    // Return a generic error message to avoid revealing sensitive information
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}
?>
