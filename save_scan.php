<?php
include ('database.php');
// Set the default timezone to the Philippines
date_default_timezone_set('Asia/Manila');
// Retrieve the scanned content and the current time
$scannedContent = $_POST['content'];
$currentTime = date("Y-m-d H:i:s");
$checkUsername= "SELECT lower(last_name), lower(first_name), email, user_id FROM users WHERE email='$scannedContent'";
$checkResult= mysqli_query($db, $checkUsername);
$result= mysqli_fetch_assoc($checkResult);
$lname=$result['lower(last_name)'];
$name=$result['lower(first_name)'];
$EmployeeID=$result['user_id'];
// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$countContent="SELECT count(EmployeeID) FROM attendance WHERE EmployeeID='$EmployeeID'";
$checkResultcountContent= mysqli_query($db, $countContent);
$resultcheckResultcountContent= mysqli_fetch_assoc($checkResultcountContent);
$count=$resultcheckResultcountContent['count(EmployeeID)'];

if($count % 2 == 0){
// Insert into the database
$sql = "INSERT INTO attendance (EmployeeID, ScanTime, ScanType) VALUES ('$EmployeeID', '$currentTime', 'In' )";

} else{
  $sql = "INSERT INTO attendance (EmployeeID, ScanTime, ScanType) VALUES ('$EmployeeID', '$currentTime', 'Out')";
}

if ($db->query($sql) === TRUE) {
    array_push($errors, "RECORDED! Your logs has been updated.");
} else {
    echo "Error: " . $sql . "<br>" . $db->error;
    array_push($errors, "Ooops! Something went wrong. Please, try again.");
}

$db->close();
?>
