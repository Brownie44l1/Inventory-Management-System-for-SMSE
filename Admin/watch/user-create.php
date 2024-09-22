<?php
include '../build/config/db_conn.php';
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];

    // Validate password
    if ($password !== $confirm_password) {
        echo '<script>alert("Passwords do not match.");</script>';
    } else {
        // Check if email already exists
        $checkEmailStmt = $conn->prepare("SELECT * FROM user_login WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $result = $checkEmailStmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Email already exists. Please choose a different email.");</script>';
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO user_login (username, email, first_name, last_name, password, phone, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt->bind_param("sssssss", $username, $email, $firstname, $lastname, $hashed_password, $phone, $gender);

            if ($stmt->execute()) {
                // Redirect on success
                header("Location: ./user.php");
                exit(); // It's good to call exit after header redirection
            } else {
                echo '<script>alert("Error: ' . $stmt->error . '");</script>';
            }

            // Close statement
            $stmt->close();
        }

        // Close the checkEmail statement
        $checkEmailStmt->close();
        // Close the connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
                    <h1 class="text-xl font-semibold mb-6">Add User</h1>
                    <nav class="text-sm text-gray-500">
                        <a href="./dashboard.php" class="hover:underline">Home</a> &gt; <span>User</span>
                    </nav>
                </div>

                <!-- Form Section -->
                <div class="bg-slate-100 shadow-lg rounded-lg p-6 px-8 mb-6">
                    <form action="" method="POST">
                        <div class="grid grid-cols-1 gap-y-4">
                            <label class="block mt-2">
                                <span class="text-gray-700">Username</span>
                                <input type="text" name="username" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">Email</span>
                                <input type="email" name="email" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">First name</span>
                                <input type="text" name="firstname" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">Last name</span>
                                <input type="text" name="lastname" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">Password</span>
                                <input type="password" name="password" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">Confirm Password</span>
                                <input type="password" name="confirm_password" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <label class="block mt-2">
                                <span class="text-gray-700">Phone</span>
                                <input type="tel" name="phone" required class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"/>
                            </label>
                            <fieldset class="block mt-3 mb-2">
                                <span class="text-gray-700">Gender</span>
                                <div class="mt-1">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="male" checked class="text-blue-600 border-gray-300 focus:ring-blue-500"/>
                                        <span class="ml-2">Male</span>
                                    </label>
                                    <label class="inline-flex items-center ml-6">
                                        <input type="radio" name="gender" value="female" class="text-blue-600 border-gray-300 focus:ring-blue-500"/>
                                        <span class="ml-2">Female</span>
                                    </label>
                                </div>
                            </fieldset>
                            <!--Button-->
                            <div class="flex justify-between my-3">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Add User</button>
                                <button type="button" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600" onclick="window.location.href='./user.php'">Back</button>
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
    </div>
    <script src="../build/javascript/script.js"></script>
</body>
</html>