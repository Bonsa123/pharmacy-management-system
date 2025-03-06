<?php
session_start();
include('db_connection.php');
include('permissions.php');

// Handle Delete Request
if (isset($_GET['delete'])) {
    $cosmetic_id = intval($_GET['delete']);
    $sql_delete = "DELETE FROM cosmetics WHERE cosmetic_id = $cosmetic_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: manage_cosmetics.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch categories for dropdown
$category_query = "SELECT DISTINCT category FROM cosmetics";
$category_result = $conn->query($category_query);

// Handle category filter
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$category_filter = $selected_category ? "WHERE c.category = '$selected_category'" : '';

$sql = "SELECT c.cosmetic_id, c.name AS cosmetic_name, c.quantity, c.price, c.expiry_date, c.description, 
               p.company_name, c.category 
        FROM cosmetics c 
        JOIN pharmacy_company p ON c.company_id = p.company_id
        $category_filter";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cosmetics</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/manage_cosmetics.css">
   
</head>
<body>
<?php include('nav.php'); ?>

    <div class="main-content">
        <h1>Cosmetics Management</h1>

        <?php
        if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
            echo "<p class='success'>Cosmetic deleted successfully.</p>";
        }
        ?>

        <a href="add_cosmetic.php" class="button">Add New Cosmetic</a>

        <form method="GET" action="manage_cosmetics.php">
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
                    <th>Cosmetic ID</th>
                    <th>Cosmetic Name</th>
                   
                    <th>Quantity</th>
                    <th>Price</th>
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
                                <td>{$row['cosmetic_id']}</td>
                                <td>{$row['cosmetic_name']}</td>
                              
                                <td>{$row['quantity']}</td>
                                <td>{$row['price']}</td>
                                <td>{$row['expiry_date']}</td>
                                <td>{$row['company_name']}</td>
                                <td>{$row['category']}</td>
                                <td>
                                    <a href='edit_cosmetics.php?id={$row['cosmetic_id']}'>Edit</a> | 
                                    <a href='manage_cosmetics.php?delete={$row['cosmetic_id']}' onclick=\"return confirm('Are you sure you want to delete this cosmetic?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No cosmetics found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        &copy; 2024 Your Pharmacy Management System <hr>
         <a style="color:white;" href="mailto:bonsatakale123@gmail.com">Meet Developer</a>
       

   

    </div>
</body>
</html>
