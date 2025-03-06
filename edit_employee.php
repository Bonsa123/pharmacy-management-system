<?php
session_start();
include('db_connection.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit(); 
}

// Include permissions (if necessary)
include('permissions.php');

// Check if the employee ID is set in the URL
if (isset($_GET['id'])) {
    $employeeId = intval($_GET['id']); // Ensure the ID is an integer

    // Fetch employee details
    $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $employee = $result->fetch_assoc();
    } else {
        // If no employee found, redirect back with a message
        header("Location: view_employees.php?message=Employee not found.");
        exit();
    }
} else {
    // If ID is not provided, redirect back with a message
    header("Location: view_employees.php?message=No employee ID provided.");
    exit();
}

// Handle form submission for updating employee details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    // Prepare the update statement
    $updateStmt = $conn->prepare("UPDATE employees SET username = ?, role = ? WHERE employee_id = ?");
    $updateStmt->bind_param("ssi", $username, $role, $employeeId);

    if ($updateStmt->execute()) {
        // Redirect back with a success message
        header("Location: view_employees.php?message=Employee updated successfully.");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: view_employees.php?message=Error updating employee.");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Employee</title>
    <link rel="stylesheet" href="styles/edit_employee.css">
    <link rel="stylesheet" href="styles/navbar.css">
</head>
<body>

<h1>Edit Employee</h1>

<form action="edit_employee.php?id=<?php echo $employeeId; ?>" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($employee['username']); ?>" required>

    <label for="role">Role:</label>
    <input type="text" name="role" id="role" value="<?php echo htmlspecialchars($employee['role']); ?>" required>

    <input type="submit" value="Update Employee">
</form>

<a href="view_employees.php">Cancel</a>

</body>
</html>
