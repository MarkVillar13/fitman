<nav class="navbar navbar-expand-sm w3-theme-d5 w3-top">
  <div class="container-fluid">
  <a href="index.php" class="logo">
    <img src="logo.png" alt="The Northern Might" class="logo-img">The Northern Might
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="fa fa-bars w3-text-white"></span>
    </button>
    <div class="collapse navbar-collapse w3-padding" id="collapsibleNavbar">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a href="home.php" class="nav-link">Home</a>
        </li>
        <?php if ($role_name=="Admin") { ?>
  <li class="nav-item">
    <a href="pos.php" class="nav-link">POS</a>
  </li>
  <li class="nav-item">
    <a href="subscription.php" class="nav-link">Subscriptions</a>
  </li>
  <li class="nav-item">
    <a href="mails.php" class="nav-link">Mails</a>
  </li>
  <li class="nav-item">
    <a href="offerings_products.php" class="nav-link ">Products</a>
  </li>
  <li class="nav-item">
    <a href="offerings_services.php" class="nav-link">Services</a>
  </li>
  <li class="nav-item">
    <a href="users.php" class="nav-link">Users</a>
  </li>
  <li class="nav-item">
    <a href="dtr.php" class="nav-link">Attendance</a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link  position-relative" href="#" role="button" data-bs-toggle="dropdown">
      <i class="fas fa-bell"></i>
      <?php
      // Query to count products expiring next month
      $today = new DateTime();
      $next_month = new DateTime('first day of next month');
      $last_day_next_month = new DateTime('last day of next month');
      
      $expiring_query = "SELECT COUNT(*) as count FROM products 
                        WHERE expiration BETWEEN '{$next_month->format('Y-m-d')}' 
                        AND '{$last_day_next_month->format('Y-m-d')}'
                        AND picture != ''";
      $expiring_result = $db->query($expiring_query);
      $count = $expiring_result->fetch_assoc()['count'];
      
      if ($count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?= $count ?>
          <span class="visually-hidden">expiring products</span>
        </span>
      <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
      <h6 class="dropdown-header">Expiring Products</h6>
      <?php
      // Get details of expiring products
      $products_query = "SELECT name, expiration, price 
                        FROM products 
                        WHERE expiration BETWEEN '{$next_month->format('Y-m-d')}' 
                        AND '{$last_day_next_month->format('Y-m-d')}'
                        AND picture != ''
                        ORDER BY expiration ASC";
      $products_result = $db->query($products_query);
      
      if ($products_result->num_rows > 0):
        while($product = $products_result->fetch_assoc()): ?>
          <a class="dropdown-item notification-item border-bottom py-3" href="offerings_products.php">
            <div class="d-flex align-items-center gap-2 w-100">
              <i class="fas fa-exclamation-triangle text-warning"></i>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="mb-0 text-warning" style="font-size: 14px;">Expiring Products</h6>
                  <h6 class="mb-0" style="font-size: 12px; color: #6c757d;">
                    <?= date('M d, Y', strtotime($product['expiration'])) ?>
                  </h6>
                </div>
              <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted" style="font-size: 12px;">
                  <?= htmlspecialchars($product['name']) ?> - ₱<?= number_format($product['price'], 2) ?>
                </small>
              </div>
              </div>
            </div>
          </a>
        <?php endwhile;
      else: ?>
        <div class="dropdown-item text-center py-3">
          <p class="text-muted mb-0">No products expiring next month</p>
        </div>
      <?php endif; ?>
    </div>
  </li>
<?php } ?>
        <?php if ($role_name=="Customer") { ?>
          <li class="nav-item">
            <a href="transactions.php" class="nav-link ">Transactions</a>
          </li>
          <li class="nav-item">
          <a href="subscription.php" class="nav-link">Subscriptions</a>
        </li>
          <li class="nav-item">
            <a href="mails.php" class="nav-link ">Contact Us</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link " href="#" role="button" data-bs-toggle="dropdown">Work-outs</a>
            <ul class="dropdown-menu">
              <li><a href="barbell.php" class="dropdown-item">Barbell</a></li>
              <li><a href="dumbbell.php" class="dropdown-item">Dumbbell</a></li>
              <li><a href="stretching.php" class="dropdown-item">Stretching</a></li>
              <li><a href="resistance-loop.php" class="dropdown-item">Resistance Loop</a></li>
              <li><a href="cable.php" class="dropdown-item">Cable</a></li>
              <li><a href="bodyweight.php" class="dropdown-item">Bodyweight</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link " href="#" role="button" data-bs-toggle="dropdown">
              <span class='fas fa-clipboard-check'></span>
            </a>
            <ul class="dropdown-menu">
              <li><i class="dropdown-item">Respect others</i></li>
              <li><i class="dropdown-item">Entry without socks is restricted</i></li>
              <li><i class="dropdown-item">Don't leave accessories or your belongings lying around</i></li>
              <li><i class="dropdown-item">No smoking within the gym premises</i></li>
              <li><i class="dropdown-item">Dispose your trash properly</i></li>
              <li><i class="dropdown-item">Re-rack your weights</i></li>
              <li><i class="dropdown-item">Do not slam/drop weights</i></li>
              <li><i class="dropdown-item">Don't hog the equipment</i></li>
              <li><i class="dropdown-item">Disinfect equipment after use</i></li>
              <li><i class="dropdown-item">Do not hoard weights</i></li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="calendar.php" class="nav-link"><i class='fas fa-calendar'></i></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link  position-relative" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-bell"></i>
              <?php
            $notification_query = "SELECT 
            (SELECT COUNT(*) FROM reminders 
             WHERE user_id = '$user_id' 
             AND notification_status = 'unread' 
             AND DATE(reminder_date) = CURDATE()
             AND reminder_date <= NOW() 
             AND status = 'Not Started') +
            (SELECT COUNT(*) FROM reminders 
             WHERE user_id = '$user_id' 
             AND DATE(reminder_date) = CURDATE()
             AND reminder_date > NOW() 
             AND status = 'Not Started') as count";
              $notification_result = $db->query($notification_query);
              $count = $notification_result->fetch_assoc()['count'];
              if ($count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  <?= $count ?>
                  <span class="visually-hidden">unread notifications</span>
                </span>
              <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
              <h6 class="dropdown-header">Notifications</h6>

              <?php
              $currentTime = new DateTime();

              // Query for missed workouts
              $missed_query = "
                  SELECT *, 
                  'Missed' AS display_status
                  FROM reminders 
                  WHERE user_id = '$user_id' 
                  AND DATE(reminder_date) = CURDATE()
                  AND reminder_date <= '{$currentTime->format('Y-m-d H:i:s')}' 
                  AND status = 'Not Started'
                  ORDER BY reminder_date ASC
              ";
              $missed_result = $db->query($missed_query);

              // Query for upcoming/today's workouts
              $today_query = "
                  SELECT *, 
                  'Today' AS display_status
                  FROM reminders 
                  WHERE user_id = '$user_id' 
                  AND DATE(reminder_date) = CURDATE()
                  AND reminder_date > '{$currentTime->format('Y-m-d H:i:s')}' 
                  AND status = 'Not Started'
                  ORDER BY reminder_date ASC
              ";
              $today_result = $db->query($today_query);

              // Count total upcoming/today's workouts
              $today_workout_count = $today_result->num_rows;
              ?>

              <?php if ($today_workout_count > 0): ?>
                  <div class="dropdown-item notification-item border-bottom py-3">
                      <div class="d-flex align-items-center gap-2 w-100">
                          <i class="fas fa-dumbbell text-primary"></i>
                          <div class="flex-grow-1">
                              <h6 class="mb-0 text-primary" style="font-size: 14px;">
                                  There are <?= $today_workout_count ?> pending workouts for today
                              </h6>
                          </div>
                      </div>
                  </div>
              <?php endif; ?>

              <?php if ($missed_result->num_rows > 0): ?>
                  <?php while($workout = $missed_result->fetch_assoc()): ?>
                      <a class="dropdown-item notification-item border-bottom py-3" href="calendar.php">
                          <div class="d-flex align-items-center gap-2 w-100">
                              <i class="fas fa-dumbbell text-danger"></i>
                              <div class="flex-grow-1">
                                  <div class="d-flex justify-content-between align-items-center">
                                      <h6 class="mb-0 text-danger" style="font-size: 14px;">Missed Workout</h6>
                                      <h6 class="mb-0" style="font-size: 12px; color: #6c757d;">
                                          <?= date('h:i A', strtotime($workout['reminder_date'])) ?>
                                      </h6>
                                  </div>
                                  <small class="text-muted" style="font-size: 12px;"><?= htmlspecialchars($workout['reminder_text']) ?></small>
                              </div>
                          </div>
                      </a>
                  <?php endwhile; ?>
              <?php else: ?>
                  <div class="dropdown-item text-center py-3">
                      <p class="text-muted mb-0">No new notifications</p>
                  </div>
              <?php endif; ?>
            </div>
          </li>
        <?php } ?>
        <li class="nav-item">
          <a href="profile.php" class="nav-link "><i class="fas fa-user-shield"></i></a>
        </li>
        <li class="nav-item">
          <form class="" action="index.php" method="post">
            <button type="submit" class="nav-link" name="logout"><i class="fas fa-sign-out-alt"> logout</i></button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>


<style>
.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
}

.logo:hover {
  color: white;
  opacity: 0.8;
  text-decoration: none;
}

.logo-img {
  height: 40px;
  width: auto;
  transition: transform 0.3s ease;
}

.logo:hover .logo-img {
  transform: scale(1.05);
}

@media (max-width: 576px) {
 .logo-img {
   height: 32px;
 }
}
  .navbar {
    z-index: 2147483647 !important;
}

.nav-item .fas {
  transition: transform 0.3s ease;
}

.navbar-nav {
    z-index: 2147483647 !important;
}

.nav-item.dropdown {
    z-index: 2147483647 !important;
}

.dropdown-menu.notification-dropdown {
    z-index: 2147483647 !important;
}
  .navbar .nav-item.dropdown {
    position: relative;
    z-index: 2147483647 !important;
}
.navbar {
    position: relative !important;
}

.nav-item.dropdown {
    position: relative !important;
}

.dropdown-menu.notification-dropdown {
    position: absolute !important;
}
.w3-top {
    z-index: auto !important;
}
 .notification-dropdown {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
    z-index: 2147483647 !important;
    min-width: 320px !important; /* Added minimum width */
    right: -100px !important; /* Adjust position */
    background-color: white !important; /* Ensure white background */
    padding: 8px 0;
}

.notification-item {
    padding: 12px 16px !important;
    width: 100% !important;
    white-space: normal !important; /* Allow text wrapping */
}

/* Ensure dropdown header is styled properly */
.dropdown-header {
    padding: 12px 16px;
    font-size: 16px;
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}


  .notification-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
  }

  .notification-item i {
    font-size: 1.25rem;
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }

  .badge.bg-danger {
    animation: pulse 2s infinite;
  }
  .navbar {
 background-color: #5719a8 !important;
 padding: 1rem;
 position: fixed !important;
 width: 100%;
 top: 0;
 z-index: 2147483647 !important;
}

.navbar-brand {
 font-size: 1.2rem;
 font-weight: bold;
 color: white !important;
 text-decoration: none;
 margin-right: 2rem;
}

.navbar-nav {
 margin-left: auto;
 align-items: center;
 z-index: 2147483647 !important;
}

.nav-item.dropdown {
 position: relative !important;
 z-index: 2147483647 !important;
}

.dropdown-menu.notification-dropdown {
 box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
 border: none;
 border-radius: 0.5rem;
 z-index: 2147483647 !important;
 min-width: 320px !important;
 right: -100px !important;
 background-color: white !important;
 padding: 8px 0;
 position: absolute !important;
}

.notification-item {
 padding: 12px 16px !important;
 width: 100% !important;
 white-space: normal !important;
}

.dropdown-header {
 padding: 12px 16px;
 font-size: 16px;
 font-weight: 600;
 background-color: #f8f9fa;
 border-bottom: 1px solid #e9ecef;
}

.notification-item:hover {
 background-color: rgba(0, 0, 0, 0.05);
}

.notification-item i {
 font-size: 1.25rem;
}

.nav-link {
  color: rgba(255,255,255,0.85) !important;
  font-weight: 500;
  padding: 0.5rem 1rem !important;
  position: relative;
  transition: all 0.3s ease;
}

.nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  background-color: white;
  transform: translateX(-50%);
  transition: width 0.3s ease;
}
.nav-link:hover::after {
  width: 70%;
}
.nav-link:hover {
  color: white !important;
  transform: translateY(-1px);
}

.navbar-toggler {
 border: none;
 padding: 0.5rem;
}

.navbar-toggler:focus {
 box-shadow: none;
}

.navbar-toggler span {
 color: white;
}

.dropdown-menu {
  background: white;
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.dropdown-menu.show {
  opacity: 1;
  transform: translateY(0);
}

.dropdown-item {
  padding: 0.75rem 1.25rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.dropdown-item:hover {
  background: rgba(87, 25, 168, 0.1);
  transform: translateX(5px);
}

button[name="logout"] {
  background: rgba(255,255,255,0.1);
  border-radius: 6px;
  padding: 0.5rem 1rem;
}

button[name="logout"]:hover {
  background: rgba(255,255,255,0.2);
  transform: translateY(-1px);
}

@keyframes pulse {
 0% { transform: scale(1); }
 50% { transform: scale(1.2); }
 100% { transform: scale(1); }
}

.badge.bg-danger {
 animation: pulse 2s infinite;
}

@media (max-width: 576px) {
 .navbar-nav {
   padding: 1rem 0;
 }
 
 .nav-link {
   padding: 0.75rem 0 !important;
 }
}
.container-fluid {
  padding-left: 6rem;
  padding-right: 6rem;
}
@media (max-width: 768px) {
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>

<script>
$(document).ready(function() {
    // Mark notifications as read when dropdown is opened
    $('.notification-dropdown').on('show.bs.dropdown', function () {
        markNotificationsAsRead();
    });

    function markNotificationsAsRead() {
        $.ajax({
            url: 'mark_notifications_read.php',
            method: 'POST',
            success: function() {
                $('.badge.bg-danger').fadeOut();
            }
        });
    }
});
</script>