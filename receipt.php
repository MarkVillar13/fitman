<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FitMan Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="assets\img\158534286_1325351574499404_570873476507921606_n.jpg">
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
            text-align: left;
        }

        .items-table th {
            background-color: #f4f4f4;
        }

        .items-table td:nth-child(2),
        .items-table td:nth-child(3),
        .items-table td:nth-child(4) {
            text-align: right;
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
            @page {
                size: A4 Portrait;
            }
        }
    </style>
</head>
<body>
    <?php include ('database.php'); ?>
    <div class="receipt-container">
        <div class="header">
            <img src="assets/img/fit.png" alt="FitMan Logo" class="logo">
            <h1>Acknowledgment Receipt</h1>
        </div>
        <?php
        if (isset($_GET['user']) || isset($_GET['date'])) {
            $user = isset($_GET['user']) ? mysqli_real_escape_string($db, $_GET['user']) : null;
            $date = isset($_GET['date']) ? mysqli_real_escape_string($db, $_GET['date']) : null;
            $amount = isset($_GET['amount']) ? mysqli_real_escape_string($db, $_GET['amount']) : '0.00';
            
            $userFN = '';
            $userLN = '';
            $formattedDateTime = '';
            
            if ($date) {
                $timestamp = strtotime($date);
                $formattedDateTime = date('YmdHis', $timestamp);
            }
            
            if ($user) {
                $customerSelect = mysqli_query($db, "SELECT * FROM `users` WHERE user_id='$user' LIMIT 1");
                if ($customerSelect && $userFetch = mysqli_fetch_assoc($customerSelect)) {
                    $userFN = htmlspecialchars($userFetch['first_name']);
                    $userLN = htmlspecialchars($userFetch['last_name']);
                }
            }
        ?>
        <div class="customer-details">
            <p><strong>Customer Name:</strong> <?php echo $user ? ($userFN . " " . $userLN) : "Walk-in Customer"; ?></p>
            <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($date)); ?></p>
        </div>
        <div class="transaction-details">
            <p><strong>Transaction ID:</strong> <?php echo $formattedDateTime . $user; ?></p>
            <p><strong>Total Amount:</strong> Php <?php echo number_format($amount, 2, ".", ","); ?></p>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Format the date for exact matching
                $formatted_date = date('Y-m-d H:i:s', strtotime($date));
                
                $transaction = mysqli_query($db, "SELECT s.*, 
                    CASE 
                        WHEN s.item_type = 'Service' THEN srv.name
                        ELSE p.name 
                    END as item_name
                    FROM `sales` s 
                    LEFT JOIN services srv ON s.item_type = 'Service' AND s.item_id = srv.service_id
                    LEFT JOIN products p ON s.item_type = 'Product' AND s.item_id = p.product_id
                    WHERE " . ($user ? "s.user_id = '$user'" : "s.user_id IS NULL") . " 
                    AND s.sale_date = '$formatted_date'");

                if (!$transaction) {
                    echo "<tr><td colspan='4'>Error retrieving transaction details: " . mysqli_error($db) . "</td></tr>";
                } else if (mysqli_num_rows($transaction) == 0) {
                    echo "<tr><td colspan='4'>No items found for this transaction.</td></tr>";
                } else {
                    while ($fetchOrder = mysqli_fetch_assoc($transaction)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fetchOrder['item_name']); ?></td>
                            <td>Php <?php echo number_format($fetchOrder['total_price'], 2, ".", ","); ?></td>
                            <td><?php echo number_format($fetchOrder['quantity'], 2, ".", ","); ?></td>
                            <td>Php <?php echo number_format($fetchOrder['total_price'] * $fetchOrder['quantity'], 2, ".", ","); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Visit us at <a href="https://computinginnovators.org/BSIT4A/fitman/index.php">The Northern Might Gym</a></p>
        </div>
        <?php
        } else {
            echo "<p>Error: Missing required parameters.</p>";
        }
        ?>
    </div>
</body>
</html>