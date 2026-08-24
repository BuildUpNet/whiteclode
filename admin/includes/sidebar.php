<div class="sidebar-container">
    <div class="logo">
        <img src="assets/images/white-cloud.png" />
    </div>
    <div class="sidebar-items">
        <?php $activePage = $activePage ?? basename($_SERVER['PHP_SELF']); ?>
        <ul>
            <li class="<?= $activePage === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i><a href="dashboard.php">Dashboard</a>
            </li>
            <li><i class="fa-solid fa-plane"></i><a>Tours</a></li>
            <li><i class="fa-solid fa-calendar-check"></i><a>Bookings</a></li>
            <li><i class="fa-solid fa-user"></i>Customers</li>
            <li><i class="fa-solid fa-star"></i>Reviews</li>
            <li><i class="fa-solid fa-credit-card"></i>Payments</li>
            <li><i class="fa-solid fa-chart-column"></i>Enquiry</li>
            <li class="<?= $activePage === 'cms.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i><a href="cms.php">Website Content</a>
            </li>
            <li><i class="fa-solid fa-gear"></i>Settings</li>
            <li><i class="fa-solid fa-right-from-bracket"></i><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>
