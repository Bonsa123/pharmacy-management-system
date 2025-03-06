<?php
session_start();
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $sql = "INSERT INTO pharmacy_company (company_name, address, contact_number) VALUES ('$company_name', '$address', '$contact_number')";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_companies.php?msg=added");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pharmacy Company</title>
    <link rel="stylesheet" href="styles/addc.css">
    <link rel="stylesheet" href="styles/navbar.css">
    
</head>
<body>
<?php include('nav.php'); ?>
    <div class="main-content">
        <h1>Add New Pharmacy Company</h1>
        <form method="post" action="">
            <label>Company Name:</label>
            <input type="text" name="company_name" required>

            <label>Address:</label>
            <textarea name="address" rows="4" required></textarea>

            <label>Contact Number:</label>
            <input type="text" name="contact_number" required>

            <button type="submit">Add Company</button>
        </form>
        <br>
        <a href="manage_companies.php">Back to Manage Companies</a>
    </div>
    
</body>
</html>
