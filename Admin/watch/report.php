<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <link rel="stylesheet" href="../../dist/output.css">
    <link rel="stylesheet" href="../build/css/overflow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body class="bg-white font-sans">
    <div class="flex min-h-screen">
       <!-- Sidebar -->
        <div id="sidebar" class="sidebar w-60">
            <?php include '../build/include/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="main-content flex flex-col flex-grow sidebar-expanded">

        <!-- Header -->
        <div>
            <?php include '../build/include/header.php'; ?>
        </div>

        <main class="w-full max-w-4xl p-6 mt-3 mx-auto">
             <div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-200 border-b border-gray-300 flex justify-between items-center">
                    <h1 class="text-xl font-bold">Reports</h1>
                </div>
                
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Total Sales - Report</h2>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
                
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Total Paid Orders - Report Data</h2>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-left px-4 py-2">Month - Year</th>
                                <th class="text-right px-4 py-2">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../build/config/db_conn.php';

                            // Query to get total sales per month
                            $sql = "
                            SELECT 
                                DATE_FORMAT(order_date, '%Y-%m') AS month_year, 
                                SUM(oi.amount) AS total_amount 
                            FROM orders o 
                            JOIN order_items oi ON o.id = oi.order_id
                            WHERE o.order_date BETWEEN '2024-09-01' AND '2025-05-31'
                            GROUP BY month_year 
                            ORDER BY month_year ASC";
                            
                            $result = $conn->query($sql);
                            $months = [];
                            $amounts = [];

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $month_year = $row['month_year'];
                                    $total_amount = $row['total_amount'];

                                    // Populate data for the table
                                    echo "
                                        <tr>
                                            <td class='border-t px-4 py-2'>{$month_year}</td>
                                            <td class='border-t px-4 py-2 text-right'>₦ " . number_format($total_amount, 2) . "</td>
                                        </tr>";

                                    // Store data for the chart
                                    $months[] = $month_year;
                                    $amounts[] = $total_amount;
                                }
                            } else {
                                echo "
                                    <tr>
                                        <td class='border-t px-4 py-2' colspan='2'>No data available</td>
                                    </tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <div class="w-full flex items-center justify-center">
            <?php include '../build/include/footer.php'; ?>
        </div>
    </div>
  <script src="../build/javascript/script.js"></script>
  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'September 2024', 'October 2024', 'November 2024', 'December 2024', 
                'January 2025', 'February 2025', 'March 2025', 'April 2025', 'May 2025'
            ], // Fixed x-axis labels
            datasets: [{
                label: 'Total Sales',
                data: <?php echo json_encode($amounts); ?>,
                backgroundColor: 'rgb(34, 197, 94)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 5000, 
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString(); 
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
  </script>
</body>
</html>
