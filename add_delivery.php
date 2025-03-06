<?php
session_start();
include('db_connection.php');
include('permissions.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_type = $_POST['product_type'];
    $quantity = $_POST['quantity'];
    $delivery_address = $_POST['delivery_address'];
    $prescription_image = $_FILES['prescription_image']['name'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $location_link = $_POST['location_link'];
    $payment_status = 'half_paid'; // Default to half payment

    // Handle file upload
    if (move_uploaded_file($_FILES['prescription_image']['tmp_name'], "uploads/" . $prescription_image)) {
        // Insert new delivery order into the database
        $sql = "INSERT INTO delivery_orders (product_type, quantity, delivery_address, prescription_image, name, phone_number, location_link, payment_status) VALUES ('$product_type', $quantity, '$delivery_address', '$prescription_image', '$name', '$phone_number', '$location_link', '$payment_status')";
        if ($conn->query($sql) === TRUE) {
            echo "New delivery order created successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading the prescription image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Delivery Order</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/add_delivery.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include('nav.php'); ?>

<div class="main-content">
    <h1>Add New Delivery Order</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="product_type">Product Type:</label>
        <input type="text" id="product_type" name="product_type" required>
        
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>
        
        <label for="delivery_address">Delivery Address:</label>
        <input type="text" id="delivery_address" name="delivery_address" required>
        
        <label for="prescription_image">Upload Prescription Image:</label>
        <input type="file" id="prescription_image" name="prescription_image" accept="image/*" required>
        
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>
        
        <label for="location_link">Google Maps Location Link:</label>
        <input type="text" id="location_link" name="location_link" placeholder="https://maps.app.goo.gl/..." required>
        
        <button type="submit">Add Delivery Order</button>
    </form>
    <p>To share your location, navigate to your location in Google Maps, copy the link, and paste it above.</p>
    <button onclick="window.open('https://www.google.com/maps', '_blank')">Open Google Maps</button>
</div>

<script>
$(document).ready(function() {
    $('#product_type, #quantity').on('change', function() {
        var productType = $('#product_type').val();
        var quantity = $('#quantity').val();
        
        // Example AJAX call to fetch product details
        $.ajax({
            url: 'manage_medicines.php', // Adjust this URL as needed
            method: 'GET',
            data: { product_type: productType },
            success: function(data) {
                // Process the response and calculate total price
                // Display total price to the user
            }
        });
    });
});
</script>

</body>
</html>
