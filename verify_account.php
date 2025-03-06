<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['pending_verification'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verification_code'];
    $username = $_SESSION['pending_verification'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND verification_code = ?");
    $stmt->bind_param('ss', $username, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Verification successful
        $stmt = $conn->prepare("UPDATE admin SET verification_code = NULL WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();

        $_SESSION['admin'] = $username;
        unset($_SESSION['pending_verification']);
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid verification code";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>
</head>
<body>
    <div class="verify-container">
        <h1>Verify Your Account</h1>
        <form method="post">
            <label for="verification_code">Enter the code sent to your phone:</label>
            <input type="text" name="verification_code" id="verification_code" required>
            <button type="submit">Verify</button>
        </form>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
