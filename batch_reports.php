<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php"); // Redirect if not logged in
    exit();
}

// Check if the user has permission to view batch reports
if (!in_array('batch_reports', $_SESSION['permissions'])) {
    header("Location: admin_dashboard.php"); // Redirect if no permission
    exit();
}
include 'db_connection.php'; // Include your database connection
include('permissions.php');
// Fetch batch report data
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM medicine WHERE batch_number LIKE ? OR pin LIKE ? OR name LIKE ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Output the SQL error
    die('Error in SQL statement: ' . htmlspecialchars($conn->error));
}

$searchTerm = "%{$search}%";
$stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Reports</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/batch.css">
</head>
<body>
<?php include('nav.php'); ?>


<header>
    <h1>Pharmacy Management System</h1>
</header>

<main>
    <section>
        <h2>Batch Reports</h2>
        <div class="search-filter">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Batch Number, PIN, or Medicine Name" value="<?php echo htmlspecialchars($search); ?>" />
                <input type="submit" value="Search">
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Medicine Name</th>
                
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusClass = strtotime($row['expiry_date']) < time() ? 'status-expired' : 'status-active';
                        $statusText = strtotime($row['expiry_date']) < time() ? 'Expired' : 'Active';
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                       
                        echo "<td>" . htmlspecialchars($row['batch_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td class='$statusClass'>" . $statusText . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>
 <!-- Footer -->
 <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</body>
</html>
