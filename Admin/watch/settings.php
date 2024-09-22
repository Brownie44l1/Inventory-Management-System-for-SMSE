<?php
session_start();
require_once '../build/config/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null; // Safe handling of gender

    // Update query
    $stmt = $conn->prepare("UPDATE admin_login SET username = ?, email = ?, first_name = ?, last_name = ?, phone = ?, gender = ? WHERE admin_id = ?");
    $stmt->bind_param("ssssssi", $username, $email, $firstname, $lastname, $phone, $gender, $admin_id);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
    $stmt->close();
}


// Fetch admin data from database
$stmt = $conn->prepare("SELECT * FROM admin_login WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
                <h1 class="text-xl font-semibold mb-6">Update Information</h1>
                <nav class="text-sm text-gray-500">
                    <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>Settings</span>
                </nav>
            </div>
            <?php
            if (isset($success_message)) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>{$success_message}</div>";
            }
            if (isset($error_message)) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>{$error_message}</div>";
            }
            ?>
            <!-- Form Section -->
            <div class="bg-slate-100 shadow-lg rounded-lg p-6 px-8">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="grid grid-cols-1 gap-y-4">
                        <label class="block mt-2">
                            <span class="text-gray-700">Username</span>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                        </label>
                        <label class="block mt-2">
                            <span class="text-gray-700">Email</span>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                        </label>
                        <label class="block mt-2">
                            <span class="text-gray-700">First name</span>
                            <input type="text" name="firstname" value="<?php echo htmlspecialchars($admin['first_name']); ?>" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                        </label>
                        <label class="block mt-2">
                            <span class="text-gray-700">Last name</span>
                            <input type="text" name="lastname" value="<?php echo htmlspecialchars($admin['last_name']); ?>" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                        </label>
                        <label class="block mt-2">
                            <span class="text-gray-700">Phone</span>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                        </label>
                        <fieldset class="block mt-3 mb-6">
                            <span class="text-gray-700">Gender</span>
                            <div class="mt-1">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="Male" <?php echo (isset($admin['gender']) && $admin['gender'] == 'Male') ? 'checked' : ''; ?> class="text-blue-600 border-gray-300 focus:ring-blue-500"/>
                                    <span class="ml-2">Male</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="gender" value="Female" <?php echo (isset($admin['gender']) && $admin['gender'] == 'Female') ? 'checked' : ''; ?> class="text-blue-600 border-gray-300 focus:ring-blue-500"/>
                                    <span class="ml-2">Female</span>
                                </label>
                                
                            </div>
                        </fieldset>

                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
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