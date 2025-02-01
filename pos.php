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
    <?php
    if ($role_name == "Admin") {
     ?>
    <div class="w3-col min-vh-100" style="background: #f6f6f6" id="page">
      <?php
      // include 'pages/searchSales.php';
      include ('pages/pos.php');
      ?>
    </div>
    <!-- <div class="w3-col w3-white" id="page">
      <?php
      include ('pages/sales.php');
      ?>
    </div> -->
    <?php
    } else{ echo "<div class='h4 w3-display-middle w3-text-white'>You are not allowed to enter this page.</div>";}
     ?>
  </body>
</html>
