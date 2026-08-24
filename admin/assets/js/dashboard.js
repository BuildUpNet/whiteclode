document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar-container');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Close mobile sidebar when a menu item is selected
    if (sidebar) {
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                }
            });
        });
    }

    // Admin profile dropdown (Profile / Change Password / Logout)
    const profileToggle = document.getElementById('adminProfileToggle');
    const profileDropdown = document.getElementById('adminDropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            profileToggle.classList.toggle('open');
        });

        // Prevent clicks inside the dropdown from closing it immediately
        profileDropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Close when clicking anywhere else
        document.addEventListener('click', function () {
            profileToggle.classList.remove('open');
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                profileToggle.classList.remove('open');
            }
        });
    }
});
