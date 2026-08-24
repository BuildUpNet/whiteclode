<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$adminId = $_SESSION['admin_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Please fill in all fields.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ? LIMIT 1");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            $error = 'Current password is incorrect.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $update->execute([$newHash, $adminId]);

            $success = 'Password updated successfully.';
        }
    }
}

$pageTitle = 'Change Password';
$pageSubtitle = 'Keep your account secure';
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
    <title>Change Password · Admin</title>
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
                    <h3>Change Password</h3>
                    <p class="settings-desc">Enter your current password, then choose a new one (min. 6 characters).</p>

                    <form method="POST" action="change-password.php">
                        <div class="form-row">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-row">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6">
                        </div>

                        <div class="form-row">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                            <small>Re-enter the new password exactly as above.</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Update Password</button>
                            <a href="profile.php" class="btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>

        </section>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>

</html>
