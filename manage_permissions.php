<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already active
}


include('db_connection.php');
include('permissions.php');

// Redirect if not admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Ensure user role is set
$user_role = $_SESSION['role'] ?? null;

if (!$user_role) {
    header("Location: admin_login.php");
    exit();
}

// Define available permissions based on the pages in your navigation
$available_permissions = [
    'view_dashboard' => 'View Dashboard',
    'manage' => 'Manage', // Added to represent the manage section
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
        // Convert the array of permissions into a comma-separated string
        $perm_string = implode(',', $perms);
        
        // Update the permissions for each role in the employees table
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Permissions</title>
    <link rel="stylesheet" href="styles/manage_permissions.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownLinks = document.querySelectorAll('nav ul li > a');

        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.nextElementSibling && this.nextElementSibling.tagName === 'UL') {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });

        document.addEventListener('click', function(e) {
            dropdownLinks.forEach(link => {
                const submenu = link.nextElementSibling;
                if (submenu && submenu.tagName === 'UL') {
                    if (!link.contains(e.target) && !submenu.contains(e.target)) {
                        submenu.style.display = 'none';
                    }
                }
            });
        });
    });
    </script>
</head>

<body>
<?php include('nav.php'); ?>
    <h1>Manage Permissions</h1>
    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?php echo htmlspecialchars($role); ?></td>
                    <td>
                        <?php foreach ($available_permissions as $key => $permission): ?>
                            <label>
                                <input type="checkbox" 
                                       name="permissions[<?php echo htmlspecialchars($role); ?>][]" 
                                       value="<?php echo htmlspecialchars($key); ?>" 
                                       <?php 
                                       // Check if the role already has this permission
                                       $current_perms = isset($role_permissions[$role]) ? explode(',', $role_permissions[$role]) : [];
                                       echo in_array($key, $current_perms) ? 'checked' : ''; 
                                       ?>>
                                <?php echo htmlspecialchars($permission); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Update Permissions</button>
    </form>

    <br>
    <br>
    <a href="admin_dashboard.php">Back To Dashboard</a>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</body>
</html>
