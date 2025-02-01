<?php
require_once('connection.php');

$date = $_POST['date'];
$employeeId = $_POST['employeeId'];

$query = "SELECT COUNT(*) as count FROM attendance 
          WHERE DATE(ScanTime) = ? AND EmployeeID = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("ss", $date, $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode(['hasAttendance' => $row['count'] > 0]);
?>