<?php
$message = "";
$modalTitle = "";
$modalClass = "";
if ($role_name == "Admin") {

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $price = mysqli_real_escape_string($db, $_POST['price']);
    $quantity = mysqli_real_escape_string($db, $_POST['quantity']);
    $expiration = mysqli_real_escape_string($db, $_POST['expiration']);

    $maxSelect = mysqli_query($db, "SELECT MAX(`product_id`) AS maxID FROM `products`");
    $fetchMax = mysqli_fetch_assoc($maxSelect);
    $maxID = $fetchMax['maxID'] + 1;

    $target_dir = "assets/products/";
    $file_hash = md5($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $file_hash . '.' . $imageFileType;
    $file = $file_hash . '.' . $imageFileType;
    $errors = array();

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $stmt = $db->prepare("INSERT INTO `products`(`name`, `description`, `expiration`, `price`, `picture`, `created_at`)
                                    VALUES (?, ?, ?, ?, ?, now())");
                $stmt->bind_param("sssds", $name, $description, $expiration, $price, $file);
                $submit = $stmt->execute();
                $stmt->close();

                if ($submit) {
                    $stmt = $db->prepare("INSERT INTO `inventory`(`product_id`, `quantity`, `last_updated`) VALUES (?, ?, now())");
                    $stmt->bind_param("ii", $maxID, $quantity);
                    $stmt->execute();
                    $stmt->close();
                    echo "<script>window.location.href='offerings_products.php?productPosted';</script>";
                } else {
                    $errors[] = "Database error: Unable to register item.";
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your photo.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        $errors[] = "File is not an image.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            $modalTitle = "Error!";
            $message = $error;
            $modalClass = "text-danger";
        }
    }
}

if (isset($_GET['deleteProduct'])) {
    $product_id = $_GET['deleteProduct'];
    $delete_query = mysqli_query($db, "UPDATE `products` SET picture = '' WHERE product_id = '$product_id'");
    if ($delete_query) {
        mysqli_query($db, "DELETE FROM `inventory` WHERE product_id = '$product_id'");
        echo "<script>window.location.href='offerings_products.php?deleteProductSuccess';</script>";
    }
}

if (isset($_GET['deleteProductSuccess'])) {
    $message = "Your product has been deleted in your website.";
    $modalTitle = "Product Deleted!";
    $modalClass = "text-success";
}

if (isset($_GET['productPosted'])) {
    $message = "Your product has been posted/updated in your website.";
    $modalTitle = "Product Posted/Updated!";
    $modalClass = "text-success";
}

if (isset($_GET['updateProduct'])) {
    $updateProduct = $_GET['updateProduct'];
}

if(isset($_POST['update'])) {
    $updateID = mysqli_real_escape_string($db, $_POST['updateID']);
    $updateName = mysqli_real_escape_string($db, $_POST['updateName']);
    $updateDescription = mysqli_real_escape_string($db, $_POST['updateDescription']);
    $updatePrice = mysqli_real_escape_string($db, $_POST['updatePrice']);
    $updateExpiration = mysqli_real_escape_string($db, $_POST['updateExpiration']);
    $updateQuantity = mysqli_real_escape_string($db, $_POST['updateQuantity']);

    if(!empty($_FILES['updatePhoto']['name'])) {
        $target_dir = "assets/products/";
        $file_hash = md5($_FILES["updatePhoto"]["name"]);
        $imageFileType = strtolower(pathinfo($_FILES["updatePhoto"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $file_hash . '.' . $imageFileType;
        $file = $file_hash . '.' . $imageFileType;

        if(move_uploaded_file($_FILES["updatePhoto"]["tmp_name"], $target_file)) {
            mysqli_query($db, "UPDATE products SET 
                name = '$updateName',
                description = '$updateDescription',
                price = '$updatePrice',
                expiration = '$updateExpiration',
                picture = '$file'
                WHERE product_id = '$updateID'");
        }
    } else {
        mysqli_query($db, "UPDATE products SET 
            name = '$updateName',
            description = '$updateDescription',
            price = '$updatePrice',
            expiration = '$updateExpiration'
            WHERE product_id = '$updateID'");
    }

    mysqli_query($db, "UPDATE inventory SET 
        quantity = '$updateQuantity',
        last_updated = now()
        WHERE product_id = '$updateID'");
    
    echo "<script>window.location.href='offerings_products.php?productPosted';</script>";
}
?>

<?php include('scripts/modal.php'); ?>

<?php if(isset($_GET['updateProduct'])) {
    $updateProduct = $_GET['updateProduct'];
    $selectProduct = mysqli_query($db, "SELECT * FROM products LEFT JOIN inventory ON products.product_id = inventory.product_id WHERE products.product_id = '$updateProduct'");
    $fetchProduct = mysqli_fetch_assoc($selectProduct);
?>
<div class="modal fade" id="updateModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-white py-3">
                <h5 class="modal-title">Update Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="updateName" value="<?php echo $fetchProduct['name'] ?>" required>
                                <label>Name</label>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" name="updateDescription" required><?php echo $fetchProduct['description'] ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <img src="assets/products/<?php echo $fetchProduct['picture'] ?>" 
                                     class="img-thumbnail mb-2" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="form-floating">
                                    <input type="file" class="form-control" name="updatePhoto" accept="image/*">
                                    <label>Update Photo (Optional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" name="updateExpiration" value="<?php echo $fetchProduct['expiration'] ?>" required>
                                <label>Expiration</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" min="1" class="form-control" name="updatePrice" value="<?php echo $fetchProduct['price'] ?>" required>
                                <label>Price</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" min="1" class="form-control" name="updateQuantity" value="<?php echo $fetchProduct['quantity'] ?>" required>
                                <label>Quantity</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="updateID" value="<?php echo $updateProduct ?>">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
    updateModal.show();
});
</script>
<?php } ?>

<div class="container-fluid" style="margin-top: 8rem">
    <div class="row">
        <!-- Product Registry Form -->
        <div class="col-lg-4 order-lg-2 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Product Registry</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" placeholder="Name" name="name" required>
                            <label>Name</label>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" placeholder="Description" rows="4" name="description" required></textarea>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" placeholder="Expiration" name="expiration" required>
                            <label>Expiration</label>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="form-floating mb-3">
                                    <input type="number" min="1" class="form-control" placeholder="Price" name="price" required>
                                    <label>Price</label>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-floating mb-3">
                                    <input type="number" min="1" class="form-control" placeholder="Quantity" name="quantity" required>
                                    <label>Quantity</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" placeholder="Picture" name="photo" accept="image/*" required>
                            <label>Picture</label>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary float-end">Register</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="col-lg-8 order-lg-1">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Products List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 25%">Particulars</th>
                                    <th style="width: 15%">Expiration</th>
                                    <th style="width: 25%">Description</th>
                                    <th style="width: 15%" class="text-end">Price</th>
                                    <th style="width: 15%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $selectItems = mysqli_query($db, "SELECT 
                                products.*, 
                                inventory.quantity 
                                FROM products 
                                LEFT JOIN inventory ON products.product_id = inventory.product_id 
                                WHERE products.picture != '' 
                                ORDER BY products.product_id DESC");
                                $i = 1;
                                while ($fetchItems = mysqli_fetch_assoc($selectItems)) {
                                    $searchText = '\r\n';
                                    $replaceText = '<br>';
                                    $fetchedText = $fetchItems['description'];
                                    $replacedText = str_replace($searchText, $replaceText, $fetchedText);
                                ?>
                                <tr>
                                    <td class="align-middle"><?php echo $i ?></td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/products/<?php echo $fetchItems['picture']; ?>" 
                                                 alt="<?php echo $fetchItems['name']; ?>" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $fetchItems['name']; ?></h6>
                                                <small class="text-muted">Stock: <?php 
    echo isset($fetchItems['quantity']) ? $fetchItems['quantity'] : '0'; 
?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle"><?php 
                                        $expirationDate = new DateTime($fetchItems['expiration']);
                                        echo $expirationDate->format('M d, Y'); 
                                    ?></td>
                                    <td class="align-middle"><small><?php echo $replacedText; ?></small></td>
                                    <td class="align-middle text-end">₱<?php echo number_format($fetchItems['price'], 2, ".", ",") ?></td>
                                    <td class="align-middle text-center">
                                        <a href="?updateProduct=<?php echo $fetchItems['product_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary me-1">
                                            View
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?php echo $fetchItems['product_id'] ?>')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php $i++; } ?>

                                <?php if(mysqli_num_rows($selectItems) < 1) { ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No products found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="alert alert-warning">
            You are not allowed to enter this page.
        </div>
    </div>
<?php } ?>

<script>
function confirmDelete(productId) {
    document.getElementById('confirmDeleteButton').href = "?deleteProduct=" + productId;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
}
</script>

<style>
.table td, .table th {
    vertical-align: middle;
    padding: 1rem;
}
.table thead th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
.btn-group {
    gap: 0.25rem;
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
.pagination {
    margin-bottom: 0;
}
.page-link {
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin: 0 0.25rem;
}
.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.btn-outline-primary {
    border-width: 1px;
}
.btn-outline-danger {
    border-width: 1px;
}
#updateModal {
  margin-top: 8rem;
}

/* Or for a more responsive approach */
#updateModal .modal-dialog {
  margin-top: 50px;
}
</style>