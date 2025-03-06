<?php
include('db_connection.php'); // Include your database connection
include('permissions.php');
// Example credentials

$username = 'meseret'; // Set the desired username
$password = '@jesus'; // Set the desired password

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param('ss', $username, $hashed_password);

// Execute the statement
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();

echo "Admin user created successfully!";
?>
