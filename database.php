<?php
date_default_timezone_set('Asia/Manila');

$host = 'roundhouse.proxy.rlwy.net';
$username = 'root';
$password = 'EWQxjnDgBwJvYBtVirFUlHxJRPethxcT';
$dbname = 'fitman';
$port = 53624;

// Create a connection to the database
$db = new mysqli($host, $username, $password, $dbname, $port);

// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>