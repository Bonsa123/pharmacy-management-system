<?php
session_start();
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include('permissions.php');

// Handle Search Request
$search_results = [];
$search_query = '';

if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $sql = "SELECT 
    s.sale_id, 
    s.customer_name, 
    s.customer_mobile, 
    s.sale_date, 
    s.employee_id, 
    si.medicine_name ,
    si.quantity
FROM 
    sales s 
INNER JOIN 
    sale_items si ON s.sale_id = si.sale_id 
WHERE 
    s.sale_id LIKE ? OR s.customer_mobile LIKE ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
echo 'Failed to prepare statement: ' . $conn->error;
exit;
}

$search_term = "%$search_query%";
$stmt->bind_param('ss', $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Search</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/invoice_search.css">
   
</head>
<body>
<?php include('nav.php'); ?>

    
    <div class="main-content">
        <h2>Search Invoices</h2>

        <!-- Search Form -->
        <div class="search-form">
            <form action="invoice_search.php" method="post">
                <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter customer mobile number">
                <input type="submit" name="search" value="Search">
            </form>
        </div>

        <!-- Search Results -->
        <?php if (!empty($search_results)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Customer Name</th>
                        <th>Customer Mobile</th>
                        <th>Sale Date</th>
                        <th>Pharmacist ID</th>
                        <th>Medicine Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $result) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['sale_id']); ?></td>
                            <td><?php echo htmlspecialchars($result['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['customer_mobile']); ?></td>
                            <td><?php echo htmlspecialchars($result['sale_date']); ?></td>
                            <td><?php echo htmlspecialchars($result['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($result['medicine_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_POST['search'])) : ?>
            <p class="success">No results found for "<?php echo htmlspecialchars($search_query); ?>".</p>
        <?php endif; ?>
    </div>
        <!-- Modal Structure -->
<div id="passwordModal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red" viewBox="0 0 24 24" style="margin-right: 8px;">
        <path d="M12 2a4 4 0 0 1 4 4v2h-8V6a4 4 0 0 1 4-4zm6 6V6a6 6 0 0 0-12 0v2H6a4 4 0 0 0-4 4v8a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4v-8a4 4 0 0 0-4-4h-2zm0 10H6v-8h12v8z"/>
    </svg>
    <h2 style="color: red; margin: 0;">Private</h2>
</div>

        <h2>Enter Password</h2>
        <input type="password" id="modalPassword" placeholder="Password">
        <button id="submitPassword">Submit</button>
    </div>
</div>
    <script>
    const modal = document.getElementById("passwordModal");
const closeModal = document.getElementsByClassName("close")[0];
const submitButton = document.getElementById("submitPassword");

document.getElementById("profit-link").addEventListener("click", function(event) {
    event.preventDefault(); // Prevent the default link action
    modal.style.display = "block"; // Show the modal
});

// Close the modal when the user clicks the close button
closeModal.onclick = function() {
    modal.style.display = "none";
}

// Handle password submission
submitButton.onclick = function() {
    const password = document.getElementById("modalPassword").value;
    if (password === "1234") {
        window.location.href = "profitpage.php"; // Redirect to profit page
    } else {
        alert("Incorrect password. Access denied.");
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

    </script>

    <!-- Modal Structure -->
<div id="passwordModal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red" viewBox="0 0 24 24" style="margin-right: 8px;">
        <path d="M12 2a4 4 0 0 1 4 4v2h-8V6a4 4 0 0 1 4-4zm6 6V6a6 6 0 0 0-12 0v2H6a4 4 0 0 0-4 4v8a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4v-8a4 4 0 0 0-4-4h-2zm0 10H6v-8h12v8z"/>
    </svg>
    <h2 style="color: red; margin: 0;">Private</h2>
</div>

        <h2>Enter Password</h2>
        <input type="password" id="modalPassword" placeholder="Password">
        <button id="submitPassword">Submit</button>
    </div>
</div>
    <script>
    const modal = document.getElementById("passwordModal");
const closeModal = document.getElementsByClassName("close")[0];
const submitButton = document.getElementById("submitPassword");

document.getElementById("profit-link").addEventListener("click", function(event) {
    event.preventDefault(); // Prevent the default link action
    modal.style.display = "block"; // Show the modal
});

// Close the modal when the user clicks the close button
closeModal.onclick = function() {
    modal.style.display = "none";
}

// Handle password submission
submitButton.onclick = function() {
    const password = document.getElementById("modalPassword").value;
    if (password === "1234") {
        window.location.href = "profitpage.php"; // Redirect to profit page
    } else {
        alert("Incorrect password. Access denied.");
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

    </script>


</body>
 <!-- Footer -->
 <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
</html>

<?php
// Close the database connection
$conn->close();
?>
