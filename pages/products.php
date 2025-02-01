<div class="w3-container w3-quarter w3-hide-small" id="offer" style="background: #f6f6f6">
  <?php
  $selectItems = mysqli_query($db, "SELECT * FROM products INNER JOIN inventory ON products.product_id = inventory.product_id ORDER BY products.product_id DESC");
  $i = 1;
  while ($fetchItems = mysqli_fetch_assoc($selectItems)) {
    $searchText = '\r\n'; // The text you want to replace (e.g., newlines)
    $replaceText = '<br>'; // The text you want to replace with (e.g., <br> tags)
    $fetchedText= $fetchItems['description'];
    $replacedText = str_replace($searchText, $replaceText, $fetchedText);
  ?>
  <div class="w3-container w3-center offers-image w3-theme-d5">
      <a href="#<?php echo $fetchItems['picture'] ?>"><img src="assets/products/<?php echo $fetchItems['picture'] ?>" alt="<?php echo $fetchItems['picture'] ?>" class="" style="height:30vh;"></a>
  </div>
  <?php $i++;
  } ?>
</div>
<div class="w3-container w3-threequarter w3-hide-small" id="offer">
  <?php
  $selectItems = mysqli_query($db, "SELECT * FROM products INNER JOIN inventory ON products.product_id = inventory.product_id ORDER BY products.product_id DESC");
  $i = 1;
  while ($fetchItems = mysqli_fetch_assoc($selectItems)) {
    $searchText = '\r\n'; // The text you want to replace (e.g., newlines)
    $replaceText = '<br>'; // The text you want to replace with (e.g., <br> tags)
    $fetchedText= $fetchItems['description'];
    $replacedText = str_replace($searchText, $replaceText, $fetchedText);
  ?>
  <div class="w3-container offers-image w3-theme-d5" id="<?php echo $fetchItems['picture'] ?>">
      <img src="assets/products/<?php echo $fetchItems['picture'] ?>" alt="<?php echo $fetchItems['picture'] ?>" class="w3-col s6">
      <div class="w3-col s6 w3-display-container" style="height:50vh">
        <div class="w3-container w3-display-middle w3-col w3-center">
          <span class="h3 w3-col"><?php echo $fetchItems['name'] ?></span>
          <span class="w3-col mt-3 mb-3"><?php echo $replacedText; ?></span>
          <span class="w3-col h3">Php <?php echo number_format($fetchItems['price'], 2, ".", ",") ?> only</span>
          <span class="w3-col">Available Unit(s): <?php echo $fetchItems['quantity'] ?></span>
        </div>
      </div>
  </div>
  <?php $i++;
  } ?>
</div>
<div class="w3-container w3-threequarter w3-hide-large w3-hide-medium">
  <?php
  $selectItems = mysqli_query($db, "SELECT * FROM products INNER JOIN inventory ON products.product_id = inventory.product_id ORDER BY products.product_id DESC");
  $i = 1;
  while ($fetchItems = mysqli_fetch_assoc($selectItems)) {
    $searchText = '\r\n'; // The text you want to replace (e.g., newlines)
    $replaceText = '<br>'; // The text you want to replace with (e.g., <br> tags)
    $fetchedText= $fetchItems['description'];
    $replacedText = str_replace($searchText, $replaceText, $fetchedText);
  ?>
  <div class="w3-container offers-image w3-theme-d5 w3-center" id="<?php echo $fetchItems['picture'] ?>">
      <img src="assets/products/<?php echo $fetchItems['picture'] ?>" alt="<?php echo $fetchItems['picture'] ?>" class="w3-col">
      <div class="w3-col w3-display-container" style="height:25vh">
        <div class="w3-container w3-display-middle w3-col w3-center">
          <span class="h3 w3-col"><?php echo $fetchItems['name'] ?></span>
          <span class="w3-col mt-3 mb-3"><?php echo $replacedText; ?></span>
          <span class="w3-col h3">Php <?php echo number_format($fetchItems['price'], 2, ".", ",") ?> only</span>
          <span class="w3-col">Available Unit(s): <?php echo $fetchItems['quantity'] ?></span>
        </div>
      </div>
  </div>
  <?php $i++;
  } ?>
</div>
