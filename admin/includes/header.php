<header class="dashboard-header">
    <div class="header-left">
        <button class="menu-toggle"><i class="fa-solid fa-bars"></i></button>
        <div>
            <b><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></b>
            <p><?= htmlspecialchars($pageSubtitle ?? 'Welcome back, ' . ($_SESSION['admin_name'] ?? 'Admin')) ?></p>
        </div>
    </div>
    <div class="header-right">
        <!--search-->
        <div class="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search here..." />
        </div>

        <!--Notification-->
        <button class="notification-button">
            <i class="fa-regular fa-bell"></i>
            <span class="notification-count"> 3 </span>
        </button>

        <!-- Admin Profile -->
        <div class="admin-profile" id="adminProfileToggle">
            <div class="admin-avatar">
                <img src="assets/adminImg/admin.png" />
            </div>
            <div class="admin-info">
                <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                <span><?= htmlspecialchars($_SESSION['admin_role'] ?? 'Admin') ?></span>
            </div>
            <i class="fa-solid fa-chevron-down"></i>

            <div class="admin-dropdown" id="adminDropdown">
                <a href="profile.php" class="dropdown-item">
                    <i class="fa-solid fa-user"></i> My Profile
                </a>
                <a href="change-password.php" class="dropdown-item">
                    <i class="fa-solid fa-key"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item logout-item">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>

    </div>
</header>
