<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('database.php');  

// Check if there's pending registration
if (!isset($_SESSION['temp_user'])) {
    header("Location: signup.php");
    exit();
}
$message = "";
$modalTitle = "";
$modalClass = "";

if (isset($_POST['verify_code'])) {
    $temp_user = $_SESSION['temp_user'];
    $code = mysqli_real_escape_string($db, $_POST['code']);
    
    if ($code === $temp_user['verification_code']) {
        // Insert user into database
        $insertAccount = "INSERT INTO users (first_name, last_name, email, password, role_id, created_at, status, email_verified) 
                         VALUES (
                            '{$temp_user['firstname']}', 
                            '{$temp_user['lastname']}', 
                            '{$temp_user['email']}',
                            '{$temp_user['password']}',
                            '{$temp_user['role']}',
                            NOW(),
                            'active',
                            1
                         )";
        
        if (mysqli_query($db, $insertAccount)) {
            $message = "Email verified and account created successfully! You can now login.";
            $modalTitle = "Success!";
            $modalClass = "text-success";
            
            // Store the email temporarily for display
            $verifiedEmail = $temp_user['email'];
            
            // Clear temp user data
            unset($_SESSION['temp_user']);
            
            // Set refresh header for redirect after modal is shown
            header("refresh:3;url=login.php");
        } else {
            $message = "Error creating account: " . mysqli_error($db);
            $modalTitle = "Error!";
            $modalClass = "text-danger";
        }
    } else {
        $message = "Invalid verification code! Please try again.";
        $modalTitle = "Error!";
        $modalClass = "text-danger";
    }
}
?>

<?php include('scripts/modal.php'); ?>

<div class="container  w3-display-middle w3-half">
    <div class="w3-container w3-card-4 w3-padding">
        <h2 class="w3-center">Email Verification</h2>
        <div class="alert alert-info">
            A verification code has been sent to:<br>
            <strong><?php echo isset($verifiedEmail) ? $verifiedEmail : $_SESSION['temp_user']['email']; ?></strong>
            <br>Please check your email and enter the code below.
        </div>
        
        <form class="w3-container" method="post">
            <div class="form-floating mb-3 mt-3">
                <input type="text" class="form-control" name="code" placeholder="Enter verification code" maxlength="6" required>
                <label>Enter 6-digit Verification Code</label>
            </div>
            <button type="submit" name="verify_code" class="btn btn-primary w3-right">Verify Email</button>
        </form>
    </div>
</div>

<style>
    /* Container styling */
    .container {
        max-width: 500px;
        margin: auto;
    }

    /* Card styling */
    .w3-card-4 {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Padding for the content */
    .w3-padding-32 {
        padding: 32px;
    }

    /* Centered heading */
    h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Form styling */
    .form-floating {
        position: relative;
        margin-bottom: 16px;
    }

    /* Form control input field */
    .form-control {
        padding: 12px 16px;
        font-size: 16px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        width: 100%;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Button styling */
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-primary {
        background-color: #0d6efd;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    /* Margin for the button */
    .w3-right {
        float: right;
    }

    /* Alert box styling */
    .alert-info {
        background-color: #cce5ff;
        color: #004085;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-info strong {
        font-weight: bold;
    }
</style>