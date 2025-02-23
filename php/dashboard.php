<?php
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ids_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Fetch statistics
$stats = $conn->query("SELECT COUNT(*) AS totalAlerts FROM alerts")->fetch_assoc();
$blockedIps = $conn->query("SELECT COUNT(*) AS count FROM logs")->fetch_assoc();
$activeRules = $conn->query("SELECT COUNT(*) AS count FROM  rules WHERE  is_active='Active'")->fetch_assoc();

// Fetch recent alerts
$alerts = $conn->query("SELECT created_at, `type`, severity, `action` FROM rules ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Fetch detection rules
$rules = $conn->query("SELECT id, rule_name,is_active FROM  rules ORDER BY id DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);

// Fetch notifications
$notifications = $conn->query("SELECT `description` FROM logs ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
// Fetch sms count

// Fetch logs
$logs = $conn->query("SELECT `description` FROM logs ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Fetch chart data
$chartData = $conn->query("
    SELECT MONTHNAME(created_at) AS month, COUNT(*) AS count 
    FROM logs
    GROUP BY MONTH(created_at)
")->fetch_all(MYSQLI_ASSOC);
$labels = array_column($chartData, "month");
$values = array_column($chartData, "count");

// Fetch severity levels
$severityLevels = [
    "high" => $conn->query("SELECT COUNT(*) AS count FROM logs WHERE severity='High'")->fetch_assoc()["count"],
    "medium" => $conn->query("SELECT COUNT(*) AS count FROM logs WHERE severity='Medium'")->fetch_assoc()["count"],
    "low" => $conn->query("SELECT COUNT(*) AS count FROM logs WHERE severity='Low'")->fetch_assoc()["count"],
];

// Create JSON data
$sathi = json_encode([
    "totalAlerts" => $stats["totalAlerts"],
    "blockedIps" => $blockedIps["count"],
    "activeRules" => $activeRules["count"],
    "recentAlerts" => $alerts,
    "rules" => $rules,
    "notifications" => array_column($notifications, "description"),
    "logs" => array_column($logs, "description"),
    "chart" => ["labels" => $labels, "values" => $values],
    "severityLevels" => array_values($severityLevels)
], JSON_PRETTY_PRINT); // Makes JSON readable

// Save JSON to a file
$file = 'data.json';
file_put_contents($file, $sathi);
echo $sathi; // Makes JSON readable

$conn->close();
?>
