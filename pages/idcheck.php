<style>
    /* Modal styles */
    .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.3);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 1.5rem;
    border: none;
    border-radius: 16px;
    width: 80%;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* Remove the .close class since we're using a button instead */
.modal button {
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    background-color: #f1f5f9;
    border: none;
    color: #475569;
    cursor: pointer;
    margin-top: 1rem;
}

.modal button:hover {
    background-color: #e2e8f0;
    transform: translateY(-1px);
}

    .close:hover {
        color: black;
    }
</style>

<div class="w3-third w3-padding w3-center" style="margin-top: 8rem">
    <video id="preview" style="width:100%;"></video>
    <div class="w3-col w3-center w3-black w3-padding-16 mb-3">
        <span class="h5">Scan your QR Code here...</span>
    </div>
    <a href="users.php" class="w3-col w3-center w3-black w3-padding-16 h5" style="text-decoration:none">Records</a>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <h6 id="modal-message"></h6>
        <button onclick="closeModal()">Close</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the scanner
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    let modal = document.getElementById('myModal'); // Define modal here

    scanner.addListener('scan', function(content) {
        // Send the scanned content and time to the server to be saved in MySQL
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "save_scan.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    showModal('RECORDED! Your logs have been updated.');
                } else {
                    showModal('Error saving scan and time');
                }
            }
        };
        xhr.send("content=" + encodeURIComponent(content));
    });

    // Get available cameras and start scanner
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('No cameras found.');
        }
    }).catch(function(e) {
        console.error(e);
    });

    // Function to show the modal
    function showModal(message) {
        let modalMessage = document.getElementById('modal-message');
        modalMessage.textContent = message;
        modal.style.display = 'flex';
    }

    window.closeModal = function() {
        modal.style.display = 'none';
        window.location.reload(); // Reload the page after closing
    };

    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    };
});
</script>

<!-- Table to display the results -->
<div class="w3-twothird w3-center" id="offer" style="margin-top: 8rem">
<table class="w3-table-all">
<tr class="w3-black">
        <th style="text-align: center; width: 5%">ID</th>
        <th style="text-align: left; width: 10%">Name</th>
        <th style="text-align: center; width: 10%">Date and Time</th>
        <th style="text-align: center; width: 10%">In/Out</th>
        <th style="text-align: center; width: 10%">Action</th>
    </tr>
    <?php
    $dtrSelect = "SELECT * FROM attendance INNER JOIN users ON attendance.EmployeeID = users.user_id ORDER BY ScanTime DESC LIMIT 50";
    $querydtrSelect = mysqli_query($db, $dtrSelect);
    while ($fetchdtrSelect = mysqli_fetch_assoc($querydtrSelect)) {
    ?>
        <tr>
            <td style="text-align: center"><?php echo $fetchdtrSelect['user_id']; ?></td>
            <td style="text-align: left; text-transform: capitalize"><?php echo $fetchdtrSelect['last_name'] . ", " . $fetchdtrSelect['first_name']; ?></td>
            <td style="text-align: center">
                <?php 
                    $date = new DateTime($fetchdtrSelect['ScanTime']);
                    echo $date->format('F j, Y g:i A'); 
                ?>
            </td>
            <td style="text-align: center"><?php echo $fetchdtrSelect['ScanType']; ?></td>
            <td style="text-align: center">
                <a href="userSubscription.php?account=<?php echo $fetchdtrSelect['user_id']; ?>">View</a>
            </td>
        </tr>
    <?php } ?>
</table>
</div>
