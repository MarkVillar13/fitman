<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('head.php'); ?>
    <style>
        /* Fixed header and navigation for desktop */
        header, nav {
            position: fixed;
            width: 100%;
            z-index: 1000;
        }
        header {
            top: 0;
        }
        nav {
            top: 20vh;
        }
        .content {
            margin-top: 25vh;
            height: 72vh;
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 768px) {
            header {
                top: 0;
            }
            nav {
                top: 5vh; /* Reduced top spacing for mobile */
            }
            .content {
                margin-top: 0;
                height: 150vh;
            }
        }
    </style>
</head>
<body>
    <?php
    // Handle logout
    if (isset($_POST['logout'])) {
        session_destroy();
        unset($_SESSION['email']);
        unset($_SESSION['security']);

    }
    ?>

    <!-- Fixed Header and Navigation -->
    <!-- <header>
        <?php include('header.php'); ?>
    </header> -->
    <nav>
        <?php include('navigations/nav_main.php'); ?>
    </nav>

    <!-- Sections with content -->
    <!-- <section>
        <main class="w3-col content" style="">
            <?php include('pages/index.php'); ?>
        </main>
    </section> -->

    <section>
        <div class="w3-col" style="">
            <?php include('pages/home.php'); ?>
        </div>
    </section>

    <!-- Footer (appears only once) -->
    <footer class="w3-bottom w3-text-black" style="z-index: 1000;">
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>