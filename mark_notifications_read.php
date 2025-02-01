<?php
session_start();
require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];

$update_query = "UPDATE reminders 
                SET notification_status = 'read' 
                WHERE user_id = ? 
                AND notification_status = 'unread'
                AND status = 'Not Started'
                AND (
                    DATE(reminder_date) = CURDATE()
                    OR DATE(reminder_date) < CURDATE()
                )";

$stmt = $db->prepare($update_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

echo 'success';
?>