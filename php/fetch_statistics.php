<?php
require 'db.php';

header('Content-Type: application/json');

try {
    // Fetch total alerts
    $stmt = $conn->query("SELECT COUNT(*) AS totalAlerts FROM alerts");
    $totalAlerts = $stmt->fetch(PDO::FETCH_ASSOC)['totalAlerts'];

    // Fetch critical alerts
    $stmt = $conn->query("SELECT COUNT(*) AS criticalAlerts FROM alerts WHERE type = 'Critical'");
    $criticalAlerts = $stmt->fetch(PDO::FETCH_ASSOC)['criticalAlerts'];

    // Fetch active rules
    $stmt = $conn->query("SELECT COUNT(*) AS activeRules FROM detection_rules WHERE active = 1");
    $activeRules = $stmt->fetch(PDO::FETCH_ASSOC)['activeRules'];

    // Return statistics as JSON
    echo json_encode([
        'totalAlerts' => $totalAlerts,
        'criticalAlerts' => $criticalAlerts,
        'activeRules' => $activeRules
    ]);
} catch (PDOException $e) {
    // Return an error message in case of failure
    echo json_encode(['error' => 'Failed to fetch statistics: ' . $e->getMessage()]);
}
?>
