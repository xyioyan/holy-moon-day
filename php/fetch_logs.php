<?php
require 'db.php'; // Database connection

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'get_logs') {
        getLogs();
    } elseif ($action === 'get_severity_distribution') {
        getSeverityDistribution();
    }
}

function getLogs() {
    global $conn;

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Number of logs per page
    $offset = ($page - 1) * $limit;

    // Apply filters
    $filters = [];
    $whereClause = '';
    if (!empty($_GET['start_date'])) {
        $filters[] = "created_at >= :start_date";
    }
    if (!empty($_GET['end_date'])) {
        $filters[] = "created_at <= :end_date";
    }
    if (!empty($_GET['severity'])) {
        $filters[] = "severity = :severity";
    }
    if (!empty($_GET['event_type'])) {
        $filters[] = "event_type LIKE :event_type";
    }
    if (!empty($filters)) {
        $whereClause = 'WHERE ' . implode(' AND ', $filters);
    }

    $query = "SELECT id, description, created_at, source_ip, event_type, severity FROM logs $whereClause LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($query);

    // Bind parameters
    if (!empty($_GET['start_date'])) {
        $stmt->bindValue(':start_date', $_GET['start_date']);
    }
    if (!empty($_GET['end_date'])) {
        $stmt->bindValue(':end_date', $_GET['end_date']);
    }
    if (!empty($_GET['severity'])) {
        $stmt->bindValue(':severity', $_GET['severity']);
    }
    if (!empty($_GET['event_type'])) {
        $stmt->bindValue(':event_type', '%' . $_GET['event_type'] . '%');
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM logs $whereClause";
        $countStmt = $conn->prepare($countQuery);

        // Bind same parameters for total count query
        if (!empty($_GET['start_date'])) {
            $countStmt->bindValue(':start_date', $_GET['start_date']);
        }
        if (!empty($_GET['end_date'])) {
            $countStmt->bindValue(':end_date', $_GET['end_date']);
        }
        if (!empty($_GET['severity'])) {
            $countStmt->bindValue(':severity', $_GET['severity']);
        }
        if (!empty($_GET['event_type'])) {
            $countStmt->bindValue(':event_type', '%' . $_GET['event_type'] . '%');
        }


        $countStmt->execute();
        $totalLogs = $countStmt->fetch()['total'];

        echo json_encode([
            'success' => true,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => ceil($totalLogs / $limit),
        ]);
        $jsonData = json_encode($totalLogs,JSON_PRETTY_PRINT);
    $filePath= 'data.json';
    file_put_contents($filePath,$jsonData);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function getSeverityDistribution() {
    global $conn;

    $query = "SELECT 
                SUM(CASE WHEN severity = 'High' THEN 1 ELSE 0 END) AS high,
                SUM(CASE WHEN severity = 'Medium' THEN 1 ELSE 0 END) AS medium,
                SUM(CASE WHEN severity = 'Low' THEN 1 ELSE 0 END) AS low
              FROM logs";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'severityCounts' => [
                'high' => $result['high'] ?? 0,
                'medium' => $result['medium'] ?? 0,
                'low' => $result['low'] ?? 0,
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
