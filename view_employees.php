<?php
session_start();
include('db_connection.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');
// Fetch all employees
$result = $conn->query("SELECT * FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Employees</title>
    <link rel="stylesheet" href="styles/view_employees.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <script>
        // Function to confirm deletion
        function confirmDelete(employeeId) {
            const confirmAction = confirm("Are you sure you want to delete this employee?");
            if (confirmAction) {
                // Redirect to delete_employee.php with the employee ID
                window.location.href = "delete_employee.php?id=" + employeeId;
            }
        }
    </script>
</head>
<body>
<nav>
<?php include('nav.php'); ?>
</nav>
<main>
<h1>Employees List</h1>
</main>
    

    <?php if (isset($_GET['message'])): ?>
        <div class="message"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <table>
        
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                <td data-label="Role"><?php echo htmlspecialchars($row['role']); ?></td>
                <td data-label="Actions">
                    <a href="edit_employee.php?id=<?php echo $row['employee_id']; ?>">Edit</a>
                    <a href="#" onclick="confirmDelete(<?php echo $row['employee_id']; ?>); return false;">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
   <!-- Footer -->
   <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</body>
        
</html>
