<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $selected_question = $conn->real_escape_string($_POST['security_question']);
    $answer = $conn->real_escape_string($_POST['answer']);

    // Prepare and execute a statement to fetch the user
    $stmt = $conn->prepare("SELECT security_question, security_answer FROM employees WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // User exists, fetch security question and answer
        $stmt->bind_result($security_question, $security_answer);
        $stmt->fetch();

        // Check if the selected question matches and if the answer is correct
        if ($selected_question === $security_question && strtolower($answer) === strtolower($security_answer)) {
            // Answers match, allow password change
            if (isset($_POST['new_password'])) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                $update_stmt = $conn->prepare("UPDATE employees SET password = ? WHERE username = ?");
                $update_stmt->bind_param('ss', $new_password, $username);
                $update_stmt->execute();
                $update_stmt->close();
                $success = "Password updated successfully.";
            }
        } else {
            $error = "Incorrect answer to the security question.";
        }
    } else {
        $error = "Username not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Password Recovery</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background: url('plastic-box-with-different-pills-light-blue-background-top-view-space-text_144356-80594.avif') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    animation: fadeIn 1s;
}

        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            width: 320px;
        }
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        h1{
            color: #00796b;
        }
        label{
            color: #00796b;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color:  #00796b;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .success {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
        a{
            color: #00796b;
        }
    </style>
</head>
<body>
    
    <form method="post">
    <h1>Password Recovery</h1>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="security_question">Security Question:</label>
        <select name="security_question" id="security_question" required>
            <option value="">Select a question</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What is the name of your first pet?">What is the name of your first pet?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <!-- Add more questions as needed -->
        </select>

        <label for="answer">Your Answer:</label>
        <input type="text" name="answer" id="answer" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required>

        <button type="submit">Change Password</button>
        <br>
        <br>
        <a href="admin_login.php">Back to Login</a>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    </form>
    
    
    <script>
        // Add your existing script here...
    </script>
</body>
</html>
