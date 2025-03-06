<?php
session_start();
include('db_connection.php');
// Check if the user is logged in and set permissions
if (isset($_SESSION['user_id'])) {
    // Assuming you have logic to fetch user role and permissions
    // For example:
    $user_role = $_SESSION['user_role']; // Retrieve user role from session
    // Assuming $role_permissions is fetched from the database based on the user role
    $_SESSION['permissions'] = explode(',', $role_permissions[$user_role]);
} else {
    // Set permissions to an empty array if the user is not logged in
    $_SESSION['permissions'] = [];
}
// Handle account creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_account'])) {
    $new_username = $conn->real_escape_string($_POST['new_username']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
  

    // Check if the user already exists
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param('s', $new_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?,  ?)");
        $stmt->bind_param('sss', $new_username, $new_password);
        if ($stmt->execute()) {
            $_SESSION['admin'] = $new_username; // Log the user in after successful account creation
            header("Location: admin_login.php");
            exit();
        } else {
            $error = "Account creation failed!";
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/createAcc.css">
    
</head>
<body>
    <div class="login-container">
        <!-- Create Account Form -->
        <h2>Create Account</h2>
        <form method="post">
            <label for="new_username"><i class="fas fa-user-plus"></i> New Username:</label>
            <input type="text" name="new_username" id="new_username" placeholder="Enter new username" required>

            <label for="new_password"><i class="fas fa-lock"></i> New Password:</label>
            <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>

         
            <button type="submit" name="create_account">Create Account</button>
        </form>
    </div>
</body>
</html>
