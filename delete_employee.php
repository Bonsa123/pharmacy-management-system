<?php
session_start();
include('db_connection.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');
// Check if employee ID is provided
if (isset($_GET['id'])) {
    $employee_id = intval($_GET['id']);

    // Prepare and execute the deletion query
    $stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = ?");
    $stmt->bind_param("i", $employee_id);

    if ($stmt->execute()) {
        // Redirect back to the employees list with a success message
        header("Location: view_employees.php?message=Employee deleted successfully.");
    } else {
        // Redirect back with an error message
        header("Location: view_employees.php?message=Error deleting employee.");
    }
    
    $stmt->close();
} else {
    header("Location: view_employees.php?message=No employee ID provided.");
}

$conn->close();
?>
