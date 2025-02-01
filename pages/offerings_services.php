<?php
$message = "";
$modalTitle = "";
$modalClass = "";
if ($role_name == "Admin") {

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $price = mysqli_real_escape_string($db, $_POST['price']);
    $subscription = mysqli_real_escape_string($db, $_POST['subscription']);

    $target_dir = "assets/services/";
    $file_hash = md5($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $file_hash . '.' . $imageFileType;
    $file = $file_hash . '.' . $imageFileType;
    $errors = array();

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $stmt = $db->prepare("INSERT INTO `services`(`name`, `description`, `price`, `picture`, `subscription`, `created_at`)
                                    VALUES (?, ?, ?, ?, ?, now())");
                $stmt->bind_param("ssdss", $name, $description, $price, $file, $subscription);
                $submit = $stmt->execute();
                $stmt->close();
                echo "<script>window.location.href='offerings_services.php?servicePosted';</script>";
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

if (isset($_GET['deleteService'])) {
    $service_id = $_GET['deleteService'];
    mysqli_query($db, "DELETE FROM `services` WHERE service_id = '$service_id'");
    echo "<script>window.location.href='offerings_services.php?deleteServiceSuccess';</script>";
}

if (isset($_GET['deleteServiceSuccess'])) {
    $message = "Your service has been deleted in your website.";
    $modalTitle = "Service Deleted!";
    $modalClass = "text-success";
}

if (isset($_GET['servicePosted'])) {
    $message = "Your service has been posted/updated in your website.";
    $modalTitle = "Service Posted/Updated!";
    $modalClass = "text-success";
}

if (isset($_GET['updateService'])) {
    $updateService = $_GET['updateService'];
}

if(isset($_POST['update'])) {
    $updateID = mysqli_real_escape_string($db, $_POST['updateID']);
    $updateName = mysqli_real_escape_string($db, $_POST['updateName']);
    $updateDescription = mysqli_real_escape_string($db, $_POST['updateDescription']);
    $updatePrice = mysqli_real_escape_string($db, $_POST['updatePrice']);
    $updateSubscription = mysqli_real_escape_string($db, $_POST['updateSubscription']);

    if(!empty($_FILES['updatePhoto']['name'])) {
        $target_dir = "assets/services/";
        $file_hash = md5($_FILES["updatePhoto"]["name"]);
        $imageFileType = strtolower(pathinfo($_FILES["updatePhoto"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $file_hash . '.' . $imageFileType;
        $file = $file_hash . '.' . $imageFileType;

        if(move_uploaded_file($_FILES["updatePhoto"]["tmp_name"], $target_file)) {
            mysqli_query($db, "UPDATE services SET 
                name = '$updateName',
                description = '$updateDescription',
                price = '$updatePrice',
                subscription = '$updateSubscription',
                picture = '$file'
                WHERE service_id = '$updateID'");
        }
    } else {
        mysqli_query($db, "UPDATE services SET 
            name = '$updateName',
            description = '$updateDescription',
            price = '$updatePrice',
            subscription = '$updateSubscription'
            WHERE service_id = '$updateID'");
    }
    
    echo "<script>window.location.href='offerings_services.php?servicePosted';</script>";
}
?>

<?php include('scripts/modal.php'); ?>

<?php if(isset($_GET['updateService'])) {
    $updateService = $_GET['updateService'];
    $selectService = mysqli_query($db, "SELECT * FROM services WHERE service_id = '$updateService'");
    $fetchService = mysqli_fetch_assoc($selectService);
?>
<div class="modal fade" id="updateModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-white py-3">
                <h5 class="modal-title">Update Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="updateName" value="<?php echo $fetchService['name'] ?>" required>
                                <label>Name</label>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" name="updateDescription" required><?php echo $fetchService['description'] ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <img src="assets/services/<?php echo $fetchService['picture'] ?>" 
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
                                <input type="number" min="1" class="form-control" name="updatePrice" value="<?php echo $fetchService['price'] ?>" required>
                                <label>Price</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="updateSubscription" required>
                                    <option value="1" <?php if($fetchService['subscription'] == "1") echo "selected"; ?>>1 day</option>
                                    <option value="7" <?php if($fetchService['subscription'] == "7") echo "selected"; ?>>7 days</option>
                                    <option value="15" <?php if($fetchService['subscription'] == "15") echo "selected"; ?>>15 days</option>
                                    <option value="30" <?php if($fetchService['subscription'] == "30") echo "selected"; ?>>1 month</option>
                                </select>
                                <label>Subscription</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="updateID" value="<?php echo $updateService ?>">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Service</button>
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
        <!-- Service Registry Form -->
        <div class="col-lg-4 order-lg-2 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Service Registry</h5>
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
                        <div class="row">
                            <div class="col-4">
                                <div class="form-floating mb-3">
                                    <input type="number" min="1" class="form-control" placeholder="Price" name="price" required>
                                    <label>Price</label>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="subscription" required>
                                        <option value="1">1 day</option>
                                        <option value="7">7 days</option>
                                        <option value="15">15 days</option>
                                        <option value="30">1 month</option>
                                    </select>
                                    <label>Subscription</label>
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

        <!-- Services Table -->
        <div class="col-lg-8 order-lg-1">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Services List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 25%">Particulars</th>
                                    <th style="width: 35%">Description</th>
                                    <th style="width: 15%" class="text-end">Price</th>
                                    <th style="width: 20%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $selectItems = mysqli_query($db, "SELECT * FROM services ORDER BY service_id DESC");
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
                                            <img src="assets/services/<?php echo $fetchItems['picture']; ?>" 
                                                 alt="<?php echo $fetchItems['name']; ?>" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $fetchItems['name']; ?></h6>
                                                <small class="text-muted"><?php echo $fetchItems['subscription']; ?> days</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle"><small><?php echo $replacedText; ?></small></td>
                                    <td class="align-middle text-end">₱<?php echo number_format($fetchItems['price'], 2, ".", ",") ?></td>
                                    <td class="align-middle text-center">
                                        <a href="?updateService=<?php echo $fetchItems['service_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary me-1">
                                            View
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?php echo $fetchItems['service_id'] ?>')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php $i++; } ?>

                                <?php if(mysqli_num_rows($selectItems) < 1) { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No services found</td>
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
                Are you sure you want to delete this service?
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
function confirmDelete(serviceId) {
    document.getElementById('confirmDeleteButton').href = "?deleteService=" + serviceId;
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