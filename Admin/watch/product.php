<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
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
                $totalQuery = "SELECT COUNT(*) AS total FROM brands";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);
                $sql = "SELECT id, product_name, sku, price, quantity, store, availability FROM product LIMIT $limit     OFFSET $offset";
                $result = $conn->query($sql);
            ?>

            <!-- Head -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold mb-6">Manage Products</h1>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Products</span>
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Product Name</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">SKU</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Price</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Qty</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Store</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Availabilty</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php 
                                if ($result && $result->num_rows > 0) {
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='border-b'>
                                                    <td class='px-4 py-2 text-sm text-black font-medium'>" . htmlspecialchars($row['product_name']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>" . htmlspecialchars($row['sku']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>" . htmlspecialchars($row['price']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>" . htmlspecialchars($row['quantity']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>" . htmlspecialchars($row['store']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>" . htmlspecialchars($row['availability']) . "</td>
                                                    <td class='px-4 py-2 text-sm'>
                                                        <button class='edit-btn text-slate-500 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400 mr-1' 
                                                            data-id='" . $row['id'] . "'
                                                            data-action='edit'>
                                                            <i class='fa-solid fa-pen text-xs'></i>
                                                        </button>
                                                        <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                                            data-id='" . $row['id'] . "' 
                                                            data-action='delete'>
                                                            <i class='fas fa-trash-alt text-xs'></i>
                                                        </button>
                                                    </td>
                                                </tr>";
                                    }
                                } else {
                                    // If no data is available
                                    echo "<tr>
                                            <td class='px-4 py-4 border-b text-center text-sm text-gray-600' colspan='7'>No data available in table</td>
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
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.edit-btn, .delete-btn');
        
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const action = this.getAttribute('data-action');
                
                // Redirect to product-create.php with parameters
                window.location.href = `product-create.php?action=${action}&id=${id}`;
            });
        });
    });
</script>
</body>
</html>