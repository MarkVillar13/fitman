Here's the modernized table design that matches your previous styling:

```php
<?php 
// Pagination calculations stay the same
$countAccounts = mysqli_query($db, "SELECT count(sale_id) FROM sales WHERE user_id = '$user_id'");
$accountCount = mysqli_fetch_array($countAccounts);
$countP = $accountCount['count(sale_id)'];
$totalPages = ceil($countP/10);

if (isset($_GET['page'])) {
    $currentPageGet = $_GET['page'];
    if($currentPageGet <= 1) {
        $currentPage = 1;
        $offsetData = 0;
    } elseif ($currentPageGet >= $totalPages) {
        $currentPage = $totalPages;
        $offsetData = 10*($currentPage-1);
    } else {
        $currentPage = $_GET['page'];
        $offsetData = 10*($currentPage-1);
    }
} else {
    $currentPage = 1;
    $offsetData = 0;
}
?>

<div class="container-fluid" style="margin-top: 8rem">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Transaction History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">Transaction No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $userquery = mysqli_query($db, "SELECT distinct sale_date, sale_id FROM sales WHERE user_id = '$user_id' order by sale_id desc limit 10 offset $offsetData");
                        if(mysqli_num_rows($userquery) < 1) {
                            echo '<tr><td colspan="5" class="text-center text-muted py-3">No transactions found</td></tr>';
                        }
                        
                        while($fetchUser = mysqli_fetch_assoc($userquery)) {
                            $transaction = $fetchUser['sale_date'];
                            $transactionQuery = mysqli_query($db, "SELECT SUM(quantity * total_price) AS totalCost, user_id 
                                FROM sales WHERE sale_date = '$transaction' GROUP BY user_id");
                            $fetchCost = mysqli_fetch_assoc($transactionQuery);
                            $fetchTotalCost = $fetchCost['totalCost'];
                        ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo date('YmdHis', strtotime($transaction)).$fetchCost['user_id'] ?></td>
                            <td class="text-center align-middle"><?php echo date('M d, Y', strtotime($transaction)) ?></td>
                            <td class="text-center align-middle"><?php echo date('h:i a', strtotime($transaction)) ?></td>
                            <td class="text-center align-middle">₱<?php echo number_format($fetchTotalCost, 2, ".", ","); ?></td>
                            <td class="text-center align-middle">
                                <a href="receipt.php?user=<?php echo $fetchCost['user_id'] ?>&date=<?php echo $transaction ?>&amount=<?php echo $fetchTotalCost ?>" 
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Transaction pagination" class="p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="pagination mb-0">
                        <li class="page-item"><a class="page-link" href="?page=1">First</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?>">Last</a></li>
                    </ul>
                    <span class="text-muted">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                </div>
            </nav>
        </div>
    </div>
</div>
```