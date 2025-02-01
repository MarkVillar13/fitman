<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    include('head.php');
     ?>
  </head>
  <body class="">
    <header class="" style="z-index:10000">
      <?php
      include ('header.php');
       ?>
    </header>
    <nav style="z-index:10000">
      <?php
      include ('navigations/nav_main.php');
      ?>
    </nav>
    <?php
    if (isset($_POST['logout'])) {
      session_destroy();
      unset($_SESSION['email']);
      unset($_SESSION['security']);
    }
    ?>
    <div class="w3-col w3-display-container merienda-regular" style="top:35vh">
      <?php
      include ('pages/dashboard.php');
      ?>
    </div>
    <footer class="w3-bottom w3-text-black" style="z-index:10000">
      <?php
       include('footer.php');
       ?>
    </footer>
  </body>
</html>
