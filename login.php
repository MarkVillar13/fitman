<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    include('head.php');
     ?>
  </head>
  <body class="">
    <!-- <header class="" style="">
      <?php
      include ('header.php');
       ?>
    </header> -->
    <nav>
      <?php
      include ('navigations/nav_main.php');
      ?>
    </nav>
    <div class="w3-col merienda-regular" id="page">
      <?php
      include ('pages/login.php');
      ?>
    </div>
    <footer class="w3-bottom w3-text-black" style="z-index:200">
      <?php
       include('footer.php');
       ?>
    </footer>
  </body>
</html>
