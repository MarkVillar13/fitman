<?php
if (isset($_POST['updateProductNow'])) {
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $description = mysqli_real_escape_string($db, $_POST['description']);
  $price = mysqli_real_escape_string($db, $_POST['price']);
  $quantity = mysqli_real_escape_string($db, $_POST['quantity']);

  $target_dir = "assets/products/";
  $file_hash = md5($_FILES["photo"]["name"]);
  $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
  $target_file = $target_dir . $file_hash . '.' . $imageFileType;
  $file=$file_hash . '.' . $imageFileType;
  $errors = array();

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["photo"]["tmp_name"]);
  if ($check !== false) {
      if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
          if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
              $submit = mysqli_query($db, "UPDATE `products` SET name = '$name', description = '$description', price = '$price', picture = '$file', created_at = now() WHERE product_id = '$updateProduct'");
              if ($submit) {
                  mysqli_query($db, "UPDATE inventory SET quantity = '$quantity', last_updated = now() WHERE product_id = '$updateProduct' ");
                  echo "<script>
                        window.location.href='offerings_products.php?productPosted';</script>";
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
if (isset($_POST['updateServiceNow'])){
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $description = mysqli_real_escape_string($db, $_POST['description']);
  $price = mysqli_real_escape_string($db, $_POST['price']);

  $target_dir = "assets/services/";
  $file_hash = md5($_FILES["photo"]["name"]);
  $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
  $target_file = $target_dir . $file_hash . '.' . $imageFileType;
  $file=$file_hash . '.' . $imageFileType;
  $errors = array();

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["photo"]["tmp_name"]);
  if ($check !== false) {
      if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
          if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
              mysqli_query($db, "UPDATE `services` SET name = '$name', description = '$description', price = '$price', picture = '$file', created_at = now() WHERE service_id = '$updateService'");
              echo "<script>
                    window.location.href='offerings_services.php?servicePosted';</script>";
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
 ?>
<!-- Service Modal -->
<div class="modal fade" id="updateService">
    <div class="modal-dialog" style="display:flex;align-items: center;min-height: calc(100% - 1rem);">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title w3-text-yellow">
                    Update Service
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
              <!-- Modal body -->
              <div class="modal-body">
                <?php
                $servicesModal = mysqli_query($db, "SELECT * FROM services WHERE service_id = '$updateService'");
                $fetchServicesModal = mysqli_fetch_assoc($servicesModal);
                ?>
                <div class="w3-col mb-3">
                  <input type="text" class="form-control" placeholder="Name" name="name" value="<?php echo $fetchServicesModal['name']; ?>" required>
                </div>
                <div class="w3-col mb-3">
                  <textarea class="form-control" placeholder="Description" rows="4" cols="80" name="description" required><?php echo $fetchServicesModal['description']; ?></textarea>
                </div>
                <div class="w3-col mb-3">
                  <span class="w3-col s2">Php</span>
                  <input type="number" min="1" class="w3-col s10 form-control" placeholder="Price" name="price" value="<?php echo $fetchServicesModal['price']; ?>" required>
                </div>
                <div class="w3-col mb-3">
                  <input type="file" class="form-control mt-2" placeholder="Picture" name="photo" accept="image/*" required>
                </div>
              </div>
              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="submit" name="updateServiceNow" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
            <img src="assets/services/<?php echo $fetchServicesModal['picture']; ?>" alt="Current Picture" style="max-width: 100%; height: auto;">
        </div>
    </div>
</div>
<!-- Product Modal -->
<div class="modal fade" id="updateProduct">
    <div class="modal-dialog" style="display:flex;align-items: center;min-height: calc(100% - 1rem);">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title w3-text-yellow">
                    Update Product
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
              <!-- Modal body -->
              <div class="modal-body">
                <?php
                $productModal = mysqli_query($db, "SELECT * FROM products INNER JOIN inventory ON products.product_id = inventory.product_id WHERE products.product_id = '$updateProduct'");
                $fetchProductModal = mysqli_fetch_assoc($productModal);
                ?>
                <div class="w3-col mb-3">
                  <input type="text" class="form-control" placeholder="Name" name="name" value="<?php echo $fetchProductModal['name']; ?>" required>
                </div>
                <div class="w3-col mb-3">
                  <textarea class="form-control" placeholder="Description" rows="4" cols="80" name="description" required><?php echo $fetchProductModal['description']; ?></textarea>
                </div>
                <div class="w3-col s6 mb-3">
                  <span class="w3-col s3">Php</span>
                  <input type="number" min="1" class="w3-col s9 form-control" placeholder="Price" name="price" value="<?php echo $fetchProductModal['price']; ?>" required>
                </div>
                <div class="w3-col s6 mb-3">
                  <input type="number" min="1" class="form-control" placeholder="Quantity" name="quantity" value="<?php echo $fetchProductModal['quantity']; ?>" required>
                </div>
                <div class="w3-col mb-3">
                  <input type="file" class="form-control mt-2" placeholder="Picture" name="photo" accept="image/*" required>
                </div>
              </div>
              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="submit" name="updateProductNow" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
            <img src="assets/products/<?php echo $fetchProductModal['picture']; ?>" alt="Current Picture" style="max-width: 100%; height: auto;">
        </div>
    </div>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($updateService)) { ?>
        $('#updateService').modal('show');
    <?php } ?>
    <?php if (!empty($updateProduct)) { ?>
        $('#updateProduct').modal('show');
    <?php } ?>
});
</script>
