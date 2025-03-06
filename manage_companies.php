<?php
session_start();
include('db_connection.php');
include('permissions.php');
// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $company_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM pharmacy_company WHERE company_id = $company_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: manage_companies.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch all pharmacy companies
$sql = "SELECT * FROM pharmacy_company";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pharmacy Companies</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_companies.css">
   
</head>
<body>
<?php include('nav.php'); ?>

  <!--<div class="sidebar">
        <h2>Obed Drug Store</h2>
       <p>
       "In every bottle you prepare, there’s a chance to impact someone's life."
       </p>
    </div>-->

    <div class="main-content">
        <h1>Pharmacy Branch Management</h1>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'deleted') {
                echo "<p class='success'>Company deleted successfully.</p>";
            } elseif ($_GET['msg'] == 'added') {
                echo "<p class='success'>Company added successfully.</p>";
            } elseif ($_GET['msg'] == 'updated') {
                echo "<p class='success'>Company updated successfully.</p>";
            }
        }
        ?>

        <a href="add_company.php" class="button">Add New Branch</a>

        <table>
            <tr>
                <th>Branch ID</th>
                <th>Branch Name</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['company_id']}</td>
                            <td>{$row['company_name']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['contact_number']}</td>
                            <td>
                                <a href='edit_company.php?id={$row['company_id']}'>Edit</a> | 
                                <a href='manage_companies.php?delete={$row['company_id']}' onclick=\"return confirm('Are you sure you want to delete this company?');\">Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No companies found.</td></tr>";
            }
            ?>
        </table>
    </div>
     <!-- Footer -->
     <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</body>
</html>
