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

    // Hero media upload — quick client-side check so users get instant feedback.
    // The real, authoritative check always happens on the server.
    const heroInput = document.getElementById('hero_media');
    const heroForm = document.getElementById('heroUploadForm');
    const heroMsg = document.getElementById('heroFileMsg');

    if (heroInput && heroForm && heroMsg) {
        const allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm'];
        const maxImageMB = 8;
        const maxVideoMB = 60;
        const defaultMsg = heroMsg.textContent;

        heroInput.addEventListener('change', function () {
            heroMsg.classList.remove('field-error');
            heroMsg.textContent = defaultMsg;

            if (!heroInput.files.length) return;

            const file = heroInput.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const isVideo = ext === 'mp4' || ext === 'webm';
            const maxBytes = (isVideo ? maxVideoMB : maxImageMB) * 1024 * 1024;

            if (!allowedExt.includes(ext)) {
                heroMsg.textContent = 'That file type isn\'t allowed. Use JPG, PNG, GIF, WEBP, MP4 or WEBM.';
                heroMsg.classList.add('field-error');
                heroInput.value = '';
                return;
            }

            if (file.size > maxBytes) {
                heroMsg.textContent = 'File is too large (max ' + (isVideo ? maxVideoMB + 'MB for video' : maxImageMB + 'MB for images') + ').';
                heroMsg.classList.add('field-error');
                heroInput.value = '';
            }
        });

        heroForm.addEventListener('submit', function (e) {
            if (!heroInput.files.length) {
                e.preventDefault();
                heroMsg.textContent = 'Please choose a file first.';
                heroMsg.classList.add('field-error');
            }
        });
    }
});
