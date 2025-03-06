<?php
session_start();
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');

// Fetch list of pharmacy companies
$sql_companies = "SELECT * FROM pharmacy_company";
$result_companies = $conn->query($sql_companies);

// Handle form submission for updating medicine details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_id = intval($_POST['medicine_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $company_id = intval($_POST['company_id']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $batch_number = $conn->real_escape_string($_POST['batch_number']);
    $quantity = intval($_POST['quantity']);
    $cost_price = floatval($_POST['cost_price']);
    $selling_price = floatval($_POST['selling_price']);
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
    $category = $_POST['category'] === 'Other' ? $conn->real_escape_string($_POST['category_other']) : $conn->real_escape_string($_POST['category']);
    $dosage_form = $_POST['dosage_form'] === 'Other' ? $conn->real_escape_string($_POST['dosage_form_other']) : $conn->real_escape_string($_POST['dosage_form']);
    
    $facility_name = $conn->real_escape_string($_POST['facility_name']);

    // Update the medicine record with the new values
    $sql_update = "UPDATE medicine 
                   SET name='$name', company_id='$company_id', description='$description', category='$category', 
                       batch_number='$batch_number', quantity='$quantity', cost_price='$cost_price', price='$selling_price', 
                       expiry_date='$expiry_date', dosage_form='$dosage_form', facility_name='$facility_name' 
                   WHERE medicine_id='$medicine_id'";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: manage_medicines.php?msg=updated");
        exit();
    } else {
        echo "<p class='error'>Error updating record: " . $conn->error . "</p>";
    }
}

// Fetch medicine details if id is provided
$medicine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($medicine_id > 0) {
    $sql_medicine = "SELECT * FROM medicine WHERE medicine_id = '$medicine_id'";
    $result_medicine = $conn->query($sql_medicine);
    $medicine = $result_medicine->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/edit_medicines.css">
</head>
<body>
<?php include('nav.php'); ?>

<div class="main-content">
    <h1>Edit Medicine</h1>
    <form method="post" action="">
        <input type="hidden" name="medicine_id" value="<?php echo $medicine['medicine_id']; ?>">

        <label>Medicine Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($medicine['name']); ?>" required>

        <label>Pharmacy Company:</label>
        <select name="company_id" required>
            <?php
            while ($company = $result_companies->fetch_assoc()) {
                $selected = $medicine['company_id'] == $company['company_id'] ? 'selected' : '';
                echo "<option value='{$company['company_id']}' $selected>{$company['company_name']}</option>";
            }
            ?>
        </select>

        <label>Medicine Category:</label>
        <select name="category" id="category" required>
            <option value="">Select a Category</option>
            <option value="Antibiotics" <?php echo $medicine['category'] == 'Antibiotics' ? 'selected' : ''; ?>>Antibiotics</option>
            <option value="Pain & Pyretic" <?php echo $medicine['category'] == 'Pain & Pyretic' ? 'selected' : ''; ?>>Pain & Pyretic</option>
            <option value="Anti Helmentic" <?php echo $medicine['category'] == 'Anti Helmentic' ? 'selected' : ''; ?>>Anti Helmentic</option>
            <option value="GIT Drug" <?php echo $medicine['category'] == 'GIT Drug' ? 'selected' : ''; ?>>GIT Drug</option>
            <option value="Ophthalmic" <?php echo $medicine['category'] == 'Ophthalmic' ? 'selected' : ''; ?>>Ophthalmic</option>
            <option value="Anti-Inflammatory" <?php echo $medicine['category'] == 'Anti-Inflammatory' ? 'selected' : ''; ?>>Anti-Inflammatory</option>
            <option value="Vitamins & Minerals" <?php echo $medicine['category'] == 'Vitamins & Minerals' ? 'selected' : ''; ?>>Vitamins & Minerals</option>
            <option value="Cardio Vascular" <?php echo $medicine['category'] == 'Cardio Vascular' ? 'selected' : ''; ?>>Cardio Vascular</option>
            <option value="Contraceptive" <?php echo $medicine['category'] == 'Contraceptive' ? 'selected' : ''; ?>>Contraceptive</option>
            <option value="Anti-protozoa" <?php echo $medicine['category'] == 'Anti-protozoa' ? 'selected' : ''; ?>>Anti-protozoa</option>
            <option value="CNS" <?php echo $medicine['category'] == 'CNS' ? 'selected' : ''; ?>>CNS</option>
            <option value="Anti-Fungal" <?php echo $medicine['category'] == 'Anti-Fungal' ? 'selected' : ''; ?>>Anti-Fungal</option>
            <option value="Other" <?php echo $medicine['category'] == 'Other' ? 'selected' : ''; ?>>Other (Please specify)</option>
        </select>
        <input type="text" name="category_other" id="category_other" style="display: none;" placeholder="Enter new category">

        <label>Batch Number:</label>
        <input type="text" name="batch_number" value="<?php echo htmlspecialchars($medicine['batch_number']); ?>" required>

        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" value="<?php echo htmlspecialchars($medicine['quantity']); ?>" required>

        <label>Buying Price:</label>
        <input type="number" name="cost_price" step="0.01" value="<?php echo htmlspecialchars($medicine['cost_price']); ?>" required>

        <label>Selling Price:</label>
        <input type="number" name="selling_price" step="0.01" value="<?php echo htmlspecialchars($medicine['price']); ?>" required>

        <label>Expiry Date:</label>
        <input type="date" name="expiry_date" value="<?php echo htmlspecialchars($medicine['expiry_date']); ?>" required>

        <label>Dosage Form:</label>
        <select name="dosage_form" id="dosage_form" required>
            <option value="">Select a Dosage Form</option>
            <option value="Bottle" <?php echo $medicine['dosage_form'] == 'Bottle' ? 'selected' : ''; ?>>Bottle</option>
            <option value="Tube" <?php echo $medicine['dosage_form'] == 'Tube' ? 'selected' : ''; ?>>Tube</option>
            <option value="Strip" <?php echo $medicine['dosage_form'] == 'Strip' ? 'selected' : ''; ?>>Strip</option>
            <option value="Ampoule" <?php echo $medicine['dosage_form'] == 'Ampoule' ? 'selected' : ''; ?>>Ampoule</option>
            <option value="Vial" <?php echo $medicine['dosage_form'] == 'Vial' ? 'selected' : ''; ?>>Vial</option>
            <option value="Sachet" <?php echo $medicine['dosage_form'] == 'Sachet' ? 'selected' : ''; ?>>Sachet</option>
            <option value="Blister Pack" <?php echo $medicine['dosage_form'] == 'Blister Pack' ? 'selected' : ''; ?>>Blister Pack</option>
            <option value="Syringe" <?php echo $medicine['dosage_form'] == 'Syringe' ? 'selected' : ''; ?>>Syringe</option>
            <option value="Other" <?php echo $medicine['dosage_form'] == 'Other' ? 'selected' : ''; ?>>Other (Please specify)</option>
        </select>
        <input type="text" name="dosage_form_other" id="dosage_form_other" style="display: none;" placeholder="Enter new dosage form">

        <label>Facility Name:</label>
        <input type="text" name="facility_name" value="<?php echo htmlspecialchars($medicine['facility_name']); ?>" required>

        <button type="submit">Update Medicine</button>
    </form>
</div>
<a href="manage_medicines.php">Back to Manage Medicines</a>
<script>
    // JavaScript to show/hide other input fields
    document.getElementById('category').addEventListener('change', function () {
        const otherCategory = document.getElementById('category_other');
        otherCategory.style.display = this.value === 'Other' ? 'block' : 'none';
    });

    document.getElementById('dosage_form').addEventListener('change', function () {
        const otherDosageForm = document.getElementById('dosage_form_other');
        otherDosageForm.style.display = this.value === 'Other' ? 'block' : 'none';
    });
</script>

</body>
</html>
