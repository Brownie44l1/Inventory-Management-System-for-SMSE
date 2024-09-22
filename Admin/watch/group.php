<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Groups</title>
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
                
                // Handle deletion if requested
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
                    $deleteId = $_POST['delete_id'];
                    $deleteQuery = "DELETE FROM group_permissions WHERE id = ?";
                    $stmt = $conn->prepare($deleteQuery);
                    $stmt->bind_param("i", $deleteId);
                    
                    if ($stmt->execute()) {
                        // Optionally, you can set a success message here
                        echo "<script>alert('Group deleted successfully.');</script>";
                    } else {
                        echo "<script>alert('Error deleting group: " . $stmt->error . "');</script>";
                    }
                    $stmt->close();
                }
                
                // Pagination settings
                $limit = 5;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;
                
                // Get total number of records
                $totalQuery = "SELECT COUNT(*) AS total FROM group_permissions";
                $totalResult = $conn->query($totalQuery);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);

                // Fetch group permissions
                $sql = "SELECT * FROM group_permissions LIMIT $limit OFFSET $offset";
                $result = $conn->query($sql);
                ?>
                
                <h1 class="text-xl font-semibold mb-6">Manage Groups</h1>

                <!-- Add Group Button -->
                <div class="flex justify-between items-center mb-4">
                    <a href="./group-create.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Add Group
                    </a>
                    <nav class="text-sm text-gray-500">
                        <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Groups</span>
                    </nav>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Group Name</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Users View</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Groups View</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Brands View</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Category View</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    $groupName = htmlspecialchars($row['group_name'], ENT_QUOTES);
                                    $usersView = $row['users_view'] ? "<span class='text-white font-bold px-2 py-1 rounded-md text-xs bg-green-700'>Active</span>" : 'Inactive';
                                    $groupsView = $row['groups_view'] ? "<span class='text-white font-bold px-2 py-1 rounded-md text-xs bg-green-700'>Active</span>" : 'Inactive';
                                    $brandsView = $row['brands_view'] ? "<span class='text-white font-bold px-2 py-1 rounded-md text-xs bg-green-700'>Active</span>" : 'Inactive';
                                    $categoryView = $row['category_view'] ? "<span class='text-white font-bold px-2 py-1 rounded-md text-xs bg-green-700'>Active</span>" : 'Inactive';
                                    $groupId = $row['id'];

                                    echo "<tr class='border-b'>
                                            <td class='px-4 py-2 text-sm text-black font-medium'>{$groupName}</td>
                                            <td class='px-4 py-2 text-sm text-black'>{$usersView}</td>
                                            <td class='px-4 py-2 text-sm text-black'>{$groupsView}</td>
                                            <td class='px-4 py-2 text-sm text-black'>{$brandsView}</td>
                                            <td class='px-4 py-2 text-sm text-black'>{$categoryView}</td>
                                            <td class='px-4 py-2 text-left'>
                                                <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                                    data-id='{$groupId}'>
                                                    <i class='fas fa-trash-alt text-xs'></i>
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

                <!-- Delete Group Modal -->
                <div id="deleteGroupModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" style="display: none;">
                    <div class="bg-white rounded-lg w-96 p-4">
                        <div class="flex justify-between items-center py-1 mb-1">
                            <h3 class="text-lg font-semibold">Remove Group</h3>
                            <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <p>Do you really want to remove this group?</p>
                        </div>
                        <form id="deleteForm" method="POST" action="">
                            <input type="hidden" name="delete_id" id="delete_id" value="">
                            <div class="flex justify-end items-center gap-2 mt-2 px-4 py-2">
                                <button type="button" id="cancelDeleteBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded">Cancel</button>
                                <button type="submit" id="confirmDeleteBtn" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                            </div>
                        </form>
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
    <script>
        // Handle delete button click
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteGroupModal = document.getElementById('deleteGroupModal');
        const deleteIdInput = document.getElementById('delete_id');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const closeDeleteModal = document.getElementById('closeDeleteModal');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                deleteIdInput.value = this.getAttribute('data-id');
                deleteGroupModal.style.display = 'flex';
            });
        });

        cancelDeleteBtn.addEventListener('click', function() {
            deleteGroupModal.style.display = 'none';
        });

        closeDeleteModal.addEventListener('click', function() {
            deleteGroupModal.style.display = 'none';
        });
    </script>
</body>
</html>
