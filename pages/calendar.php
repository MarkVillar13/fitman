
<style>
    .custom-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.custom-modal .modal-header {
    border-bottom: none;
    padding-bottom: 0;
}

.custom-modal .modal-footer {
    border-top: none;
    padding-top: 0;
}

.custom-modal .lead {
    font-size: 1.15rem;
    font-weight: 500;
}

.custom-modal .text-muted {
    font-size: 0.95rem;
}

.custom-modal .fa-2x {
    margin: 1rem 0;
}

/* Button hover effects */
.custom-modal .btn {
    transition: all 0.2s ease;
}

.custom-modal .btn:hover {
    transform: translateY(-1px);
}

/* Icon animations */
.custom-modal .fa-dumbbell,
.custom-modal .fa-trash-alt {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    /* Ensure the calendar and reminders sections are properly aligned on mobile */
    .w3-display-container {
        display: flex;
        flex-wrap: wrap;
    }

    /* Adjust the layout on smaller screens */
    .calendar-container, .reminder-container {
        width: 100%;
        padding: 10px;
    }

    /* For medium to large screens, keep the original two-column layout */
    @media (min-width: 768px) {
        .calendar-container {
            width: 33.33%;
        }

        .reminder-container {
            width: 66.66%;
        }
    }

    /* Card body adjustments */
    .card-body {
        padding: 15px;
    }

    .form-label {
        font-size: 14px;
    }

    /* Adjust table styles for responsiveness */
    table {
        width: 100%;
        table-layout: auto;
    }

    .table th, .table td {
        padding: 8px;
        text-align: left;
    }

    /* Ensure buttons are properly sized and spaced */
    .btn {
        font-size: 14px;
        padding: 8px 15px;
    }

    /* Handle larger screens (desktops) */
    @media (min-width: 1024px) {
        .w3-display-container {
            height: 72vh;
        }
    }

    /* Modal Base Styles */
    .custom-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* Header Styles */
    .custom-modal .modal-header {
        padding: 1.5rem 1.5rem 0.5rem;
    }

    .custom-modal .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .custom-modal .btn-success {
    background-color: #22c55e !important;
    color: white !important;
}

.custom-modal .btn-success:hover {
    background-color: #16a34a !important;
    transform: translateY(-1px);
}

.custom-modal .btn-danger {
    background-color: #ef4444 !important;
    color: white !important;
}

.custom-modal .btn-danger:hover {
    background-color: #dc2626 !important;
    transform: translateY(-1px);
}
    /* Close Button */
    .custom-modal .custom-close {
        background: none;
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
        transition: opacity 0.2s ease;
    }

    .custom-modal .custom-close:hover {
        opacity: 0.75;
    }

    /* Body Styles */
    .custom-modal .modal-body {
        padding: 1.5rem;
        font-size: 1rem;
        color: #4a5568;
        line-height: 1.5;
    }

    /* Footer Styles */
    .custom-modal .modal-footer {
        padding: 0.5rem 1.5rem 1.5rem;
    }

    /* Button Styles */
    .custom-modal .custom-btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        background-color: #f1f5f9;
        border: none;
        color: #475569;
    }

    .custom-modal .custom-btn:hover {
        background-color: #e2e8f0;
        transform: translateY(-1px);
    }

    /* Animation and Backdrop */
    .custom-modal.modal {
        background-color: rgba(0, 0, 0, 0.3);
    }

    .custom-modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .custom-modal.show .modal-dialog {
        transform: scale(1);
    }

    /* Status Colors */
    .custom-modal .text-danger {
        color: #ef4444 !important;
    }

    .custom-modal .text-success {
        color: #22c55e !important;
    }

    .custom-modal .text-warning {
        color: #f59e0b !important;
    }
    .custom-modal .delete-text {
    color: #ef4444 !important;
}

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .custom-modal .modal-dialog {
            margin: 1rem;
        }
        
        .custom-modal .modal-content {
            border-radius: 12px;
        }
    }
</style>

<div class="w3-display-container" style="height:72vh;text-transform:capitalize;">
    <!-- Calendar Container -->
    <div class="calendar-container" style="margin-top: 8rem">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Reminder Container -->
    <div class="reminder-container">
        <?php

        // Auto-update missed workouts at page load
        $update_missed = "UPDATE reminders 
        SET status = 'Missed' 
        WHERE reminder_date < NOW()
        AND status = 'Not Started'
        AND user_id = ?";
    $missed_stmt = $db->prepare($update_missed);
    $missed_stmt->bind_param("i", $user_id);
    $missed_stmt->execute();
    $missed_stmt->close();

        $table_check_query = "SHOW TABLES LIKE 'reminders'";
        $table_exists = $db->query($table_check_query);

        if ($table_exists->num_rows == 0) {
            // Table does not exist, create it
            $create_table_query = "CREATE TABLE reminders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                reminder_text VARCHAR(255) NOT NULL,
                reminder_date DATETIME NOT NULL,
                status ENUM('Done', 'Not Started', 'Missed') DEFAULT 'Not Started',
                notification_status ENUM('unread', 'read') DEFAULT 'unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            if ($db->query($create_table_query) === TRUE) {
                echo "<div class='alert alert-success'>Table 'reminders' created successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error creating table: " . $db->error . "</div>";
            }
        }

        // Add Reminder
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_reminder'])) {
            $reminder_text = $_POST['reminder_text'];
            $reminder_date = $_POST['reminder_date'];

            $sql = "INSERT INTO reminders (user_id, reminder_text, reminder_date, status) VALUES (?, ?, ?, 'Not Started')";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iss", $user_id, $reminder_text, $reminder_date);
            if($stmt->execute()) {
                $stmt->close();
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }

        // Delete Reminder
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_reminder'])) {
            $id = $_POST['reminder_id'];
            $delete_sql = "DELETE FROM reminders WHERE id = ?";
            $delete_stmt = $db->prepare($delete_sql);
            $delete_stmt->bind_param("i", $id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        // Mark Reminder as Done
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['done_reminder'])) {
            $id = $_POST['reminder_id'];
            $update_sql = "UPDATE reminders SET status = 'Done' WHERE id = ?";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();
            $update_stmt->close();
        }

        $reminders = $db->query("
    SELECT * FROM reminders 
    WHERE user_id = '$user_id' 
    AND reminder_date > NOW()
    AND status = 'Not Started'
    ORDER BY reminder_date ASC
");
        ?>

        <div class="card mb-4" style="margin-top: 8rem">
            <div class="card-header bg-primary text-white">
                <h4>Add Workout Program</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="reminder_text" class="form-label">Reminder Text</label>
                        <input type="text" name="reminder_text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="reminder_date" class="form-label">Reminder Date</label>
                        <input type="datetime-local" name="reminder_date" class="form-control" required>
                    </div>
                    <button type="submit" name="add_reminder" class="btn btn-success">Add Workout</button>
                </form>
            </div>
        </div>

        <div class="card">
    <div class="card-header bg-secondary text-white">
        <h4>Upcoming reminders</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Reminder text</th>
                        <th class="text-center">Scheduled Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reminders->num_rows > 0): ?>
                        <?php while($row = $reminders->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($row['reminder_text']) ?></td>
                            <td class="text-center"><?= date('M d, Y h:i A', strtotime($row['reminder_date'])) ?></td>
                            <td class="text-center"><?= $row['status'] ?></td>
                            <td class="text-center"><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
                            <td class="text-center">
    <form method="POST" action="" class="d-inline">
        <input type="hidden" name="reminder_id" value="<?= $row['id'] ?>">
        <button type="button" class="btn btn-warning btn-sm done-btn me-2" 
                data-reminder-id="<?= $row['id'] ?>" 
                data-reminder-text="<?= htmlspecialchars($row['reminder_text']) ?>">Done</button>
        <button type="button" class="btn btn-danger btn-sm delete-btn" 
                data-reminder-id="<?= $row['id'] ?>" 
                data-reminder-text="<?= htmlspecialchars($row['reminder_text']) ?>">Delete</button>
    </form>
</td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No future workouts scheduled</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

        <!-- Message Modal -->
        <div class="modal fade custom-modal" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="messageModalLabel"></h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                    </div>
                    <div class="modal-footer border-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Fetch Subscription Data
    function fetchSalesData() {
        const userId = <?php echo isset($user_id) ? $user_id : 'null'; ?>;
        
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
        const userId = <?php echo isset($user_id) ? $user_id : 'null'; ?>;
        
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

document.addEventListener('DOMContentLoaded', function() {
    // Handle Done Button
    document.querySelectorAll('.done-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reminderId = this.dataset.reminderId;
            const reminderText = this.dataset.reminderText;

            // Create and show modal for confirmation
            const modal = new bootstrap.Modal(document.getElementById('messageModal'));
            document.getElementById('messageModalLabel').textContent = 'Mark as Done';
            document.getElementById('messageModal').querySelector('.modal-body').innerHTML = 
                `Are you sure you want to mark "${reminderText}" as done?`;
            document.getElementById('messageModal').querySelector('.modal-footer').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success confirm-done">Confirm</button>
            `;

            // Handle confirm button click
            document.querySelector('.confirm-done').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="reminder_id" value="${reminderId}">
                    <input type="hidden" name="done_reminder" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            };

            modal.show();
        });
    });

    // Handle Delete Button
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reminderId = this.dataset.reminderId;
            const reminderText = this.dataset.reminderText;

            // Create and show modal for confirmation
            const modal = new bootstrap.Modal(document.getElementById('messageModal'));
            document.getElementById('messageModalLabel').textContent = 'Delete Reminder';
            document.getElementById('messageModal').querySelector('.modal-body').innerHTML = 
                `Are you sure you want to delete "${reminderText}"?`;
            document.getElementById('messageModal').querySelector('.modal-footer').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger confirm-delete">Delete</button>
            `;

            // Handle confirm button click
            document.querySelector('.confirm-delete').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="reminder_id" value="${reminderId}">
                    <input type="hidden" name="delete_reminder" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            };

            modal.show();
        });
    });
});
</script>