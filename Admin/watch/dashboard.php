<?php
session_start();
require_once '../build/config/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// SQL Queries to count data from tables
$sql_brands = "SELECT COUNT(*) AS total_brands FROM brands";
$sql_categories = "SELECT COUNT(*) AS total_categories FROM category";
$sql_elements = "SELECT COUNT(*) AS total_elements FROM attribute";
$sql_values = "SELECT COUNT(*) AS total_value FROM attribute_value";
$sql_products = "SELECT COUNT(*) AS total_products FROM product";
$sql_sales = "SELECT SUM(amount) AS total_sales FROM order_items";
$sql_orders = "SELECT COUNT(DISTINCT order_id) AS total_orders FROM order_items";
$sql_members = "SELECT COUNT(*) AS total_members FROM user_login";

// Execute queries and fetch results
$total_brands = $conn->query($sql_brands)->fetch_assoc()['total_brands'];
$total_categories = $conn->query($sql_categories)->fetch_assoc()['total_categories'];
$total_elements = $conn->query($sql_elements)->fetch_assoc()['total_elements'];
$total_values = $conn->query($sql_values)->fetch_assoc()['total_value'];
$total_products = $conn->query($sql_products)->fetch_assoc()['total_products'];
$total_sales = $conn->query($sql_sales)->fetch_assoc()['total_sales'];
$total_orders = $conn->query($sql_orders)->fetch_assoc()['total_orders'];
$total_members = $conn->query($sql_members)->fetch_assoc()['total_members'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../dist/output.css">
  <link rel="stylesheet" href="../build/css/overflow.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
            <!--First Section-->
            <main class="w-full max-w-4xl p-6 mt-3 mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 pb-8">
                    <div class="bg-purple-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_brands; ?></div>
                        <div class="text-sm">Total Items</div>
                        <div class="mt-2 text-right">
                            <a href="./brand.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>  
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_categories; ?></div>
                        <div class="text-sm">Total Categories</div>
                        <div class="mt-2 text-right">
                            <a href="./category.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-orange-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_elements; ?></div>
                        <div class="text-sm">Total Elements</div>
                        <div class="mt-2 text-right">
                            <a href="./attributes.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>  
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-green-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold">₦<?php echo number_format($total_sales, 2); ?></div>
                        <div class="text-sm">Total Sales</div>
                        <div class="mt-2 text-right">
                            <a href="./report.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-blue-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_products; ?></div>
                        <div class="text-sm">Total Products</div>
                        <div class="mt-2 text-right">
                            <a href="./product.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>    
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-green-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_orders; ?></div>
                        <div class="text-sm">Paid Orders</div>
                        <div class="mt-2 text-right">
                            <a href="./order.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-red-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold">0</div>
                        <div class="text-sm">Unpaid Orders</div>
                        <div class="mt-2 text-right">
                            <a href="./order.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>    
                                More Info
                            </a>
                        </div>
                    </div>
                    <div class="bg-teal-600 text-white p-4 rounded-lg shadow-md">
                        <div class="text-2xl font-bold"><?php echo $total_members; ?></div>
                        <div class="text-sm">Total Users</div>
                        <div class="mt-2 text-right">
                            <a href="./user.php" class="text-xs text-white underline">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                More Info
                            </a>
                        </div>
                    </div>
                </div>

                <!--Second Section-->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mx-auto mt-6 pt-4 pb-8 mb-6">
                    <!-- Item Summary -->
                    <div class="bg-slate-200 p-6 rounded-lg shadow-lg mb-3">
                        <h2 class="text-lg font-semibold mb-4">Item Summary</h2>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col items-center">
                                <div class="text-orange-600">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800"><?php echo $total_brands; ?></div>
                                <div class="text-sm text-slate-600">Quantity in Hand</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="text-blue-600">
                                    <i class="fas fa-truck text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800">20</div>
                                <div class="text-sm text-slate-600">To be received</div>
                            </div>
                        </div>
                    </div>
                    <!-- Product Summary -->
                    <div class="bg-slate-200 p-6 rounded-lg shadow-lg mb-3">
                        <h2 class="text-lg font-semibold mb-4">Elements Summary</h2>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col items-center">
                                <div class="text-green-600">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800"><?php echo $total_elements; ?></div>
                                <div class="text-sm text-slate-600">Number of Categories</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="text-purple-600">
                                    <i class="fas fa-tags text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800"><?php echo $total_values; ?></div>
                                <div class="text-sm text-slate-600">Sub-categories</div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Items -->
                    <div class="bg-slate-200 p-6 rounded-lg shadow-lg mb-4">
                        <h2 class="text-lg font-semibold mb-4">Total Products</h2>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col items-center">
                                <div class="text-red-600">
                                    <i class="fas fa-cube text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800"><?php echo $total_products; ?></div>
                                <div class="text-sm text-slate-600">Sold Products</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="text-green-600">
                                    <i class="fas fa-truck-loading text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-900">5</div>
                                <div class="text-sm text-slate-600">To be sold</div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Assets -->
                    <div class="bg-slate-200 p-6 rounded-lg shadow-lg mb-4">
                        <h2 class="text-lg font-semibold mb-4">Total Revenue</h2>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col items-center">
                                <div class="text-blue-600">
                                    <i class="fas fa-warehouse text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800"><?php echo $total_orders; ?></div>
                                <div class="text-sm text-slate-600">Total Number of Order</div>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="text-red-600">
                                    <i class="fas fa-shipping-fast text-xl"></i>
                                </div>
                                <div class="text-xl font-semibold text-slate-800">8</div>
                                <div class="text-sm text-slate-600">To be delivered</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Footer -->
            <div class="w-full flex items-center justify-center">
                <?php include '../build/include/footer.php'; ?>
            </div>
        </div>
    </div>
    <script src="../build/javascript/script.js"></script>
</body>
</html>