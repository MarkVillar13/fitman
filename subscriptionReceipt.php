<?php
      require 'PHPMailer/PHPMailer.php';
      require 'PHPMailer/SMTP.php';
      require 'PHPMailer/Exception.php';

      use PHPMailer\PHPMailer\PHPMailer;
      use PHPMailer\PHPMailer\Exception;
      
session_start();
include('database.php');
if(isset($_SESSION['email'])) {
    $email=$_SESSION['email'];
    $checkUsername= "SELECT * FROM users inner join roles on users.role_id = roles.role_id WHERE email='$email'";
    $checkResult= mysqli_query($db, $checkUsername);
    $result= mysqli_fetch_assoc($checkResult);
    $first_name=$result['first_name'];
    $last_name=$result['last_name'];
    $role_name=$result['role_name'];
    $user_id=$result['user_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitMan Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="assets\img\158534286_1325351574499404_570873476507921606_n.jpg">
    <link rel="stylesheet" href="scripts/w3.css">
    <link rel="stylesheet" href="scripts/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="scripts/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
    <script src="scripts/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/jquery-3.7.1.min.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .receipt-container {
        width: 80%;
        max-width: 600px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .logo {
        max-width: 2in;
        margin-bottom: 0;
    }

    .customer-details, .transaction-details {
        margin-bottom: 20px;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .items-table th, .items-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .items-table th {
        background-color: #f4f4f4;
    }

    .footer {
        text-align: center;
        border-top: 2px solid #eee;
        padding-top: 10px;
    }

    .footer a {
        color: #007bff;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    @media print {
        body {
            background-color: #fff;
        }

        .receipt-container {
            box-shadow: none;
            border: none;
            margin: 0;
            width: 100%;
        }

        .header, .footer {
            border: none;
        }

        .footer a {
            text-decoration: none;
            color: black;
        }
        @page{
            size: A4 Portrait;
        }
    }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <img src="assets/img/fit.png" alt="FitMan Logo" class="logo">
            <h1>Subscription Receipt</h1>
        </div>
        <?php 
        if (isset($_GET['user'])) {
            $user=$_GET['user'];
            $date=$_GET['date'];
            $amount=$_GET['amount'];
            $customerSelect=mysqli_query($db, "SELECT * FROM `users` WHERE user_id='$user' limit 1");
            $userFetch=mysqli_fetch_assoc($customerSelect);
            $userFN=$userFetch['first_name'];
            $userLN=$userFetch['last_name'];
            $customer=$userFetch['email'];
            $dateTime = $date;
            $timestamp = strtotime($dateTime);
            $formattedDateTime = date('YmdHis', $timestamp);
        }

        if(isset($_POST['send'])){

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
                $mail->addAddress($customer);

                $mail->isHTML(true);
                $mail->Subject = "Subscription Monitor";
                $mail->Body = "Please, be reminded of your subscription from Northern Might Fitness Gym with a total amount of Php " . number_format($amount, 2, ".", ",") . ". Renew your subscription before it expires.";

                $mail->send();
                echo "<script>window.location.href='subscriptionReceipt.php?user=" . $user . "&date=" . $date . "&amount=" . $amount . "&reminderSent';</script>";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        if (isset($_GET['reminderSent'])) {
            $message = "Reminder is delivered.";
            $modalTitle = "Reminder Sent!";
            $modalClass = "text-success";
        }

        include('scripts/modal.php');
        
        if ($role_name == "Admin") {
        ?>
            <form class="" action="" method="post">
                <button type="submit" class="btn btn-primary w3-right" name="send">Send Reminder</button>
            </form>
        <?php } ?>
        <div class="customer-details">
            <p><strong>Customer Name:</strong> <?php echo $userFN." ".$userLN ?></p>
            <p><strong>Date:</strong> <?php echo $date ?></p>
        </div>
        <div class="transaction-details">
            <p><strong>Transaction ID:</strong> <?php echo $formattedDateTime.$user ?></p>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $transaction=mysqli_query($db,"SELECT * FROM `sales` WHERE `user_id`= '$user' and `sale_date`= '$date'");
              while ($fetchOrder=mysqli_fetch_assoc($transaction)) {
                $itemID=$fetchOrder['item_id'];
                if($fetchOrder['item_type'] == 'Service'){
                  $serviceName=mysqli_query($db,"SELECT * FROM services WHERE service_id='$itemID'");
                  $fetchName=mysqli_fetch_assoc($serviceName);
                }
                else {
                  $serviceName=mysqli_query($db,"SELECT * FROM products WHERE product_id='$itemID'");
                  $fetchName=mysqli_fetch_assoc($serviceName);
                }
               ?>
                <tr>
                    <td><?php echo $fetchName['name'] ?></td>
                    <td>Php <?php echo number_format($fetchOrder['total_price'],2,".",",") ?></td>
                    <?php
                    $dateNow = date('Y-m-d H:i:s');
                    $date = new DateTime($date);
                    $monthsToAdd = $fetchOrder['quantity'];
                    $date->modify("+{$monthsToAdd} months");
                    $targetDate = $date->format('Y-m-d H:i:s');
                    $timestampNow = strtotime($dateNow);
                    $timestampTarget = strtotime($targetDate);
                    $remainingSeconds = $timestampTarget - $timestampNow;

                    if ($remainingSeconds > 0) {
                        $remainingDays = floor($remainingSeconds / (60 * 60 * 24));
                        $remainingHours = floor(($remainingSeconds % (60 * 60 * 24)) / (60 * 60));
                        $remainingMinutes = floor(($remainingSeconds % (60 * 60)) / 60);
                        $remainingSeconds = $remainingSeconds % 60;
                        $formattedDateTimeremaining = sprintf('%d days, %d hours, %d minutes, %d seconds',
                            $remainingDays, $remainingHours, $remainingMinutes, $remainingSeconds);
                    } else {
                        $formattedDateTimeremaining = 'This subscription has already expired.';
                    }
                     ?>
                    <td>Php <?php echo number_format($fetchOrder['total_price']*$fetchOrder['quantity'],2,".",",") ?></td>
                </tr>
              <?php } ?>
            </tbody>
        </table>
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Visit us at <a href="https://computinginnovators.org/BSIT4A/fitman/index.php">The Northern Might Gym</a></p>
        </div>
    </div>
</body>
</html>