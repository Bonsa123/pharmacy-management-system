<?php

include('db_connection.php');

// Redirect if not admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Ensure the role is set in the session before using it
$user_role = $_SESSION['role'] ?? null;

if (!$user_role) {
    header("Location: admin_login.php");
    exit();
}

// Define available permissions based on the pages in your navigation
$available_permissions = [
    'view_dashboard' => 'View Dashboard',
    'manage_medicines' => 'Manage Medicines',
    'view_medicines' => 'View Medicines',
    'add_medicine' => 'Add Medicine',
    'manage_cosmetics' => 'Manage Cosmetics',
    'view_cosmetics' => 'View Cosmetics',
    'add_cosmetic' => 'Add Cosmetic',
    'sales' => 'Sales',
    'medicine_sales' => 'Medicine Sales',
    'cosmetic_sales' => 'Cosmetic Sales',
    'manage_companies' => 'Manage Companies',
    'manage_stock' => 'Manage Stock (BIN CARD)',
    'manage_expired_medicine' => 'Manage Expired Medicine',
    'batch_reports' => 'Batch Reports',
    'add_employee' => 'Add Employee',
    'view_employees' => 'View Employees',
    'invoice_search' => 'Invoice Search',
    'cosmetic_profits' => 'Cosmetic Profits',
    'total_profits' => 'Total (C + M) Profits',
  
];

// Fetch unique roles from the employees table
$roles = [];
$result = $conn->query("SELECT DISTINCT role FROM employees");
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role'];
}

// Handle form submission to update permissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['permissions'])) {
    foreach ($_POST['permissions'] as $role => $perms) {
        $perm_string = implode(',', $perms);
        
        $stmt = $conn->prepare("UPDATE employees SET permissions = ? WHERE role = ?");
        $stmt->bind_param('ss', $perm_string, $role);
        
        if ($stmt->execute()) {
            $success = "Permissions updated successfully for role: " . htmlspecialchars($role) . "!";
        } else {
            $error = "Error updating permissions for role: " . htmlspecialchars($role) . " - " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Fetch current permissions to check against
$role_permissions = [];
$result = $conn->query("SELECT role, permissions FROM employees");
while ($row = $result->fetch_assoc()) {
    $role_permissions[$row['role']] = $row['permissions'];
}
?>
