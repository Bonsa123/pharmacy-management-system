<?php
session_start();
include('db_connection.php');
include('permissions.php');
// Handle registration request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "INSERT INTO admin (username, password) VALUES ('$username', MD5('$password'))";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_login.php");
        exit();
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include('nav.php'); ?>
    <h1>Admin Registration</h1>

    <form method="post" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <a href="admin_login.php">Back to Login</a>
</body>
</html>
