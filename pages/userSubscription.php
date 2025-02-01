<?php
if (isset($_GET['account'])) {
    $account = $_GET['account'];
    $checkUsername1 = "SELECT * FROM users INNER JOIN roles ON users.role_id = roles.role_id WHERE user_id='$account'";
    $checkResult1 = mysqli_query($db, $checkUsername1);
    $result1 = mysqli_fetch_assoc($checkResult1);
    $first_name1 = $result1['first_name'];
    $last_name1 = $result1['last_name'];
    $role_name1 = $result1['role_name'];
    $user_id1 = $result1['user_id'];
    $_SESSION['USER'] = $account;
}

// Count distinct subscriptions for pagination
$countAccounts = mysqli_query($db, "SELECT COUNT(DISTINCT item_id) AS distinct_count FROM sales WHERE item_type = 'Service' AND user_id = '$account'") or die(mysqli_error($db));
$accountCount = mysqli_fetch_array($countAccounts);
$countP = $accountCount['distinct_count'];
$totalPages = ceil($countP / 10);

// Handle pagination
if (isset($_GET['page'])) {
    $currentPageGet = $_GET['page'];
    if ($currentPageGet <= 1) {
        $currentPage = 1;
        $offsetData = 0;
    } elseif ($currentPageGet >= $totalPages) {
        $currentPage = $totalPages;
        $offsetData = 10 * ($currentPage - 1);
    } else {
        $currentPage = $_GET['page'];
        $offsetData = 10 * ($currentPage - 1);
    }
} else {
    $currentPage = 1;
    $offsetData = 0;
}
?>
<div class="w3-twothird w3-container">
  <div class="w3-twothird">
    <table class="w3-table w3-hoverable mb-3">
        <tr>
            <th colspan="2" class="w3-hide-small">
                <span style="text-transform:capitalize">Client: <?php echo strtolower($first_name1) . " " . strtolower($last_name1); ?></span><br>
                <span>Email: <?php echo $result1['email']; ?></span>
            </th>
            <?php
            $transactionQuery1 = mysqli_query($db, "SELECT SUM(quantity * total_price) AS totalCost1 FROM sales WHERE item_type = 'Service' and user_id = '$account'");
            $fetchCost1 = mysqli_fetch_assoc($transactionQuery1);
            $fetchTotalCost1 = $fetchCost1['totalCost1'];
            ?>
            <th class="w3-right">Total Sales: Php <?php echo number_format($fetchTotalCost1, 2, ".", ",") ?></th>
        </tr>
        <tr>
            <th style="text-align:center">Transaction No</th>
            <th style="text-align:center">Total</th>
            <th style="text-align:center">Action</th>
        </tr>
        <?php
        // Fetch user subscriptions
        $userquery = mysqli_query($db, "SELECT * FROM sales WHERE item_type = 'Service' and user_id = '$account' ORDER BY sale_id DESC LIMIT 10 OFFSET $offsetData");
        $previousEndDate = null; // Variable to hold the previous subscription end date

        while ($fetchUser = mysqli_fetch_assoc($userquery)) {
            $transaction = $fetchUser['sale_date'];
            $transactionQuery = mysqli_query($db, "SELECT SUM(quantity * total_price) AS totalCost, user_id FROM sales WHERE sale_date = '$transaction' GROUP BY user_id");
            $fetchCost = mysqli_fetch_assoc($transactionQuery);
            $fetchTotalCost = $fetchCost['totalCost'];
            ?>
            <tr>
                <td class="w3-center"><?php echo date('YmdHis', strtotime($transaction)) . $fetchCost['user_id'] ?></td>
                <td class="w3-center">Php <?php echo number_format($fetchTotalCost, 2, ".", ","); ?></td>
                <?php
                $date = $fetchUser['sale_date'];
                $quantity = $fetchUser['quantity'];
                $date1 = new DateTime($date);

                // If there's a previous subscription, start from the end date of that subscription
                if ($previousEndDate) {
                    $date1 = new DateTime($previousEndDate);
                }

                // Calculate the new end date based on the quantity
                $date1->modify("+{$quantity} months");
                $targetDate = $date1->format('Y-m-d H:i:s');

                // Update the previous end date for the next iteration
                $previousEndDate = $targetDate;

                $dateNow = date('Y-m-d H:i:s');
                $timestampNow = strtotime($dateNow);
                $timestampTarget = strtotime($targetDate);
                $remainingSeconds = $timestampTarget - $timestampNow;

                if ($remainingSeconds > 0) {
                    $remainingDays = floor($remainingSeconds / (60 * 60 * 24));
                    $remainingHours = floor(($remainingSeconds % (60 * 60 * 24)) / (60 * 60));
                    $remainingMinutes = floor(($remainingSeconds % (60 * 60)) / 60);
                    $remainingSeconds = $remainingSeconds % 60;
                    $formattedDateTimeremaining = sprintf('%d days, %d hours, %d minutes, %d seconds', $remainingDays, $remainingHours, $remainingMinutes, $remainingSeconds);

                    if ($remainingDays < 3) {
                        $textColor = 'red';
                    } elseif ($remainingDays <= 7) {
                        $textColor = 'orange';
                    } else {
                        $textColor = 'black';
                    }
                } else {
                    $formattedDateTimeremaining = 'This subscription has already expired.';
                    $textColor = 'black';
                }
                ?>

                <td class="w3-center">
                    <a href="subscriptionReceipt.php?user=<?php echo urlencode($fetchCost['user_id']); ?>&date=<?php echo urlencode($date); ?>&amount=<?php echo urlencode($fetchTotalCost); ?>" target="_blank">View Receipt</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>


    <div class="w3-col">
        <ul class="pagination w3-left w3-col s6">
            <li class="page-item"><a class="page-link w3-center" href="?page=1"><b><<</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage - 1; ?>"><b><</b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $currentPage + 1; ?>"><b>></b></a></li>
            <li class="page-item"><a class="page-link w3-center" href="?page=<?php echo $totalPages; ?>"><b>>></b></a></li>
        </ul>
        <i class="w3-right"><?php echo $currentPage . " of " . $totalPages; ?></i>
    </div>
  </div>
  <div class="w3-col w3-padding w3-right w3-third">
        <span class="h4">Attendance</span>
    <table id="attendanceTable" class="w3-table-all">
        <tbody>
          <!-- Attendance records will be loaded here -->
        </tbody>
    </table>
  </div>
</div>
<style>
    .fc-day {
        cursor: pointer;
    }
    .fc-event {
        background-color: red;
        border: none;
    }
    .fc-event:hover {
        background-color: #0056b3;
    }
</style>
<div class="w3-display-container w3-third" style="height:72vh;text-transform:capitalize">
  <div class="w3-display-middle w3-col">
    <div class="card">
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    // Fetch Subscription Data
    function fetchSalesData() {
        const userId = <?php echo isset($user_id1) ? $user_id1 : 'null'; ?>;

        
        if (!userId) {
            console.error('User ID is not set');
            return Promise.reject('User ID is not available');
        }

        return $.ajax({
            url: 'getSubscriptions.php', // Your existing subscription data endpoint
            method: 'GET',
            data: { user_id: userId },
            dataType: 'json'
        }).then(function(response) {
            console.log('Subscription data:', response);
            return response;
        });
    }

    // Fetch Attendance Data
    function fetchAttendanceData() {
        const userId = <?php echo isset($account) ? $account : 'null'; ?>;
        
        if (!userId) {
            console.error('User ID is not set');
            return Promise.reject('User ID is not available');
        }

        return $.ajax({
            url: 'getAttendance.php', // PHP file that returns attendance data
            method: 'GET',
            data: { user_id: userId },
            dataType: 'json'
        }).then(function(response) {
            console.log('Attendance data:', response);
            return response;
        });
    }

    // Initialize Calendar
    function initializeCalendar(subscriptionData, attendanceData) {
        if (!Array.isArray(subscriptionData) || !Array.isArray(attendanceData)) {
            console.error('Invalid data format for subscription or attendance:', subscriptionData, attendanceData);
            return;
        }

        console.log('Initializing calendar with data:', subscriptionData, attendanceData);

        $('#calendar').fullCalendar({
            events: [
                // Add subscription start and end events
                ...subscriptionData.map(subscription => {
                    return [
                        {
                            title: 'Start',
                            start: subscription.start_date,
                            color: 'blue',
                            allDay: true,
                            description: `Subscription #${subscription.subscription_id}\nDuration: ${subscription.total_duration} days\nStatus: ${subscription.status}`
                        },
                        {
                            title: 'End',
                            start: subscription.end_date,
                            color: 'maroon',
                            allDay: true,
                            description: `Subscription #${subscription.subscription_id}\nStatus: ${subscription.status}`
                        }
                    ];
                }).flat(),

                // Add attendance data to the events
                ...attendanceData.map(attendance => {
                    return {
                        title: attendance.status === 'present' ? 'Present' : 'Absent',
                        start: attendance.date,
                        color: attendance.status === 'present' ? 'green' : 'red',
                        allDay: true,
                        description: `Status: ${attendance.status}`
                    };
                })
            ],

            // Handle day render to change the background color for attendance
            dayRender: function(date, cell) {
    const today = moment();
    const formattedDate = date.format('YYYY-MM-DD');

    // Check if there is attendance for this day
    const attendance = attendanceData.find(a => a.date === formattedDate);

    if (attendance) {
        // If the status is "absent", mark the day red
        if (attendance.status === 'absent') {
            cell.css('background-color', 'red');
        }
        // If the status is "present", mark the day green (optional)
        else if (attendance.status === 'present') {
            cell.css('background-color', 'green');
        }
    } else {
        // If no attendance record is found, mark it as "absent" by default
        cell.css('background-color', '#ff8080');
    }

    // Highlight today's date with a custom background (optional)
    if (date.isSame(today, 'day')) {
        cell.css('background-color', '#fff3e0');
    }
},

            editable: false,
            eventLimit: true,
            
            // Handle clicks on the day
            dayClick: function(date) {
                const clickedDate = date.format('YYYY-MM-DD');
                const activeSubscriptions = subscriptionData.filter(sub => {
                    const start = moment(sub.start_date);
                    const end = moment(sub.end_date);
                    return date.isBetween(start, end, 'day', '[]');
                });

                if (activeSubscriptions.length > 0) {
                    let message = 'Active Subscriptions on this date:\n\n';
                    activeSubscriptions.forEach(sub => {
                        message += `Subscription #${sub.subscription_id}\n`;
                        message += `Status: ${sub.status}\n`;
                        message += `Duration: ${sub.total_duration} days\n`;
                        if (sub.additional_duration > 0) {
                            message += `Additional Time: ${sub.additional_duration} days\n`;
                        }
                        message += `\n`;
                    });
                    alert(message);
                } else {
                    alert('No active subscriptions on ' + clickedDate);
                }
            }
        });
    }

    // Fetch both subscription and attendance data and initialize the calendar
    Promise.all([fetchSalesData(), fetchAttendanceData()])
        .then(function([subscriptionData, attendanceData]) {
            console.log("Fetched subscription and attendance data:", subscriptionData, attendanceData);
            initializeCalendar(subscriptionData, attendanceData);
        })
        .catch(function(error) {
            console.error("Error fetching data:", error);
            alert("There was an issue fetching your data. Please check the console for details.");
        });
});
</script>