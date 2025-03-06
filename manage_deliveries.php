<?php
session_start();
include('db_connection.php');
include('permissions.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch delivery orders from the database
$sql = "SELECT * FROM delivery_orders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Deliveries</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_deliveries.css">
</head>
<body>
<?php include('nav.php'); ?>

<div class="main-content">
    <h1>Manage Deliveries</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product Type</th>
                <th>Quantity</th>
                <th>Delivery Address</th>
                <th>Prescription Image</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Location Link</th>
                <th>Payment Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['product_type']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['delivery_address']; ?></td>
                    <td><img src="uploads/<?php echo $row['prescription_image']; ?>" alt="Prescription Image" width="100"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><a href="<?php echo $row['location_link']; ?>" target="_blank">View Location</a></td>
                    <td><?php echo $row['payment_status']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
