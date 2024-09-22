<?php
include '../build/config/db_conn.php';

// Error logging function
//function logError($message) {
  //  error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../logs/error.log');
//}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_username'])) {
    $username = $conn->real_escape_string($_POST['delete_username']);
    $deleteQuery = "DELETE FROM user_login WHERE username = '$username'";
    
    try {
        if ($conn->query($deleteQuery)) {
  //          logError("User deleted successfully: $username");
            header("Location: " . $_SERVER['PHP_SELF'] . "?delete_success=1");
            exit();
        } else {
            throw new Exception("Delete query failed: " . $conn->error);
        }
    } catch (Exception $e) {
    //    logError("Delete failed: " . $e->getMessage());
        header("Location: " . $_SERVER['PHP_SELF'] . "?delete_error=1");
        exit();
    }
}

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records
$totalQuery = "SELECT COUNT(*) AS total FROM user_login";
$totalResult = $conn->query($totalQuery);
if (!$totalResult) {
    //logError("Total count query failed: " . $conn->error);
}
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch group name with id '1'
$groupQuery = "SELECT group_name FROM group_permissions WHERE id = 1";
$groupResult = $conn->query($groupQuery);
if (!$groupResult) {
    //logError("Group query failed: " . $conn->error);
    $groupName = "Unknown Group";
} else {
    $groupRow = $groupResult->fetch_assoc();
    $groupName = $groupRow ? $groupRow['group_name'] : "Unknown Group";
}

// Fetch users
$sql = "SELECT username, email, first_name, last_name FROM user_login LIMIT $limit OFFSET $offset";
try {
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("User fetch query failed: " . $conn->error);
    }
} catch (Exception $e) {
    //logError($e->getMessage());
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            <h1 class="text-xl font-semibold mb-6">Manage Users</h1>
            
            <?php
            if (isset($_GET['delete_success'])) {
                echo "<p class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>User deleted successfully.</p>";
            }
            if (isset($_GET['delete_error'])) {
                echo "<p class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>Error deleting user. Please try again.</p>";
            }
            ?>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Username</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Group</th>
                            <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $username = htmlspecialchars($row['username'], ENT_QUOTES);
                                $email = htmlspecialchars($row['email'], ENT_QUOTES);
                                $name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name'], ENT_QUOTES);

                                echo "<tr class='border-b'>
                                        <td class='px-4 py-2 text-sm text-black font-medium'>{$username}</td>
                                        <td class='px-4 py-2 text-sm text-black'>{$email}</td>
                                        <td class='px-4 py-2 text-sm text-black'>{$name}</td>
                                        <td class='px-4 py-2 text-sm text-black'>{$groupName}</td>
                                        <td class='px-4 py-2 text-left'>
                                            <button class='delete-btn text-slate-600 hover:text-slate-800 bg-slate-200 py-1 px-2 border border-slate-400'
                                                data-username='{$username}' data-group='{$groupName}'>
                                                <i class='fas fa-trash-alt text-xs'></i>
                                            </button>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-4 py-2 text-sm text-gray-500 text-center'>No users found or error occurred</td></tr>";
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

            <!-- Delete User Modal -->
            <div id="deleteUserModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center " style="display: none;">
                <div class="bg-white rounded-lg w-96 p-4">
                    <div class="flex justify-between items-center py-1 mb-1">
                        <h3 class="text-lg font-semibold">Remove User</h3>
                        <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <p id="deleteUserMessage"></p>
                    </div>
                    <div class="flex justify-end items-center gap-2 mt-2 px-4 py-2">
                        <button id="cancelDeleteBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded">Cancel</button>
                        <form id="deleteUserForm" method="POST">
                            <input type="hidden" name="delete_username" id="deleteUsername">
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                        </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteUserModal');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const closeModalBtn = document.getElementById('closeDeleteModal');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const deleteUserMessage = document.getElementById('deleteUserMessage');
            const deleteUsernameInput = document.getElementById('deleteUsername');
            const deleteUserForm = document.getElementById('deleteUserForm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const username = this.getAttribute('data-username');
                    const groupName = this.getAttribute('data-group');
                    deleteUserMessage.textContent = `Do you really want to remove ${username} from ${groupName}?`;
                    deleteUsernameInput.value = username;
                    deleteModal.classList.remove('hidden');
                    deleteModal.style.display = 'flex';
                });
            });

            closeModalBtn.addEventListener('click', closeModal);
            cancelDeleteBtn.addEventListener('click', closeModal);

            deleteUserForm.addEventListener('submit', function(e) {
                // Form submission is handled by default
                console.log('Form submitted');
            });

            function closeModal() {
                deleteModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>