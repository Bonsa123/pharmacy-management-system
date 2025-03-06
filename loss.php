<?php
// Start the session to manage admin login status
session_start();
include 'db_connection.php'; // Include database connection file

// Check if the admin is logged in, redirect if not
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Include permissions file if necessary
include('permissions.php');

// Fetch medicines for the dropdown selection
$medicines = [];
$medicines_query = "SELECT medicine_id, name FROM medicine";
$medicines_result = $conn->query($medicines_query);
if ($medicines_result) {
    while ($row = $medicines_result->fetch_assoc()) {
        $medicines[] = $row;
    }
}

// Handle form submission for recording medicine loss/adjustment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs safely
    $medicine_id = trim($_POST['medicine_id']);
    $entered_medicine_name = trim($_POST['entered_medicine_name']);
    $quantity = (int)$_POST['quantity'];
    $reason = $_POST['reason'];
    $description = $_POST['description'] ?? '';

    // Determine the selected medicine ID based on dropdown or manual entry
    if (!empty($medicine_id)) {
        $selected_medicine_id = $medicine_id;
    } else {
        $query = "SELECT medicine_id FROM medicine WHERE name = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $entered_medicine_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $selected_medicine = $result->fetch_assoc();
                $selected_medicine_id = $selected_medicine['medicine_id'];
            } else {
                echo "<script>alert('Error: Medicine not found in the database.');</script>";
                $selected_medicine_id = null;
            }
        } else {
            die("Preparation failed: " . $conn->error);
        }
    }

    // Proceed only if a valid medicine ID was found or selected
    if ($selected_medicine_id) {
        $query = "SELECT * FROM medicine WHERE medicine_id = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $selected_medicine_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $medicine = $result->fetch_assoc();
                $current_quantity = $medicine['quantity'];

                if ($current_quantity >= $quantity) {
                    // Update stock in the medicine table
                    $new_quantity = $current_quantity - $quantity;
                    $update_query = "UPDATE medicine SET quantity = ? WHERE medicine_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    
                    if ($update_stmt) {
                        $update_stmt->bind_param("is", $new_quantity, $selected_medicine_id);
                        $update_stmt->execute();

                        if ($update_stmt->affected_rows > 0) {
                            // Insert the loss/adjustment record
                            $insert_query = "INSERT INTO loss_adjustments (medicine_id, quantity, reason, description) VALUES (?, ?, ?, ?)";
                            $insert_stmt = $conn->prepare($insert_query);
                            
                            if ($insert_stmt) {
                                $insert_stmt->bind_param("siss", $selected_medicine_id, $quantity, $reason, $description);
                                $insert_stmt->execute();

                                if ($insert_stmt->affected_rows > 0) {
                                    echo "<script>alert('Loss/Adjustment recorded successfully. Medicine stock updated.');</script>";
                                } else {
                                    echo "<script>alert('Error recording loss/adjustment in the database.');</script>";
                                }
                            } else {
                                die("Preparation failed: " . $conn->error);
                            }
                        } else {
                            echo "<script>alert('Error: Unable to update the medicine stock.');</script>";
                        }
                    } else {
                        die("Preparation failed: " . $conn->error);
                    }
                } else {
                    echo "<script>alert('Error: The loss quantity exceeds the available stock.');</script>";
                }
            } else {
                echo "<script>alert('Error: Medicine not found in the database.');</script>";
            }
        }
    }

    // Close statements and connection
    $stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($insert_stmt)) $insert_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Medicine Loss/Adjustment</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/loss.css">
    <script>
        function toggleInputMethod() {
            const selectMedicine = document.getElementById('medicine_id');
            const enteredMedicine = document.getElementById('entered_medicine_name');
            const selectOption = document.querySelector('input[name="input_method"]:checked').value;

            if (selectOption === 'dropdown') {
                selectMedicine.style.display = 'block';
                enteredMedicine.style.display = 'none';
                enteredMedicine.value = '';
            } else {
                selectMedicine.style.display = 'none';
                enteredMedicine.style.display = 'block';
                selectMedicine.value = '';
            }
        }
    </script>
</head>
<body>
    <nav>
    <?php include('nav.php'); ?>
    </nav>
    
    <header>
        <h1>Record Medicine Loss/Adjustment</h1>
    </header>

    <div class="container">
        <form action="" method="post">
            <label for="input_method">Choose Medicine Entry Method:</label>
            <label><input type="radio" name="input_method" value="dropdown" checked onclick="toggleInputMethod()"> Select from dropdown</label>
            <label><input type="radio" name="input_method" value="manual" onclick="toggleInputMethod()"> Enter manually</label>

            <div id="medicine_entry">
                <label for="medicine_id">Medicine:</label>
                <select id="medicine_id" name="medicine_id" required>
                    <option value="">Select Medicine</option>
                    <?php foreach ($medicines as $medicine): ?>
                        <option value="<?php echo htmlspecialchars($medicine['medicine_id']); ?>">
                            <?php echo htmlspecialchars($medicine['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="entered_medicine_name" style="display:none;">Enter Medicine Name:</label>
                <input type="text" id="entered_medicine_name" name="entered_medicine_name" placeholder="Enter medicine name..." style="display:none;">
            </div>

            <label for="quantity">Quantity Lost/Adjusted:</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="reason">Reason for Loss/Adjustment:</label>
            <select id="reason" name="reason">
                <option value="expired">Expired</option>
                <option value="broken">Broken</option>
                <option value="damaged">Damaged</option>
                <option value="lost">Lost</option>
                <option value="other">Other</option>
            </select>

            <label for="description">Description (Optional):</label>
            <input type="text" id="description" name="description" placeholder="Describe the reason...">

            <input type="submit" value="Submit Loss/Adjustment">
        </form>
        <br>
    </div>
    <a href="manage_stock.php" style="color:green;margin-left:20px;">Back To BIN Card</a>
    <script>
        document.addEventListener('DOMContentLoaded', toggleInputMethod);
    </script>
</body>
</html>
