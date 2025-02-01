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
    <section>
        <main class="w3-col content" style="">
            <?php include('pages/index.php'); ?>
        </main>
    </section>
    <div class="w3-col" style="height:72vh">
      <?php
      include ('pages/home.php');
      ?>
    </div>
    <footer class="w3-bottom w3-text-black" style="z-index:10000">
      <?php
       include('footer.php');
       ?>
    </footer>
  </body>
</html>
