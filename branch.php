<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include('permissions.php');
// Get the branch ID from the URL
$branch_id = $_GET['branch_id'];

// Fetch branch details for the given ID
$stmt = $pdo->prepare("SELECT * FROM pharmacy_company WHERE company_id = :branch_id");
$stmt->execute(['branch_id' => $branch_id]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user has access to this branch
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM employeesBranches 
    WHERE employee_id = :employee_id AND company_id = :branch_id
");
$stmt->execute(['employee_id' => $_SESSION['user_id'], 'branch_id' => $branch_id]);
$hasAccess = $stmt->fetchColumn();

if (!$hasAccess) {
    echo "Access denied";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pharmacy_name = $_POST['pharmacy_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Update branch details
    $stmt = $pdo->prepare("
        UPDATE pharmacy_company 
        SET pharmacy_name = :pharmacy_name, address = :address, phone = :phone 
        WHERE company_id = :branch_id
    ");
    $stmt->execute([
        'pharmacy_name' => $pharmacy_name,
        'address' => $address,
        'phone' => $phone,
        'branch_id' => $branch_id
    ]);

    echo "Branch updated successfully!";
}
?>

<h1>Update Branch</h1>
<form method="post">
    <label for="pharmacy_name">Pharmacy Name:</label>
    <input type="text" name="pharmacy_name" value="<?php echo htmlspecialchars($branch['pharmacy_name']); ?>" required>

    <label for="address">Address:</label>
    <input type="text" name="address" value="<?php echo htmlspecialchars($branch['address']); ?>" required>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($branch['phone']); ?>" required>

    <button type="submit">Update Branch</button>
</form>
