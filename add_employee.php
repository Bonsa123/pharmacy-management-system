<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $role = $conn->real_escape_string($_POST['role']); // E.g., 'employee'
    $security_question = $conn->real_escape_string($_POST['security_question']);
    $security_answer = $conn->real_escape_string($_POST['security_answer']);

    $stmt = $conn->prepare("INSERT INTO employees (username, password, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $password, $role, $security_question, $security_answer);

    if ($stmt->execute()) {
        $success = "Employee added successfully!";
    } else {
        $error = "Error adding employee: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Add Employee</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/addemp.css">
   
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all the main links that have dropdowns
        const dropdownLinks = document.querySelectorAll('nav ul li > a');

        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Prevent default action for dropdown links
                if (this.nextElementSibling && this.nextElementSibling.tagName === 'UL') {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block'; // Toggle dropdown
                }
            });
        });

        // Click anywhere else to close the dropdown
        document.addEventListener('click', function(e) {
            dropdownLinks.forEach(link => {
                const submenu = link.nextElementSibling;
                if (submenu && submenu.tagName === 'UL') {
                    if (!link.contains(e.target) && !submenu.contains(e.target)) {
                        submenu.style.display = 'none'; // Hide dropdown if clicked outside
                    }
                }
            });
        });
    });
</script>
</head>

<body>

   
    <form method="post">
    <h1>Add New Employee</h1>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="employee">Employee</option>
            <option value="employee">Casher</option>
            <option value="manager">Manager</option>
        </select>

        <label for="security_question">Security Question:</label>
        <select name="security_question" id="security_question" required>
            <option value="">Select a question</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="What is your mother's midle name?">What is your mother's maiden name?</option>
            <option value="What is the name of your first school?">What is the name of your first pet?</option>
            <option value="What city were you born in?">What city were you born in?</option>
            <!-- Add more questions as needed -->
        </select>

        <label for="security_answer">Your Answer:</label>
        <input type="text" name="security_answer" id="security_answer" required>

        <button type="submit">Add Employee</button>
        <br>
        <br>
        <a href="admin_login.php">Login</a>
        <br>
        <br>
        <a href="admin_dashboard.php">Back To Dashboard</a>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
  
</body>
</html>
