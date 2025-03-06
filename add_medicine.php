<?php
session_start();
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');

// Fetch list of pharmacy companies for the dropdown
$sql_companies = "SELECT * FROM pharmacy_company";
$result_companies = $conn->query($sql_companies);

// Handle form submission for adding medicine
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_medicine'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $company_id = intval($_POST['company_id']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $_POST['category'] === 'Other' ? $conn->real_escape_string($_POST['category_other']) : $conn->real_escape_string($_POST['category']);
    $dosage_form = $_POST['dosage_form'] === 'Other' ? $conn->real_escape_string($_POST['dosage_form_other']) : $conn->real_escape_string($_POST['dosage_form']);
    
    $batch_number = $conn->real_escape_string($_POST['batch_number']);
    $quantity = intval($_POST['quantity']);
    $cost_price = floatval($_POST['cost_price']); // Cost price of the medicine
    $selling_price = floatval($_POST['selling_price']); // Selling price
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
   
    $facility_name = $conn->real_escape_string($_POST['facility_name']);
    $date = date("Y-m-d"); // Current date for record
    $initial_quantity = $quantity; // Set initial_quantity to the same as quantity

    // Validate the expiry date format
    $expiry_date_object = DateTime::createFromFormat('Y-m-d', $expiry_date);
    if (!$expiry_date_object || $expiry_date_object->format('Y-m-d') !== $expiry_date) {
        echo "<p class='error'>Invalid expiry date format.</p>";
        exit();
    }

    // Insert the medicine into the database
    if ($name !== null && $quantity > 0) {
        $sql = "INSERT INTO medicine 
                (name, company_id, description, category, batch_number, initial_quantity, quantity, cost_price, price, expiry_date, dosage_form, facility_name, date) 
                VALUES 
                ('$name', '$company_id', '$description', '$category', '$batch_number', '$initial_quantity', '$quantity', '$cost_price', '$selling_price', '$expiry_date', '$dosage_form', '$facility_name', '$date')";

        if ($conn->query($sql) === TRUE) {
            header("Location: manage_medicines.php?msg=added");
            exit();
        } else {
            echo "<p class='error'>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine</title>
    <link rel="stylesheet" href="styles/addmedc.css">
    <link rel="stylesheet" href="styles/navbar.css">
</head>
<body>
<nav>
<?php include('nav.php'); ?>
</nav>


<div class="main-content">
    <h1>Add New Medicine</h1>
    <form method="post" action="">
        <label>Medicine Name:</label>
        <input type="text" name="name" required>

        <label>Pharmacy Company:</label>
        <select name="company_id" required>
            <option value="">Select a Company</option>
            <?php
            if ($result_companies->num_rows > 0) {
                while ($row = $result_companies->fetch_assoc()) {
                    echo "<option value='" . $row['company_id'] . "'>" . $row['company_name'] . "</option>";
                }
            }
            ?>
        </select>

        <label>Category:</label>
      

        <select name="category" id="category" required>
    <option value="">Select a Category</option>
    <option value="Antibiotics">Antibiotics</option>
    <option value="Pain & Pyretic">Pain & Pyretic</option>
    <option value="Anti Helmentic">Anti Helmentic</option>
    <option value="GIT Drug">GIT Drug</option>
    <option value="Ophthalmic">Ophthalmic</option>
    <option value="Anti-Inflammatory">Anti-Inflammatory</option>
    <option value="Vitamins & Minerals">Vitamins & Minerals</option>
    <option value="Cardio Vascular">Cardio Vascular</option>
    <option value="Contraceptive">Contraceptive</option>
    <option value="Anti-protozoa">Anti-protozoa</option>
    <option value="CNS">CNS</option>
    <option value="Anti-Fungal">Anti-Fungal</option>
    <option value="Other">Other (Please specify)</option>
</select>
<input type="text" name="category_other" id="category_other" style="display: none;" placeholder="Enter new category">

        <label>Batch Number:</label>
        <input type="text" name="batch_number" required>

        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" required>

        <label>Buying Price:</label>
        <input type="number" name="cost_price" step="0.01" required>

        <label>Selling Price:</label>
        <input type="number" name="selling_price" step="0.01" required>

        <label>Expiry Date:</label>
        <input type="date" name="expiry_date" required>

        <label>Dosage Form:</label>
<select name="dosage_form" id="dosage_form" required>
    <option value="">Select a Dosage Form</option>
    <option value="Bottle">Bottle</option>
    <option value="Tube">Tube</option>
    <option value="Strip">Strip</option>
    <option value="Ampoule">Ampoule</option>
    <option value="Vial">Vial</option>
    <option value="Sachet">Sachet</option>
    <option value="Blister Pack">Blister Pack</option>
    <option value="Syringe">Syringe</option>
    <option value="Other">Other (Please specify)</option>
</select>
<input type="text" name="dosage_form_other" id="dosage_form_other" style="display: none;" placeholder="Enter new dosage form">

        <label>Facility Name:</label>
        <input type="text" name="facility_name" required>

        <button type="submit" name="submit_medicine">Add Medicine</button>
    </form>
</div>
<a href="manage_medicines.php">Back to Manage Medicines</a>

<script>
    // Show/Hide other category input
    document.querySelector('select[name="category"]').addEventListener('change', function() {
        document.querySelector('input[name="category_other"]').style.display = this.value === 'Other' ? 'block' : 'none';
    });

    // Show/Hide other dosage form input
    document.querySelector('select[name="dosage_form"]').addEventListener('change', function() {
        document.querySelector('input[name="dosage_form_other"]').style.display = this.value === 'Other' ? 'block' : 'none';
    });
</script>
</body>
</html>
