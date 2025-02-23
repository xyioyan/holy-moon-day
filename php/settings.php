<?php
// Database connection
require_once 'db.php';

// Fetch data from POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';
    

    // Handle actions
    switch ($action) {
        case 'fetchRules':
            fetchRules($conn);
            break;
        case 'addRule':
            addRule($conn, $data);
            break;
        case 'editRule':
            editRule($conn, $data);
            break;
        case 'deleteRule':
            deleteRule($conn, $data);
            break;
        case 'updateNotifications':
            updateNotifications($conn, $data);
            break;
        case 'getRule':
            getRule($conn, $data);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }
}

// Fetch all rules
function fetchRules($conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM rules ORDER BY created_at DESC");
        $stmt->execute();
        $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'rules' => $rules]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching rules: ' . $e->getMessage()]);
    }
}

// Add a new rule
function addRule($conn, $data) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $rule_name = $data['rule_name'] ?? '';
    $condition = $data['condition'] ?? '';
    $severity = $data['severity'] ?? '';
    $is_active = $data['is_active'] ?? 0;

    if (!empty($rule_name) && !empty($condition) && !empty($severity)) {
        try {
            $stmt = $conn->prepare("INSERT INTO rules (rule_name, `condition`, severity, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$rule_name, $condition, $severity, $is_active]);
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('New rule added', '$ip_address','RULE ADD SUCCESS', 'Medium')");
            $stmt1->execute();
            echo json_encode(['success' => true, 'newRuleId' => $conn->lastInsertId()]);

        } catch (Exception $e) {
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('New rule adding failed', '$ip_address','RULE ADD FAILD', 'High')");
            $stmt1->execute();
            echo json_encode(['success' => false, 'message' => 'Failed to add rule: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}

// Edit an existing rule
function editRule($conn, $data) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $rule_id = $data['rule_id'] ?? 0;
    $rule_name = $data['rule_name'] ?? '';
    $condition = $data['condition'] ?? '';
    $severity = $data['severity'] ?? '';
    $is_active = $data['is_active'] ?? 0;

    if ($rule_id && !empty($rule_name) && !empty($condition) && !empty($severity)) {
        try {
            $stmt = $conn->prepare("UPDATE rules SET rule_name = ?, `condition` = ?, severity = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$rule_name, $condition, $severity, $is_active, $rule_id]);
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('Rule edited', '$ip_address','RULE EDIT SUCCESS', 'High')");
            $stmt1->execute();
            echo json_encode(['success' => true]);
            
        } catch (PDOException $e) {
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('Rule edit faild', '$ip_address','RULE EDIT FAILD', 'Medium')");
            $stmt1->execute();
            echo json_encode(['success' => false, 'message' => 'Failed to edit rule: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}

// Delete a rule
function deleteRule($conn, $data) {
    $rule_id = $data['rule_id'] ?? 0;
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    if ($rule_id) {
        try {
            $stmt = $conn->prepare("DELETE FROM rules WHERE id = ?");
            $stmt->execute([$rule_id]);
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('Rule deleted', '$ip_address','RULE DELETE SUCCESS', 'High')");
            $stmt1->execute();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('Rule deletion failed', '$ip_address','RULE DELETE FAILED', 'Medium')");
            $stmt1->execute();
            echo json_encode(['success' => false, 'message' => 'Failed to delete rule: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}

// Update notification preferences
function updateNotifications($conn, $data) {
    $email_notifications = $data['email_notifications'] ?? 0;
    $sms_notifications = $data['sms_notifications'] ?? 0;
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];


    try {
        $stmt = $conn->prepare("UPDATE rules SET email_notifications = ?, sms_notifications = ? WHERE id = 1");
        $stmt->execute([$email_notifications, $sms_notifications]);
        $stmt1 = $conn->prepare("INSERT INTO logs (`description`, source_ip, event_type, severity) VALUES ('Notification preference set', '$ip_address','SET NOTIFICATION SUCCESS', 'Medium')");
            $stmt1->execute();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update notification settings: ' . $e->getMessage()]);
    }
}

function getRule($conn, $data) {
    try {
        // Check if an ID was provided
        $rule_id = $data['rule_id'] ?? null;

        if ($rule_id) {
            // Fetch a single rule by ID
            $stmt = $conn->prepare("SELECT * FROM rules WHERE id = ?");
            $stmt->execute([$rule_id]);
            $rule = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rule) {
                echo json_encode(['success' => true, 'rule' => $rule]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Rule not found.']);
            }
        } else {
            // Fetch all rules
            $stmt = $conn->prepare("SELECT * FROM rules ORDER BY created_at DESC");
            $stmt->execute();
            $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'rules' => $rules]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching rules: ' . $e->getMessage()]);
    }
}
