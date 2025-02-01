<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
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

        .product-list .product-availability, .cart .cart-quantity {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #718096;
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

        .product-list button:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
        }

        .cart input[type="number"] {
            width: 60px;
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-align: center;
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

        .complete-transaction {
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

        .complete-transaction:hover {
            background: #38a169;
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
</style>
<?php
$message = "";
$modalTitle = "";
$modalClass = "";

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['order'])) {
    $total = $_POST['total'];
    $customer = isset($_POST['customer']) ? mysqli_real_escape_string($db, $_POST['customer']) : '';
    $name = $_POST['name'];
    $productID = $_POST['productID'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');
    
    $userID = null;
    if (!empty($customer)) {
        $customerSelect = mysqli_query($db, "SELECT user_id FROM `users` WHERE email='$customer' limit 1");
        $userFetch = mysqli_fetch_assoc($customerSelect);
        $userID = $userFetch['user_id'];
    }

    foreach($productID as $index => $productID) {
        $s_productID = mysqli_real_escape_string($db, $productID);
        $s_price = mysqli_real_escape_string($db, $price[$index]);
        $s_quantity = mysqli_real_escape_string($db, $quantity[$index]);
        
        $addSale = mysqli_query($db, "INSERT INTO `sales`(`user_id`, `item_type`, `item_id`, `quantity`, `total_price`, `sale_date`,`status`) 
        VALUES (" . ($userID ? "'$userID'" : "NULL") . ",'Product','$s_productID','$s_quantity','$s_price','$date', 'paid')");
        
        if($addSale) {
            $quantityInventory = mysqli_query($db, "SELECT * FROM inventory WHERE product_id = '$s_productID'");
            $fetchQuantity = mysqli_fetch_assoc($quantityInventory);
            $newQuantity = $fetchQuantity['quantity'] - $s_quantity;
            mysqli_query($db, "UPDATE inventory SET quantity = '$newQuantity' WHERE product_id = '$s_productID'");
        }
    }

    // Send email only if customer email is provided
    if (!empty($customer)) {
        $email = $customer;
        $to = $email;
        $subject = "Transaction Monitor";
        $emailmessage = "You have purchased from FitMan an amount of Php " . number_format($total, 2, ".", ",") . ". Thank you for your patronage.<br>
        Click this, to print your receipt: <a href='https://computinginnovators.org/BSIT4A/fitman/receipt.php?user=" . $userID . "&date=" . $date . "&amount=" . $total . "'>Print Receipt</a>";
       
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
            $mail->addAddress($email);
        
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $emailmessage;
            if ($mail->send()) {
                $message = "Transaction is posted.";
                $modalTitle = "Success!";
                $modalClass = "text-success";
            } else {
                $message = "Transaction posted but email failed to send.";
                $modalTitle = "Warning";
                $modalClass = "text-warning";
            }
        } catch (Exception $e) {
            $message = "Transaction posted but email failed to send.";
            $modalTitle = "Warning";
            $modalClass = "text-warning";
        }
    }

    $message = "Transaction is posted.";
    $modalTitle = "Success!";
    $modalClass = "text-success";
}
 ?>
 <?php include('scripts/modal.php'); ?>
 <div class="w3-container" style="margin-top: 8rem">
    <!-- POS System -->
    <div class="w3-row mb-4">
        <div class="product-list w3-half w3-padding">
            <h2>Products</h2>
            <input type="text" id="searchBar" placeholder="Search product..." oninput="filterProducts()">
            <ul id="productList" style="max-height:60vh; overflow: auto"></ul>
        </div>
        <div class="cart w3-half w3-padding">
            <h2>Shopping Cart</h2>
            <form id="cartForm" method="POST" action="">
                <ul id="cartList" style="max-height:50vh; overflow: auto"></ul>
                <div class="transaction-details">
                    <div class="form-floating mb-3 mt-3" style="max-width: 100%;">
                        <select class="form-select" name="customer" id="customerSelect">
                            <option value="">No Receipt</option>
                            <?php
                            $userQuery = mysqli_query($db, "SELECT email FROM users WHERE role_id = 2 ORDER BY email");
                            while($user = mysqli_fetch_assoc($userQuery)) {
                                echo "<option value='" . htmlspecialchars($user['email']) . "'>" . htmlspecialchars($user['email']) . "</option>";
                            }
                            ?>
                        </select>
                        <label for="customerSelect">Customer Email</label>
                    </div>
                    <p class="total">Total: Php<span id="totalAmount">0.00</span></p>
                    <input type="hidden" name="total" id="totalAmountInput">
                    <button type="submit" class="complete-transaction" name="order">Complete Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Report -->
    <?php
    // Pagination setup
    $resultsPerPage = 10; // Number of results per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page, default to 1
    $offset = ($page - 1) * $resultsPerPage; // Calculate SQL offset

    $countQuery = "SELECT COUNT(DISTINCT s.sale_date, s.user_id) AS total 
                   FROM sales s WHERE s.item_type = 'Product'";

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
    FROM sales s WHERE s.item_type = 'Product'";

    // Apply date filter
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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Product Sales Report</h5>
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
    AND s.item_type = 'Product'";
    
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
                    ₱<?php echo number_format($totalSales, 2, ".", ","); ?>
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
</div>

<script>
const products = [
    <?php
    $productquery = mysqli_query($db, "SELECT * FROM products LEFT JOIN inventory ON products.product_id = inventory.product_id WHERE picture != '' ORDER BY name");
    $i = 1;
    while ($fetchproduct = mysqli_fetch_assoc($productquery)) {
        echo "{ id: {$i}, name: '{$fetchproduct['name']}', quantityInventory: '{$fetchproduct['quantity']}', description: '{$fetchproduct['description']}', price: " . number_format($fetchproduct['price'], 2, ".", "") . ", image: '{$fetchproduct['picture']}', productID: '{$fetchproduct['product_id']}' },";
        $i++;
    }
    ?>
];
const cart = [];
let filteredProducts = [...products];

function displayProducts() {
    const productList = document.getElementById('productList');
    productList.innerHTML = '';
    filteredProducts.forEach(product => {
        const isOutOfStock = product.quantityInventory <= 0; // Check if out of stock
        const li = document.createElement('li');
        li.innerHTML = `
    <div class="product-info">
        <img class="product-image" src="assets/products/${product.image}" alt="${product.name}">
        <div class="product-details">
            <div class="product-name">${product.name}</div>
        </div>
    </div>
    <div class="product-availability">
        Available: ${product.quantityInventory}
        <button type="button" 
            onclick="addToCart(${product.id})" 
            ${isOutOfStock ? 'disabled' : ''}>
            +
        </button>
    </div>`;
        productList.appendChild(li);
    });
}

function filterProducts() {
    const searchQuery = document.getElementById('searchBar').value.toLowerCase();
    filteredProducts = products.filter(product =>
        product.name.toLowerCase().includes(searchQuery) ||
        product.description.toLowerCase().includes(searchQuery)
    );
    displayProducts();
}

function displayCart() {
    const cartList = document.getElementById('cartList');
    cartList.innerHTML = ''; // Clear the current cart list

    const cartForm = document.getElementById('cartForm');
    // Clear existing hidden inputs
    while (cartForm.lastChild.tagName === 'INPUT') {
        cartForm.removeChild(cartForm.lastChild);
    }

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
    <div class="cart-info">
        <img class="cart-image" src="assets/products/${item.image}" alt="${item.name}">
        <div class="cart-details">
            <div class="cart-name">${item.name}</div>
        </div>
    </div>
    <div class="cart-quantity">
        <input type="number" min="1" value="${item.quantity}" onchange="updateDays(${item.id}, this.value)">
        <button type="button" onclick="removeFromCart(${item.id})">-</button>
    </div>`;
        cartList.appendChild(li);

        // Create hidden input fields for each cart item
        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = `name[]`;
        inputName.value = item.name;

        const inputPrice = document.createElement('input');
        inputPrice.type = 'hidden';
        inputPrice.name = `price[]`;
        inputPrice.value = item.price;

        const inputQuantity = document.createElement('input');
        inputQuantity.type = 'hidden';
        inputQuantity.name = `quantity[]`;
        inputQuantity.value = item.quantity;

        const inputProduct = document.createElement('input');
        inputProduct.type = 'hidden';
        inputProduct.name = `productID[]`;
        inputProduct.value = item.productID;

        cartForm.appendChild(inputName);
        cartForm.appendChild(inputPrice);
        cartForm.appendChild(inputQuantity);
        cartForm.appendChild(inputProduct);
    });
    updateTotal();
}

function addToCart(id) {
    const product = products.find(p => p.id === id);
    const cartItem = cart.find(item => item.id === id);

    if (product.quantityInventory > 0) {
        if (cartItem) {
            cartItem.quantity++;
        } else {
            cart.push({...product, quantity: 1});
        }
        product.quantityInventory--; // Decrease stock quantity
    }

    displayCart();
    displayProducts(); // Refresh product list
}

function removeFromCart(id) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
        const cartItem = cart[index];
        const product = products.find(p => p.id === cartItem.id);

        cart[index].quantity--;
        product.quantityInventory++; // Return stock to inventory

        if (cart[index].quantity === 0) {
            cart.splice(index, 1);
        }
    }
    displayCart();
    displayProducts(); // Refresh product list
}

function updateDays(id, days) {
    const cartItem = cart.find(item => item.id === id);
    const product = products.find(p => p.id === id);

    if (cartItem) {
        const updatedQuantity = parseInt(days, 10);
        const difference = updatedQuantity - cartItem.quantity;

        if (difference > 0 && product.quantityInventory >= difference) {
            cartItem.quantity = updatedQuantity;
            product.quantityInventory -= difference;
        } else if (difference < 0) {
            cartItem.quantity = updatedQuantity;
            product.quantityInventory += Math.abs(difference);
        }
    }
    updateTotal();
    displayCart();
    displayProducts(); // Refresh product list
}

function updateTotal() {
    const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
    document.getElementById('totalAmountInput').value = totalAmount.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
    displayProducts();
    $('#customerSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select customer email or leave empty for no receipt',
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