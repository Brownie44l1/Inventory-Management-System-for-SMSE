
<style>
    .submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 2.7s ease-out;
    }

    .submenu.active {
        max-height: 300px;
        transition: max-height 2.7s ease-in;
    }

    .submenu-toggle .fa-plus {
        transition: transform 0.3s ease-in-out;
    }

    .submenu-toggle.active .fa-plus {
        transform: rotate(45deg);
    }
</style>
<div>
    <aside id="sidebar" class="w-60 min-h-screen bg-blue-100 text-blue-600 p-5 pl-3 left-0 top-0 flex flex-col md:shadow-none shadow-3xl">
        <div class="mb-5 mt-3 flex">
            <h1 class="text-xl font-bold flex items-center font-sans text-black">
                <img src="/Php_Project/Integrated Web-based Management System for SMSE/Admin/assets/img/Rubik.png" class="w-10 h-10 mr-1" alt="Logo">
                Inventory
            </h1>
            <button id="closeSidebar" class="close-sidebar-btn absolute top-6 right-2 md:hidden">
                <i class="fas fa-chevron-left text-gray-600"></i>
            </button>
        </div>
        <nav class="flex-col text-black font-sans flex-grow">
            <a href="../watch/dashboard.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                <i class="fas fa-th-large w-5 mr-1"></i>
                Dashboard
            </a>
            <div class="mt-4">
                <div class="px-4 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Human Management</div>
                <!-- Users Sub-Menu -->
                <div class="relative">
                    <a href="#" class="submenu-toggle py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                        <i class="fas fa-users w-5 mr-3"></i>
                        Users
                        <i class="fas fa-plus ml-auto w-0.5 transition-transform duration-300 ease-in-out"></i>
                    </a>
                    <div class="submenu ml-8 border-blue-800 border-l-2 pl-2 text-sm hidden">
                        <a href="../watch/user-create.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-user-plus w-4 mr-2"></i>Add User
                        </a>
                        <a href="../watch/user.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-users-cog w-4 mr-2"></i>Manage Users
                        </a>
                    </div>
                </div>
                <!-- Groups Menu -->
                <div class="relative">
                    <a href="#" class="submenu-toggle py-2 px-4 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                        <i class="fas fa-layer-group w-5 mr-3"></i>
                        Groups
                        <i class="fas fa-plus ml-auto w-0.5 transition-transform duration-300 ease-in-out"></i>
                    </a>
                    <div class="submenu ml-8 border-blue-800 border-l-2 pl-2 text-sm hidden">
                        <a href="../watch/group-create.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-plus-circle w-4 mr-2"></i>Add Group
                        </a>
                        <a href="../watch/group.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-cogs w-4 mr-2"></i>Manage Groups
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="px-4 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Inventory Management</div>
                <a href="../watch/brand.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fas fa-cube w-5 mr-3"></i>
                    Brands
                </a>
                <a href="../watch/category.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fa-solid fa-table-list w-5 mr-3"></i>
                    Category
                </a>
                <a href="../watch/store.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fas fa-store w-5 mr-3"></i>
                    Stores
                </a>
                <a href="../watch/attributes.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fa-brands fa-creative-commons-by w-5 mr-3"></i>
                    Attributes
                </a>
                <!-- Products Menu -->
                <div class="relative">
                    <a href="#" class="submenu-toggle py-2 px-4 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                        <i class="fas fa-box w-5 mr-3"></i>
                        Products
                        <i class="fas fa-plus ml-auto w-0.5 transition-transform duration-300 ease-in-out"></i>
                    </a>
                    <div class="submenu ml-8 border-blue-800 border-l-2 pl-2 text-sm hidden">
                        <a href="../watch/product-create.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-plus-square w-4 mr-2"></i>Add Product
                        </a>
                        <a href="../watch/product.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md block">
                            <i class="fas fa-edit w-4 mr-2"></i>Manage Products
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="px-4 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Finance Management</div>
                <!-- Orders Menu -->
                <div class="relative">
                    <a href="#" class="submenu-toggle py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                        <i class="fas fa-shopping-cart w-5 mr-3"></i>
                        Orders
                        <i class="fas fa-plus ml-auto w-0.5 transition-transform duration-300 ease-in-out"></i>
                    </a>
                    <div class="submenu ml-8 border-blue-800 border-l-2 pl-2 text-sm hidden">
                        <a href="../watch/order-create.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 rounded-md block">
                            <i class="fas fa-cart-plus w-4 mr-2"></i>Add Order
                        </a>
                        <a href="../watch/order.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 rounded-md block">
                            <i class="fas fa-tasks w-4 mr-2"></i>Manage Orders
                        </a>
                    </div>
                </div>
                <a href="../watch/report.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Reports
                </a>
                <a href="../watch/company.php" class="py-2 px-4 mb-6 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                    <i class="fas fa-building w-5 mr-3"></i>
                    Company
                </a>
            </div>
        </nav>
        <div class="mt-auto text-black font-sans border-t-2 border-slate-200">
            <a href="../watch/profile.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                <i class="fas fa-user-circle w-5 mr-3"></i>
                Profile
            </a>
            <a href="../watch/settings.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                <i class="fas fa-cog w-5 mr-3"></i>
                Settings
            </a>
            <a href="../watch/login.php" class="py-2 px-4 mb-2 hover:text-blue-800 hover:bg-blue-200 hover:border-l-4 hover:border-blue-800 rounded-md flex items-center">
                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                Logout
            </a>
        </div>
    </aside>
</div>