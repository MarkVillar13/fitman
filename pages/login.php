<?php
$messageForgot = "";
$message = "";
$modalTitle = "";
$modalClass = "";

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generatePassword($length = 8) {
 $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
 $charactersLength = strlen($characters);
 $randomPassword = '';
 for ($i = 0; $i < $length; $i++) {
     $randomPassword .= $characters[rand(0, $charactersLength - 1)];
 }
 return $randomPassword;
}

if (isset($_POST['login'])) {
 $email = mysqli_real_escape_string($db, $_POST['email']);
 $password = md5(mysqli_real_escape_string($db, $_POST['password']));
 
 $checkUser = "SELECT * FROM users WHERE email = '$email' AND password = '$password' AND status = 'active' LIMIT 1";
 $result = mysqli_query($db, $checkUser);
 $user = mysqli_fetch_assoc($result);

 if ($user) {
     if (!$user['verified']) {
         $message = "Account pending admin verification";
         $modalTitle = "Verification Required";
         $modalClass = "text-warning";
     } else {
         $_SESSION['email'] = $email;
         $_SESSION['security'] = $password;
         header("Location: home.php?loginSuccess");
         exit();
     }
 } else {
     $message = "Incorrect Username or Password";
     $modalTitle = "Wrong Combination!";
     $modalClass = "text-danger";
 }
}

if(isset($_GET['forgotPassword'])){
 $messageForgot = "You are about to send an email to your account with your new password. Do you wish to continue?";
 $modalTitle = "Change Password";
 $modalClass = "text-danger";
}

if (isset($_GET['ChangePassword'])) {
 $emailReset = mysqli_real_escape_string($db, $_GET['email']); 
 
 // Verify email exists
 $checkEmail = mysqli_query($db, "SELECT * FROM users WHERE email = '$emailReset' AND status = 'active'");
 if(mysqli_num_rows($checkEmail) == 0) {
   $message = "Email not found or account inactive";
   $modalTitle = "Error";
   $modalClass = "text-danger";
   return;
 }

 $password = generatePassword();
 $fpassword = md5($password);

 try {
   $mail = new PHPMailer(true);
   $mail->isSMTP();
   $mail->Host = 'smtp.gmail.com';
   $mail->SMTPAuth = true;
   $mail->Username = 'raquelpilapil13@gmail.com'; 
   $mail->Password = 'vcoghxjjcncvqvqs';
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $mail->Port = 587;

   $mail->setFrom('raquelpilapil13@gmail.com', 'FitMan');
   $mail->addAddress($emailReset);
   $mail->isHTML(true);
   $mail->Subject = "Password Reset";
   $mail->Body = "Your new password is: ".$password.". Please change it after logging in.";

   if($mail->send()) {
     // Only update password if email sends successfully
     $updateQuery = mysqli_query($db, "UPDATE users SET password = '$fpassword' WHERE email = '$emailReset'");
     
     if($updateQuery) {
       header("Location: login.php?passwordReset=success");
       exit();
     } else {
       throw new Exception("Failed to update password in database");
     }
   } else {
     throw new Exception("Failed to send email");
   }

 } catch (Exception $e) {
   $message = "Password reset failed: " . $e->getMessage();
   $modalTitle = "Error";
   $modalClass = "text-danger";
 }
}
?>

<?php include('scripts/modalForgotPassword.php'); ?>
<?php include('scripts/modal.php'); ?>
<!-- Main container -->
<div class="main-container">
  <!-- Login form container -->
  <div class="form-section">
    <div class="login-container">
      <h2 class="login-title">Welcome back!</h2>
      <h4 class="login-subtitle">Login to your account</h4>
      
      <form class="login-form" action="" method="post">
          <div class="form-floating mb-3">
              <input type="text" class="form-control" placeholder="Email" name="email" required>
              <label for="email">Email</label>
          </div>
          
          <div class="form-floating mb-3 password-container">
              <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
              <label for="password">Password</label>
          </div>

          <div class="show-password-container">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="showPassword">
        <label class="form-check-label" for="showPassword">Show Password</label>
    </div>
</div>

          <div class="form-footer">
              <div class="additional-links">
                  <a href="?forgotPassword" class="forgot-password">Forgot password?</a>
                  <span class="signup-link">Not yet a user? Signup <a href="dashboard.php">here</a>!</span>
              </div>
              <button type="submit" name="login" class="login-button">Login</button>
          </div>
      </form>
    </div>
  </div>

  <!-- Calendar section -->
  <div class="calendar-section">
    <h2 id="calendar-header">Calendar</h2>
    <table id="calendar" class="w3-table w3-text-black">
      <thead>
        <tr>
          <th>Sun</th>
          <th>Mon</th>
          <th>Tue</th>
          <th>Wed</th>
          <th>Thu</th>
          <th>Fri</th>
          <th>Sat</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<style>
/* Add these new styles for the layout */
.main-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 2rem;
    padding: 2rem;
    margin-top: 6rem;
    min-height: calc(100vh - 100px); /* Adjust based on your header/footer height */
}

.form-section {
    flex: 1;
    max-width: 500px;
    display: flex;
    justify-content: center;
}

.calendar-section {
    flex: 1;
    max-width: 400px;
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#calendar-header {
    color: #333;
    margin-bottom: 1.5rem;
    font-size: 24px;
    font-weight: 600;
}

/* Existing login styles */
.login-container {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
}

/* Rest of your existing styles remain the same */
.login-title {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 8px;
    font-weight: 600;
}

.login-subtitle {
    text-align: center;
    color: #666;
    margin: 8px 0 24px;
    font-size: 16px;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-floating {
    position: relative;
    width: 100%;
}

.form-control {
    width: 100%;
    padding: 1rem 0.75rem;
    font-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    outline: none;
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.show-password-container {
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-check-input {
    margin-top: 0;
    cursor: pointer;
}

.form-check-label {
    margin-bottom: 0;
    color: black;
    cursor: pointer;
}

.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    flex-wrap: wrap;
    gap: 15px;
}

.additional-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.forgot-password {
    color: #0d6efd;
    text-decoration: none;
    font-size: 14px;
}

.forgot-password:hover {
    text-decoration: underline;
}

.signup-link {
    color: #495057;
    font-size: 14px;
}

.signup-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.signup-link a:hover {
    text-decoration: underline;
}

.login-button {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.login-button:hover {
    background: #0b5ed7;
}

/* Responsive styles */
@media (max-width: 768px) {
    .main-container {
        flex-direction: column;
        align-items: center;
    }
    
    .form-section,
    .calendar-section {
        max-width: 100%;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .form-footer {
        flex-direction: column-reverse;
        align-items: stretch;
    }
    
    .login-button {
        width: 100%;
    }
    
    .additional-links {
        align-items: center;
        text-align: center;
    }
}
</style>

<?php include('scripts/calendar.js'); ?>
<?php
  include ('scripts/password.js');
 ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const showPasswordCheckbox = document.getElementById('showPassword');

    if (passwordInput && showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>