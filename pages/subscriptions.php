<?php
$countAccounts = mysqli_query($db, "SELECT COUNT(sale_id) AS distinct_count FROM sales WHERE item_type = 'Service'") or die(mysqli_error($db));
$accountCount = mysqli_fetch_array($countAccounts);
$countP = $accountCount['distinct_count'];
$totalPages = ceil($countP / 10);

if (isset($_GET['page'])) {
    $currentPageGet = (int)$_GET['page'];
    if ($currentPageGet < 1) {
        $currentPage = 1;
    } elseif ($currentPageGet > $totalPages) {
        $currentPage = $totalPages;
    } else {
        $currentPage = $currentPageGet;
    }
    $offsetData = 10 * ($currentPage - 1);
} else {
    $currentPage = 1;
    $offsetData = 0;
}
?>

<div class="w3-container">
    <table class="w3-table w3-hoverable mb-3">
        <tr>
            <th colspan="2" class="h4 w3-hide-small">Subscriptions Report</th>
            <?php
            $transactionQuery1 = mysqli_query($db, "SELECT SUM(quantity * total_price) AS totalCost1 FROM sales WHERE item_type = 'Services'");
            $fetchCost1 = mysqli_fetch_assoc($transactionQuery1);
            $fetchTotalCost1 = $fetchCost1['totalCost1'];
            ?>
            <th class="w3-right">Total Sales: Php <?php echo number_format($fetchTotalCost1, 2, ".", ",") ?></th>
        </tr>
        <tr>
            <th style="text-align:center">Transaction No</th>
            <th style="text-align:center">Total</th>

            <th style="text-align:center">Action</th>
        </tr>
        <?php
        $userquery = mysqli_query($db, "SELECT * FROM sales WHERE item_type = 'Service' ORDER BY sale_id DESC LIMIT 10 OFFSET $offsetData");

        while ($fetchUser = mysqli_fetch_assoc($userquery)) {
            $transaction = $fetchUser['sale_date'];
            $transactionQuery = mysqli_query($db, "SELECT SUM(quantity * total_price) AS totalCost, user_id FROM sales WHERE sale_date = '$transaction' GROUP BY user_id");
            $fetchCost = mysqli_fetch_assoc($transactionQuery);
            $fetchTotalCost = $fetchCost['totalCost'];
            ?>
            <tr>
                <td class="w3-center"><?php echo date('YmdHis', strtotime($transaction)) . $fetchCost['user_id'] ?></td>
                <td class="w3-center">Php <?php echo number_format($fetchTotalCost, 2, ".", ","); ?></td>
                <?php
                $date = $fetchUser['sale_date'];
                $dateNow = new DateTime(); // Get current date and time
                $date1 = new DateTime($date);
                $monthsToAdd = (int)$fetchUser['quantity'];

                // Add months to the date
                $date1->modify("+{$monthsToAdd} months");
                $targetDate = $date1; // Target date after adding months

                $remainingSeconds = $targetDate->getTimestamp() - $dateNow->getTimestamp();

                if ($remainingSeconds > 0) {
                    $remainingDays = floor($remainingSeconds / (60 * 60 * 24));
                    $remainingHours = floor(($remainingSeconds % (60 * 60 * 24)) / (60 * 60));
                    $remainingMinutes = floor(($remainingSeconds % (60 * 60)) / 60);
                    $remainingSeconds %= 60;

                    $formattedDateTimeremaining = sprintf('%d days, %d hours, %d minutes, %d seconds', $remainingDays, $remainingHours, $remainingMinutes, $remainingSeconds);

                    // Set text color based on remaining days
                    if ($remainingDays < 3) {
                        $textColor = 'red';
                    } elseif ($remainingDays <= 7) {
                        $textColor = 'orange';
                    } else {
                        $textColor = 'black';
                    }
                } else {
                    $formattedDateTimeremaining = 'This subscription has already expired.';
                    $textColor = 'black';
                }
                ?>
                
                <td class="w3-center">
                    <a href="subscriptionReceipt.php?user=<?php echo $fetchCost['user_id'] ?>&date=<?php echo $date ?>&amount=<?php echo $fetchTotalCost ?>" target="_blank">View Receipt</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <div class="w3-col">
        <ul class="pagination w3-left w3-col s6">
            <li class="page-item"><a class="page-link w3-center" href="?page=1"><b><<</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo max($currentPage - 1, 1); ?>"><b><</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo min($currentPage + 1, $totalPages); ?>"><b>></b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $totalPages; ?>"><b>>></b></a></li>
        </ul>
        <i class="w3-right"><?php echo $currentPage . " of " . $totalPages; ?></i>
    </div>
</div>
