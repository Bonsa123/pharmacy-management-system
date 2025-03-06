<?php
session_start();
include('db_connection.php');
include('permissions.php');

// Check if the cosmetic ID is provided
if (isset($_GET['id'])) {
    $cosmetic_id = intval($_GET['id']);
    $sql = "SELECT * FROM cosmetics WHERE cosmetic_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cosmetic_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cosmetic = $result->fetch_assoc();
    } else {
        header("Location: manage_cosmetics.php?msg=not_found");
        exit();
    }
} else {
    header("Location: manage_cosmetics.php");
    exit();
}

// Fetch company options for the dropdown
$company_query = "SELECT company_id, company_name FROM pharmacy_company";
$company_result = $conn->query($company_query);

// Handle the form submission
if (isset($_POST['update_cosmetic'])) {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $quantity = htmlspecialchars($_POST['quantity']);
    $price = htmlspecialchars($_POST['price']);
    $cost_price = htmlspecialchars($_POST['cost_price']);
    $expiry_date = htmlspecialchars($_POST['expiry_date']);
    $company_id = intval($_POST['company_id']);
    $category = htmlspecialchars($_POST['category']);
    $custom_category = htmlspecialchars($_POST['custom_category']);

    // Use custom category if "Other" is selected
    if ($category === 'Other' && !empty($custom_category)) {
        $category = $custom_category;
    }

    // Update the record
    $sql_update = "UPDATE cosmetics 
                   SET name = ?, description = ?, quantity = ?, price = ?, cost_price = ?, expiry_date = ?, company_id = ?, category = ? 
                   WHERE cosmetic_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssiddsisi", $name, $description, $quantity, $price, $cost_price, $expiry_date, $company_id, $category, $cosmetic_id);

    if ($stmt_update->execute()) {
        header("Location: manage_cosmetics.php?msg=updated");
        exit();
    } else {
        echo "Error updating record: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cosmetic</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/edit_cosmetics.css">
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
</head>
<body>
<?php include('nav.php'); ?>

<div class="form-container">
    <h2>Edit Cosmetic</h2>
    <form action="" method="POST">
        <label for="name">Cosmetic Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($cosmetic['name']); ?>" required>

       
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($cosmetic['quantity']); ?>" required>

        <label for="price">Selling Price</label>
        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($cosmetic['price']); ?>" required>

        <label for="cost_price">Buying Price</label>
        <input type="number" id="cost_price" name="cost_price" step="0.01" value="<?php echo htmlspecialchars($cosmetic['cost_price']); ?>" required>

        <label for="expiry_date">Expiry Date</label>
        <input type="date" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($cosmetic['expiry_date']); ?>">

        <label for="company_id">Company</label>
        <select name="company_id" id="company_id">
            <option value="">Select Company</option>
            <?php while ($company_row = $company_result->fetch_assoc()) { ?>
                <option value="<?php echo $company_row['company_id']; ?>" <?php echo ($company_row['company_id'] == $cosmetic['company_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($company_row['company_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="category">Category</label>
        <select name="category" id="category" onchange="toggleCategoryInput(this)">
            <option value="">Select Category</option>
            <option value="food" <?php if ($cosmetic['category'] == 'food') echo 'selected'; ?>>Food</option>
            <option value="Skin Care" <?php if ($cosmetic['category'] == 'Skin Care') echo 'selected'; ?>>Skin Care</option>
            <option value="Hair Care" <?php if ($cosmetic['category'] == 'Hair Care') echo 'selected'; ?>>Hair Care</option>
            <option value="Makeup" <?php if ($cosmetic['category'] == 'Makeup') echo 'selected'; ?>>Makeup</option>
            <option value="Fragrance" <?php if ($cosmetic['category'] == 'Fragrance') echo 'selected'; ?>>Fragrance</option>
            <option value="Other">Other (Please specify)</option>
        </select>
        <input type="text" name="custom_category" id="custom_category" style="display: none;" placeholder="Enter new category">

        <button type="submit" class="button" name="update_cosmetic">Update Cosmetic</button>
    </form>
    <a href="manage_cosmetics.php" style="color:green;">Back To Manage Cosmetics</a>
</div>

</body>
</html>
