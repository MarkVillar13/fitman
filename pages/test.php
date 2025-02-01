<?php
$message = "";
$modalTitle = "";
$modalClass = "";
if (isset($_POST['send'])) {
  $messageSend=mysqli_real_escape_string($db, $_POST['message']);
  $date=date('Y-m-d H:i:s');
  $targetDir = "assets/messageFiles/";
  $allowTypes = array('jpg','png','jpeg','gif');
  $queryid = "SELECT MAX(mail_id) AS max_id FROM mails";
  $idresult = mysqli_query($db, $queryid);
  $rowid = mysqli_fetch_assoc($idresult);
  $last_used_number = $rowid['max_id'];
  $postno=$last_used_number+1;
  $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
  $fileNames = array_filter($_FILES['files']['name']);
  if(!empty($fileNames)){
          foreach($_FILES['files']['name'] as $key=>$val){
              // File upload path
              $fileName = basename($_FILES['files']['name'][$key]);
              $targetFilePath = $targetDir . $fileName;

              // Check whether file type is valid
              $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
              if(in_array($fileType, $allowTypes)){
                  // Upload file to server
                  if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                      // Image db insert sql
                      $insertValuesSQL .= "('".$postno."','".$fileName."'),";
                  }else{
                      $errorUpload .= $_FILES['files']['name'][$key].' | ';
                  }
              }else{
                  $errorUploadType .= $_FILES['files']['name'][$key].' | ';
              }
          }
          // Error message
                $errorUpload = !empty($errorUpload)?'Upload Error: '.trim($errorUpload, ' | '):'';
                $errorUploadType = !empty($errorUploadType)?'File Type Error: '.trim($errorUploadType, ' | '):'';
                $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType;

                if(!empty($insertValuesSQL)){

                    $insertValuesSQL = trim($insertValuesSQL, ',');

                    // Insert image file name into database
                    $insert = $db->query("INSERT INTO mailfiles (mail_id, file) VALUES $insertValuesSQL");
                    if($insert){
                        $query = "INSERT INTO mails (user_id, message, remarks, `date`)
                            VALUES('$user_id','$messageSend', 'admin', '$date')";
                            mysqli_query($db, $query);
                            echo "<script>
                                  window.location.href='mails.php?messageSent';</script>";
                    }else{
                      $message = "Sending failed. Try it again.";
                      $modalTitle = "Message not Sent";
                      $modalClass = "text-danger";
                    }
                }else{
                    $message = "Uploading failed. Try uploading it again.";
                    $modalTitle = "Upload failed";
                    $modalClass = "text-danger";
                }
  }
  else {
    $query = "INSERT INTO mails (user_id, message, remarks, `date`)
        VALUES('$user_id','$messageSend', 'user', '$date')";
        mysqli_query($db, $query);
        echo "<script>
              window.location.href='mails.php?messageSent';</script>";
  }

}
if (isset($_GET['messageSent'])) {
  $message = "Your message has been successfully delivered to the System Administrator. You can expect a response within 24 hours. Thank you for your patience.";
  $modalTitle = "Message Sent";
  $modalClass = "text-success";
}
include('scripts/modal.php');
 ?>
<style>
    body {
        font-family: "Verdana", sans-serif;
    }
    .file-input-wrapper {
        display: flex;
        align-items: center;
    }
    .file-input-wrapper input[type="file"] {
        display: none;
    }
    .file-input-wrapper label {
        cursor: pointer;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: flex;
        align-items: center;
    }
    .file-input-wrapper label:hover {
        background-color: #f0f0f0;
    }
    .file-input-wrapper .icon {
        margin-right: 8px;
    }
    .form-popup {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 9;
    }
    #mail {
      position: fixed;
      bottom: 10px;
      right: 10px;
      cursor: pointer;
      width: 50px;
      height: 50px;
      background-color: #0078ff; /* Messenger blue color */
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      animation: jump 1s ease infinite;
      transition: transform 0.2s;
    }

    #mail:hover {
      animation: none;
    }

    .icon {
      font-size: 24px;
      color: black;
    }

    .notification {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: red;
      color: black;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 12px;
    }

    @keyframes jump {
      0%, 100% {
          transform: translateY(0);
      }
      50% {
          transform: translateY(-10px);
      }
    }
    .message {
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
    }

    .message-content {
      max-width: 75%;
      min-width: 45%;
      padding: 10px;
      border-radius: 10px;
      black-space: pre-line;
    }

    .received .message-time {
      font-size: 10px;
      color: black;
      text-align: left;
      margin-top: 5px;
    }
    .sent .message-time {
      font-size: 10px;
      color: black;
      text-align: right;
      margin-top: 5px;
    }

    .received .message-content {
      background-color: #e1ffc7;
      align-self: flex-start;
    }

    .sent .message-content {
      background-color: #0078ff;
      color: black;
      align-self: flex-end;
    }
    .received img{
      max-width: 45%;
      align-self: flex-start;
    }
    .sent img{
      max-width: 45%;
      align-self: flex-end;
    }
</style>
<script>
    function toggleForm() {
        var form = document.getElementById("mailForm");
        var formButton = document.getElementById("mail");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
            formButton.style.display = "none";
        } else {
            form.style.display = "none";
            formButton.style.display = "block";
        }
    }
</script>
<script>
    var messageDiv1 = document.getElementById('message1');
    messageDiv1.textContent = messageDiv1.textContent.trim();
    var messageDiv = document.getElementById('message');
    messageDiv.textContent = messageDiv.textContent.trim();
  </script>
<!-- messages -->
<div class="w3-quarter w3-container">

</div>
<div class="w3-half">
  <?php
  function convertTo12HourFormat($dateTime) {
      $date = new DateTime($dateTime);
      return $date->format('Y-m-d h:i:s A');
  }
  $messagesQuery=mysqli_query($db,"SELECT * FROM mails WHERE user_id = '$user_id' OR user_id = '0' ORDER BY mail_id desc limit 50");
  while ($message_fetch=mysqli_fetch_assoc($messagesQuery)) {
    $mail_id=$message_fetch['mail_id'];
    $dateTime = $message_fetch['date'];
    $formattedDateTime = convertTo12HourFormat($dateTime);
    if ($message_fetch['remarks'] == "admin") {
  ?>
  <div class="message sent">
      <div id="message1" class="message-content">
        <div class="" style="margin-top:-16px">
          <?php echo $message_fetch['message'] ?>
        </div>
      </div>

      <?php
      $fileQuery=mysqli_query($db,"SELECT * FROM mailfiles WHERE mail_id = '$mail_id'");
      while ($file_fetch=mysqli_fetch_assoc($fileQuery)) {
        echo "
        <img src='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>
        ";
      }
       ?>
       <div class="message-time"><?php echo $formattedDateTime; ?></div>
  </div>
<?php } else{ ?>
    <div class="message received">
      <div id="message" class="message-content">
        <div class="" style="margin-top:-16px">
          <?php echo $message_fetch['message'] ?>
        </div>
      </div>

      <?php
      $fileQuery=mysqli_query($db,"SELECT * FROM mailfiles WHERE mail_id = '$mail_id'");
      while ($file_fetch=mysqli_fetch_assoc($fileQuery)) {
        echo "
        <img src='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>
        ";
      }
       ?>
       <div class="message-time"><?php echo $formattedDateTime; ?></div>
    </div>
  <?php } } ?>
</div>
<div class="w3-quarter w3-container w3-grey"  id="offer">
  <span class="h4">Contacts</span>
  <?php

   ?>
</div>
<!--end of messages -->
<button onclick="toggleForm()" class="w3-display-bottomright btn btn-primary icon" id="mail">
  &#9993;
</button>

<div id="mailForm" class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-left form-popup">
<form class="w3-container" action="" method="post" enctype="multipart/form-data">
  <button onclick="toggleForm()" class="btn btn-danger w3-hover-black w3-right">
    &times
  </button>
    <div class="w3-section">
        <label for="message">Message</label>
        <textarea class="w3-input w3-border" id="message" name="message" rows="2" required></textarea>
    </div>
    <div class="w3-col">
        <button type="submit" class="w3-right w3-button w3-blue w3-padding-large" name="send">Send Message</button>
        <div class="file-input-wrapper w3-right">
            <input type="file" id="file" name="files[]"  capture="user" multiple>
            <label for="file" class="btn btn-success">
                <img src="assets/icons/file_open_FILL0_wght400_GRAD0_opsz48.png" alt="Attach File" style="width:25px" class="icons">
            </label>
        </div>
    </div>
</form>
</div>
