

<a href="?inbox" class="w3-display-bottomright btn btn-primary icon" id="mail">
  <?php
  $countMessages=mysqli_query($db,"SELECT COUNT(mail_id) as countMessages FROM mails WHERE m_to = '$user_id' and status ='unread'");
  $fetchCountMessages=mysqli_fetch_assoc($countMessages);
  if ($fetchCountMessages['countMessages'] > 0) {
    echo"
    <span class='notification'>" . $fetchCountMessages['countMessages'] . " </span>
    ";
  }
   ?>
  &#9993;
</a>
<?php if (isset($_GET['inbox'])){
  $inbox='on';
  ?>
  <script>
    var idleTime = 0;

    // Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 1000); // 1 second

    // Reset idle time when user interacts
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.ontouchstart = resetTimer; // For mobile devices

    function timerIncrement() {
        idleTime++;
        if (idleTime >= 15) {  // 15 seconds of inactivity
            location.reload(); // Auto-refresh the page
        }
    }

    function resetTimer() {
        idleTime = 0; // Reset idle time on user activity
    }
</script>
  <?php
} ?>
<style media="screen">
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
#mail {
  position: fixed;
  bottom: 30px;
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
@keyframes jump {
  0%, 100% {
      transform: translateY(0);
  }
  50% {
      transform: translateY(-10px);
  }
}
.icon {
  font-size: 24px;
  color: black;
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
.message {
  display: flex;
  flex-direction: column-reverse;
  margin-bottom: 10px;
}
.message::-webkit-scrollbar {
      display: none;
    }
.message-content {
  max-width: 75%;
  min-width: 45%;
  padding: 1px;
  border-radius: 10px;
  black-space: pre-line;
}

.received .message-time {
  font-size: 10px;
  color: grey;
  text-align: left;
  margin-top: 5px;
}
.sent .message-time {
  font-size: 10px;
  color: grey;
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
.received img, .received a{
  max-width: 45%;
  align-self: flex-start;
}
.sent img, .sent a{
  max-width: 45%;
  align-self: flex-end;
}
</style>
<?php
if (isset($_GET['to'])) {
  $idSearch=$_GET['to']; }
  else {
    $idSearch=0;
  }

if (isset($_POST['send'])) {
  $messageSend=mysqli_real_escape_string($db, $_POST['message']);
  $m_to=mysqli_real_escape_string($db, $_POST['m_to']);
  $date=date('Y-m-d H:i:s');
  $targetDir = "assets/messageFiles/";
  if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
  }
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

                  // Upload file to server
                  if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                      // Image db insert sql
                      $insertValuesSQL .= "('".$postno."','".$fileName."','".$fileType."'),";
                  }else{
                      $errorUpload .= $_FILES['files']['name'][$key].' | ';
                  }
          }
          // Error message
                $errorUpload = !empty($errorUpload)?'Upload Error: '.trim($errorUpload, ' | '):'';
                $errorUploadType = !empty($errorUploadType)?'File Type Error: '.trim($errorUploadType, ' | '):'';
                $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType;

                if(!empty($insertValuesSQL)){

                    $insertValuesSQL = trim($insertValuesSQL, ',');

                    // Insert image file name into database
                    $insert = $db->query("INSERT INTO mailfiles (mail_id, file, type) VALUES $insertValuesSQL");
                    if($insert){
                        $query = "INSERT INTO mails (user_id, message, m_to, `date`, status)
                            VALUES('$user_id','$messageSend', '$m_to', '$date', 'unread')";
                            mysqli_query($db, $query);
                            echo "<script>
                                  window.location.href='mails.php?inbox&to=". $m_to ."';</script>";
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
    $query = "INSERT INTO mails (user_id, message, m_to, `date`, status)
        VALUES('$user_id','$messageSend', '$m_to', '$date', 'unread')";
        mysqli_query($db, $query);
        echo "<script>
              window.location.href='mails.php?inbox&to=". $m_to ."';</script>";
  }

}
 ?>
<!-- The Modal -->
<div class="modal fade" id="myModal" style="">
    <div class="modal-dialog" style="display:flex;align-items: center;min-height: calc(100% - 1rem);">
        <div class="modal-content w3-theme-l4" style="font-size:12px">

            <!-- Modal Header -->
            <div class="modal-header w3-theme-d5">
                <h5 class="modal-title">
                    Inbox
                </h5>
                <button type="button" class="btn-close w3-text-black" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <div class="w3-col" style="height: 30vh">
                <div class="w3-col w3-padding s8 w3-theme-l5 message" style="height: 28vh;max-height:28vh; overflow:auto">
                  <?php
                  function convertTo12HourFormat($dateTime) {
                      $date = new DateTime($dateTime);
                      return $date->format('Y-m-d h:i:s A');
                  }
                  if (isset($_GET['to'])) {

                    if ($_GET['to'] == 0) {
                      $messagesQuery=mysqli_query($db,"SELECT *
                        FROM mails inner join users on mails.user_id = users.user_id
                        WHERE
                        m_to = '0'
                        ORDER BY mail_id DESC
                        LIMIT 100
                        ");
                    } else {
                      $messagesQuery=mysqli_query($db,"SELECT *
                        FROM mails inner join users on mails.user_id = users.user_id
                        WHERE
                        (mails.user_id = '$user_id' and m_to = '$idSearch')
                        or
                        (mails.user_id = '$idSearch' and m_to = '$user_id')
                        ORDER BY mail_id DESC
                        LIMIT 100
                        ");
                    }
                  }

                  else {
                    $messagesQuery=mysqli_query($db,"SELECT *
                      FROM mails inner join users on mails.user_id = users.user_id
                      WHERE
                      m_to = '0'
                      ORDER BY mail_id DESC
                      LIMIT 100
                      ");
                  }
                  while ($message_fetch=mysqli_fetch_assoc($messagesQuery)) {
                    $senderName=$message_fetch['first_name']." ".$message_fetch['last_name'];
                    $mail_id=$message_fetch['mail_id'];
                    $dateTime = $message_fetch['date'];
                    $formattedDateTime = convertTo12HourFormat($dateTime);
                    if ($message_fetch['user_id'] == "$user_id") {
                  ?>
                  <div class="message sent">
                      <div id="message1" class="message-content">
                        <div class="">
                          <pre><?php echo $message_fetch['message'] ?></pre>
                        </div>
                      </div>
                      <?php
                      $fileQuery=mysqli_query($db,"SELECT * FROM mailfiles
                         WHERE mail_id = '$mail_id'");
                      while ($file_fetch=mysqli_fetch_assoc($fileQuery)) {
                        $allowed_extensions = array('jpg', 'png', 'jpeg', 'gif');
                        $file_extension = strtolower(pathinfo($file_fetch['file'], PATHINFO_EXTENSION));
                        if (in_array($file_extension, $allowed_extensions)) {
                          echo "
                          <img src='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>
                          ";
                        } else {
                          echo "
                          <a href='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>". $file_fetch['file'] ."</a>
                          ";
                        }

                      }
                       ?>
                       <div class="message-time"><?php echo $senderName ?></div>
                  </div>
                <?php } else{ ?>
                    <div class="message received">
                      <div id="message" class="message-content">
                        <div class="">
                          <pre><?php echo $message_fetch['message'] ?></pre>
                        </div>
                      </div>

                      <?php
                      $fileQuery=mysqli_query($db,"SELECT * FROM mailfiles WHERE mail_id = '$mail_id'");
                      while ($file_fetch=mysqli_fetch_assoc($fileQuery)) {
                        $allowed_extensions1 = array('jpg', 'png', 'jpeg', 'gif');
                        $file_extension1 = strtolower(pathinfo($file_fetch['file'], PATHINFO_EXTENSION));
                        if (in_array($file_extension1, $allowed_extensions1)) {
                          echo "
                          <img src='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>
                          ";
                        } else {
                          echo "
                          <a href='assets/messageFiles/". $file_fetch['file'] ."' class='w3-col'>". $file_fetch['file'] ."</a>
                          ";
                        }
                      }
                       ?>
                       <div class="message-time"><?php echo $senderName; ?></div>
                    </div>
                  <?php } } ?>
                </div>
                <script>
                        function liveSearch() {
                            const search = document.getElementById('searchInput').value;
                            const resultsDiv = document.getElementById('contactResults');

                            // Create a new XMLHttpRequest object
                            const xhr = new XMLHttpRequest();

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    // Update the results div with the response
                                    resultsDiv.innerHTML = xhr.responseText;
                                }
                            };

                            // Send a request to the server with the search query
                            xhr.open('GET', `?search=${search}`, true);
                            xhr.send();
                        }
                </script>
                <div class="w3-col s4 w3-padding" style="height: 28vh; max-height:28vh; display: block; black-space: nowrap; overflow-x: hidden; text-overflow: ellipsis">
                    <form class="" onsubmit="event.preventDefault();">
                        <input type="text" class="form-control" placeholder="Contacts" id="searchInput" onkeyup="liveSearch()" style="font-size:12px">
                    </form><br>
                    <div id="contactResults">
                        <!-- Search results will appear here -->
                    </div>
                    <?php
                    $userquery1 = mysqli_query($db, "SELECT users.*, COUNT(mails.user_id) AS match_count
                                                    FROM users
                                                    LEFT JOIN mails ON users.user_id = mails.user_id
                                                    WHERE mails.status = 'unread' and users.user_id = '$user_id'
                                                    GROUP BY users.user_id
                                                    ORDER BY match_count DESC");

                                                    $userquery1 = mysqli_query($db, "SELECT users.*, COUNT(mails.user_id) AS match_count
                                                                                    FROM users
                                                                                    LEFT JOIN mails ON users.user_id = mails.user_id
                                                                                    WHERE mails.status = 'unread' AND m_to = '$user_id'
                                                                                    GROUP BY users.user_id
                                                                                    ORDER BY match_count DESC");

                                                    if (!$userquery1 || mysqli_num_rows($userquery1) < 1) {
                                                        echo '<i>...No unread messages...</i>';
                                                    } else {
                                                        while ($fetchUser1 = mysqli_fetch_assoc($userquery1)) {
                                                            $nameTo1 = strtolower($fetchUser1['first_name'])." ".strtolower($fetchUser1['last_name']);
                                                            $id = $fetchUser1['user_id'];  // Use $fetchUser1 instead of $fetchUser
                                                            echo "<sup class='w3-text-red'>" . $fetchUser1['match_count'] . "</sup>
                                                            <a href='?inbox&to=" . $id . "&read' style='text-transform: capitalize; text-decoration: none;'>". $nameTo1 ."</a><br>";
                                                        }
                                                    }
                ?>

                </div>
              </div>
              <form class="" action="" method="post" enctype="multipart/form-data">
              <div class="w3-col">
                <?php
                if (isset($_GET['to'])) {
                  $personnelSearchResult=mysqli_query($db,"SELECT * FROM users WHERE user_id = '$idSearch'");
                  $fetchSearchResult=mysqli_fetch_assoc($personnelSearchResult);
                  if ($fetchSearchResult) {
                  echo "
                  <span class='w3-theme-d5' style='text-transform:capitalize'>To: ". strtolower($fetchSearchResult['last_name']) .", ". strtolower($fetchSearchResult['first_name']) ."</span>
                  "; } else {
                    echo "
                    <span class='w3-theme-d5' style='text-transform:capitalize'>To: Everyone</span>
                    ";
                  }
                } else {
                  echo "
                  <span class='w3-theme-d5' style='text-transform:capitalize'>To: Everyone</span>
                  ";
                }
                if (isset($_GET['read'])) {
                  mysqli_query($db,"UPDATE mails SET status = 'read' where m_to='$user_id' and user_id='$idSearch'");
                }
                 ?>
                <textarea name="message" rows="4" cols="80" class="w3-col mb-3" required></textarea>
                <input type="hidden" name="m_to" value="<?php echo $idSearch ?>">
              </div>
              <input type="file" id="file" name="files[]" class="w3-left" multiple>
              <button type="submit" name="send" class="btn w3-theme-d5 w3-right">Send</button>
              <button type="button" class="btn btn-danger w3-hover-red w3-right" data-bs-dismiss="modal">Close</button>
              </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($inbox)) { ?>
        $('#myModal').modal('show');
    <?php } ?>
});
</script>
