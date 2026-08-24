<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$success = '';
$error = '';
$validGroups = ['general', 'home', 'about', 'contact'];
$activeTab = $_GET['tab'] ?? 'general';
if (!in_array($activeTab, $validGroups, true)) {
    $activeTab = 'general';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group  = $_POST['group'] ?? '';
    $values = $_POST['settings'] ?? [];

    if (!in_array($group, $validGroups, true) || !is_array($values)) {
        $error = 'Invalid form submission.';
    } else {
        // Only update keys that actually belong to this group (prevents tampering)
        $check = $pdo->prepare("SELECT setting_key FROM cms_content WHERE page_group = ?");
        $check->execute([$group]);
        $allowedKeys = $check->fetchAll(PDO::FETCH_COLUMN);

        $upd = $pdo->prepare("UPDATE cms_content SET setting_value = ? WHERE setting_key = ? AND page_group = ?");

        foreach ($values as $key => $val) {
            if (in_array($key, $allowedKeys, true)) {
                $upd->execute([trim($val), $key, $group]);
            }
        }

        $success = ucfirst($group === 'about' ? 'About Us' : ($group === 'contact' ? 'Contact Us' : $group)) . ' content updated successfully.';
        $activeTab = $group;
    }
}

// Fetch everything, grouped
$stmt = $pdo->query("SELECT * FROM cms_content ORDER BY page_group, display_order, id");
$rows = $stmt->fetchAll();

$grouped = ['general' => [], 'home' => [], 'about' => [], 'contact' => []];
foreach ($rows as $row) {
    if (isset($grouped[$row['page_group']])) {
        $grouped[$row['page_group']][] = $row;
    }
}

$tabLabels = [
    'general' => 'General / Contact Info',
    'home'    => 'Home Page',
    'about'   => 'About Us',
    'contact' => 'Contact Us Page',
];

$pageTitle = 'Website Content';
$pageSubtitle = 'Manage text shown on the public website';
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
    <title>Website Content · Admin</title>
</head>

<body>
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <section class="dashboard-main">

            <?php include __DIR__ . '/includes/header.php'; ?>

            <div class="settings-wrap cms-wrap">

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

                <p class="cms-note">
                    <i class="fa-solid fa-circle-info"></i>
                    These values are saved to the database. Connecting the public website pages to read from
                    here is a separate step — for now this manages the content in one place.
                </p>

                <!-- Tabs -->
                <div class="cms-tabs">
                    <?php foreach ($tabLabels as $key => $label): ?>
                        <a href="cms.php?tab=<?= $key ?>"
                           class="cms-tab <?= $activeTab === $key ? 'active' : '' ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($grouped as $groupKey => $fields): ?>
                    <?php if ($groupKey !== $activeTab || empty($fields)) continue; ?>

                    <div class="settings-card">
                        <h3><?= htmlspecialchars($tabLabels[$groupKey]) ?></h3>
                        <p class="settings-desc">
                            <?php if ($groupKey === 'general'): ?>
                                Shown site-wide (footer, contact links, social icons).
                            <?php elseif ($groupKey === 'home'): ?>
                                Text shown on the homepage hero and stats section.
                            <?php elseif ($groupKey === 'about'): ?>
                                Text shown on the About Us page.
                            <?php else: ?>
                                Address, phone, hours, and map shown on the Contact Us page.
                            <?php endif; ?>
                        </p>

                        <form method="POST" action="cms.php?tab=<?= $groupKey ?>">
                            <input type="hidden" name="group" value="<?= $groupKey ?>">

                            <?php foreach ($fields as $field): ?>
                                <div class="form-row">
                                    <label for="f_<?= htmlspecialchars($field['setting_key']) ?>">
                                        <?= htmlspecialchars($field['label']) ?>
                                    </label>

                                    <?php if ($field['field_type'] === 'textarea'): ?>
                                        <textarea
                                            id="f_<?= htmlspecialchars($field['setting_key']) ?>"
                                            name="settings[<?= htmlspecialchars($field['setting_key']) ?>]"
                                            rows="3"><?= htmlspecialchars($field['setting_value']) ?></textarea>
                                    <?php else: ?>
                                        <input type="text"
                                            id="f_<?= htmlspecialchars($field['setting_key']) ?>"
                                            name="settings[<?= htmlspecialchars($field['setting_key']) ?>]"
                                            value="<?= htmlspecialchars($field['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>

                <?php endforeach; ?>

            </div>

        </section>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>

</html>
