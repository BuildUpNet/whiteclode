<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$adminId = $_SESSION['admin_id'];
$success = '';
$error = '';

// Fetch current admin data
$stmt = $pdo->prepare("SELECT id, name, email, role FROM admins WHERE id = ? LIMIT 1");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if (!$admin) {
    // Account no longer exists — force logout
    header("Location: logout.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Make sure another admin isn't already using this email
        $check = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ? LIMIT 1");
        $check->execute([$email, $adminId]);

        if ($check->fetch()) {
            $error = 'This email is already in use by another account.';
        } else {
            $update = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
            $update->execute([$name, $email, $adminId]);

            // Keep session + page data in sync
            $_SESSION['admin_name']  = $name;
            $_SESSION['admin_email'] = $email;
            $admin['name']  = $name;
            $admin['email'] = $email;

            $success = 'Profile updated successfully.';
        }
    }
}

$pageTitle = 'My Profile';
$pageSubtitle = 'Manage your account details';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <link rel="stylesheet" href="assets/css/admin-extra.css">
    <title>My Profile · Admin</title>
</head>

<body>
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <section class="dashboard-main">

            <?php include __DIR__ . '/includes/header.php'; ?>

            <div class="settings-wrap">

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="settings-card">

                    <div class="profile-head">
                        <img src="assets/adminImg/admin.png" alt="Admin">
                        <div class="profile-head-info">
                            <strong><?= htmlspecialchars($admin['name']) ?></strong>
                            <span><?= htmlspecialchars($admin['role']) ?></span>
                        </div>
                    </div>

                    <h3>Profile Details</h3>
                    <p class="settings-desc">Update your name and email address.</p>

                    <form method="POST" action="profile.php">
                        <div class="form-row">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
                        </div>

                        <div class="form-row">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>

                        <div class="form-row">
                            <label>Role</label>
                            <input type="text" value="<?= htmlspecialchars($admin['role']) ?>" disabled>
                            <small>Your role is managed by a Super Admin and can't be changed here.</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Changes</button>
                            <a href="dashboard.php" class="btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Cancel</a>
                        </div>
                    </form>

                </div>

                <div class="settings-card">
                    <h3>Password</h3>
                    <p class="settings-desc">Want to change your password instead?</p>
                    <a href="change-password.php" class="btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">
                        <i class="fa-solid fa-key" style="margin-right:8px;"></i> Change Password
                    </a>
                </div>

            </div>

        </section>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>

</html>
