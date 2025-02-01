<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['security'])) {
  $_SESSION['msg'] = "You must log in first";
  header('location: index.php');
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
    <div class="w3-display-container" style="height:75vh">
      <div class="w3-half w3-display-middle" style="height:75vh">
        <img src="assets/img/462251936_1413955319992000_3580934758826825782_n.jpg" alt="Barbell Workout" style="max-width: 100%; height: auto;">
      </div>
    </div>
  </body>
</html>
