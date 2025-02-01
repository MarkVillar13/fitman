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
    <div class="w3-col min-vh-100" id="page" style="background: #f6f6f6">
      <?php
      // include 'pages/searchSales.php';
      include ('pages/subscription.php');
      ?>
    </div>
    <!-- <div class="w3-col w3-white" id="page">
      <?php
      include ('pages/subscriptions.php');
      ?>
    </div> -->
  </body>
</html>
