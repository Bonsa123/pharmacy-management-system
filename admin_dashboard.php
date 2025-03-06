<?php
session_start();
include('db_connection.php');
include('permissions.php');
// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
}

// Function to execute query and handle errors
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return $result;
}

// Fetch total number of pharmacists
$sql_pharmacists = "SELECT COUNT(*) AS total_Employee FROM employees";
$result_pharmacists = executeQuery($conn, $sql_pharmacists);
$row_pharmacists = $result_pharmacists->fetch_assoc();

// Fetch total number of pharmacy companies
$sql_companies = "SELECT COUNT(*) AS total_companies FROM pharmacy_company";
$result_companies = executeQuery($conn, $sql_companies);
$row_companies = $result_companies->fetch_assoc();

// Fetch total number of medicines
$sql_medicines = "SELECT COUNT(*) AS total_medicines FROM medicine";
$result_medicines = executeQuery($conn, $sql_medicines);
$row_medicines = $result_medicines->fetch_assoc();

// Fetch total number of cosmetics
$sql_cosmetics = "SELECT COUNT(*) AS total_cosmetics FROM cosmetics";
$result_cosmetics = executeQuery($conn, $sql_cosmetics);
$row_cosmetics = $result_cosmetics->fetch_assoc();

// Fetch today's total sales (medicines + cosmetics)
$sql_today_sales = "SELECT SUM(total_amount) AS total_today_sales FROM (
                        SELECT total_amount FROM sales WHERE DATE(sale_date) = CURDATE()
                        UNION ALL
                        SELECT total_amount FROM cosmetic_sales WHERE DATE(sale_date) = CURDATE()
                    ) AS combined_sales";
$result_today_sales = executeQuery($conn, $sql_today_sales);
$row_today_sales = $result_today_sales->fetch_assoc();

// Fetch yesterday's total sales
$sql_yesterday_sales = "SELECT SUM(total_amount) AS total_yesterday_sales FROM (
                            SELECT total_amount FROM sales WHERE DATE(sale_date) = CURDATE() - INTERVAL 1 DAY
                            UNION ALL
                            SELECT total_amount FROM cosmetic_sales WHERE DATE(sale_date) = CURDATE() - INTERVAL 1 DAY
                        ) AS combined_sales";
$result_yesterday_sales = executeQuery($conn, $sql_yesterday_sales);
$row_yesterday_sales = $result_yesterday_sales->fetch_assoc();




// Fetch total sales in the last 7 days
$sql_week_sales = "SELECT SUM(total_amount) AS total_week_sales FROM (
                        SELECT total_amount FROM sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY
                        UNION ALL
                        SELECT total_amount FROM cosmetic_sales WHERE sale_date >= CURDATE() - INTERVAL 7 DAY
                    ) AS combined_sales";
$result_week_sales = executeQuery($conn, $sql_week_sales);
$row_week_sales = $result_week_sales->fetch_assoc();



// Fetch total sales in the last 6 month
$sql_halfyear_sales = "SELECT SUM(total_amount) AS total_halfyear_sales FROM (
    SELECT total_amount FROM sales WHERE sale_date >= CURDATE() - INTERVAL 6 MONTH
    UNION ALL
    SELECT total_amount FROM cosmetic_sales WHERE sale_date >= CURDATE() - INTERVAL 6 MONTH
) AS combined_sales";
$result_halfyear_sales = executeQuery($conn, $sql_halfyear_sales);
$row_halfyear_sales = $result_halfyear_sales->fetch_assoc();

// Fetch total sales in the last 1 year
$sql_oneyear_sales = "SELECT SUM(total_amount) AS total_oneyear_sales FROM (
    SELECT total_amount FROM sales WHERE sale_date >= CURDATE() - INTERVAL 12 MONTH
    UNION ALL
    SELECT total_amount FROM cosmetic_sales WHERE sale_date >= CURDATE() - INTERVAL 12 MONTH
) AS combined_sales";
$result_oneyear_sales = executeQuery($conn, $sql_oneyear_sales);
$row_oneyear_sales = $result_oneyear_sales->fetch_assoc();

// Fetch sales data for the chart (last 30 days)
$sql_sales_chart = "SELECT DATE(sale_date) AS date, SUM(total_amount) AS total_sales 
                     FROM (
                        SELECT sale_date, total_amount FROM sales WHERE sale_date >= CURDATE() - INTERVAL 30 DAY
                        UNION ALL
                        SELECT sale_date, total_amount FROM cosmetic_sales WHERE sale_date >= CURDATE() - INTERVAL 30 DAY
                     ) AS combined_sales
                     GROUP BY DATE(sale_date)";
$result_sales_chart = executeQuery($conn, $sql_sales_chart);
$sales_data = array();
while ($row = $result_sales_chart->fetch_assoc()) {
    $sales_data[] = array('date' => $row['date'], 'total_sales' => $row['total_sales']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    
    <link rel="stylesheet" href="styles/navbar.css">

</head>
<body>
<?php include('nav.php'); ?>


    <div class="main-content">
        <h1> OBED</h1>
        <h2>Online Deliveries</h2>
        <div class="delivery-buttons">
            <a href="manage_deliveries.php" class="btn">View Delivery Orders</a>
            <a href="add_delivery.php" class="btn">Add New Delivery Order</a>
        </div>
        <p>A Quick Data Overview of Different Tasks </p>

        <div class="cards">
            <div class="card">
                <h3>Total Pharmacists </h3>
                <p><?php echo $row_pharmacists['total_Employee']; ?></p>
               
            </div>
            <div class="card">
                <h3>Total Companies</h3>
                <p><?php echo $row_companies['total_companies']; ?></p>
            </div>
            <div class="card">
                <h3>Total Medicines</h3>
                <p><?php echo $row_medicines['total_medicines']; ?></p>
            </div>
            <div class="card">
                <h3>Total Cosmetics</h3>
                <p><?php echo $row_cosmetics['total_cosmetics']; ?></p>
            </div>
            <div class="card">
                <h3>Today's Sale</h3>
                <p>Birr: <?php echo $row_today_sales['total_today_sales']; ?></p>
            </div>
            <div class="card">
                <h3>Yesterday's Sale</h3>
                <p>Birr: <?php echo $row_yesterday_sales['total_yesterday_sales']; ?></p>
            </div>
            <div class="card">
                <h3>Last 7 Days Sale</h3>
                <p>Birr: <?php echo $row_week_sales['total_week_sales']; ?></p>
            </div>
            <div class="card">
                <h3>Last Six(6) Month's Sale</h3>
                <p>Birr: <?php echo $row_halfyear_sales['total_halfyear_sales']; ?></p>
            </div>

            <div class="card">
                <h3>Last 1 Year Sale</h3>
                <p>Birr: <?php echo $row_oneyear_sales['total_oneyear_sales']; ?></p>
            </div>
        </div>

        <div class="chart-container">
    <div class="sales-chart">
        <canvas id="sales-chart" width="600" height="400"></canvas>
    </div>
    
    <div class="overview-chart">
        <canvas id="overview-chart" width="300" height="200" style="margin-top: 0px;"></canvas>
    </div>
    
    <div class="daily-sales-chart">
        <canvas id="daily-sales-chart" width="300" height="200" style="margin-top: 0px;"></canvas>
    </div>  
    
    <div class="sales-chart">
        <canvas id="sales-chart-2" width="600" height="400"></canvas>
    </div>
    
    <div class="overview-chart">
        <canvas id="overview-chart-2" width="300" height="200" style="margin-top: 0px;"></canvas>
    </div>
    
    <div class="daily-sales-chart">
        <canvas id="daily-sales-chart-2" width="300" height="200" style="margin-top: 0px;"></canvas>
    </div>  
</div>

</div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
    <script>
        
        document.addEventListener('DOMContentLoaded', (event) => {
            // Existing sales chart code...
            const salesDataLabels = <?php echo json_encode(array_column($sales_data, 'date')); ?>;
            const salesDataValues = <?php echo json_encode(array_column($sales_data, 'total_sales')); ?>;

            if (salesDataLabels.length === 0 || salesDataValues.length === 0) {
                console.error('Sales data is empty or not available.');
                return;
            }

            // Data for the general sales chart (last 30 days)
            const salesCtx = document.getElementById('sales-chart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesDataLabels,
                    datasets: [{
                        label: 'Total Sales (Medicines + Cosmetics)',
                        data: salesDataValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Blue gradient fill
                        borderColor: 'rgba(54, 162, 235, 1)', // Blue border color
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Blue points
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.4, // Smooth curves
                        cubicInterpolationMode: 'monotone'
                    }]
                },
                options: {
                    plugins: {
                        subtitle: {
                            display: true,
                            text: 'Sales data for the last 30 days',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#333'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Total Sales: Birr ${tooltipItem.raw.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500, // Animation duration
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#ddd'
                            },
                            ticks: {
                                color: '#333'
                            }
                        }
                    }
                }
            });

            // Data for the pie chart (total overview)
            const overviewData = {
                labels: ['employees', 'Companies', 'Medicines', 'Cosmetics'],
                datasets: [{
                    data: [
                        <?php echo $row_pharmacists['total_Employee']; ?>,
                        <?php echo $row_companies['total_companies']; ?>,
                        <?php echo $row_medicines['total_medicines']; ?>,
                        <?php echo $row_cosmetics['total_cosmetics']; ?>
                    ],
                    backgroundColor: ['#4caf50', '#ff9800', '#03a9f4', '#9c27b0'], // Colors for each section
                    borderColor: ['#388e3c', '#f57c00', '#0288d1', '#7b1fa2'],
                    borderWidth: 1
                }]
            };

            const overviewCtx = document.getElementById('overview-chart').getContext('2d');
            const overviewChart = new Chart(overviewCtx, {
                type: 'pie',
                data: overviewData,
                options: {
                    plugins: {
                        subtitle: {
                            display: true,
                            text: 'Overview of Total Entities',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#333'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${overviewData.labels[tooltipItem.dataIndex]}: ${overviewData.datasets[0].data[tooltipItem.dataIndex]}`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500, // Animation duration
                        easing: 'easeOutQuart'
                    }
                }
            });

            // Data for the daily sales pie chart
            const dailySalesData = {
                labels: ['Today\'s Sales', 'Yesterday\'s Sales', 'Last 7 Days Sales'],
                datasets: [{
                    data: [
                        <?php echo $row_today_sales['total_today_sales']; ?>,
                        <?php echo $row_yesterday_sales['total_yesterday_sales']; ?>,
                        <?php echo $row_week_sales['total_week_sales']; ?>
                    ],
                    backgroundColor: ['#ff5722', '#8bc34a', '#00bcd4'], // Colors for each section
                    borderColor: ['#e64a19', '#689f38', '#0097a7'],
                    borderWidth: 1
                }]
            };

            const dailySalesCtx = document.getElementById('daily-sales-chart').getContext('2d');
            const dailySalesChart = new Chart(dailySalesCtx, {
                type: 'pie',
                data: dailySalesData,
                options: {
                    plugins: {
                        subtitle: {
                            display: true,
                            text: 'Sales Breakdown: Today, Yesterday, Last 7 Days',
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#333'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${dailySalesData.labels[tooltipItem.dataIndex]}: Birr ${dailySalesData.datasets[0].data[tooltipItem.dataIndex].toFixed(2)}`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500, // Animation duration
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
       <!-- Footer -->
       <footer class="footer">
        <p>&copy; 2024 Pharmacy Management System</p>
      
       

    </footer>
   



</body>
</html>
