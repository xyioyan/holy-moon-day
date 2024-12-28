<?php
require 'db.php';

header('Content-Type: application/json');

try {
    // Query to fetch the recent alerts
    $stmt = $conn->query("SELECT time, type, details FROM alerts ORDER BY time DESC LIMIT 10");
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the alerts as JSON
    echo json_encode($alerts);
} catch (PDOException $e) {
    // Return an error message in case of failure
    echo json_encode(['error' => 'Failed to fetch alerts: ' . $e->getMessage()]);
}
?>
 