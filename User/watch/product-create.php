<?php
include '../build/config/db_conn.php';

// Fetch colors
$colors = $conn->query("SELECT value FROM attribute_value WHERE attribute_id='4'");
$colorOptions = [];
if ($colors) {
    while ($row = $colors->fetch_assoc()) {
        $colorOptions[] = $row['value'];
    }
}

// Fetch sizes
$sizes = $conn->query("SELECT value FROM attribute_value WHERE attribute_id='2'");
$sizeOptions = [];
if ($sizes) {
    while ($row = $sizes->fetch_assoc()) {
        $sizeOptions[] = $row['value'];
    }
}

// Fetch brands
$brands = $conn->query("SELECT name FROM brands");
$brandOptions = [];
if ($brands) {
    while ($row = $brands->fetch_assoc()) {
        $brandOptions[] = $row['name'];
    }
}

// Fetch categories
$categories = $conn->query("SELECT name FROM category");
$categoryOptions = [];
if ($categories) {
    while ($row = $categories->fetch_assoc()) {
        $categoryOptions[] = $row['name'];
    }
}

// Fetch stores
$stores = $conn->query("SELECT name FROM store");
$storeOptions = [];
if ($stores) {
    while ($row = $stores->fetch_assoc()) {
        $storeOptions[] = $row['name'];
    }
}

// Initialize variables
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$productData = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $productName = $_POST['product_name'];
    $sku = $_POST['sku'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $store = $_POST['store'];
    $availability = $_POST['availability'];

    if ($action === 'add') {
        // Insert new product
        $insertQuery = "INSERT INTO product (product_name, sku, price, quantity, description, color, size, brand, category, store, availability) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssdsissssss", $productName, $sku, $price, $quantity, $description, $color, $size, $brand, $category, $store, $availability);
    } elseif ($action === 'edit') {
        // Update existing product
        $updateQuery = "UPDATE product SET product_name=?, sku=?, price=?, quantity=?, description=?, color=?, size=?, brand=?, category=?, store=?, availability=? WHERE id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssdsissssssi", $productName, $sku, $price, $quantity, $description, $color, $size, $brand, $category, $store, $availability, $productId);
    }

    if ($stmt->execute()) {
        header("Location: product.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    } elseif ($action === 'delete' && $productId > 0) {
        // Delete product
        $deleteQuery = "DELETE FROM product WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            header("Location: product.php");
            exit();
        } else {
            echo "Error deleting product: " . $stmt->error;
        }

        $stmt->close();
    } elseif ($action === 'edit' && $productId > 0) {
        // Fetch product data for editing
        $fetchQuery = "SELECT * FROM product WHERE id = ?";
        $stmt = $conn->prepare($fetchQuery);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $productData = $result->fetch_assoc();
        $stmt->close();

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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

        <main class="w-full max-w-4xl p-6 mt-3 mx-auto">
            <h1 class="text-xl font-semibold mb-6"><?php echo ucfirst($action); ?> Product</h1>
            <div class="flex justify-between items-center mb-4">
                <button id="addProductButton" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Add Product
                </button>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <a href="./product.php" class="hover:underline">Products</a> &gt; <span><?php echo ucfirst($action); ?> Product</span>
                </nav>
                </nav>
            </div>

            <div class="py-5 px-8 mb-4 bg-slate-100">
                <!-- Form -->
                <form action="product-create.php?action=<?php echo $action; ?><?php echo $productId ? "&id=$productId" : ''; ?>" method="POST">

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Image</label>
                        <div class="flex items-center">
                            <button class="p-2 bg-blue-500 text-white rounded-md mr-2"><i class="fas fa-folder"></i></button>
                            <span class="text-gray-600">Upload Image</span>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Product name</label>
                        <input type="text" name="product_name" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter Product Name" required value="<?php echo $productData ? htmlspecialchars($productData['product_name']) : ''; ?>">
                    </div>

                    <!-- SKU -->
                    <div class="mb-4">
                        <label class="block text-gray-700">SKU</label>
                        <input type="text" name="sku" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter SKU" required value="<?php echo $productData ? htmlspecialchars($productData['sku']) : ''; ?>">
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Price</label>
                        <input type="text" name="price" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter Price" required value="<?php echo $productData ? htmlspecialchars($productData['price']) : ''; ?>">
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Qty</label>
                        <input type="text" name="quantity" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter Quantity" required value="<?php echo $productData ? htmlspecialchars($productData['quantity']) : ''; ?>">
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Description</label>
                        <textarea name="description" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter description"><?php echo $productData ? htmlspecialchars($productData['description']) : ''; ?></textarea>
                    </div>

                    <!-- Color -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Color</label>
                        <select name="color" class="w-full p-2 border border-gray-300 rounded-md">
                            <?php foreach ($colorOptions as $color): ?>
                                <option value="<?= htmlspecialchars($color) ?>" <?php echo ($productData && $productData['color'] == $color) ? 'selected' : ''; ?>><?= htmlspecialchars($color) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Size -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Size</label>
                        <select name="size" class="w-full p-2 border border-gray-300 rounded-md">
                            <?php foreach ($sizeOptions as $size): ?>
                                <option value="<?= htmlspecialchars($size) ?>" <?php echo ($productData && $productData['size'] == $size) ? 'selected' : ''; ?>><?= htmlspecialchars($size) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Brands -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Brands</label>
                        <select name="brand" class="w-full p-2 border border-gray-300 rounded-md">
                            <?php foreach ($brandOptions as $brand): ?>
                                <option value="<?= htmlspecialchars($brand) ?>" <?php echo ($productData && $productData['brand'] == $brand) ? 'selected' : ''; ?>><?= htmlspecialchars($brand) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Category</label>
                        <select name="category" class="w-full p-2 border border-gray-300 rounded-md">
                            <?php foreach ($categoryOptions as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>" <?php echo ($productData && $productData['category'] == $category) ? 'selected' : ''; ?>><?= htmlspecialchars($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Store -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Store</label>
                        <select name="store" class="w-full p-2 border border-gray-300 rounded-md">
                            <?php foreach ($storeOptions as $store): ?>
                                <option value="<?= htmlspecialchars($store) ?>" <?php echo ($productData && $productData['store'] == $store) ? 'selected' : ''; ?>><?= htmlspecialchars($store) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Availability -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Availability</label>
                        <select name="availability" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="Yes" <?php echo ($productData && $productData['availability'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo ($productData && $productData['availability'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <div class="flex items-center mt-6">
                        <?php if ($action !== 'delete'): ?>
                            <button type="submit" class="bg-blue-500 mr-4 text-white p-2 rounded-md"><?php echo $action === 'edit' ? 'Update' : 'Save'; ?> Product</button>
                        <?php endif; ?>
                        <a href="product.php" class="bg-orange-500 text-white p-2 rounded-md">Back</a>
                    </div>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <div class="w-full flex items-center justify-center">
            <?php include '../build/include/footer.php'; ?>
        </div>
    </div>
  <script>
      document.getElementById('addProductButton').addEventListener('click', function() {
          // Logic for handling button click (if needed)
          console.log('Add Product button clicked');
      });
  </script>
  <script src="../build/javascript/script.js"></script>
</body>
</html>
