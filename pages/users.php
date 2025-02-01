<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$message = "";
$modalTitle = "";
$modalClass = "";

if ($role_name == "Admin") {
    $countAccounts = mysqli_query($db, "SELECT count(email) FROM users WHERE role_id = 2") or die(mysqli_error());
    $accountCount = mysqli_fetch_array($countAccounts);
    $countP=$accountCount['count(email)'];
    $totalPages=ceil($countP/10);
    if (isset($_GET['page'])){
        $currentPageGet=$_GET['page'];
        if($currentPageGet<=1){
            $currentPage=1;
            $offsetData=0;
        } elseif ($currentPageGet>=$totalPages) {
            $currentPage=$totalPages;
            $offsetData=10*($currentPage-1);
        } else {
            $currentPage=$_GET['page'];
            $offsetData=10*($currentPage-1);
        }
    } else {
        $currentPage=1;
        $offsetData=0;
    }

    function sendEmail($to, $subject, $messageBody) {
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
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $messageBody;
            return $mail->send();
        } catch (Exception $e) {
            throw new Exception("Email sending failed: " . $mail->ErrorInfo);
        }
    }

    // Handle Verification
    if (isset($_GET['Verify'])) {
        try {
            $email = $_GET['Verify'];
            $updateResult = mysqli_query($db, "UPDATE users SET verified = 1 WHERE email='$email'");
            if (!$updateResult) {
                throw new Exception("Database update failed");
            }
            $subject = "Account Verified";
            $messageBody = "Your account has been verified by the System Admin. You can now login to your account.";
            
            if (sendEmail($email, $subject, $messageBody)) {
                echo "<script>window.location.href='users.php?Verified=" . $email . "';</script>";
            }
        } catch (Exception $e) {
            echo "<script>
                alert('Operation failed: " . addslashes($e->getMessage()) . "');
                window.location.href = 'users.php';
            </script>";
        }
    }

    // Handle Deactivation
    if (isset($_GET['Deactivate'])) {
        try {
            $email = $_GET['Deactivate'];
            $updateResult = mysqli_query($db, "UPDATE users SET status='inactive' WHERE email='$email'");
            if (!$updateResult) {
                throw new Exception("Database update failed");
            }
            $subject = "Account Deactivated";
            $messageBody = "We are sorry to inform you that your account has been deactivated by the System Admin. To reactivate your account, you may seek the assistance of the FitMan Administrators.";
            
            if (sendEmail($email, $subject, $messageBody)) {
                echo "<script>window.location.href='users.php?Deactivated=" . $email . "';</script>";
            }
        } catch (Exception $e) {
            echo "<script>
                alert('Operation failed: " . addslashes($e->getMessage()) . "');
                window.location.href = 'users.php';
            </script>";
        }
    }

    // Handle Delete
    if (isset($_GET['Delete'])) {
        try {
            $email = $_GET['Delete'];
            $deleteResult = mysqli_query($db, "DELETE FROM users WHERE email='$email'");
            if (!$deleteResult) {
                throw new Exception("Database deletion failed");
            }
            $subject = "Account Deleted";
            $messageBody = "Your account has been deleted by the System Admin.";
            
            if (sendEmail($email, $subject, $messageBody)) {
                echo "<script>window.location.href='users.php?Deleted=" . $email . "';</script>";
            }
        } catch (Exception $e) {
            echo "<script>
                alert('Operation failed: " . addslashes($e->getMessage()) . "');
                window.location.href = 'users.php';
            </script>";
        }
    }

    // Handle Activation
    if (isset($_GET['Activate'])) {
        try {
            $email = $_GET['Activate'];
            $updateResult = mysqli_query($db, "UPDATE users SET status='active' WHERE email='$email'");
            if (!$updateResult) {
                throw new Exception("Database update failed");
            }
            $subject = "Account Activated";
            $messageBody = "We are glad to inform you that your account has been Activated by the System Admin. Welcome to Northern Might Fitness Gym.";
            
            if (sendEmail($email, $subject, $messageBody)) {
                echo "<script>window.location.href='users.php?Activated=" . $email . "';</script>";
            }
        } catch (Exception $e) {
            echo "<script>
                alert('Operation failed: " . addslashes($e->getMessage()) . "');
                window.location.href = 'users.php';
            </script>";
        }
    }

    // Handle success messages
    if (isset($_GET['Verified'])) {
        $message = "User verified successfully and notification sent.";
        $modalTitle = "User Verified!";
        $modalClass = "text-success";
    }
    if (isset($_GET['Activated'])) {
        $message = "User activated successfully and notification sent.";
        $modalTitle = "User Activated!";
        $modalClass = "text-success";
    }
    if (isset($_GET['Deactivated'])) {
        $message = "User deactivated successfully and notification sent.";
        $modalTitle = "User Deactivated!";
        $modalClass = "text-success";
    }
    if (isset($_GET['Deleted'])) {
        $message = "User deleted successfully and notification sent.";
        $modalTitle = "User Deleted!";
        $modalClass = "text-success";
    }
?>

<div class="w3-threequarter w3-container w3-padding-64" style="margin-top: 8rem">
    <div class="w3-col">
        <ul class="pagination w3-left w3-col s6">
            <li class="page-item"><a class="page-link w3-center" href="?page=1"><b><<</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage - 1; ?>"><b><</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage + 1; ?>"><b>></b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $totalPages; ?>"><b>>></b></a></li>
        </ul>
        <i class="w3-right"><?php echo $currentPage." of ".$totalPages; ?></i>
    </div>
    <table class="w3-table-all mb-3">
        <tr>
            <th colspan="6">Status of Users</th>
        </tr>
        <tr>
            <th style="text-align:center">No</th>
            <th style="text-align:center">Name</th>
            <th style="text-align:center" class="w3-hide-small">Email</th>
            <th style="text-align:center">Verification</th>
            <th style="text-align:center">Actions</th>
        </tr>
        <?php
        $userquery = mysqli_query($db, "SELECT * FROM users WHERE role_id = 2 ORDER BY last_name LIMIT 10 OFFSET $offsetData");
        $i = 1;
        while($fetchUser = mysqli_fetch_assoc($userquery)){
        ?>
        <tr>
            <td style="text-align:left"><?php echo $i ?></td>
            <td style="text-align:left; text-transform:capitalize">
                <a href="userSubscription.php?account=<?php echo $fetchUser['user_id'] ?>">
                    <?php echo $fetchUser['last_name'].", ".$fetchUser['first_name'] ?>
                </a>
            </td>
            <td class="w3-hide-small" style="text-align:left;"><?php echo $fetchUser['email'] ?></td>
            <td style="text-align:center">
                <?php echo $fetchUser['verified'] ? '<span class="badge bg-success">Verified</span>' : 
                    '<button onclick="showVerifyConfirmation(\''.$fetchUser['email'].'\')" class="btn btn-primary btn-sm">Accept</button>'; ?>
            </td>
            <td style="text-align:center;vertical-align:middle">
                <?php if ($fetchUser['status'] == "active") { ?>
                    <button onclick="showDeactivateConfirmation('<?php echo $fetchUser['email'] ?>')" 
                            class="btn btn-danger btn-sm">Deactivate</button>
                <?php } else { ?>
                    <a href="?Activate=<?php echo $fetchUser['email'] ?>" 
                       class="btn btn-success btn-sm">Activate</a>
                <?php } ?>
                <button onclick="showDeleteConfirmation('<?php echo $fetchUser['email'] ?>')"
                        class="btn btn-danger btn-sm">Delete</button>
            </td>
        </tr>
        <?php
        $i++;
        } ?>
    </table>
    <div class="w3-col">
        <ul class="pagination w3-left w3-col s6">
            <li class="page-item"><a class="page-link w3-center" href="?page=1"><b><<</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage - 1; ?>"><b><</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage + 1; ?>"><b>></b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $totalPages; ?>"><b>>></b></a></li>
        </ul>
        <i class="w3-right"><?php echo $currentPage." of ".$totalPages; ?></i>
    </div>
</div>

<div class="w3-container w3-quarter w3-padding-64" style="margin-top: 8rem">
    <div class="w3-text-black">
        <h2 id="calendar-header">Calendar</h2>
        <table id="calendar" class="w3-table">
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

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary">Confirm Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                Are you sure you want to verify this user? They will be notified via email.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmVerify" class="btn btn-primary">Verify</a>
            </div>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-warning">Confirm Deactivation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                Are you sure you want to deactivate this user? They will be notified via email.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeactivate" class="btn btn-warning">Deactivate</a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                Are you sure you want to permanently delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function showVerifyConfirmation(email) {
   var modal = new bootstrap.Modal(document.getElementById('verifyModal'));
   document.getElementById('confirmVerify').href = '?Verify=' + encodeURIComponent(email);
   modal.show();
}

function showDeactivateConfirmation(email) {
   var modal = new bootstrap.Modal(document.getElementById('deactivateModal'));
   document.getElementById('confirmDeactivate').href = '?Deactivate=' + encodeURIComponent(email);
   modal.show();
}

function showDeleteConfirmation(email) {
   var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
   document.getElementById('confirmDelete').href = '?Delete=' + encodeURIComponent(email);
   modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
   var messageModal = document.getElementById('messageModal');
   if (messageModal) {
       var modal = new bootstrap.Modal(messageModal);
       modal.show();
   }
});
</script>

<?php include('scripts/calendar.js'); ?>
<?php } else { 
    echo "<div class='h4 w3-display-middle w3-text-black'>You are not allowed to enter this page.</div>";
} ?>