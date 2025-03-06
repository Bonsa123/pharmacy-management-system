<?php
session_start();
include('db_connection.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute a statement to fetch the user
    $stmt = $conn->prepare("SELECT employee_id, password, role FROM employees WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // User exists, fetch password hash and role
        $stmt->bind_result($employee_id, $hashed_password, $role);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $employee_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            $_SESSION['employee_id'] = $employee_id; // Set after successful login

            // Fetch permissions for the user's role
            $permissions_stmt = $conn->prepare("SELECT permissions FROM employees WHERE role = ?");
            $permissions_stmt->bind_param('s', $role);
            $permissions_stmt->execute();
            $permissions_stmt->bind_result($permissions);
            $permissions_stmt->fetch();
            $permissions_stmt->close();

            // Set permissions in session
            $_SESSION['permissions'] = $permissions ? explode(',', $permissions) : [];

            // Redirect based on role
            if ($role === 'manager') {
                $_SESSION['admin'] = true;
                header("Location: admin_dashboard.php");
            } else {
                $_SESSION['admin'] = false;
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
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


h1 {
    text-align: center;
    color: #00796b;
    margin-bottom: 20px;
    font-size: 2.5em; /* Larger heading size */
    font-weight: 700; /* Bold font weight */
}

form {
    background: white;
    padding: 40px; /* Increased padding for a spacious feel */
    border-radius: 10px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    width: 90%; /* Adjusted width for better responsiveness */
    max-width: 400px; /* Added max-width for larger screens */
    transition: box-shadow 0.3s ease;
    animation: slideIn 0.5s forwards;
}

form:hover {
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
}

label {
    display: block;
    margin: 15px 0 5px; /* Increased margin for better spacing */
    color: #00796b;
    font-weight: 600; /* Bold label text */
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px; /* Increased padding for comfort */
    margin: 8px 0 20px; /* Adjusted margins */
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-size: 1em; /* Consistent font size */
}

input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
    outline: none;
}

button {
    width: 100%;
    padding: 12px; /* Increased padding for a larger button */
    background-color:#00796b;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 18px; /* Slightly larger font size */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s;
}

button:hover {
    background-color:  #16a085;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Added shadow on hover */
}

.error {
    color: red;
    text-align: center;
    margin-top: 10px;
}

.icon {
    margin-right: 10px;
    color: #00796b;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Media query for responsiveness */
@media (max-width: 480px) {
    h1 {
        font-size: 2em; /* Smaller font size on mobile */
    }

    form {
        padding: 20px; /* Reduced padding on mobile */
    }

    button {
        font-size: 16px; /* Consistent button font size on mobile */
    }
}
a{
    color:#00796b;
}
a:hover{
    color:black;
}
    </style>
</head>
<body>
  
    <form method="post">
          <h1>Login  Page</h1>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
        <p><a href="password_recovery.php">Forgot your password?</a></p>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
    
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const usernameInput = document.getElementById("username");
            const passwordInput = document.getElementById("password");
            const loginForm = document.querySelector("form");

            usernameInput.addEventListener("focus", () => {
                usernameInput.style.borderColor = "#4a90e2";
                usernameInput.style.boxShadow = "0 0 5px rgba(74, 144, 226, 0.5)";
            });
            usernameInput.addEventListener("blur", () => {
                usernameInput.style.borderColor = "#ccc";
                usernameInput.style.boxShadow = "none";
            });

            passwordInput.addEventListener("focus", () => {
                passwordInput.style.borderColor = "#4a90e2";
                passwordInput.style.boxShadow = "0 0 5px rgba(74, 144, 226, 0.5)";
            });
            passwordInput.addEventListener("blur", () => {
                passwordInput.style.borderColor = "#ccc";
                passwordInput.style.boxShadow = "none";
            });

            loginForm.addEventListener("submit", (e) => {
                const usernameValue = usernameInput.value.trim();
                const passwordValue = passwordInput.value.trim();

                if (!usernameValue || !passwordValue) {
                    e.preventDefault();
                    alert("Please fill in all fields.");
                }
            });
        });
    </script>
</body>
</html>
