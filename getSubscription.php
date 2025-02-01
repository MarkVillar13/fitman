<?php
session_start();
include 'database.php';

$salesData = [];

// Check if the user is logged in
if (isset($_SESSION['USER'])) {
    $account = $_SESSION['USER'];

    // Prepare and execute the query to get user information
    $query = "
        SELECT
            users.first_name,
            users.last_name,
            roles.role_name,
            users.user_id
        FROM
            users
        INNER JOIN
            roles ON users.role_id = roles.role_id
        WHERE
            user_id = '$account'
    ";

    $result = mysqli_query($db, $query);

    if ($result) {
        $userData = mysqli_fetch_assoc($result);
        if ($userData) {
            $first_name = $userData['first_name'];
            $last_name = $userData['last_name'];
            $role_name = $userData['role_name'];
            $user_id = $userData['user_id'];
        }
    }
}

// Prepare and execute the query to get sales data
$salesQuery = "
    SELECT
        service_id,
        item_id,
        user_id,
        item_type,
        SUM(quantity * subscription) AS total_quantity,
        MIN(sale_date) AS first_sale_date,
        SUM(total_price) AS total_price
    FROM
        sales
    LEFT JOIN
        services ON sales.item_id = services.service_id
    WHERE
        user_id = '$user_id' AND item_type = 'Service'
    GROUP BY
        item_id, user_id
";

$salesResult = $db->query($salesQuery);

// Fetch sales data
if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $salesData[] = $row;
    }
}

$db->close();

// Output sales data as JSON
header('Content-Type: application/json');
echo json_encode($salesData);
?>
