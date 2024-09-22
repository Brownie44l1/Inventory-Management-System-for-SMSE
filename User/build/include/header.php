<style>
    .toggle-btn {
        width: 10px;
        height: auto;
        border: none;
        cursor: pointer;
    }
    .rotated {
        transform: rotate(180deg);
    }
</style>

<header class="flex items-center justify-between p-3 py-2 bg-white w-full sm:p-5 sm:py-3">
    <!-- Left Section -->
    <div class="flex items-center justify-center transition-all duration-300">
        <!-- Toggle Button -->
        <button id="toggleSidebar" class="toggle-btn mr-3 sm:mr-6 ml-2 sm:ml-3">
            <i class="fas fa-chevron-left text-gray-600"></i>
        </button>
        <div class="flex items-center space-x-3 sm:space-x-4">
            <h1 class="text-lg sm:text-xl font-semibold underline underline-offset-4 decoration-blue-500">
                <?php
                    // Get the current page's file name and remove the ".php" extension
                    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
                    // Capitalize the first letter
                    echo ucfirst($currentPage);
                ?>
            </h1>
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2 sm:space-x-4 mr-3 sm:mr-8">
        <img src="/Php_Project/Integrated Web-based Management System for SMSE/Admin/assets/img/Rubik.png" alt="User" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full">
        <div>
            <span class="text-xs sm:text-sm font-medium">Elon Musk</span><br>
            <span class="text-xs text-gray-500">User</span>
        </div>
    </div>
</header>
