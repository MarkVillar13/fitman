<?php
require_once('database.php');

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = "SELECT DATE(ScanTime) AS attendance_date, ScanType FROM attendance WHERE EmployeeID = ? GROUP BY attendance_date";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = [
            'date' => $row['attendance_date'],
            'status' => ($row['ScanType'] == 'IN') ? 'present' : 'absent'
        ];
    }
    
    // Send the response as JSON
    echo json_encode($attendanceData);
} else {
    echo json_encode(['error' => 'User ID is required']);
}
?>
