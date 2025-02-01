<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('database.php');

// Log the incoming request
error_log("Received request with user_id: " . $_GET['user_id']);

// Check if the user_id is provided and valid
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(["error" => "Missing or invalid user_id"]);
    exit();
}

$user_id = $_GET['user_id'];

try {
    // Prepare the query
    $query = "SELECT 
        subscription_id,
        user_id,
        start_date,
        end_date,
        total_duration,
        additional_duration,
        status,
        last_updated
    FROM subscriptions 
   WHERE user_id = ? AND isAdditional = 0";

    $stmt = $db->prepare($query);

    if ($stmt === false) {
        throw new Exception("Failed to prepare query: " . $db->error);
    }

    $stmt->bind_param("i", $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    $subscriptions = [];
    while ($row = $result->fetch_assoc()) {
        $subscriptions[] = [
            'subscription_id' => $row['subscription_id'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'total_duration' => $row['total_duration'],
            'additional_duration' => $row['additional_duration'],
            'status' => $row['status'],
            'last_updated' => $row['last_updated']
        ];
    }

    echo json_encode($subscriptions);

} catch (Exception $e) {
    error_log("Error in getSubscriptions.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
}
?>