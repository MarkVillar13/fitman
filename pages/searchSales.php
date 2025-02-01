<div class="mt-3">
  <form class="w3-quarter w3-right mt-3" action="" method="get">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Transaction number..." name="transaction">
      <button class="w3-btn w3-green" type="submit">Go</button>
    </div>
  </form>
  <div class="w3-col">
    <?php
    if (isset($_GET['transaction'])) {
      if(strlen($_GET['transaction']) == 15){
      $transaction=substr($_GET['transaction'], -15, -1);
    } else{
      $transaction=$_GET['transaction'];
    }
     ?>
    <table class="w3-table w3-hoverable">
      <tr>
        <th colspan="4">Search Result(s)</th>
      </tr>
      <tr>
        <th style="text-align:center">Transaction No</th>
        <th style="text-align:center">Total</th>
        <th style="text-align:center">Action</th>
        <th style="text-align:center">Remarks</th>
      </tr>
      <?php
      $userquery = mysqli_query($db, "SELECT DISTINCT sale_date FROM sales WHERE DATE_FORMAT(sale_date, '%Y%m%d%H%i%s') LIKE CONCAT('%', '$transaction', '%')");
      if($userquery ->num_rows < 1){ ?>
        <tr>
          <td colspan="4"><i>...Receipt not found...</i></td>
        </tr>
      <?php }
      while($fetchUser=mysqli_fetch_assoc($userquery)){
        $transaction=$fetchUser['sale_date'];
        $transactionQuery=mysqli_query($db,"SELECT SUM(quantity * total_price) AS totalCost, user_id, status, sale_date
        FROM sales
        WHERE sale_date = '$transaction'
        GROUP BY user_id, status, sale_date;
        ");
        $fetchCost=mysqli_fetch_assoc($transactionQuery);
        $fetchTotalCost=$fetchCost['totalCost'];

       ?>
      <tr>
        <td class="w3-center"><?php echo date('YmdHis', strtotime($transaction)).$fetchCost['user_id'] ?></td>
        <td class="w3-center">Php<?php echo number_format($fetchTotalCost,2,".",","); ?></td>
        <td class="w3-center">
          <a href="receipt.php?user=<?php echo $fetchCost['user_id'] ?>&date=<?php echo $transaction ?>&amount=<?php echo $fetchTotalCost ?>" target="_blank" class="w3-btn w3-blue" style="width:.8in">View</a>
        </td>
        <td class="w3-center" style="text-transform:capitalize"><?php echo $fetchCost['status'] ?></td>
      </tr>
    <?php
    } ?>
    </table>
  <?php } ?>
  </div>
</div>
