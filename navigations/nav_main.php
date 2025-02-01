<?php 
$is_root = $_SERVER['REQUEST_URI'] == '/' || 
           $_SERVER['REQUEST_URI'] == '/index.php' || 
           basename($_SERVER['PHP_SELF']) == 'index.php';

if (!isset($_SESSION['user_id'])) { ?>
<nav class="auth-nav fixed-top py-4">
  <div class="nav-wrapper">
    <?php if (!$is_root) { ?>
      <a href="#" onclick="history.back()" class="back-btn">
        <i class="fas fa-arrow-left text-white"></i> Back
      </a>
    <?php } else { ?>
      <a href="index.php" class="logo">
      <img src="logo.png" alt="The Northern Might" class="logo-img">
 The Northern Might
</a>
    <?php } ?>
    
    <div class="auth-buttons">
      <a id="signup-btn" href="dashboard.php" class="btn btn-light auth-btn">
        <i class="fas fa-user-plus"></i> Sign Up
      </a>
      <a id="login-btn" href="login.php" class="btn btn-outline-light auth-btn">
        <i class="fas fa-sign-in-alt"></i> Login
      </a>
    </div>
  </div>
</nav>

<style>
  .logo {
 display: flex;
 align-items: center;
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
.auth-nav {
  background: #5719a8;
  padding: 0.75rem 1rem;
  z-index: 1030;
}

.nav-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.back-btn, .auth-btn {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: opacity 0.2s;
}

.back-btn:hover, .auth-btn:hover {
  opacity: 0.8;
  color: white;
}

.auth-buttons {
  display: flex;
  gap: 1rem;
}

@media (max-width: 576px) {
  .auth-nav {
    padding: 0.5rem;
  }
  
  .auth-buttons {
    gap: 0.5rem;
  }
  
  .auth-btn {
    padding: 0.5rem;
  }
}
.logo {
  color: white;
  text-decoration: none;
  font-weight: bold;
  font-size: 1.2rem;
}

.logo:hover {
  color: white;
  opacity: 0.8;
}
.auth-btn {
  font-weight: 500;
  padding: 0.5rem 1.25rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.btn-light.auth-btn {
  background: white;
  color: #5719a8;
  border: 2px solid white;
}

.btn-light.auth-btn:hover {
  background: rgba(255,255,255,0.9);
  color: #5719a8;
}

.btn-outline-light.auth-btn:hover {
  background: rgba(255,255,255,0.1);
}
</style>

<script>
window.addEventListener('load', function() {
  const currentPage = window.location.pathname.replace(/^\/+|\/+$/g, '').split('/').pop();
  
  if (currentPage === 'login.php') {
    document.getElementById('login-btn').style.display = 'none';
  }
  
  if (currentPage === 'dashboard.php') {
    document.getElementById('signup-btn').style.display = 'none';
  }
});
</script>
<?php } ?>