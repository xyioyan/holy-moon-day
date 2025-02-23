<?php
require 'db.php'; // Database connection

if (isset($_GET['action']) && $_GET['action'] === 'export_logs') {
    exportLogs();
}

function exportLogs() {
    global $conn;

    // Apply any filters
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

    // Fetch logs from the database
    $query = "SELECT id, description, created_at, source_ip, event_type, severity 
              FROM logs $whereClause";
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

    // Execute query and fetch results
    try {
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set the headers for CSV file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="logs_export.csv"');

        // Open output stream to write CSV
        $output = fopen('php://output', 'w');

        // Add column headers to CSV
        fputcsv($output, ['ID', 'Description', 'Timestamp', 'Source IP', 'Event Type', 'Severity']);

        // Write log data to CSV
        foreach ($logs as $log) {
            fputcsv($output, $log);
        }

        fclose($output); // Close output stream
        exit(); // Stop further execution
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
