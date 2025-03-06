<?php
session_start();
include 'db_connection.php';
include('permissions.php');

// Initialize variables
$result = null;
$no_results_message = '';
$total_price = 0;
$message = ''; // Initialize message variable

// Initialize or reset cart if needed
if (!isset($_SESSION['cart_items_cosmetics'])) {
    $_SESSION['cart_items_cosmetics'] = [];
    $_SESSION['total_price_cosmetics'] = 0;
}

// Fetch all cosmetics by default
$query_all_cosmetics = "SELECT * FROM cosmetics";
$result_all_cosmetics = $conn->query($query_all_cosmetics);

// Handle cosmetics search
if (isset($_POST['search_cosmetic'])) {
    $cosmetic_name = $_POST['cosmetic_name'];
    $query = "SELECT * FROM cosmetics WHERE name LIKE ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $cosmetic_name = "%" . $conn->real_escape_string($cosmetic_name) . "%";
    $stmt->bind_param('s', $cosmetic_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $no_results_message = 'No cosmetics found matching your search criteria.';
    }
    $stmt->close();
}

// Handle adding cosmetics to cart
if (isset($_POST['add_to_cart'])) {
    $cosmetic_id = (int)$_POST['cosmetic_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch cosmetic details
    $query = "SELECT cosmetic_id, name, price, cost_price, quantity FROM cosmetics WHERE cosmetic_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('i', $cosmetic_id);
    $stmt->execute();
    $cosmetic_result = $stmt->get_result();

    if ($cosmetic_result->num_rows > 0) {
        $cosmetic = $cosmetic_result->fetch_assoc();

        // Check stock availability
        if ($quantity > $cosmetic['quantity']) {
            $message = "<p class='message'>Not enough stock available for " . htmlspecialchars($cosmetic['name']) . ".</p>";
        } else {
            // Check if cosmetic is already in the cart
            $found = false;
            foreach ($_SESSION['cart_items_cosmetics'] as &$item) {
                if ($item['cosmetic_id'] === $cosmetic_id) {
                    $item['quantity'] += $quantity;
                    $item['total'] = $item['price'] * $item['quantity'];
                    $found = true;
                    break;
                }
            }

            // If cosmetic not found, add new item to cart
            if (!$found) {
                $_SESSION['cart_items_cosmetics'][] = [
                    'cosmetic_id' => $cosmetic['cosmetic_id'],
                    'name' => $cosmetic['name'],
                    'price' => $cosmetic['price'],
                    'cost_price' => $cosmetic['cost_price'],
                    'quantity' => $quantity,
                    'total' => $cosmetic['price'] * $quantity
                ];
            }

            // Recalculate total price
            $_SESSION['total_price_cosmetics'] = array_sum(array_column($_SESSION['cart_items_cosmetics'], 'total'));
            $message = "<p class='message'>Cosmetic added to cart successfully.</p>";
        }
    } else {
        $message = "<p class='message'>Cosmetic not found.</p>";
    }
    $stmt->close();
}

// Handle removing item from cart
if (isset($_POST['remove_from_cart'])) {
    $cosmetic_id = (int)$_POST['cosmetic_id'];

    // Loop through cart items to find the item to remove
    foreach ($_SESSION['cart_items_cosmetics'] as $key => $item) {
        if ($item['cosmetic_id'] === $cosmetic_id) {
            // Deduct the total from the overall price
            $_SESSION['total_price_cosmetics'] -= $item['total'];
            // Remove item from cart
            unset($_SESSION['cart_items_cosmetics'][$key]);
            // Re-index the array
            $_SESSION['cart_items_cosmetics'] = array_values($_SESSION['cart_items_cosmetics']);
            $message = "<p class='message'>Item removed from cart.</p>";
            break;
        }
    }
}

// Handle completing sale for cosmetics
if (isset($_POST['complete_sale_cosmetics'])) {
    // Check if user is logged in and has permission
    if (!isset($_SESSION['employee_id'])) {
        $message = "<p class='message'>Error: You must log in to complete a sale.</p>";
    } elseif ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'employee') {
        $message = "<p class='message'>Error: You do not have permission to complete a sale.</p>";
    } elseif (empty($_SESSION['cart_items_cosmetics'])) {
        $message = "<p class='message'>No items in the cart to complete the sale.</p>";
    } else {
        $customer_name = $_POST['customer_name'];
        $customer_mobile = $_POST['customer_mobile'];
        $employee_id = $_SESSION['employee_id'];

        // Insert sale record into the database for cosmetics
        $stmt = $conn->prepare("INSERT INTO cosmetic_sales (total_amount, sale_date, pharmacist_id, customer_name, customer_mobile) VALUES (?, NOW(), ?, ?, ?)");
        $total_amount = $_SESSION['total_price_cosmetics'];
        $stmt->bind_param('diss', $total_amount, $employee_id, $customer_name, $customer_mobile);

        if (!$stmt->execute()) {
            die("Error inserting sale: " . $stmt->error);
        }

        $sale_id = $stmt->insert_id;

        // Insert sale items and update stock
        foreach ($_SESSION['cart_items_cosmetics'] as $item) {
            $stmt = $conn->prepare("INSERT INTO cosmetic_sale_items (sale_id, cosmetic_name, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('isdii', $sale_id, $item['name'], $item['price'], $item['quantity'], $item['total']);
            $stmt->execute();

            $update_stmt = $conn->prepare("UPDATE cosmetics SET quantity = quantity - ? WHERE cosmetic_id = ?");
            $update_stmt->bind_param('ii', $item['quantity'], $item['cosmetic_id']);
            $update_stmt->execute();

            $profit = ($item['price'] - $item['cost_price']) * $item['quantity'];
            $profit_stmt = $conn->prepare("INSERT INTO cosmetic_profits (cosmetic_sale_id, cosmetic_name, profit, created_at) VALUES (?, ?, ?, NOW())");
            $profit_stmt->bind_param('isd', $sale_id, $item['name'], $profit);
            $profit_stmt->execute();
        }

        // Store the cart items and total price in the session for the receipt
        $_SESSION['receipt_cart_items'] = $_SESSION['cart_items_cosmetics'];
        $_SESSION['receipt_total_price'] = $_SESSION['total_price_cosmetics'];

        // Reset cart
        $_SESSION['cart_items_cosmetics'] = [];
        $_SESSION['total_price_cosmetics'] = 0;

        // Success message with a print receipt button
        $message = "<p class='message'>Sale completed successfully.</p>";
        $message .= "<button onclick='printReceipt()' class='btn btn-primary'>Print Receipt</button>";
    }
}
?>

<!-- HTML Structure -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cosmetics Sales Page</title>
    <link rel="stylesheet" href="styles/sale_pagec.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    
<body>
    <nav>
    <?php include('nav.php'); ?>
    </nav>
   <main class="header">
   <h1>Cosmetics Sales</h1>
   <?= $message ?> 
   </main>

    <div class="container">
        <div class="search-form">
            <form method="post">
                <input type="text" name="cosmetic_name" placeholder="Search Cosmetics">
                <button type="submit" name="search_cosmetic">Search</button>
            </form>
        </div>

        <!-- Display available cosmetics if no search results -->
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="search-results">
                <h2>Search Results</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td><?= htmlspecialchars($row['expiry_date']) ?></td>

                                <td>
                                    <form method="post">
                                        <input type="hidden" name="cosmetic_id" value="<?= $row['cosmetic_id'] ?>">
                                        <input type="number" name="quantity" min="1" max="<?= $row['quantity'] ?>" required>
                                        <button type="submit" name="add_to_cart">Add to Cart</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($result_all_cosmetics && $result_all_cosmetics->num_rows > 0): ?>
            <div class="cosmetics-list">
                <h2>Available Cosmetics</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_all_cosmetics->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td><?= htmlspecialchars($row['expiry_date']) ?></td>

                                <td>
                                    <form method="post">
                                        <input type="hidden" name="cosmetic_id" value="<?= $row['cosmetic_id'] ?>">
                                        <input type="number" name="quantity" min="1" max="<?= $row['quantity'] ?>" required>
                                        <button type="submit" name="add_to_cart">Add to Cart</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="message"><?= $no_results_message ?: 'No cosmetics available.' ?></p>
        <?php endif; ?>
    </div>

    <div class="cart">
        <h2>Your Cart</h2>
        <?php if (!empty($_SESSION['cart_items_cosmetics'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Cosmetic Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart_items_cosmetics'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['price']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= htmlspecialchars($item['total']) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cosmetic_id" value="<?= $item['cosmetic_id'] ?>">
                                    <button type="submit" name="remove_from_cart">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>Total Price: <?= $_SESSION['total_price_cosmetics'] ?></p>
            <form method="post">
                <input type="text" name="customer_name" placeholder="Customer Name (optional)">
                <input type="text" name="customer_mobile" placeholder="Customer Mobile (optional)">
                <button type="submit" name="complete_sale_cosmetics">Complete Sale</button>
            </form>
        <?php else: ?>
            <p class="message">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script>
    function printReceipt() {
        const receiptWindow = window.open('', 'PrintReceipt', 'width=600,height=600');
        receiptWindow.document.write('<html><head><title>Receipt</title></head><body>');
        receiptWindow.document.write('<h1>Receipt</h1>');
        
        // Retrieve the PHP session data passed in as JSON
        const receiptItems = <?= json_encode($_SESSION['receipt_cart_items']); ?>;
        const totalPrice = <?= json_encode($_SESSION['receipt_total_price']); ?>;

        // Construct receipt content
        receiptWindow.document.write('<table><thead><tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th></tr></thead><tbody>');
        receiptItems.forEach(item => {
            receiptWindow.document.write(
                `<tr>
                    <td>${item.name}</td>
                    <td>${item.price}</td>
                    <td>${item.quantity}</td>
                    <td>${item.total}</td>
                </tr>`
            );
        });
        receiptWindow.document.write('</tbody></table>');
        receiptWindow.document.write(`<p>Total Price: ${totalPrice}</p>`);
        
        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.print();
    }
</script>

</body>
</html>
