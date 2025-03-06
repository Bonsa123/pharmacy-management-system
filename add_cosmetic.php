<?php 
session_start(); 
include('db_connection.php');  

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');
// Initialize variables
$name = $description = $quantity = $price = $cost_price = $expiry_date = $company_id = $category = '';
$name_err = $description_err = $quantity_err = $price_err = $cost_price_err = $expiry_date_err = $company_err = $category_err = '';

// Fetch company options
$company_query = "SELECT company_id, company_name FROM pharmacy_company";
$company_result = $conn->query($company_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Safely retrieve form values using htmlspecialchars and isset
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
    $quantity = isset($_POST['quantity']) ? htmlspecialchars(trim($_POST['quantity'])) : '';
    $price = isset($_POST['price']) ? htmlspecialchars(trim($_POST['price'])) : '';
    $cost_price = isset($_POST['cost_price']) ? htmlspecialchars(trim($_POST['cost_price'])) : '';
    $expiry_date = isset($_POST['expiry_date']) ? htmlspecialchars(trim($_POST['expiry_date'])) : '';
    $company_id = isset($_POST['company_id']) ? intval($_POST['company_id']) : 0;
    $category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : '';
    $custom_category = isset($_POST['custom_category']) ? htmlspecialchars(trim($_POST['custom_category'])) : '';

    // If the user selected "Other", use the custom category
    if ($category === 'Other' && !empty($custom_category)) {
        $category = $custom_category;
    }
    $date = date("Y-m-d"); // Current date for record

    // Validate the expiry date format
    $expiry_date_object = DateTime::createFromFormat('Y-m-d', $expiry_date);
    if (!$expiry_date_object || $expiry_date_object->format('Y-m-d') !== $expiry_date) {
        echo "<p class='error'>Invalid expiry date format.</p>";
        exit();
    }

    // Insert data into the database if no errors
    if (empty($name_err) && empty($description_err) && empty($quantity_err) && empty($price_err) 
        && empty($cost_price_err) && empty($expiry_date_err) && empty($company_err) && empty($category_err)) {

        $sql = "INSERT INTO cosmetics (name, description, quantity, price, cost_price, company_id, category, expiry_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ssidsiss", $name, $description, $quantity, $price, $cost_price, $company_id, $category, $expiry_date);

            // Execute statement
            if ($stmt->execute()) {
                header("Location: manage_cosmetics.php?msg=added");
                exit();
            } else {
                echo "Execution failed: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            die("Prepare failed: " . $conn->error);
        }
    } else {
        // Display errors
        foreach ([$name_err, $description_err, $quantity_err, $price_err, $cost_price_err, $expiry_date_err, $company_err, $category_err] as $error) {
            if (!empty($error)) {
                echo "<p class='error'>$error</p>";
            }
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cosmetic</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/addcsm.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all the main links that have dropdowns
        const dropdownLinks = document.querySelectorAll('nav ul li > a');

        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Prevent default action for dropdown links
                if (this.nextElementSibling && this.nextElementSibling.tagName === 'UL') {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block'; // Toggle dropdown
                }
            });
        });

        // Click anywhere else to close the dropdown
        document.addEventListener('click', function(e) {
            dropdownLinks.forEach(link => {
                const submenu = link.nextElementSibling;
                if (submenu && submenu.tagName === 'UL') {
                    if (!link.contains(e.target) && !submenu.contains(e.target)) {
                        submenu.style.display = 'none'; // Hide dropdown if clicked outside
                    }
                }
            });
        });
    });
</script>

</head>
<body>
<nav>
<?php include('nav.php'); ?>
</nav>

    <div class="container">
        <h1>Add New Cosmetic</h1>
        <form action="add_cosmetic.php" method="POST">
            <label for="name">Cosmetic Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
            <span class="error"><?php echo $name_err; ?></span>

            
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>">
            <span class="error"><?php echo $quantity_err; ?></span>

            <label for="cost_price">Buying Price:</label>
            <input type="number" name="cost_price" id="cost_price" step="0.01" value="<?php echo htmlspecialchars($cost_price); ?>">
            <span class="error"><?php echo $cost_price_err; ?></span>

            <label for="price">Selling Price:</label>
            <input type="number" name="price" id="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>">
            <span class="error"><?php echo $price_err; ?></span>

            <label for="expiry_date">Expiry Date:</label>
            <input type="date" name="expiry_date" value="<?php echo htmlspecialchars($expiry_date); ?>">

            <label for="company_id">Company:</label>
            <select name="company_id" id="company_id">
                <option value="">Select Company</option>
                <?php while ($company_row = $company_result->fetch_assoc()) { ?>
                    <option value="<?php echo $company_row['company_id']; ?>" <?php echo ($company_row['company_id'] == $company_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($company_row['company_name']); ?>
                    </option>
                <?php } ?>
            </select>
            <span class="error"><?php echo $company_err; ?></span>

            <label for="category">Category:</label>
<select name="category" id="category" onchange="toggleCategoryInput(this)">
    <option value="">Select Category</option>
    <option value="food" <?php if ($category == 'food') echo 'selected'; ?>>Food</option>
    <option value="Skin Care" <?php if ($category == 'Skin Care') echo 'selected'; ?>>Skin Care</option>
    <option value="Hair Care" <?php if ($category == 'Hair Care') echo 'selected'; ?>>Hair Care</option>
    <option value="Makeup" <?php if ($category == 'Makeup') echo 'selected'; ?>>Makeup</option>
    <option value="Fragrance" <?php if ($category == 'Fragrance') echo 'selected'; ?>>Fragrance</option>
    <option value="Other">Other (Please specify)</option>
</select>
<!-- Hidden input for custom category, shown only when "Other" is selected -->
<input type="text" name="custom_category" id="custom_category" style="display: none;" placeholder="Enter new category">
<span class="error"><?php echo $category_err; ?></span>

            <button type="submit" class="button">Add Cosmetic</button>
            <a href="manage_cosmetics.php">Back To Manage Cosmetics</a>
        </form>
    </div>
    <script>
function toggleCategoryInput(selectElement) {
    var customCategoryInput = document.getElementById('custom_category');
    if (selectElement.value === 'Other') {
        customCategoryInput.style.display = 'block';
    } else {
        customCategoryInput.style.display = 'none';
    }
}
</script>

</body>
</html>
