 <?php
//total data and pages
$countAccounts = mysqli_query($db, "SELECT COUNT(DISTINCT sale_date) AS distinct_count FROM sales WHERE item_type = 'Product'") or die(mysqli_error());
$accountCount = mysqli_fetch_array($countAccounts);
$countP=$accountCount['distinct_count'];
$totalPages=ceil($countP/10);
if (isset($_GET['page'])){
  $currentPageGet=$_GET['page'];
  if($currentPageGet<=1){
    $currentPage=1;
    //offset data
    $offsetData=0;
  } elseif ($currentPageGet>=$totalPages) {
    $currentPage=$totalPages;
    //offset data
    $offsetData=10*($currentPage-1);
  } else {
    $currentPage=$_GET['page'];
    //offset data
    $offsetData=10*($currentPage-1);
  }
}
  else {
    $currentPage=1;
    //offset data
    $offsetData=0;
  }
 ?>

<div class="w3-container">
  <table class="w3-table w3-hoverable mb-3">
    <tr>
      <th colspan="2" class="h4 w3-hide-small">Point of Sale Report</th>
      <?php
      $transactionQuery1=mysqli_query($db,"SELECT SUM(quantity * total_price) AS totalCost1
      FROM sales
      WHERE item_type = 'Product'
      ");
      $fetchCost1=mysqli_fetch_assoc($transactionQuery1);
      $fetchTotalCost1=$fetchCost1['totalCost1'];
       ?>
    </tr>
    <tr>
      <th style="text-align:center">Transaction No</th>
      <th style="text-align:center">Total</th>
      <th style="text-align:center">Action</th>
    </tr>
    <?php
    $userquery=mysqli_query($db,"SELECT distinct sale_date, sale_id FROM sales WHERE item_type = 'Product'  order by sale_id desc limit 10 offset $offsetData");$i=1;
    while($fetchUser=mysqli_fetch_assoc($userquery)){
      $transaction=$fetchUser['sale_date'];
      $transactionQuery=mysqli_query($db,"SELECT SUM(quantity * total_price) AS totalCost, user_id
      FROM sales
      WHERE sale_date = '$transaction'
      GROUP BY user_id");
      $fetchCost=mysqli_fetch_assoc($transactionQuery);
      $fetchTotalCost=$fetchCost['totalCost'];

     ?>
    <tr>
      <td class="w3-center"><?php echo date('YmdHis', strtotime($transaction)).$fetchCost['user_id'] ?></td>
      <td class="w3-center">Php<?php echo number_format($fetchTotalCost,2,".",","); ?></td>
      <td class="w3-center"><a href="receipt.php?user=<?php echo $fetchCost['user_id'] ?>&date=<?php echo $transaction ?>&amount=<?php echo $fetchTotalCost ?>" target="_blank">View Receipt</a></td>
    </tr>
  <?php
  $i++;
  } ?>
  </table>
  <h6 class="w3-center mt-4" style="font-weight: bold;">Total Sales: Php <?php echo number_format($fetchTotalCost1, 2, ".", ","); ?></h4>
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
