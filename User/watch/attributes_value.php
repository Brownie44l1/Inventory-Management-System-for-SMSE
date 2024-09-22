<?php
include '../build/config/db_conn.php';

$attribute_id = null;

// Validate incoming request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['attribute_id']) && is_numeric($_GET['attribute_id'])) {
    $attribute_id = intval($_GET['attribute_id']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attributeId']) && is_numeric($_POST['attributeId'])) {
    $attribute_id = intval($_POST['attributeId']);
}

if ($attribute_id === null) {
    error_log("Invalid attribute ID.");
    echo json_encode(['success' => false, 'message' => 'Invalid attribute ID.']);
    exit;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if this is a delete request
    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        // Read the JSON input and convert it to an array
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if the value ID is provided
        if (!isset($data['id'])) {
            echo json_encode(["success" => false, "message" => "No value ID provided."]);
            exit;
        }

        // Retrieve and validate the value ID
        $valueId = intval($data['id']);
        if ($valueId <= 0) {
            echo json_encode(["success" => false, "message" => "Invalid value ID."]);
            exit;
        }

        // Prepare and execute the DELETE statement
        $stmt = $conn->prepare("DELETE FROM attribute_value WHERE id = ? AND attribute_id = ?");
        $stmt->bind_param("ii", $valueId, $attribute_id);

        if ($stmt->execute()) {
            // If deletion was successful
            echo json_encode(["success" => true, "message" => "Attribute value deleted successfully."]);
        } else {
            // If there was an error in the query
            echo json_encode(["success" => false, "message" => "Error deleting attribute value: " . $stmt->error]);
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
        exit;
    }

    // For form submissions (Add/Edit Attribute Value)
    // Retrieve form data
    $valueId = isset($_POST['valueId']) ? intval($_POST['valueId']) : 0;
    $value = isset($_POST['value']) ? trim($_POST['value']) : '';

    // Check if it's an edit operation or a new insert
    if ($valueId > 0) {
        // Edit existing attribute value
        $stmt = $conn->prepare("UPDATE attribute_value SET value = ? WHERE id = ? AND attribute_id = ?");
        $stmt->bind_param("sii", $value, $valueId, $attribute_id);
    } else {
        // Insert new attribute value
        $stmt = $conn->prepare("INSERT INTO attribute_value (value, attribute_id) VALUES (?, ?)");
        $stmt->bind_param("si", $value, $attribute_id);
    }

    // Execute the query and check the result
    if ($stmt->execute()) {
        $message = $valueId > 0 ? "Attribute value updated successfully" : "Attribute value added successfully";
        echo json_encode(["success" => true, "message" => $message]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attributes</title>
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
                $totalQuery = "SELECT COUNT(*) AS total FROM attribute_value WHERE attribute_id = $attribute_id";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);
                $sql = "SELECT * FROM attribute_value WHERE attribute_id = $attribute_id LIMIT $limit OFFSET $offset";
                $result = $conn->query($sql);
            ?>

            <h1 class="text-xl font-semibold mb-6">Manage Attributes</h1>
            <!-- Success Message -->
            <div class="mb-4" id="successMessage" style="display: none;">
                <div class="bg-green-500 w-full text-white flex justify-between py-2 px-5 rounded shadow-xl">
                    <div>
                        <i class="fa-solid fa-circle-check mr-1"></i>
                        Successfully Updated.
                    </div>
                    <button id="closeMsg" class="text-gray-700 hover:text-black">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div> 

            <!-- Add Attribute Button -->
            <div class="flex justify-between items-center mb-4">
                <button id="addAttributeButton" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded" type="button">
                    Add Values
                </button>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Attributes</span>
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Attribute Value</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                $valueId = isset($row['id']) ? $row['id'] : '';
                                $attributeId = isset($row['attribute_id']) ? $row['attribute_id'] : '';
                                $value = isset($row['value']) ? htmlspecialchars($row['value'], ENT_QUOTES) : '';
                                
                                echo "<tr class='border-b'>
                                        <td class='px-4 py-2 text-sm text-black font-medium'>{$value}</td>
                                        <td class='px-4 py-2 text-left'>
                                            <!-- Edit Button -->
                                            <button class='edit-btn text-slate-500 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400 mr-2' 
                                                data-value-id='{$valueId}' 
                                                data-attribute-id='{$attributeId}'
                                                data-attribute-value='{$value}'>
                                                <i class='fa-solid fa-pen text-xs'></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                                data-id='{$valueId}' 
                                                data-attribute='{$attributeId}'>
                                                <i class='fas fa-trash-alt text-xs'></i>
                                            </button>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            // If no data is available
                            echo "<tr>
                                    <td class='px-4 py-4 border-b text-center text-sm text-gray-600' colspan='2'>No data available in table</td>
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
                        <a href="?attribute_id=<?php echo $attribute_id; ?>&page=<?php echo $page - 1; ?>" class="px-3 py-1 border rounded text-sm text-gray-600 hover:bg-gray-100">Previous</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?attribute_id=<?php echo $attribute_id; ?>&page=<?php echo $page + 1; ?>" class="px-3 py-1 border rounded text-sm text-gray-600 hover:bg-gray-100">Next</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add Attribute Modal -->
            <div id="addAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Add Attribute Value</h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="attributeForm" action="attributes_value.php" method="POST">
                        <input type="hidden" id="attributeId" name="attributeId" value="<?php echo $attribute_id; ?>">
                        
                        <div class="mb-6">
                            <label for="attributeValue" class="block text-sm font-semibold mb-2">Attribute Value</label>
                            <input type="text" id="attributeValue" name="value" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500" placeholder="Enter attribute value">
                            <p class="text-red-500 text-sm mt-1 hidden" id="attributeValueError">The attribute value field is required.</p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Attribute Value Modal -->
            <div id="editAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Edit Attribute Value</h2>
                        <button id="editCloseModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="editAttributeForm" method="POST" action="attributes_value.php?action=edit">
                        <input type="hidden" id="valueId" name="valueId">
                        <input type="hidden" id="attributeId" name="attributeId">
                        <div class="mb-4">
                            <label for="editAttributeValue" class="block text-sm font-semibold mb-2">Attribute Value</label>
                            <input type="text" id="editAttributeValue" name="value" value="" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-red-500 text-sm mt-1 hidden" id="editAttributeValueError">The attribute value field is required.</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="editCloseModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Attribute Modal -->
            <div id="deleteAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" style="display: none;" >
                <div class="bg-white rounded-lg w-96 p-4">
                    <div class="flex justify-between items-center py-1 mb-1">
                        <h3 class="text-lg font-semibold">Remove Attribute</h3>
                        <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <p>Do you really want to remove this attribute?</p>
                    </div>
                    <div class="flex justify-end items-center gap-2 mt-2 px-4 py-2">
                        <button id="cancelDeleteBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded">Cancel</button>
                        <button id="confirmDeleteBtn" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <div class="w-full flex items-center justify-center">
            <?php include '../build/include/footer.php'; ?>
        </div>
    </div>
  <script src="../build/javascript/script.js"></script>
  <script src="../build/javascript/attribute_value.js"></script>
  <script src="../build/javascript/edit_attribute.js"></script>
</body>
</html>