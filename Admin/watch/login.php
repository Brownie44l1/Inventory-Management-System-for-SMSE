<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../dist/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="w-full max-w-md bg-white pt-5 p-8 rounded-xl shadow-xl">
        <div class="flex items-center justify-center mb-4">
            <img src="../assets/img/Rubik.png" alt="Logo" class="size-12">
            <h1 class="mr-5 text-lg font-semibold">Inventory</h1>
        </div>
        <form action="authenticate.php" method="POST" class="space-y-6">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-800">Username</label>
                <input type="text" id="username" name="username" placeholder="Username"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-800">Email</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Password with eye icon -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-800">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <span class="absolute inset-y-0 right-4 flex items-center cursor-pointer" onclick="togglePassword()">
                        <i id="eyeIcon" class="fa fa-eye-slash text-gray-600"></i>
                    </span>
                </div>
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">Login as Admin</label>
                </div>

                <div>
                    <a href="#" class="text-sm text-black font-medium hover:underline no-underline">Forgot Password?</a>
                </div>
            </div>

            <!-- Create Account Button -->
            <div>
                <button type="submit"
                    class="w-full bg-blue-900 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 focus:outline-none transition duration-200">
                    Login
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {
                password.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                password.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
    <!--Usename: admin
        Email: admin@gmail.com
        password: admin_password123    


        Username: user
        Email: user@gmail.com
        Password: user_password123
    -->
</html>
