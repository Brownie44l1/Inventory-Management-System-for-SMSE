<?php
include '../build/config/db_conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = $_POST['group_name'];

    // Prepare an INSERT statement to add a new group
    $stmt = $conn->prepare("INSERT INTO group_permissions (group_name, users_view, groups_view, brands_view, category_view) VALUES (?, ?, ?, ?, ?)");

    // Assuming default values for the views; change as needed
    $users_view = isset($_POST['users_view']) ? 1 : 0;
    $groups_view = isset($_POST['groups_view']) ? 1 : 0;
    $brands_view = isset($_POST['brands_view']) ? 1 : 0;
    $category_view = isset($_POST['category_view']) ? 1 : 0;

    $stmt->bind_param('siiii', $group_name, $users_view, $groups_view, $brands_view, $category_view);

    if ($stmt->execute()) {
        header("Location: ./group.php");
        exit();
    } else {
        echo "Error creating group: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
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
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold mb-6">Create Group</h1>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Group</span>
                </nav>
            </div>

            <form action="#" method="POST">
                <!-- Group Name -->
                <div class="mb-6">
                    <label for="group-name" class="block text-gray-700 font-medium">Group Name</label>
                    <input type="text" id="group-name" name="group_name" value="Staff" class="mt-1 p-2 block w-full rounded-md bg-slate-100 border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                </div>

                <!-- Permissions Table -->
                <h3 class="text-lg font-medium text-gray-800 mb-4">Permission</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Modules</th>
                                <th class="px-4 py-2 border">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-2 border">Users</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="users_view" class="form-checkbox"/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Groups</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="groups_view" class="form-checkbox"/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Brands</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="brands_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Stores</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="category_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Attributes</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="users_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Products</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="groups_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Orders</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="brands_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Report</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="category_view" class="form-checkbox" checked/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Company</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="users_view" class="form-checkbox"/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Profile</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="groups_view" class="form-checkbox"/></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border">Settings</td>
                                <td class="px-4 py-2 border text-center"><input type="checkbox" name="brands_view" class="form-checkbox"/></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Create Group</button>
                    <button type="button" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600" onclick="window.location.href='./group.php'">Back</button>
                </div>

            </form>
        </main>

        <!-- Footer -->
        <div class="w-full flex items-center justify-center">
            <?php include '../build/include/footer.php'; ?>
        </div>
    </div>
    <script src="../build/javascript/script.js"></script>
</body>
</html>