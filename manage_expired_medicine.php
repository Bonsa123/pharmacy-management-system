<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php"); // Redirect if not logged in
    exit();
}

// Check if the user has permission to manage expired medicine
if (!in_array('manage_expired_medicine', $_SESSION['permissions'])) {
    header("Location: admin_dashboard.php"); // Redirect if no permission
    exit();
}

include 'db_connection.php'; // Include your database connection
include('permissions.php');
// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['medicine_id'])) {
    $medicine_id = $_POST['medicine_id'];

    // Prepare and execute the deletion
    $sql = "DELETE FROM medicine WHERE medicine_id = ?"; // Ensure 'id' is the correct primary key column name
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . htmlspecialchars($conn->error)); // Display error if preparation fails
    }

    $stmt->bind_param("i", $medicine_id); // Bind the medicine ID
    $stmt->execute();

    // Check for success
    if ($stmt->affected_rows > 0) {
        // Optionally handle success (e.g., set a success message)
    } else {
        // Optionally handle failure (e.g., set an error message)
    }

    $stmt->close(); // Close the statement
    // Redirect back to the expired medicine page
    header("Location: manage_expired_medicine.php");
    exit();
}

// Fetch expired medicine data
$sql = "SELECT * FROM medicine WHERE expiry_date < CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Medicine Management</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_expired.css">
    

    
</head>
<body>
<?php include('nav.php'); ?>

<header>
    <h1>Pharmacy Management System</h1>
</header>

<main>
    <section>
        <h2>Expired Medicines</h2>
        <table>
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Actions</th> <!-- New Actions column -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['batch_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td >
                                <form method='POST' action=''>
                                    <input type='hidden' name='medicine_id' value='" . htmlspecialchars($row['medicine_id']) . "'> <!-- Ensure 'id' is used here -->
                                    <button type='submit' onclick='return confirm(\"Are you sure you want to delete this medicine?\");'>Delete</button>
                                </form>
                              </td>"; // Delete button
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No expired medicines found</td></tr>"; // Adjust colspan
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
