<?php
session_start();
require_once '../build/config/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Fetch admin data from database
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM admin_login WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
            <div class="flex justify-between items-center mb-5">
                <h1 class="text-xl font-semibold mb-6">User Profile</h1>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Profile</span>
                </nav>
            </div>
            <!-- Profile Card -->
            <div class="bg-slate-100 shadow-xl rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 text-gray-800">
                    <!-- Profile Fields -->
                    <div>
                        <div class="mb-4">
                            <span class="font-semibold">Username:</span>
                            <span><?php echo htmlspecialchars($admin['username']); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="font-semibold">Email:</span>
                            <span><?php echo htmlspecialchars($admin['email']); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="font-semibold">First Name:</span>
                            <span><?php echo htmlspecialchars($admin['first_name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="font-semibold">Last Name:</span>
                            <span><?php echo htmlspecialchars($admin['last_name'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <span class="font-semibold">Gender:</span>
                            <span><?php echo htmlspecialchars($admin['gender'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="font-semibold">Phone:</span>
                            <span><?php echo htmlspecialchars($admin['phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="font-semibold">Group:</span>
                            <span class="inline-block px-3 py-1 bg-blue-500 text-white rounded-lg">Administrator</span>
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
  <script src="../build/javascript/script.js"></script>
</body>
</html>