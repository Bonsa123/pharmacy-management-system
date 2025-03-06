<?php
session_start();
include('db_connection.php');
include('permissions.php');

// Check if admin or cashier is logged in
if (!isset($_SESSION['admin']) && !isset($_SESSION['cashier'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
}

// Function to execute query and handle errors
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return $result;
}

// Fetch total sold cosmetics for today
$sql_cosmetic_sales = "
    SELECT co.name AS cosmetic_name, SUM(csi.quantity) AS total_quantity, SUM(csi.total) AS total_amount
    FROM cosmetic_sale_items csi
    JOIN cosmetic_sales cs ON csi.sale_id = cs.sale_id
    JOIN cosmetics co ON csi.cosmetic_id = co.cosmetic_id
    WHERE DATE(cs.sale_date) = CURDATE()
    GROUP BY co.name";

$result_cosmetic_sales = executeQuery($conn, $sql_cosmetic_sales);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <link rel="stylesheet" href="styles/report.css">
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="main-content">
        <h1>Daily Sales Report</h1>
        <p>Date: <?php echo date('Y-m-d'); ?></p>

        <h2>Cosmetics Sold Today</h2>
        <table>
            <thead>
                <tr>
                    <th>Cosmetic Name</th>
                    <th>Total Sold</th>
                    <th>Total Amount (Birr)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_cosmetic_sales->num_rows > 0): ?>
                    <?php while ($row = $result_cosmetic_sales->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cosmetic_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No cosmetics sold today.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="footer">
            <p>&copy; 2024 Pharmacy Management System</p>
        </footer>
    </div>
</body>
</html>
