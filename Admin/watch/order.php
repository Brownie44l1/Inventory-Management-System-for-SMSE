<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <link rel="stylesheet" href="../../dist/output.css">
    <link rel="stylesheet" href="../build/css/underflow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-white font-sans">
    <div class="flex min-h-screen">
       <!-- Sidebar -->
        <div id="sidebar" class="sidebar w-60">
            <?php include '../build/include/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="main-content flex flex-col flex-grow sidebar-expanded fixed">

        <!-- Header -->
        <div>
            <?php include '../build/include/header.php'; ?>
        </div>

        <main class="w-full max-w-4xl p-6 mt-3 mx-auto">

            <?php
                include '../build/config/db_conn.php';
                
                // Pagination settings
                $limit = 5;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;
                
                // Total count of records for pagination
                $totalQuery = "SELECT COUNT(*) AS total FROM orders";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);

                // Fetch orders with limit for pagination
                $sql = "
                    SELECT 
                        o.id, 
                        o.customer_name, 
                        o.customer_phone, 
                        o.order_date, 
                        COUNT(oi.id) AS total_products, 
                        SUM(oi.amount) AS total_amount
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    GROUP BY o.id, o.customer_name, o.customer_phone, o.order_date
                    ORDER BY o.order_date DESC
                    LIMIT $limit OFFSET $offset
                ";

                // Execute query
                $result = $conn->query($sql);
            ?>

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold mb-6">Manage Orders</h1>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Orders</span>
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Customer Name</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Customer Phone</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Date & Time</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Total Products</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Total Amount</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if ($result->num_rows > 0) {
                                // Output data of each order row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='border-b'>
                                            <td class='px-4 py-2 text-sm text-black font-medium'>{$row['customer_name']}</td>
                                            <td class='px-4 py-2 text-sm text-black'>{$row['customer_phone']}</td>
                                            <td class='px-4 py-2 text-sm text-black'>" . date('Y-m-d H:i', strtotime($row['order_date'])) . "</td>
                                            <td class='px-4 py-2 text-sm text-black text-center'>{$row['total_products']}</td>
                                            <td class='px-4 py-2 text-sm text-black text-right'>₦ " . number_format($row['total_amount'], 2) . "</td>
                                            <td class='px-4 py-2 text-sm text-left'>
                                                <button class='bg-slate-300 px-2 py-1 border border-slate-400 text-slate-500 hover:text-slate-700 text-right' onclick='deleteOrder({$row['id']})'>
                                                    <i class='fas fa-trash-alt'></i> 
                                                </button>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                // If no data is available
                                echo "<tr>
                                        <td class='px-4 py-4 border-b text-center text-sm text-gray-600' colspan='6'>No data available in table</td>
                                      </tr>";
                            }

                            $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Showing <?php echo min($offset + 1, $totalRecords); ?> to 
                    <?php echo min($offset + $limit, $totalRecords); ?> of 
                    <?php echo $totalRecords; ?> entries
                </p>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 border rounded text-sm text-gray-600 hover:bg-gray-100">Previous</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 border rounded text-sm text-gray-600 hover:bg-gray-100">Next</a>
                    <?php endif; ?>
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
    function deleteOrder(orderId) {
        if (confirm('Are you sure you want to delete this order?')) {
            // You can add your AJAX call or form submission logic here to delete the order
            console.log('Order deleted: ' + orderId);
        }
    }
</script>
</body>
</html>
