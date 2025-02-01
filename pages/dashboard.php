<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$modalTitle = "";
$modalClass = "";

function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
}

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'raquelpilapil13@gmail.com'; 
        $mail->Password = 'vcoghxjjcncvqvqs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
     
        $mail->setFrom('raquelpilapil13@gmail.com', 'FitMan');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Fitman Account';
        $mail->Body = "
            <h2>Welcome to Fitman!</h2>
            <p>Your verification code is: <strong>$code</strong></p>
            <p>Please enter this code to complete your registration.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['signup'])) {
    $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
    $role = mysqli_real_escape_string($db, $_POST['role']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Check for existing email
    $checkDoubleEntry = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($db, $checkDoubleEntry);
    $user = mysqli_fetch_assoc($result);

    if ($user && $user['email'] == $email) {
        $message = "Email is already registered. Please try logging in or use a different email.";
        $modalTitle = "ERROR!";
        $modalClass = "text-danger";
    } else {
        // Generate verification code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Send verification email
        if (sendVerificationEmail($email, $verificationCode)) {
            // Store registration data in session
            $_SESSION['temp_user'] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => md5($password),
                'role' => $role,
                'verification_code' => $verificationCode
            ];
            
            // Redirect to verification page
            redirect("verify.php");
            exit();
        } else {
            $message = "Error sending verification email. Please try again.";
            $modalTitle = "Error!";
            $modalClass = "text-danger";
        }
    }
}
?>

<?php include('scripts/modal.php'); ?>
<div class="w3-display-middle w3-half" style="margin-top: 0;">
    <div class="signup-container">
        <h2 class="signup-title">Be a member of our growing community</h2>
        <p class="signup-subtitle">Sign-up Now!</p>
        <form class="signup-form" action="" method="post">
    <div class="form-row">
        <div class="form-col-7">
            <div class="form-floating">
                <input type="text" class="form-control" placeholder="First Name" name="firstname" required>
                <label for="firstname">First Name</label>
            </div>
        </div>
        <div class="form-col-5">
            <div class="form-floating">
                <input type="text" class="form-control" placeholder="Last Name" name="lastname" required>
                <label for="lastname">Last Name</label>
            </div>
        </div>
    </div>

    <input type="hidden" name="role" value="2" required>
    
    <div class="form-row">
        <div class="form-col-7">
            <div class="form-floating">
                <input type="email" class="form-control" placeholder="Email" name="email" required>
                <label for="email">Email</label>
            </div>
        </div>
        <div class="form-col-5">
            <div class="form-floating password-container">
                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                <label for="password">Password</label>
                <div class="password-requirements" style="display: none;">
                    <p class="requirements-title">Password must contain:</p>
                    <ul class="requirements-list">
                        <li id="length" class="invalid">
                            <i class="fas fa-times-circle"></i> 8+ characters
                        </li>
                        <li id="lowercase" class="invalid">
                            <i class="fas fa-times-circle"></i> One lowercase letter
                        </li>
                        <li id="uppercase" class="invalid">
                            <i class="fas fa-times-circle"></i> One uppercase letter
                        </li>
                        <li id="number" class="invalid">
                            <i class="fas fa-times-circle"></i> One number
                        </li>
                        <li id="special" class="invalid">
                            <i class="fas fa-times-circle"></i> One special character
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Show Password Toggle -->
    <div class="show-password-container">
        <div class="form-check">
            <input type="checkbox" id="showPassword" class="form-check-input">
            <label class="form-check-label" for="showPassword">Show Password</label>
        </div>
    </div>

    <!-- Footer with link and button -->
    <div class="form-footer">
        <span class="login-link">Already a user? Login <a href="login.php">here</a>!</span>
        <button type="submit" name="signup" class="signup-button">Sign-up</button>
    </div>
</form>
        
    </div>
</div>

<style>
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
}

.form-check-label {
    margin-bottom: 0;
    color: black;
}
.signup-container {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 100%;
}

.signup-title {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin: 0;
    font-weight: 600;
}

.signup-subtitle {
    text-align: center;
    color: #666;
    margin: 8px 0 24px;
    font-size: 16px;
}

.signup-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 5px;
}

.form-col-7 {
    flex: 7;
}

.form-col-5 {
    flex: 5;
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

.password-container {
    position: relative;
}

.password-requirements {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.requirements-title {
    color: #666;
    font-size: 14px;
    margin-bottom: 8px;
    font-weight: 500;
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirements-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #dc3545;
    font-size: 14px;
    padding: 4px 0;
    transition: color 0.2s ease;
}

.requirements-list li.valid {
    color: #198754;
}

.requirements-list li i {
    min-width: 16px;
    transition: color 0.2s ease;
}

.requirements-list li.valid i {
    color: #198754;
}

.show-password-row {
    display: flex;
    align-items: center;
    margin-top: -10px;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-wrapper label {
    color: #495057;
    font-size: 14px;
    cursor: pointer;
}

.form-check-input {
    cursor: pointer;
}

.form-footer {
    display: flex;
    justify-content: space-between; /* Space between the login link and the sign-up button */
    align-items: center;
    margin-top: 10px;
    width: 100%;
}


.login-link {
    color: #495057;
    font-size: 14px;
}

.login-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}

.signup-button {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
    width: 100%; /* Full width */
    max-width: 300px; /* Optional: Restrict the button width */
    margin-left: auto; /* Aligns the button to the right */
}

.signup-button:hover {
    background: #0b5ed7;
}
.login-link {
    color: #495057;
    font-size: 14px;
}

.login-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById('password');
    const requirements = document.querySelector('.password-requirements');
    const showPasswordCheckbox = document.getElementById('showPassword');

    if (!passwordInput || !requirements || !showPasswordCheckbox) return;

    // Show password toggle functionality
    showPasswordCheckbox.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    // Rest of your existing password validation code...
    passwordInput.addEventListener('focus', function() {
        if (this.value.length > 0) {
            requirements.style.display = 'block';
        }
    });

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length === 0) {
            requirements.style.display = 'none';
            this.style.borderColor = '';
        } else {
            requirements.style.display = 'block';
            const allValid = checkPassword(password);
            
            if (allValid) {
                setTimeout(() => {
                    requirements.style.display = 'none';
                }, 500);
            }
        }
    });

    function checkPassword(password) {
        const requirements = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        let allValid = true;

        for (const [requirement, isValid] of Object.entries(requirements)) {
            const element = document.getElementById(requirement);
            if (element) {
                element.className = isValid ? 'valid' : 'invalid';
                const icon = element.querySelector('i');
                if (icon) {
                    icon.className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
                }
                if (!isValid) allValid = false;
            }
        }

        passwordInput.style.borderColor = allValid ? '#198754' : '#dc3545';
        return allValid;
    }
});
</script>