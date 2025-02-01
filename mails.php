<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['security'])) {
  $_SESSION['msg'] = "You must log in first";
  header('location: index.php');
}
include 'database.php';
if (isset($_GET['search'])) {
    $search = strtolower(mysqli_real_escape_string($db, $_GET['search']));

    // Query the database
    $userquery = mysqli_query($db, "SELECT * FROM users WHERE LOWER(CONCAT(last_name, first_name)) LIKE '%$search%'");

    if ($userquery->num_rows < 1) {
        echo '<i>...User not found...</i>';
    } else {
        while ($fetchUser = mysqli_fetch_assoc($userquery)) {
            $nameTo = strtolower($fetchUser['last_name']) . ", " . strtolower($fetchUser['first_name']);
            $id = $fetchUser['user_id'];
            echo "<a href='?inbox&to=$id' style='text-transform: capitalize; text-decoration: none;'>$nameTo</a><br>";
        }
    }
    exit(); // Stop further execution since this is an AJAX response
}
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    include('head.php');
     ?>
  </head>
  <body class="">
    <header class="" style="">
      <?php
      include ('header.php');
       ?>
    </header>
    <nav>
      <?php
      include ('navigations/nav_fitman.php');
      ?>
    </nav>
    <div class="w3-col merienda-regular" id="page">
      <div class="w3-container w3-padding-24" style="margin-top: 8rem">
        <div class="w3-col w3-center">
          <img src="assets/img/351324058_1658856091221403_4753558233883219508_n.jpg" alt="" style="height:40vh">
        </div>

        <div class="w3-col w3-center">
          <span>Let us know what you are thinking. Message us now!</span>
        </div>
        <!-- <div class="w3-col w3-center">
          <span class="w3-col mt-3 mb-3">List of Administrators to chat with:</span>
          <?php
          $selectAdmin=mysqli_query($db,"SELECT * FROM users WHERE role_id = '1'");
          while($fetchAdmin=mysqli_fetch_assoc($selectAdmin)){
            $idAdmin=$fetchAdmin['user_id'];
            $nameAdmin=$fetchAdmin['first_name']." ".$fetchAdmin['last_name'];
            echo "<a href='?inbox&to=$idAdmin' style='text-transform: capitalize; text-decoration: none;'>$nameAdmin</a><br>";
          }
           ?>
        </div> -->
      </div>
      <?php
      include ('pages/message.php');
      ?>
    </div>
  </body>
</html>
