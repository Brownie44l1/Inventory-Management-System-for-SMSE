<?php
// Include database connection
include '../build/config/db_conn.php';

// Fetch products from the database
$product_query = "SELECT id, product_name, price FROM product";
$product_result = mysqli_query($conn, $product_query);

// Fetch company settings
$company_query = "SELECT vat_charge, fee FROM company LIMIT 1";
$company_result = mysqli_query($conn, $company_query);
$company_settings = mysqli_fetch_assoc($company_result);

// Use isset() to check if the keys exist, and provide default values if they don't
$vat_percentage = isset($company_settings['vat_charge']) ? $company_settings['vat_charge'] : 0;
$default_fee = isset($company_settings['fee']) ? $company_settings['fee'] : 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_address = mysqli_real_escape_string($conn, $_POST['customer_address']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $order_date = $_POST['order_date'] . ' ' . $_POST['order_time'];
    $gross_amount = $_POST['gross_amount'];
    $vat_amount = $_POST['vat_amount'];
    $discount = $_POST['discount'];
    $net_amount = $_POST['net_amount'];

    // Insert into orders table
    $order_query = "INSERT INTO orders (customer_name, customer_address, customer_phone, order_date, gross_amount, vat_amount, discount, net_amount) 
                    VALUES ('$customer_name', '$customer_address', '$customer_phone', '$order_date', '$gross_amount', '$vat_amount', '$discount', '$net_amount')";
    
    if (mysqli_query($conn, $order_query)) {
        $order_id = mysqli_insert_id($conn);

        // Insert order items
        for ($i = 0; $i < count($_POST['product']); $i++) {
            $product_id = mysqli_real_escape_string($conn, $_POST['product'][$i]);
            $quantity = mysqli_real_escape_string($conn, $_POST['quantity'][$i]);
            $fee = mysqli_real_escape_string($conn, $_POST['fee'][$i]);
            $price = mysqli_real_escape_string($conn, $_POST['price'][$i]);

            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, fee, amount) 
                           VALUES ('$order_id', '$product_id', '$quantity', '$fee', '$price')";
            mysqli_query($conn, $item_query);
        }

        echo "<script>alert('Order created successfully!'); window.location.href='order.php';</script>";
    } else {
        echo "<script>alert('Error creating order. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
    <link rel="stylesheet" href="../../dist/output.css">
    <link rel="stylesheet" href="../build/css/overflow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-white font-sans" data-vat-percentage="<?php echo $vat_percentage; ?>">
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
                <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-200 border-b border-gray-300">
                        <h1 class="text-xl font-bold">Add Order</h1>
                    </div>
                    <form class="p-6" method="post">
                        <div class="mb-6 flex justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                                <input type="date" name="order_date" class="border rounded px-2 py-1" value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time:</label>
                                <input type="time" name="order_time" class="border rounded px-2 py-1" value="<?php echo date('H:i'); ?>" readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name:</label>
                                <input type="text" name="customer_name" class="w-full border rounded px-2 py-1" placeholder="John Doe" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Address:</label>
                                <input type="text" name="customer_address" class="w-full border rounded px-2 py-1" placeholder="Madrid" required>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone:</label>
                            <input type="tel" name="customer_phone" class="w-full border rounded px-2 py-1" placeholder="845684434" required>
                        </div>
                        <div class="mb-6">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="text-left px-2 py-1">Product</th>
                                        <th class="text-left px-2 py-1">Qty</th>
                                        <th class="text-left px-2 py-1">Fee</th>
                                        <th class="text-left px-2 py-1">Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="order-items">
                                    <tr>
                                        <td class="px-2 mr-3 py-1 w-48">
                                            <select name="product[]" class="w-full border rounded px-2 py-1" onchange="updatePrice(this)" required>
                                                <option value="">Select a product</option>
                                                <?php while ($row = mysqli_fetch_assoc($product_result)) : ?>
                                                    <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['price']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </td>
                                        <td class="px-2 py-1"><input type="number" name="quantity[]" class="w-full border rounded px-2 py-1" value="1" onchange="updateAmount(this)" required></td>
                                        <td class="px-2 py-1"><input type="number" name="fee[]" class="w-full border rounded px-2 py-1" value="<?php echo $default_fee; ?>" readonly></td>
                                        <td class="px-2 py-1"><input type="number" name="price[]" class="w-full border rounded px-2 py-1" readonly></td>
                                        <td class="px-2 py-1"><button type="button" class="text-red-500" onclick="removeRow(this)">×</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="mt-2 text-blue-500" onclick="addRow()">+</button>
                        </div>
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span>Gross Amount</span>
                                <span id="gross-amount">0.00</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Vat <?php echo $vat_percentage; ?>%</span>
                                <span id="vat-amount">0.00</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Discount</span>
                                <input type="number" name="discount" class="border rounded px-2 py-1 w-20" placeholder="Discount" onchange="updateTotals()" value="0">
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>Net Amount</span>
                                <span id="net-amount">0.00</span>
                            </div>
                            <input type="hidden" name="gross_amount" id="gross-amount-input">
                            <input type="hidden" name="vat_amount" id="vat-amount-input">
                            <input type="hidden" name="net_amount" id="net-amount-input">
                        </div>
                        <div class="flex justify-start">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Create Order</button>
                            <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded" onclick="history.back()">Back</button>
                        </div>
                    </form>
                </div>
            </main>
            <!-- Footer -->
            <div class="w-full flex items-center justify-center">
                <?php include '../build/include/footer.php'; ?>
            </div>
        </div>
    </div>
    <script src="../build/javascript/script.js"></script>
    <script src="../build/javascript/order.js"></script>
</body>
</html>