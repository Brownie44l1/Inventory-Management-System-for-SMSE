<?php
    include '../build/config/db_conn.php';

    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if this is a delete request
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            // Read the JSON input and convert it to an array
            $data = json_decode(file_get_contents('php://input'), true);

            // Check if the brand ID is provided
            if (!isset($data['id'])) {
                echo json_encode(["success" => false, "message" => "No store ID provided."]);
                exit;
            }

            // Retrieve and validate the brand ID
            $brandId = intval($data['id']);
            if ($brandId <= 0) {
                echo json_encode(["success" => false, "message" => "Invalid store ID."]);
                exit;
            }

            // Prepare and execute the DELETE statement
            $stmt = $conn->prepare("DELETE FROM store WHERE id = ?");
            $stmt->bind_param("i", $brandId);

            if ($stmt->execute()) {
                // If deletion was successful
                echo json_encode(["success" => true, "message" => "Store deleted successfully."]);
            } else {
                // If there was an error in the query
                echo json_encode(["success" => false, "message" => "Error deleting store: " . $stmt->error]);
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
            exit;
        }

        // For form submissions (Add/Edit Brand)
        // Retrieve form data
        $brandId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $brandName = isset($_POST['name']) ? trim($_POST['name']) : '';
        $status = isset($_POST['status']) ? ($_POST['status'] == 'Active' ? 1 : 0) : 0;

        // Validate input
        if (empty($brandName)) {
            echo json_encode(["success" => false, "message" => "Store name cannot be empty"]);
            exit;
        }

        // Check if it's an edit operation or a new insert
        if ($brandId > 0) {
            // Edit existing brand
            $stmt = $conn->prepare("UPDATE store SET name = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sii", $brandName, $status, $brandId);
        } else {
            // Insert new brand
            $stmt = $conn->prepare("INSERT INTO store (name, status) VALUES (?, ?)");
            $stmt->bind_param("si", $brandName, $status);
        }

        // Execute the query and check the result
        if ($stmt->execute()) {
            $message = $brandId > 0 ? "Story updated successfully" : "Store added successfully";
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
    <title>Store</title>
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
                $totalQuery = "SELECT COUNT(*) AS total FROM store";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);
                $sql = "SELECT id, name, status FROM store LIMIT $limit OFFSET $offset";
                $result = $conn->query($sql);
            ?>

            <h1 class="text-xl font-semibold mb-6">Manage Stores</h1>
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
            <!-- Add Brand Button -->
            <div class="flex justify-between items-center mb-4">
                <button id="addBrandButton" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Add Store
                </button>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Stores</span>
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Store Name</th>
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
                                    $brandId = isset($row['id']) ? $row['id'] : '';
                                    echo "<tr class='border-b'>
                                                <td class='px-4 py-2 text-sm text-black font-medium'>{$row['name']}</td>
                                                <td class='px-4 py-2 text-sm'>
                                                    <span class='{$statusClass} text-white font-bold px-2 py-1 rounded-md text-xs'>{$statusLabel}</span>
                                                </td>
                                                <td class='px-4 py-2 text-left'>
                                                    <button class='edit-btn text-slate-500 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400 mr-2' 
                                                        data-id='{$row['id']}' 
                                                        data-name='" . htmlspecialchars($row['name'], ENT_QUOTES) . "' 
                                                        data-status='{$row['status']}'>
                                                        <i class='fa-solid fa-pen text-xs'></i>
                                                    </button>
                                                    
                                                    <!-- Delete Button -->
                                                    <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                                        data-id='{$row['id']}' 
                                                        data-name='" . htmlspecialchars($row['name'], ENT_QUOTES) . "' data-status='{$row['status']}' >
                                                        <i class='fas fa-trash-alt text-xs'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                }
                            } else {
                                // If no data is available
                                echo "<tr>
                                        <td class='px-4 py-4 border-b text-center text-sm text-gray-600' colspan='3'>No data available in table</td>
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

            <!-- Add Store Modal -->
            <div id="addBrandModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Add Store</h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="brandForm" action="store.php" method="POST">
                        <div class="mb-4">
                            <label for="brandName" class="block text-sm font-semibold mb-2">Store Name</label>
                            <input type="text" id="brandName" name="name" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500" placeholder="Enter company name">
                            <p class="text-red-500 text-sm mt-1 hidden" id="brandNameError">The store name field is required.</p>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-semibold mb-2">Status</label>
                            <select id="status" name="status" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div> 

            <!-- Edit Store Modal -->
            <div id="editBrandModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Edit Store</h2>
                        <button id="editCloseModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form id="editBrandForm" method="POST" action="store.php?action=edit">
                        <input type="hidden" id="brandId" name="id" value=""> 
                        
                        <div class="mb-4">
                            <label for="editBrandName" class="block text-sm font-semibold mb-2">Store Name</label>
                            <input type="text" id="editBrandName" name="name" value="" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-red-500 text-sm mt-1 hidden" id="editBrandNameError">The store name field is required.</p>
                        </div>

                        <div class="mb-4">
                            <label for="editStatus" class="block text-sm font-semibold mb-2">Status</label>
                            <select id="editStatus" name="status" class="w-full border-2 p-2 rounded focus:outline-none focus:border-blue-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="editCloseModalBtn" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Brand Modal -->
            <div id="deleteBrandModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" style="display: none;" >
                <div class="bg-white rounded-lg w-96 p-4">
                    <div class="flex justify-between items-center py-1 mb-1">
                        <h3 class="text-lg font-semibold">Remove Stores</h3>
                        <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <p>Do you really want to remove this store?</p>
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
  <script src="../build/javascript/modal.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#editBrandForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            
            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: 'store.php?action=edit',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage();
                        $('#editBrandModal').hide();
                        setTimeout(() => {
                            refreshBrandList(); 
                            location.reload();
                        }, 2000);
                    } else {
                        alert(response.message); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ', status, error); 
                }
            });
        });
        $('#editCloseModalBtn').on('click', function() {
            $('#editBrandModal').hide();
        });
    });
    function showSuccessMessage() {
        const successMessage = document.getElementById('successMessage');
        successMessage.style.display = 'block';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }
    function refreshBrandList() {
   
    }

    const deleteModal = document.getElementById('deleteBrandModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let currentBrandId = null;

    // Handle click on delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            currentBrandId = this.getAttribute('data-id'); 
            console.log('Brand ID:', currentBrandId);
            deleteModal.style.display = 'flex'; 
        });
    });

    // Close modal when clicking the close button or cancel button
    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });
    
    // Handle confirm delete
    confirmDeleteBtn.addEventListener('click', () => {
        if (currentBrandId) {
            console.log('Sending Brand ID:', currentBrandId);
            fetch(`store.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentBrandId }), // Send the brand ID to the server
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        deleteModal.style.display = 'none'; // Hide the modal
                        showSuccessMessage(); // Show success message (you can customize this)
                        setTimeout(() => {
                            location.reload(); // Reload the page after a short delay
                        }, 2000);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
    });
</script>
</body>
</html>