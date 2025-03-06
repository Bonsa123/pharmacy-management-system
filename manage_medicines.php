<?php
session_start();
include('db_connection.php');
include('permissions.php');
// Handle Delete Request
if (isset($_GET['delete'])) {
    $medicine_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM medicine WHERE medicine_id = $medicine_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: manage_medicines.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch categories for dropdown
$category_query = "SELECT DISTINCT category FROM medicine";
$category_result = $conn->query($category_query);

// Handle category filter
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$category_filter = $selected_category ? "WHERE m.category = '$selected_category'" : '';

$sql = "SELECT m.medicine_id, m.name AS medicine_name, m.quantity,m.cost_price, m.price, m.expiry_date, m.description, 
               p.company_name, m.category 
        FROM medicine m 
        JOIN pharmacy_company p ON m.company_id = p.company_id
        $category_filter";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_medicines.css">
    
</head>
<body>
<?php include('nav.php'); ?>



    <div class="main-content">
        <h1>Medicine Management</h1>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'deleted') {
                echo "<p class='success'>Medicine deleted successfully.</p>";
            } elseif ($_GET['msg'] == 'added') {
                echo "<p class='success'>Medicine added successfully.</p>";
            } elseif ($_GET['msg'] == 'updated') {
                echo "<p class='success'>Medicine updated successfully.</p>";
            }
        }
        ?>

        <a href="add_medicine.php" class="button">Add New Medicine</a>

        <form method="GET" action="manage_medicines.php">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php
                while ($cat_row = $category_result->fetch_assoc()) {
                    $selected = ($selected_category == $cat_row['category']) ? 'selected' : '';
                    echo "<option value='{$cat_row['category']}' $selected>{$cat_row['category']}</option>";
                }
                ?>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Medicine ID</th>
                    <th>Medicine Name</th>
                    
                    <th>Quantity</th>
                   
                    <th>S.Price</th>
                    <th>Expiry Date</th>
                    <th>Company Name</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['medicine_id']}</td>
                                <td>{$row['medicine_name']}</td>
                                
                                <td>{$row['quantity']}</td>
                               
                                <td>{$row['price']}</td>
                                <td>{$row['expiry_date']}</td>
                                <td>{$row['company_name']}</td>
                                <td>{$row['category']}</td>
                                <td>
                                    <a href='edit_medicine.php?id={$row['medicine_id']}'>Edit</a> | 
                                    <a href='manage_medicines.php?delete={$row['medicine_id']}' onclick=\"return confirm('Are you sure you want to delete this medicine?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No medicines found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</body>
</html>
