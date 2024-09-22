document.addEventListener('DOMContentLoaded', function () {
    // Sidebar submenu functionality
    const menuItems = document.querySelectorAll('.submenu-toggle');

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();

            const submenu = this.nextElementSibling;
            const icon = this.querySelector('i:last-child');

            if (submenu && submenu.classList.contains('submenu')) {
                this.classList.toggle('active');
                submenu.classList.toggle('active');
                submenu.classList.toggle('hidden');
                
                // Optional: if you want to keep the icon rotation
                if (icon) {
                    icon.style.transform = icon.style.transform === 'rotate(45deg)' ? 'rotate(0deg)' : 'rotate(45deg)';
                }
            }
        });
    });

    // Header Functionality
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar'); 
    const mainContent = document.getElementById('mainContent');
    const closeSidebarBtn = document.getElementById('closeSidebar');

    function updateSidebarState(show) {
        if (show) {
            sidebar.classList.remove('sidebar-hidden');
            sidebar.classList.add('sidebar-shadow', 'force-show');
            mainContent.classList.remove('main-content-expanded');
        } else {
            sidebar.classList.add('sidebar-hidden');
            sidebar.classList.remove('sidebar-shadow', 'force-show');
            mainContent.classList.add('main-content-expanded');
        }

        // Update toggle button icon
        const icon = toggleBtn.querySelector('i');
        icon.classList.toggle('rotated', !show);
    }

    function toggleSidebar() {
        const isCurrentlyShown = !sidebar.classList.contains('sidebar-hidden');
        updateSidebarState(!isCurrentlyShown);
    }

    function handleResize() {
        if (window.innerWidth <= 768) {
            updateSidebarState(false);
        } else {
            updateSidebarState(true);
        }
    }

    function closeSidebar() {
        updateSidebarState(false);
    }

    if (toggleBtn && sidebar && mainContent && closeSidebarBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
        closeSidebarBtn.addEventListener('click', closeSidebar);
        window.addEventListener('resize', handleResize);
        
        // Initial check
        handleResize();
    } else {
        console.error('Toggle button or sidebar elements are missing.');
    }
});
