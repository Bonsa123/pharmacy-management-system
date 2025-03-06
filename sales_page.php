<?php
session_start();
include 'db_connection.php';
include 'permissions.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize or reset cart if needed
if (!isset($_SESSION['cart_items'])) {
    $_SESSION['cart_items'] = [];
    $_SESSION['total_price'] = 0;
}

// Fetch all medicines by default or based on search
$no_results_message = '';
$medicine_added_message = '';  // Message for successful add to cart
$medicine_removed_message = '';  // Message for successful remove from cart
$searchQuery = isset($_POST['search_medicine']) ? "%" . $_POST['medicine_name'] . "%" : "%";
$query = "SELECT * FROM medicine WHERE name LIKE ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $searchQuery);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0 && isset($_POST['search_medicine'])) {
    $no_results_message = 'No medicines found matching your search criteria.';
}

// Function to handle expired medicines
function checkMedicineExpiry($expiry_date, $medicine_name) {
    $expiry_date = new DateTime($expiry_date);
    $current_date = new DateTime();
    if ($expiry_date < $current_date) {
        return "<p class='message'>The medicine <strong>{$medicine_name}</strong> is expired. Do you still want to sell it?</p>
                <form method='post'>
                    <button type='submit' name='confirm_sale' class='btn btn-warning'>Yes, sell it</button>
                    <button type='submit' name='cancel_sale' class='btn btn-secondary'>No, cancel</button>
                </form>";
    }
    return null;
}
// Function to check if the medicine is active or expired
function checkMedicineStatus($expiry_date) {
    $expiry_date = new DateTime($expiry_date);
    $current_date = new DateTime();
    return $expiry_date < $current_date ? "Expired" : "Active";
}

// Remove from Cart Logic
if (isset($_POST['remove_from_cart'])) {
    $remove_index = $_POST['remove_index'];
    if (isset($_SESSION['cart_items'][$remove_index])) {
        $removed_item = $_SESSION['cart_items'][$remove_index];
        $_SESSION['total_price'] -= $removed_item['total']; // Deduct total price from cart
        unset($_SESSION['cart_items'][$remove_index]); // Remove the item from the cart
        $_SESSION['cart_items'] = array_values($_SESSION['cart_items']); // Re-index the array
        $medicine_removed_message = "Medicine removed from cart successfully!";
    }
}

// Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    $medicine_id = $_POST['medicine_id'];
    $quantity = floatval($_POST['quantity']);

    // Fetch medicine details
    $query = "SELECT name, price, cost_price, quantity, expiry_date FROM medicine WHERE medicine_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $medicine_id);
    $stmt->execute();
    $medicine = $stmt->get_result()->fetch_assoc();

    if ($medicine) {
        // Check if expired
        $expiry_message = checkMedicineExpiry($medicine['expiry_date'], $medicine['name']);
        if ($expiry_message && !isset($_POST['confirm_sale'])) {
            echo $expiry_message;
            exit;
        }

        // Check stock availability
        if ($quantity > $medicine['quantity']) {
            echo "<p class='message'>Not enough stock available for " . htmlspecialchars($medicine['name']) . ".</p>";
        } else {
            // Add item to cart
            $_SESSION['cart_items'][] = [
                'name' => $medicine['name'],
                'price' => $medicine['price'],
                'quantity' => $quantity,
                'total' => $medicine['price'] * $quantity,
                'cost_price' => $medicine['cost_price']
            ];
            $_SESSION['total_price'] += $medicine['price'] * $quantity;
            $medicine_added_message = "Medicine added to cart successfully!";
        }
    }
}

// Complete Sale Logic
if (isset($_POST['complete_sale'])) {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_mobile = $_POST['customer_mobile'] ?? '';
    $employee_id = $_SESSION['employee_id'] ?? 5;

    // Insert sale details
    $stmt = $conn->prepare("INSERT INTO sales (total_amount, sale_date, employee_id, customer_name, customer_mobile) VALUES (?, NOW(), ?, ?, ?)");
    $total_amount = $_SESSION['total_price'];
    $stmt->bind_param('diss', $total_amount, $employee_id, $customer_name, $customer_mobile);
    $stmt->execute();
    $sale_id = $stmt->insert_id;

    foreach ($_SESSION['cart_items'] as $item) {
        // Insert sale items
        $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, medicine_name, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('isdii', $sale_id, $item['name'], $item['price'], $item['quantity'], $item['total']);
        $stmt->execute();

        // Update stock and insert profit
        $stmt = $conn->prepare("UPDATE medicine SET quantity = quantity - ? WHERE name = ?");
        $stmt->bind_param('is', $item['quantity'], $item['name']);
        $stmt->execute();

        $profit = ($item['price'] - $item['cost_price']) * $item['quantity'];
        $stmt = $conn->prepare("INSERT INTO profits (sale_id, medicine_name, profit, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('isd', $sale_id, $item['name'], $profit);
        $stmt->execute();
    }

    // Reset cart and total
    $_SESSION['cart_items'] = [];
    $_SESSION['total_price'] = 0;
    $sale_success_message = "Sale completed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Page</title>
    <link rel="stylesheet" href="styles/salem.css">
    <link rel="stylesheet" href="styles/navbar.css">
</head>
<body>
<nav>
<?php include('nav.php'); ?>
</nav>
<main>
<h1>Sales Page</h1>
</main>

<div class="container">
    <?php if (!empty($sale_success_message)) : ?>
        <p class="message"><?php echo $sale_success_message; ?></p>
    <?php endif; ?>

    <?php if (!empty($medicine_added_message)) : ?>
        <p class="message"><?php echo $medicine_added_message; ?></p>
    <?php endif; ?>

    <?php if (!empty($medicine_removed_message)) : ?>
        <p class="message"><?php echo $medicine_removed_message; ?></p>
    <?php endif; ?>
    
    <form method="post">
        <input type="text" name="medicine_name" placeholder="Search for medicines" required>
        <button type="submit" name="search_medicine">Search</button>
    </form>

    <?php if (!empty($no_results_message)) : ?>
        <p class="message"><?php echo $no_results_message; ?></p>
    <?php endif; ?>
    <h2>Available Medicines</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
         
            <th>Quantity</th>
            <th>Status</th> <!-- Changed to Status -->
            <th>Add to Cart</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result) : ?>
            <?php while ($medicine = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                    <td><?php echo htmlspecialchars($medicine['price']); ?></td>
                  
                    <td><?php echo htmlspecialchars($medicine['quantity']); ?></td>
                    <td>
                    <?php 
                            // Get the status (Active/Expired)
                            $status = checkMedicineStatus($medicine['expiry_date']);
                            // Display "Active" in green and "Expired" in red
                            if ($status == "Active") {
                                echo "<span style='color: green;'>$status</span>";
                            } else {
                                echo "<span style='color: red;'>$status</span>";
                            }
                            ?>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="medicine_id" value="<?php echo htmlspecialchars($medicine['medicine_id']); ?>">
                            <input type="number" name="quantity" step="0.01" min="0.01" required>
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

</div>

<div class="cart">
<h2>Your Cart</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($_SESSION['cart_items'])) : ?>
            <tr>
                <td colspan="5">Your cart is empty.</td>
            </tr>
        <?php else : ?>
            <?php foreach ($_SESSION['cart_items'] as $index => $item) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['total']); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="remove_index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_from_cart">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<h3>Total: <?php echo $_SESSION['total_price']; ?></h3>

<form method="post">
    <input type="text" name="customer_name" placeholder="Enter your name (optional)">
    <input type="text" name="customer_mobile" placeholder="Enter your phone (optional)">
    <button type="submit" name="complete_sale">Complete Sale</button>
</form>

</div>
</body>
</html>
