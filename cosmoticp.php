<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php"); // Redirect if not logged in
    exit();
}

// Check if the user has permission to view cosmetic profits
if (!in_array('cosmetic_profits', $_SESSION['permissions'])) {
    header("Location: admin_dashboard.php"); // Redirect if no permission
    exit();
}
// Connect to the database
include("db_connection.php");
include('permissions.php');

$total_profit = 0; // Initialize total_profit to avoid undefined variable notice
$today_profit = 0;
$yesterday_profit = 0;
$last_7_days_profit = 0;
$last_30_days_profit = 0;

// Calculate profits based on different time frames
if (isset($_POST['calculate_profit'])) {
    $timeframe = $_POST['time_frame']; 
    $date_limit = ""; 

    // Prepare the date limit for SQL query
    switch ($timeframe) {
        case '24 hour':
            $date_limit = "created_at >= NOW() - INTERVAL 24 HOUR";
            break;
        case 'today':
            $date_limit = "DATE(created_at) = CURDATE()"; // Adjusted for today's profits
            break;
        case '1 day':
            $date_limit = "DATE(created_at) = CURDATE() - INTERVAL 1 DAY";
            break;
        case '7 days':
            $date_limit = "created_at >= NOW() - INTERVAL 7 DAY";
            break;
        case '30 days':
            $date_limit = "created_at >= NOW() - INTERVAL 30 DAY";
            break;
        case '1 month':
            $date_limit = "created_at >= NOW() - INTERVAL 1 MONTH";
            break;
        case '3 months':
            $date_limit = "created_at >= NOW() - INTERVAL 3 MONTH";
            break;
        case '6 months':
            $date_limit = "created_at >= NOW() - INTERVAL 6 MONTH";
            break;
        case '1 year':
            $date_limit = "created_at >= NOW() - INTERVAL 1 YEAR";
            break;
        case '2 years':
            $date_limit = "created_at >= NOW() - INTERVAL 2 YEAR";
            break;
    }




  

    // SQL query for profit calculation from the profits table
    if ($date_limit) { // Ensure date_limit is set before executing the query
        $profit_query = "SELECT SUM(profit) AS total_profit FROM cosmetic_profits WHERE $date_limit";

        // Execute the query
        $profit_result = mysqli_query($conn, $profit_query);
        if ($profit_result) {
            $profit_row = mysqli_fetch_assoc($profit_result);
            $total_profit = $profit_row['total_profit'] ?? 0;
        } else {
            echo "Error executing query: " . mysqli_error($conn);
        }
    }
}

// Function to calculate profits based on interval
function calculate_profit($conn, $interval) {
    $query = "SELECT SUM(profit) AS total_profit FROM cosmetic_profits WHERE created_at >= NOW() - INTERVAL $interval";

    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result)['total_profit'] ?? 0 : 0;
}

// Calculate daily profits for different timeframes
$today_profit = calculate_profit($conn, '1 DAY'); // Today should consider 24 hours from now
//$yesterday_profit = calculate_profit($conn, 'DATE(created_at) = CURDATE() - INTERVAL 1 DAY'); // Yesterday's profit
$last_7_days_profit = calculate_profit($conn, '7 DAY');
$last_30_days_profit = calculate_profit($conn, '30 DAY');

// Display the results (you may adjust this part as needed)
echo "Total Profit: $" . number_format($total_profit, 2) . "<br>";
//echo "Today's Profit: $" . number_format($today_profit, 2) . "<br>";
//echo "Yesterday's Profit: $" . number_format($yesterday_profit, 2) . "<br>";
echo "Last 7 Days Profit: $" . number_format($last_7_days_profit, 2) . "<br>";
echo "Last 30 Days Profit: $" . number_format($last_30_days_profit, 2) . "<br>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Overview</title>
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/cosmoticp.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
</head>
<body>
<?php include('nav.php'); ?>
<div class="container">
    <h1>Cosmetic Profit Overview</h1>
    <form method="POST" action="">
        <label for="time_frame">Select Time Frame:</label>
        <select name="time_frame" id="time_frame">
            <option value="24 hour">Last 24 Hour</option>
            <option value="7 days">Last 7 Days</option>
            <option value="30 days">Last 30 Days</option>
            <option value="1 month">Last 1 Month</option>
            <option value="3 months">Last 3 Months</option>
            <option value="6 months">Last 6 Months</option>
            <option value="1 year">Last 1 Year</option>
            <option value="2 years">Last 2 Years</option>
        </select>
        <button type="submit" name="calculate_profit">Get Profit</button>
    </form>

    <div class="cards">
        <div class="card">
            <h3>Total Profit</h3>
            <p>Birr: <?php echo number_format($total_profit, 2); ?></p>
        </div>
      
      <!--  <div class="card">
            <h3>Yesterday's Profit</h3>
            <p>Birr: <?php echo number_format($yesterday_profit, 2); ?></p>
        </div>-->
        <div class="card">
            <h3>Last 7 Days Profit</h3>
            <p>Birr: <?php echo number_format($last_7_days_profit, 2); ?></p>
        </div>
        <div class="card">
            <h3>Last 30 Days Profit</h3>
            <p>Birr: <?php echo number_format($last_30_days_profit, 2); ?></p>
        </div>
    </div>

    <div class="charts">
        <canvas id="profitChart"></canvas>
        <canvas id="profitPieChart"></canvas>
    </div>
    

    <script>
        // Data for the charts
        const labels = ['Today', 'Yesterday', 'Last 7 Days', 'Last 30 Days'];
        const data = {
            labels: labels,
            datasets: [{
                label: 'Profit (Birr)',
                data: [
                    <?php echo number_format($today_profit, 2, '.', ''); ?>,
                    <?php echo number_format($yesterday_profit, 2, '.', ''); ?>,
                    <?php echo number_format($last_7_days_profit, 2, '.', ''); ?>,
                    <?php echo number_format($last_30_days_profit, 2, '.', ''); ?>
                ],
                backgroundColor: ['#4caf50', '#ff9800', '#03a9f4', '#9c27b0'], // Colors for each section
                borderColor: ['#388e3c', '#f57c00', '#0288d1', '#7b1fa2'],
                borderWidth: 2,
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Profit Overview'
                    }
                }
            },
        };

        const profitChart = new Chart(
            document.getElementById('profitChart'),
            config
        );

        // Pie chart configuration
        const pieConfig = {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Profit Distribution',
                    data: [
                        <?php echo number_format($today_profit, 2, '.', ''); ?>,
                        <?php echo number_format($yesterday_profit, 2, '.', ''); ?>,
                        <?php echo number_format($last_7_days_profit, 2, '.', ''); ?>,
                        <?php echo number_format($last_30_days_profit, 2, '.', ''); ?>
                    ],
                    backgroundColor: ['#4caf50', '#ff9800', '#03a9f4', '#9c27b0'], // Colors for each section
                    borderColor: ['#388e3c', '#f57c00', '#0288d1', '#7b1fa2'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Profit Distribution Pie Chart'
                    }
                }
            },
        };

        const profitPieChart = new Chart(
            document.getElementById('profitPieChart'),
            pieConfig
        );
    </script>
  
</div>
<!-- Footer -->
<footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>

</body>
</html>

