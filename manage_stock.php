<?php
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php"); // Redirect if not logged in
    exit();
}

if (!in_array('manage_stock', $_SESSION['permissions'])) {
    header("Location: admin_dashboard.php"); // Redirect if no permission
    exit();
}

include 'db_connection.php'; // Include your database connection
include('permissions.php');

// Initialize search variable
$search = '';
// Prepare SQL query with error handling
$sql = "SELECT m.*, la.quantity AS loss_adjustment, la.reason AS loss_reason
        FROM medicine m 
        LEFT JOIN loss_adjustments la ON m.medicine_id = la.medicine_id";

// Check if search is set and not empty
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search'])); // Sanitize search input
    // Add WHERE clause for search
    $sql .= " WHERE m.name LIKE ? OR m.batch_number LIKE ?";
}

$stmt = $conn->prepare($sql);

// Check if SQL statement was prepared successfully
if ($stmt === false) {
    die('Error preparing statement: ' . $conn->error); // Output error
}

// Bind parameters for search query if search exists
if (!empty($search)) {
    $searchParam = "%" . $search . "%"; // Prepare the search parameter
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $stmt->bind_param("ss", $searchParam, $searchParam); // Binding the parameters
    }
}

$stmt->execute();
$result = $stmt->get_result(); // Get the result set from the prepared statement

// Fetch medicine details for display
$medicine_details = $result->fetch_assoc(); // Fetch the first record for details

// Fetch loss adjustments
$query = "SELECT m.medicine_id, m.name, m.quantity
          FROM medicine m";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_stock.css">
   
</head>
<body>
<?php include('nav.php'); ?>


<header>
    <h1 >BIN CARD</h1>
</header>

<main>
    <section>
        

        <!-- Search Filter -->
        <div class="search-filter">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Medicine Name, Batch Number" value="<?php echo htmlspecialchars($search); ?>" />
                <input type="submit" value="Search">
            </form>
        </div>

        <!-- Add Loss/Adjustment Link -->
        <div class="add-loss-adjustment">
            <a href="loss.php">Add Loss/Adjustment</a>
        </div>

        <!-- Display Medicine Details if available -->
        <?php if ($medicine_details): ?>
            <div class="medicine-details">
                <div>
                    <label>Name of Health Facility:</label>
                    <span><?php echo htmlspecialchars($medicine_details['facility_name']); ?></span>
                </div>
                <br>
                <div>
                    <label> Dosage:</label>
                    <span>
                        <?php 
                        echo htmlspecialchars($medicine_details['dosage_form']); 
                        ?>
                    </span>
                </div>
                <br>
                
                <div>
                    <label>Status:</label>
                    <span class="<?php echo (strtotime($medicine_details['expiry_date']) < time()) ? 'expired' : 'active'; ?>">
                        <?php echo (strtotime($medicine_details['expiry_date']) < time()) ? 'Expired' : 'Active'; ?>
                    </span>
                </div>
            </div>
        <?php else: ?>
            <p>No medicine found matching your search.</p>
        <?php endif; ?>

        <!-- Display Stock Table only if a search is performed -->
        <?php if (!empty($search)): ?>
            <table>
   <!-- Update the table header -->
<thead>
    <tr>
        <th>Date Added</th>
        <th>Product Name</th>
        <th>Received</th> <!-- Initial Quantity -->
        <th>Issued</th>   <!-- Issued Quantity -->
        <th>Loss/Adjustments</th>
        <th>Reason for Loss</th>   <!-- New Column for Loss Reason -->
        <th>Balance</th>  <!-- Current Quantity (Balance) -->
        <th>Batch Number</th>
        <th>Expiry Date</th>
    </tr>
</thead>

<tbody>
    <?php
    // Fetch and display stock records
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $issued = $row['initial_quantity'] - $row['quantity']; // Calculate issued quantity
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['initial_quantity']) . "</td>"; // Display Initial Quantity
            echo "<td>" . htmlspecialchars($issued) . "</td>"; // Display Issued Quantity
            echo "<td>" . htmlspecialchars($row['loss_adjustment']) . "</td>"; 
            echo "<td>" . htmlspecialchars($row['loss_reason']) . "</td>"; // Display Loss Reason
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>"; // Display Balance (Current Quantity)
            echo "<td>" . htmlspecialchars($row['batch_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No stock data found.</td></tr>";
    }
    ?>
</tbody>

</table>

        <?php endif; ?>
    </section>
</main>
  <!-- Footer -->
  <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
