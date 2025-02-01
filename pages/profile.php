<div class="w3-container w3-padding">
  <div class="w3-quarter">
    <?php
    if (isset($_SESSION['email'])) {
      // Define your QR code data
      $data = $_SESSION['email'];

      // Prepare parameters for the API request
      $params = [
          'size' => '300x300', // QR code size
          'data' => $data,
      ];

      // Build query string
      $queryString = http_build_query($params);

      // API endpoint
      $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?" . $queryString;

      // Specify the folder where you want to save the QR code image
      $folder = "assets/qrcodes/";

      // Ensure the folder exists, create it if it doesn't
      if (!file_exists($folder)) {
          mkdir($folder, 0777, true);
      }

      // Use a hashed version of the email as the filename to ensure uniqueness and avoid conflicts
      $filename = $folder . $data . '.png';

      // Check if the file already exists
      if (file_exists($filename)) {
          // Display the existing QR code image
          echo '<img src="' . $filename . '" alt="QR Code" style="width:100%">';
      } else {
          // Make the request to the API
          $response = file_get_contents($apiUrl);

          // Check if the request was successful
          if ($response !== false) {
              // Save the QR code image to the img folder
              file_put_contents($filename, $response);

              // Display the saved QR code image
              echo '<img src="' . $filename . '" alt="QR Code" style="width:100%">';
          } else {
              // Handle error
              echo "Failed to generate QR code.";
          }
      }
    }
     ?>
  </div>
  <?php
  $oldPassword=$_SESSION['security'];
  if (isset($_POST['password'])) {
    $old=md5(mysqli_real_escape_string($db,$_POST['oldPassword']));
    $new=md5(mysqli_real_escape_string($db,$_POST['newPassword']));

    if ($oldPassword === $old || $temporaryPassword === $old) {
      mysqli_query($db,"UPDATE `users` SET `password`='$new' WHERE `email`='$email'");
      echo "<script>
      window.location.href='profile.php?changedPassword';</script>";
    } else{
    echo "<script>
    window.location.href='profile.php?errorChangingPassword';</script>";
     }
  }
  if (isset($_GET['changedPassword'])) {
    $message = "Your password has been updated.";
    $modalTitle = "Success!";
    $modalClass = "text-success";
  }
  if (isset($_GET['errorChangingPassword'])) {
    $message = "Ooops! Your old password is incorrect. Try again.";
    $modalTitle = "Wrong Combination!";
    $modalClass = "text-danger";
  }
   ?>
<?php include('scripts/modal.php'); ?>
 <div class="w3-threequarter" id="offer">
   <span class="h5 w3-col w3-padding w3-text-black">Change Password</span>
   <div class="w3-container">
     <form class="" action="" method="post">
       <div class="form-floating mb-3 mt-3">
         <input type="password" class="form-control" id="password1" placeholder="Old Password" name="oldPassword" required>
         <label for="password">Old Password</label>
       </div>
       <input type="checkbox" onclick="showPassword1()"><span class="w3-text-black"> Show Password</span>
       <div class="form-floating mb-3 mt-3">
         <input type="password" class="form-control" id="password2" placeholder="Old Password" name="newPassword" required>
         <label for="password">New Password</label>
       </div>
       <div id="error-message" class="w3-text-red"></div>
       <input type="checkbox" onclick="showPassword2()"><span class="w3-text-black"> Show Password</span>
       <button type="submit" name="password" class="btn btn-secondary w3-right">Update Password</button>
     </form>
   </div>
   <?php
   if (isset($_GET['updateInfo'])) {
     $lastname = mysqli_real_escape_string($db, $_GET['lastname']);
     $firstname = mysqli_real_escape_string($db, $_GET['firstname']);
     $salutation = mysqli_real_escape_string($db, $_GET['salutation']);
     $gender = mysqli_real_escape_string($db, $_GET['gender']);
     $contactNo = mysqli_real_escape_string($db, $_GET['contactNo']);
     $address = mysqli_real_escape_string($db, $_GET['address']);

     $checkInfo=mysqli_query($db,"SELECT * FROM profile
     WHERE user_id='$user_id'");
     if($checkInfo ->num_rows > 0){
       $updateUser=mysqli_query($db,"UPDATE `users` SET `last_name`='$lastname', `first_name`='$firstname'
         WHERE user_id = '$user_id'");
         if ($updateUser) {
           mysqli_query($db,"UPDATE `profile` SET `salutation`='$salutation',
             `gender`='$gender',`contactNo`='$contactNo',`address`='$address'
             WHERE user_id = '$user_id'");
             echo "<script>
             window.location.href='profile.php?updated=".$firstname." ".$lastname."';</script>";
         } else {
           $message = "Please, try again.";
           $modalTitle = "Error on updating!";
           $modalClass = "text-danger";
         }
     } else {
       $updateUser=mysqli_query($db,"UPDATE `users` SET `last_name`='$lastname', `first_name`='$firstname'
         WHERE user_id = '$user_id'");
         if ($updateUser) {
           mysqli_query($db,"INSERT INTO `profile`(`user_id`, `salutation`, `gender`, `contactNo`, `address`)
           VALUES ('$user_id','$salutation','$gender','$contactNo','$address')");
           echo "<script>
           window.location.href='profile.php?updated=".$firstname." ".$lastname."';</script>";
         } else {
           $message = "Please, try again.";
           $modalTitle = "Error on updating!";
           $modalClass = "text-danger";
         }
     }
   }
   if (isset($_GET['updated'])) {
     $message = "You have updated the information of ".$first_name." ".$last_name.".";
     $modalTitle = "Information Updated!";
     $modalClass = "text-success";
   }

   ?>
   <div class="w3-container">
     <form class="" action="" method="get">
       <?php
       $usersInfo = mysqli_query($db, "SELECT
           profile.salutation AS salutation,
           users.last_name AS lastname,
           users.first_name AS firstname,
           profile.gender AS gender,
           profile.contactNo AS contactNo,
           profile.address AS address,
           users.user_id AS user_id
       FROM users
       INNER JOIN profile ON users.user_id = profile.user_id
       WHERE users.user_id = '$user_id'");

       $fetchInfo=mysqli_fetch_assoc($usersInfo);
        ?>
       <div class="w3-col s3 form-floating mb-3 mt-3">
         <select type="text" class="form-control" placeholder="Salutation" name="salutation" required style="text-transform:capitalize">
           <?php
           if(!empty($fetchInfo['salutation'])){
           ?>
           <option value="<?php echo $fetchInfo['salutation'] ?>" selected><?php echo $fetchInfo['salutation']."."; ?></option>
         <?php } ?>
           <option value="mr">Mr.</option>
           <option value="ms">Ms.</option>
           <option value="mrs">Mrs.</option>
           <option value="dr">Dr.</option>
           <option value="dra">Dra.</option>
           <option value="atty">Atty.</option>
           <option value="engr">Engr.</option>
         </select>
         <label for="" class="w3-text-black">Salutation</label>
       </div>
       <div class="w3-col s4 form-floating mb-3 mt-3">
           <input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php echo $last_name ?>" required style="text-transform:capitalize">
           <label for="name" class="w3-text-black">Last Name</label>
       </div>
       <div class="w3-col s5 form-floating mb-3 mt-3">
           <input type="text" class="form-control" name="firstname" placeholder="Name" value="<?php echo $first_name ?>" required style="text-transform:capitalize">
           <label for="name" class="w3-text-black">First Name</label>
       </div>
       <div class="w3-col s3 form-floating mb-3">
         <select type="text" class="form-control" placeholder="Gender" name="gender" required style="text-transform:capitalize">
           <?php
           if(!empty($fetchInfo['gender'])){
           ?>
           <option value="<?php echo $fetchInfo['gender'] ?>" selected><?php echo $fetchInfo['gender'] ?></option>
         <?php } ?>
           <option value="male">Male</option>
           <option value="female">Female</option>
         </select>
         <label for="" class="w3-text-black">Gender</label>
       </div>
       <div class="w3-col s9 form-floating mb-3">
           <input type="text" style="text-transform:capitalize" class="form-control" name="contactNo" placeholder="Contact No." value="<?php if(!empty($fetchInfo['contactNo'])){ echo $fetchInfo['contactNo']; } ?>" required>
           <label for="name" class="w3-text-black">Contact No.</label>
       </div>
       <div class="w3-col form-floating mb-3">
           <input type="text" style="text-transform:capitalize" class="form-control" name="address" placeholder="Address" value="<?php if(!empty($fetchInfo['address'])){ echo $fetchInfo['address']; } ?>" required>
           <label for="name" class="w3-text-black">Address</label>
       </div>
       <button type="submit" name="updateInfo" class="btn btn-primary w3-right">Update Profile</button>
     </form>
   </div>

 </div>
</div>
<?php
include('scripts/password.js');
 ?>
 <script type="text/javascript">
 document.addEventListener("DOMContentLoaded", function() {
 const passwordInput = document.getElementById("password2");
 const errorMessage = document.getElementById("error-message");

 passwordInput.addEventListener("input", function() {
   validatePassword(passwordInput.value);
 });

 function validatePassword(password) {
   const hasLowercase = /[a-z]/.test(password);
   const hasUppercase = /[A-Z]/.test(password);
   const hasNumber = /[0-9]/.test(password);
   const hasSpecialCharacter = /[!@#$%^&*(),.?":{}|<>]/.test(password);

   let errors = [];

   if (!hasLowercase) {
       errors.push("At least one lowercase letter required.");
   }

   if (!hasUppercase) {
       errors.push("At least one uppercase letter required.");
   }

   if (!hasNumber) {
       errors.push("At least one number required.");
   }

   if (!hasSpecialCharacter) {
       errors.push("At least one special character required.");
   }

   if (errors.length > 0) {
     errorMessage.textContent = errors.join(" ");
   } else {
     errorMessage.textContent = "";
   }
 }
 });

 </script>
