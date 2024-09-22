<?php
include '../build/config/db_conn.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the action is to delete an attribute
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        // Retrieve and validate the attribute ID
        $attributeId = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($attributeId > 0) {
            $query = "DELETE FROM attribute WHERE attribute_id = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $attributeId);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(["success" => true, "message" => "Attribute deleted successfully."]);
                    } else {
                        echo json_encode(["success" => false, "message" => "Attribute not found or already deleted."]);
                    }
                } else {
                    echo json_encode(["success" => false, "message" => "Error executing query: " . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(["success" => false, "message" => "Failed to prepare the SQL statement."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid attribute ID."]);
        }
        $conn->close();
        exit;
    }

    // For add/edit operations
    $attributeId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $attributeName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    // Validate attribute name
    if (empty($attributeName)) {
        echo json_encode(["success" => false, "message" => "Attribute name cannot be empty"]);
        exit;
    }

    if ($attributeId > 0) {
        // Edit existing attribute
        $query = "UPDATE attribute SET attribute_name = ?, status = ? WHERE attribute_id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sii", $attributeName, $status, $attributeId);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Attribute updated successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error updating attribute: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Failed to prepare update query."]);
        }
    } else {
        // Insert new attribute
        $query = "INSERT INTO attribute (attribute_name, status) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("si", $attributeName, $status);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Attribute added successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error adding attribute: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Failed to prepare insert query."]);
        }
    }

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
                $totalQuery = "SELECT COUNT(*) AS total FROM attribute";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);
                    $sql = "SELECT a.attribute_id, a.attribute_name, a.status, COUNT(av.attribute_id) as value_count 
                            FROM attribute a 
                            LEFT JOIN attribute_value av ON a.attribute_id = av.attribute_id 
                            GROUP BY a.attribute_id, a.attribute_name, a.status
                            LIMIT $limit OFFSET $offset";
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
                <button id="addAttributeButton" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Add Attribute
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
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Attribute Name</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Total Value</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                $statusLabel = $row['status'] == 1 ? 'Active' : 'Inactive';
                                $statusClass = $row['status'] == 1 ? 'bg-green-700' : 'bg-orange-600';
                                $attributeId = $row['attribute_id']; 
                                $attributeName = htmlspecialchars($row['attribute_name'], ENT_QUOTES);
                                $attributeStatus = $row['status'];
                                ?>
                                <tr class='border-b'>
                                    <td class='px-4 py-2 text-sm text-black font-medium'><?php echo $attributeName; ?></td>
                                    <td class='px-4 py-2 text-sm text-black font-medium'><?php echo $row['value_count']; ?></td>
                                    <td class='px-4 py-2 text-sm'>
                                        <span class='<?php echo $statusClass; ?> text-white font-bold px-2 py-1 rounded-md text-xs'><?php echo $statusLabel; ?></span>
                                    </td>
                                    <td class='px-4 py-2 text-left'>
                                        <a href='./attributes_value.php?attribute_id=<?php echo $attributeId; ?>' class='text-slate-800 hover:text-slate-900 bg-slate-200 py-2 px-2 border border-slate-400 mr-2 text-xs addValueLink'>
                                            <i class='fa-solid fa-plus text-xs mr-2'></i>Add value
                                        </a>

                                        <!-- Edit Button -->
                                        <button class='edit-btn text-slate-500 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400 mr-2' 
                                            data-id='<?php echo $attributeId; ?>' 
                                            data-name='<?php echo $attributeName; ?>' 
                                            data-status='<?php echo $attributeStatus; ?>'>
                                            <i class='fa-solid fa-pen text-xs'></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                            data-id='<?php echo $attributeId; ?>' 
                                            data-name='<?php echo $attributeName; ?>' 
                                            data-status='<?php echo $attributeStatus; ?>'>
                                            <i class='fas fa-trash-alt text-xs'></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // If no data is available
                            echo "<tr>
                                    <td class='px-4 py-4 border-b text-center text-sm text-gray-600' colspan='4'>No data available in table</td>
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

            <!-- Add Attribute Modal -->
            <div id="addAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Add Attribute</h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="attributeForm" action="attributes.php" method="POST">
                        <div class="mb-4">
                            <label for="attributeName" class="block text-sm font-semibold mb-2">Attribute Name</label>
                            <input type="text" id="attributeName" name="name" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500" placeholder="Enter attribute name">
                            <p class="text-red-500 text-sm mt-1 hidden" id="attributeNameError">The attribute name field is required.</p>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-semibold mb-2">Status</label>
                            <select id="status" name="status" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Attribute Modal -->
            <div id="editAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Edit Attribute</h2>
                        <button id="editCloseModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="editAttributeForm" method="POST" action="attributes.php?action=edit">
                        <input type="hidden" id="attributeId" name="id" value=""> <!-- Hidden field for the attribute ID -->
                        
                        <div class="mb-4">
                            <label for="editAttributeName" class="block text-sm font-semibold mb-2">Attribute Name</label>
                            <input type="text" id="editAttributeName" name="name" value="" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-red-500 text-sm mt-1 hidden" id="editAttributeNameError">The attribute name field is required.</p>
                        </div>

                        <div class="mb-4">
                            <label for="editStatus" class="block text-sm font-semibold mb-2">Status</label>
                            <select id="editStatus" name="status" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="editCloseModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!--Delete Attribute Modal-->
            <div id="deleteAttributeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" style="display: none;">
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
                        <button id="confirmDeleteBtn" class="px-3 py-1 bg-red-600 text-white rounded delete-btn" data-id="1">Delete</button>
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
  <script src="../build/javascript/attribute.js"></script>
</body>
</html>