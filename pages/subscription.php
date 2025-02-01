<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = "";
$modalTitle = "";
$modalClass = "";

if(isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $checkUsername = "SELECT users.*, roles.role_name 
                     FROM users 
                     INNER JOIN roles ON users.role_id = roles.role_id 
                     WHERE email='$email'";
    $checkResult = mysqli_query($db, $checkUsername);
    $result = mysqli_fetch_assoc($checkResult);
    $_SESSION['role_name'] = $result['role_name'];
}

if(isset($_POST['decline_subscription'])) {
    $subscription_id = mysqli_real_escape_string($db, $_POST['subscription_id']);
    
    mysqli_begin_transaction($db);
    
    try {
        // Get subscription details with user and service info
        $subQuery = "SELECT sub.*, u.email, s.total_price, ser.name as service_name
        FROM subscriptions sub
        JOIN users u ON sub.user_id = u.user_id 
        JOIN sales s ON sub.user_id = s.user_id 
        JOIN services ser ON s.item_id = ser.service_id
        WHERE sub.subscription_id = ? 
        AND sub.isAdditional = TRUE
        AND s.status = 'pending'
        AND s.item_type = 'Service'";
                 
        $stmt = mysqli_prepare($db, $subQuery);
        if(!$stmt) {
            throw new Exception("Failed to prepare query: " . mysqli_error($db));
        }
        
        mysqli_stmt_bind_param($stmt, 'i', $subscription_id);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to execute query: " . mysqli_error($db));
        }
        
        $subResult = mysqli_stmt_get_result($stmt);
        $subData = mysqli_fetch_assoc($subResult);
        
        if(!$subData) {
            throw new Exception("Subscription not found");
        }

        // Update subscription status
        $updateSubQuery = "UPDATE subscriptions 
        SET total_duration = total_duration + additional_duration,
            end_date = DATE_ADD(end_date, INTERVAL additional_duration DAY),
            additional_duration = 0,
            isAdditional = FALSE,
            last_updated = CURRENT_TIMESTAMP
        WHERE subscription_id = ?";
        $updateStmt = mysqli_prepare($db, $updateSubQuery);
        mysqli_stmt_bind_param($updateStmt, 'i', $subscription_id);
        
        if(!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Failed to update subscription: " . mysqli_error($db));
        }

        // Send decline email
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
            $mail->addAddress($subData['email']);
            $mail->isHTML(true);
            
            $mail->Subject = "Subscription Request Declined - " . $subData['service_name'];
            $mail->Body = "Your subscription request has been declined.<br><br>" .
                         "Service: " . $subData['service_name'] . "<br>" .
                         "Duration: " . $subData['total_duration'] . " days<br>" .
                         "Amount: ₱" . number_format($subData['total_price'], 2) . "<br><br>" .
                         "Please contact us for more information or to reapply.<br><br>" .
                         "Thank you for your interest in FitMan!";

            $mail->send();
            mysqli_commit($db);

            $message = "Subscription declined successfully.";
            $modalTitle = "Success!";
            $modalClass = "text-success";

        } catch (Exception $e) {
            mysqli_rollback($db);
            throw new Exception("Failed to send decline email: " . $e->getMessage());
        }

    } catch (Exception $e) {
        mysqli_rollback($db);
        error_log("Error in subscription decline: " . $e->getMessage());
        $message = "Error: " . $e->getMessage();
        $modalTitle = "Error";
        $modalClass = "text-danger";
    }
}

if(isset($_POST['approve_subscription'])) {
    $subscription_id = mysqli_real_escape_string($db, $_POST['subscription_id']);
    
    mysqli_begin_transaction($db);
    
    try {
        // Get subscription details with user and service info
        $subQuery = "SELECT sub.*, u.email, s.total_price, ser.name as service_name
        FROM subscriptions sub
        JOIN users u ON sub.user_id = u.user_id 
        JOIN sales s ON sub.user_id = s.user_id 
        JOIN services ser ON s.item_id = ser.service_id
        WHERE sub.subscription_id = ? 
        AND sub.isAdditional = TRUE
        AND s.status = 'pending'
        AND s.item_type = 'Service'";
                 
        $stmt = mysqli_prepare($db, $subQuery);
        if(!$stmt) {
            throw new Exception("Failed to prepare query: " . mysqli_error($db));
        }
        
        mysqli_stmt_bind_param($stmt, 'i', $subscription_id);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to execute query: " . mysqli_error($db));
        }
        
        $subResult = mysqli_stmt_get_result($stmt);
        $subData = mysqli_fetch_assoc($subResult);
        
        if(!$subData) {
            throw new Exception("Subscription not found");
        }

        // Update subscription status
        $updateSubQuery = "UPDATE subscriptions 
        SET total_duration = total_duration + additional_duration,
            end_date = DATE_ADD(end_date, INTERVAL additional_duration DAY),
            additional_duration = 0,
            isAdditional = FALSE,
            last_updated = CURRENT_TIMESTAMP
        WHERE subscription_id = ?";
        $updateStmt = mysqli_prepare($db, $updateSubQuery);
        mysqli_stmt_bind_param($updateStmt, 'i', $subscription_id);
        
        if(!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Failed to update subscription: " . mysqli_error($db));
        }

        // Update related sales record
        $updateSalesQuery = "UPDATE sales SET status = 'paid' WHERE user_id = ? AND status = 'pending'";
        $salesStmt = mysqli_prepare($db, $updateSalesQuery);
        mysqli_stmt_bind_param($salesStmt, 'i', $subData['user_id']);
        
        if(!mysqli_stmt_execute($salesStmt)) {
            throw new Exception("Failed to update sales: " . mysqli_error($db));
        }

        // Send approval email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'richmondlvda@gmail.com';
            $mail->Password = 'smuracwtppmriwot';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('richmondlvda@gmail.com', 'Fitman');
            $mail->addAddress($subData['email']);
            $mail->isHTML(true);
            
            $mail->Subject = "Subscription Approved - " . $subData['service_name'];
            $mail->Body = "Your subscription has been approved!<br><br>" .
                         "Service: " . $subData['service_name'] . "<br>" .
                         "Duration: " . $subData['total_duration'] . " days<br>" .
                         "Start Date: " . date('M d, Y', strtotime($subData['start_date'])) . "<br>" .
                         "End Date: " . date('M d, Y', strtotime($subData['end_date'])) . "<br>" .
                         "Amount: ₱" . number_format($subData['total_price'], 2) . "<br><br>" .
                         "Thank you for choosing FitMan!";

            $mail->send();
            mysqli_commit($db);

            $message = "Subscription approved successfully.";
            $modalTitle = "Success!";
            $modalClass = "text-success";

        } catch (Exception $e) {
            mysqli_rollback($db);
            throw new Exception("Failed to send approval email: " . $e->getMessage());
        }

    } catch (Exception $e) {
        mysqli_rollback($db);
        error_log("Error in subscription approval: " . $e->getMessage());
        $message = "Error: " . $e->getMessage();
        $modalTitle = "Error";
        $modalClass = "text-danger";
    }
}

if (isset($_POST['orders'])) {
    try {
        $total = $_POST['total'];
        $customer = mysqli_real_escape_string($db, $_POST['customer']);
        $name = $_POST['name'];
        $productID = $_POST['productID'];
        $quantity = $_POST['days'];
        $price = $_POST['price'];
        $date = date('Y-m-d H:i:s');

        // Check if current user is admin
        $isAdmin = false;
        if(isset($_SESSION['role_name']) && $_SESSION['role_name'] === "Admin") {
            $isAdmin = true;
        }

        // Get user ID
        $customerSelect = mysqli_query($db, "SELECT user_id FROM users WHERE email='$customer' limit 1");
        if (!$customerSelect) {
            throw new Exception("Database error: " . mysqli_error($db));
        }

        $userFetch = mysqli_fetch_assoc($customerSelect);
        if (!$userFetch) {
            throw new Exception("User not found with email: " . $customer);
        }

        $userID = $userFetch['user_id'];
        
        foreach($productID as $index => $pid) {
            $s_productID = mysqli_real_escape_string($db, $pid);
            $s_price = mysqli_real_escape_string($db, $price[$index]);
            $s_quantity = mysqli_real_escape_string($db, $quantity[$index]);
            
            // Set initial sales status based on admin status
            $salesStatus = $isAdmin ? 'paid' : 'pending';
            
            // Create a sales record
            $result = mysqli_query($db, "INSERT INTO sales(user_id, item_type, item_id, quantity, total_price, sale_date, status)
                VALUES ('$userID','Service','$s_productID','$s_quantity','$s_price','$date', '$salesStatus')");
            
            if (!$result) {
                throw new Exception("Failed to create sales record: " . mysqli_error($db));
            }

            // Get service subscription duration
            $service_query = "SELECT subscription FROM services WHERE service_id = '$s_productID'";
            $service_result = mysqli_query($db, $service_query);
            if (!$service_result) {
                throw new Exception("Failed to get service details: " . mysqli_error($db));
            }
            
            $service_data = mysqli_fetch_assoc($service_result);
            $additional_days = $service_data['subscription'] * $s_quantity;

            $check_subscription = "SELECT * FROM subscriptions 
            WHERE user_id = ? 
            AND status != 'expired'";

$check_stmt = mysqli_prepare($db, $check_subscription);
if (!$check_stmt) {
throw new Exception("Failed to prepare subscription check: " . mysqli_error($db));
}

mysqli_stmt_bind_param($check_stmt, 'i', $userID);
if (!mysqli_stmt_execute($check_stmt)) {
throw new Exception("Failed to execute subscription check: " . mysqli_error($db));
}

$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
// Update existing subscription
$existing_sub = mysqli_fetch_assoc($check_result);

if ($isAdmin) {
// Admin: Directly add to total_duration and update end_date
$new_total_duration = $existing_sub['total_duration'] + $additional_days;
$new_end_date = date('Y-m-d', strtotime($existing_sub['end_date'] . " + $additional_days days"));

$update_query = "UPDATE subscriptions 
              SET end_date = ?,
                  total_duration = ?,
                  last_updated = CURRENT_TIMESTAMP
              WHERE user_id = ? AND status != 'expired'";

$update_stmt = mysqli_prepare($db, $update_query);
mysqli_stmt_bind_param($update_stmt, 'sii', $new_end_date, $new_total_duration, $userID);
} else {
// User: Add to additional_duration and set isAdditional to true
$new_additional_duration = $existing_sub['additional_duration'] + $additional_days;

$update_query = "UPDATE subscriptions 
              SET additional_duration = ?,
                  isAdditional = TRUE,
                  last_updated = CURRENT_TIMESTAMP
              WHERE user_id = ? AND status != 'expired'";

$update_stmt = mysqli_prepare($db, $update_query);
mysqli_stmt_bind_param($update_stmt, 'ii', $new_additional_duration, $userID);
}

if (!mysqli_stmt_execute($update_stmt)) {
throw new Exception("Failed to update subscription: " . mysqli_error($db));
}
} else {
// Create new subscription
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime("+ $additional_days days"));
$status = $isAdmin ? 'active' : 'pending';
$isAdditional = !$isAdmin; // True for user requests, False for admin

$insert_query = "INSERT INTO subscriptions 
          (user_id, start_date, end_date, total_duration, additional_duration, status, isAdditional) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = mysqli_prepare($db, $insert_query);
if (!$insert_stmt) {
throw new Exception("Failed to prepare insert: " . mysqli_error($db));
}

$total_duration = $isAdmin ? $additional_days : 0;
$additional_duration = $isAdmin ? 0 : $additional_days;

mysqli_stmt_bind_param($insert_stmt, 'issiisi', 
$userID, 
$start_date, 
$end_date, 
$total_duration,
$additional_duration,
$status,
$isAdditional
);

if (!mysqli_stmt_execute($insert_stmt)) {
throw new Exception("Failed to create subscription: " . mysqli_error($db));
}
}

mysqli_stmt_close($check_stmt);
        }
        
        // Send email notification
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'richmondlvda@gmail.com';
        $mail->Password = 'smuracwtppmriwot';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('richmondlvda@gmail.com', 'Fitman');
        $mail->addAddress($customer);
        $mail->isHTML(true);
        
        $mail->Subject = "Transaction Monitor";
        $mail->Body = "You have purchased from FitMan an amount of Php " . number_format($total, 2, ".", ",") . 
                     ". Thank you for your patronage.<br>Today is the start of your Subscription.<br>" .
                     "Click this, to print your receipt: <a href='https://computinginnovators.org/BSIT4A/fitman/receipt.php?" .
                     "user=" . $userID . "&date=" . $date . "&amount=" . $total . "'>Print Receipt</a>";

        $mail->send();

        $message = "Transaction is posted.";
        $modalTitle = "Success!";
        $modalClass = "text-success";

    } catch (Exception $e) {
        error_log("Error in order processing: " . $e->getMessage());
        $message = "Error: " . $e->getMessage();
        $modalTitle = "Error";
        $modalClass = "text-danger";
    }
}
?>

<?php include('scripts/modal.php'); ?>

<style>
    .form-floating input[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
    opacity: 0.7;
}
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .product-list ul, .cart ul {
        list-style-type: none;
        padding: 0;
    }

    .product-list li, .cart li {
        background: white;
        margin: 8px 0;
        padding: 12px 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .product-list li:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-list .product-info, .cart .cart-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .product-list .product-image, .cart .cart-image {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        object-fit: cover;
    }

    .product-list .product-details, .cart .cart-details {
        flex: 1;
    }

    .product-list .product-name, .cart .cart-name {
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .cart .cart-price {
        color: #718096;
        font-size: 0.9em;
    }

    .product-list .product-sets, .cart .cart-sets {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #718096;
    }

    .cart-sets input[type="number"] {
    width: 60px;
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    text-align: center;
}

    .product-list button, .cart button {
        background: #4a5568;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s ease;
        margin-left: 12px;
    }

    .product-list button:hover, .cart button:hover {
        background: #2d3748;
    }

    #searchBar {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        width: 100%;
        transition: border-color 0.2s ease;
    }

    #searchBar:focus {
        outline: none;
        border-color: #4a5568;
        box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
    }

    .transaction-details {
        margin-top: 20px;
    }

    .total {
        text-align: right;
        font-size: 1.2em;
        color: #2d3748;
        font-weight: 500;
        margin: 16px 0;
    }

    .complete-transactions {
        display: block;
        width: 100%;
        padding: 12px;
        background: #48bb78;
        color: white;
        text-align: center;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .complete-transactions:hover {
        background: #38a169;
    }

    .form-floating .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
    }

    .form-floating .form-control:focus {
        border-color: #4a5568;
        box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
    }
    .select2-container--bootstrap-5 .select2-selection {
    min-height: 3.5rem;
    padding-top: 1rem;
}

.select2-container--bootstrap-5 .select2-selection__rendered {
    padding-left: 0.75rem;
}

.select2-container {
    width: 100% !important;
}

.select2-search__field {
    width: 100% !important;
    padding: 8px !important;
}

.select2-results {
    max-height: 200px;
    overflow-y: auto;
}

.select2-search__field::placeholder {
    color: #6c757d;
}

.form-floating > .select2-container .select2-selection--single {
    height: 58px !important;
    padding-top: 20px !important;
}

.form-floating > label {
    z-index: 2;
    padding: 1rem 0.75rem;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 10px !important;
}

.form-floating input[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
    opacity: 0.7;
}
</style>

<div class="w3-container" style="margin-top: 8rem">
    <div class="product-list w3-half w3-padding">
        <h2>Products</h2>
        <input type="text" id="searchBar" placeholder="Search service...">
        <ul id="productList" style="max-height:60vh; overflow: auto"></ul>
    </div>
    <div class="cart w3-half w3-padding">
        <h2>Shopping Cart</h2>
        <form id="cartForm" method="POST" action="">
            <ul id="cartList" style="max-height:50vh; overflow: auto"></ul>
            <div class="transaction-details">
            <div class="form-floating mb-3 mt-3" style="max-width: 100%;">
        <?php if(isset($_SESSION['role_name']) && $_SESSION['role_name'] === "Admin") { ?>
            <select class="form-select" name="customer" id="customerSelect" required>
                <option value="">Select a customer</option>
                <?php
                $userQuery = mysqli_query($db, "SELECT email FROM users WHERE role_id = 2 ORDER BY email");
                while($user = mysqli_fetch_assoc($userQuery)) {
                    echo "<option value='" . htmlspecialchars($user['email']) . "'>" . htmlspecialchars($user['email']) . "</option>";
                }
                ?>
            </select>
            <label for="customerSelect">Customer Email</label>
        <?php } else { ?>
            <input type="text" class="form-control" placeholder="Customer" name="customer" value="<?php echo $_SESSION['email']; ?>" readonly required>
            <label for="username">Customer</label>
        <?php } ?>
    </div>
    <p class="total">Total: Php<span id="totalAmount">0.00</span></p>
    <input type="hidden" name="total" id="totalAmountInput">
    <button type="submit" class="complete-transactions" name="orders">Complete Transaction</button>
</div>
        </form>
    </div>
</div>

<?php if(isset($_SESSION['role_name']) && $_SESSION['role_name'] === "Admin") { ?>
<!-- Pending Subscriptions Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Pending Subscriptions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Quantity</th>
                        <th>Duration</th>
                        <th>Period</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
    // Update the pending query to include service price
      // Update the pending query to include service price and calculate total amount correctly
      $pendingQuery = "SELECT 
    s.sale_date, 
    s.quantity, 
    s.total_price, 
    s.user_id, 
    u.email, 
    ser.name as service_name, 
    ser.price as service_price, 
    sub.subscription_id, 
    sub.start_date, 
    sub.end_date, 
    sub.total_duration, 
    sub.additional_duration, 
    (ser.price * s.quantity) as calculated_total 
FROM subscriptions sub 
JOIN users u ON sub.user_id = u.user_id 
JOIN sales s ON sub.user_id = s.user_id 
JOIN services ser ON s.item_id = ser.service_id 
WHERE sub.isAdditional = TRUE 
    AND s.status = 'pending' 
    AND s.item_type = 'Service' 
GROUP BY 
    sub.subscription_id,
    s.sale_date,
    s.quantity,
    s.total_price,
    s.user_id,
    u.email,
    ser.name,
    ser.price,
    sub.start_date,
    sub.end_date,
    sub.total_duration,
    sub.additional_duration
ORDER BY s.sale_date DESC";

$pendingResult = mysqli_query($db, $pendingQuery);
        
    if(mysqli_num_rows($pendingResult) < 1) {
        echo '<tr><td colspan="9" class="text-center text-muted py-3">No pending subscriptions</td></tr>';
    }

    while($row = mysqli_fetch_assoc($pendingResult)) {
        ?>
        <tr>
            <td><?php echo date('M d, Y H:i', strtotime($row['sale_date'])); ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['service_name']; ?></td>
            <td><?php echo $row['quantity']; ?>x</td>
            <td><?php echo $row['total_duration']; ?> days</td>
            <td>
                <?php 
                echo date('M d, Y', strtotime($row['start_date'])) . ' - ' . 
                     date('M d, Y', strtotime($row['end_date'])); 
                ?>
            </td>
            <td class="text-end">₱<?php echo number_format($row['service_price'], 2, ".", ","); ?></td>
            <td class="text-end">₱<?php echo number_format($row['calculated_total'], 2, ".", ","); ?></td>
            <td class="text-center">
                <form method="POST" class="d-inline me-1">
                    <input type="hidden" name="subscription_id" value="<?php echo $row['subscription_id']; ?>">
                    <button type="submit" name="approve_subscription" class="btn btn-sm btn-success">
                        Approve
                    </button>
                </form>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="subscription_id" value="<?php echo $row['subscription_id']; ?>">
                    <button type="submit" name="decline_subscription" class="btn btn-sm btn-danger">
                        Decline
                    </button>
                </form>
            </td>
        </tr>
        <?php
    }
    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>
<!-- Sales Report -->
<?php
// Pagination setup
$resultsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

$countQuery = "SELECT COUNT(DISTINCT s.sale_date, s.user_id) AS total 
               FROM sales s 
               WHERE s.item_type = 'Service'
               AND s.status = 'paid'";

// Apply date filter to count query if dates are set
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start = mysqli_real_escape_string($db, $_GET['start_date']);
    $end = mysqli_real_escape_string($db, $_GET['end_date']);
    $countQuery .= " AND DATE(s.sale_date) BETWEEN '$start' AND '$end'";
}

$countResult = mysqli_query($db, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $resultsPerPage);

$query = "SELECT DISTINCT s.sale_date, s.user_id, s.status,
          SUM(s.quantity * s.total_price) AS totalCost
          FROM sales s 
          WHERE s.item_type = 'Service'
          AND s.status = 'paid'";

// Apply date filter if dates are set
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start = mysqli_real_escape_string($db, $_GET['start_date']);
    $end = mysqli_real_escape_string($db, $_GET['end_date']);
    $query .= " AND DATE(s.sale_date) BETWEEN '$start' AND '$end'";
}

$query .= " GROUP BY s.sale_date, s.user_id, s.status 
            ORDER BY s.sale_date DESC 
            LIMIT $resultsPerPage OFFSET $offset";

$result = mysqli_query($db, $query);
?>

<?php if(isset($_SESSION['role_name']) && $_SESSION['role_name'] === "Admin") { ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Services Sales Report</h5>
        <form class="d-flex gap-2" method="GET">
            <input type="date" class="form-control" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
            <input type="date" class="form-control" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Transaction #</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($result) < 1) {
                        echo '<tr><td colspan="5" class="text-center text-muted py-3">No transactions found</td></tr>';
                    }

                    while($row = mysqli_fetch_assoc($result)) {
                        $transaction_no = date('YmdHis', strtotime($row['sale_date'])) . $row['user_id'];
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($row['sale_date'])); ?></td>
                            <td><?php echo $transaction_no; ?></td>
                            <td class="text-end">₱<?php echo number_format($row['totalCost'], 2, ".", ","); ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo $row['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="receipt.php?user=<?php echo $row['user_id']; ?>&date=<?php echo $row['sale_date']; ?>&amount=<?php echo $row['totalCost']; ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php
        // Calculate total sales
        $totalSalesQuery = "SELECT SUM(s.quantity * s.total_price) AS total_sales 
        FROM sales s 
        WHERE s.status = 'paid' 
        AND s.item_type = 'Service'";

        
        // Apply date filter to total sales if dates are set
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $totalSalesQuery .= " AND DATE(s.sale_date) BETWEEN '$start' AND '$end'";
        }
        
        $totalSalesResult = mysqli_query($db, $totalSalesQuery);
        $totalSales = mysqli_fetch_assoc($totalSalesResult)['total_sales'];
        ?>
        
        <!-- Total Sales Summary -->
        <div class="bg-white border-top">
            <div class="d-flex justify-content-center align-items-center p-3">
                <h5 class="mb-0 fs-4">Total Sales: 
                    <span class="text-primary fw-bold">
                    ₱<?php echo $totalSales ? number_format($totalSales, 2, ".", ",") : "0.00"; ?>
                    </span>
                </h5>
            </div>
        </div>

        <!-- Pagination Controls -->
        <nav aria-label="Sales report pagination" class="p-3">
            <ul class="pagination justify-content-center">
                <?php
                // Preserve existing GET parameters
                $existingParams = $_GET;
                unset($existingParams['page']);
                $baseUrl = http_build_query($existingParams);
                
                // Previous page link
                if ($page > 1) {
                    $prevPage = $page - 1;
                    echo "<li class='page-item'><a class='page-link' href='?$baseUrl&page=$prevPage'>Previous</a></li>";
                }
                
                // Page number buttons
                for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($i == $page) ? 'active' : '';
                    echo "<li class='page-item $activeClass'><a class='page-link' href='?$baseUrl&page=$i'>$i</a></li>";
                }
                
                // Next page link
                if ($page < $totalPages) {
                    $nextPage = $page + 1;
                    echo "<li class='page-item'><a class='page-link' href='?$baseUrl&page=$nextPage'>Next</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>
</div>
<?php } ?>

<script>
const products = [
    <?php
    $productquery = mysqli_query($db, "SELECT * FROM services ORDER BY name");
    $i = 1;
    while ($fetchproduct = mysqli_fetch_assoc($productquery)) {
        echo "{ id: {$i}, name: '{$fetchproduct['name']}', description: '{$fetchproduct['description']}', price: " . number_format($fetchproduct['price'], 2, ".", "") . ", image: '{$fetchproduct['picture']}', productID: '{$fetchproduct['service_id']}' },";
        $i++;
    }
    ?>
];
const cart = [];

function displayProducts() {
    const searchBar = document.getElementById('searchBar');
    const searchText = searchBar.value.toLowerCase();
    const productList = document.getElementById('productList');
    productList.innerHTML = '';

    products.forEach(product => {
        if (product.name.toLowerCase().includes(searchText)) {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="product-info">
                    <img class="product-image" src="assets/services/${product.image}" alt="${product.name}">
                    <div class="product-details">
                        <div class="product-name">${product.name}</div>
                        <div class="product-price">Php${product.price.toFixed(2)}</div>
                    </div>
                </div>
                <button type="button" onclick="addToCart(${product.id})">+</button>`;
            productList.appendChild(li);
        }
    });
}

function displayCart() {
    const cartList = document.getElementById('cartList');
    cartList.innerHTML = '';

    const cartForm = document.getElementById('cartForm');
    while (cartForm.lastChild.tagName === 'INPUT') {
        cartForm.removeChild(cartForm.lastChild);
    }

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            <div class="cart-info">
                <img class="cart-image" src="assets/services/${item.image}" alt="${item.name}">
                <div class="cart-details">
                    <div class="cart-name">${item.name}</div>
                    <div class="cart-price">Php${item.price.toFixed(2)}</div>
                </div>
            </div>
            <div class="cart-sets">
                <input type="number" min="1" value="${item.days}" onchange="updateDays(${item.id}, this.value)">
                <button type="button" onclick="removeFromCart(${item.id})">-</button>
            </div>`;
        cartList.appendChild(li);

        const inputs = [
            { name: 'name[]', value: item.name },
            { name: 'price[]', value: item.price },
            { name: 'days[]', value: item.days, id: 'inputDays' },
            { name: 'productID[]', value: item.productID }
        ];

        inputs.forEach(input => {
            const element = document.createElement('input');
            element.type = 'hidden';
            element.name = input.name;
            element.value = input.value;
            if (input.id) element.id = input.id;
            cartForm.appendChild(element);
        });
    });
    updateTotal();
}

function updateDays(id, days) {
    const cartItem = cart.find(item => item.id === id);
    if (cartItem) {
        cartItem.days = parseInt(days);
    }
    displayCart();
}

function addToCart(id) {
    const product = products.find(p => p.id === id);
    const cartItem = cart.find(item => item.id === id);
    if (cartItem) {
        cartItem.days++;
    } else {
        cart.push({...product, days: 1});
    }
    displayCart();
}

function removeFromCart(id) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
        cart[index].days--;
        if (cart[index].days === 0) {
            cart.splice(index, 1);
        }
    }
    displayCart();
}

function updateTotal() {
    const totalAmount = cart.reduce((total, item) => total + (item.price * item.days), 0);
    document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
    document.getElementById('totalAmountInput').value = totalAmount.toFixed(2);
}

document.getElementById('searchBar').addEventListener('input', displayProducts);
document.addEventListener('DOMContentLoaded', displayProducts);
document.addEventListener('DOMContentLoaded', () => {
    $('#customerSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select customer email',
        allowClear: true,
        matcher: function(params, data) {
            if (data.id === '') return data;
            if ($.trim(params.term) === '') return data;
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }
            return null;
        }
    });
});
</script>