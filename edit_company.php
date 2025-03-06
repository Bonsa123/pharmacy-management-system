<?php
session_start();
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');
// Get company ID from URL
if (isset($_GET['id'])) {
    $company_id = intval($_GET['id']);
    
    // Fetch existing company data
    $sql = "SELECT * FROM pharmacy_company WHERE company_id = $company_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $company = $result->fetch_assoc();
    } else {
        echo "Company not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);

    $sql_update = "UPDATE pharmacy_company SET company_name='$company_name', address='$address', contact_number='$contact_number' WHERE company_id = $company_id";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: manage_companies.php?msg=updated");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pharmacy Company</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/edit_company.css">
  
</head>
<body>
<?php include('nav.php'); ?>
    <div class="main-content">
        <h1>Edit Pharmacy Company</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="4" required><?php echo htmlspecialchars($company['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($company['contact_number']); ?>" required>
            </div>

            <button type="submit" class="button">Update Company</button>
        </form>
        
        <a href="manage_companies.php" class="back-link">Back to Manage Companies</a>
    </div>
    
</body>
</html>
