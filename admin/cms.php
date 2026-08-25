<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$success = '';
$error = '';
$uploadError = '';
$uploadSuccess = '';
$validGroups = ['general', 'home', 'about', 'contact'];
$activeTab = $_GET['tab'] ?? 'general';
if (!in_array($activeTab, $validGroups, true)) {
    $activeTab = 'general';
}

// ---------------------------------------------------------------
// Hero background upload (image or video)
// Strict whitelist — only real images/videos are ever accepted.
// ---------------------------------------------------------------
$allowedHeroExtensions = [
    'jpg'  => 'image',
    'jpeg' => 'image',
    'png'  => 'image',
    'gif'  => 'image',
    'webp' => 'image',
    'mp4'  => 'video',
    'webm' => 'video',
];

// The extension check above is just a first filter. This is the real check:
// we inspect the file's actual content type, so a script renamed to ".jpg"
// still gets rejected even though the extension looks fine.
$allowedHeroMimes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'video/mp4', 'video/webm',
];

$maxImageBytes = 8 * 1024 * 1024;   // 8 MB
$maxVideoBytes = 60 * 1024 * 1024;  // 60 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_hero_media') {
    $activeTab = 'home';

    if (!isset($_FILES['hero_media']) || $_FILES['hero_media']['error'] === UPLOAD_ERR_NO_FILE) {
        $uploadError = 'Please choose a file to upload.';
    } elseif ($_FILES['hero_media']['error'] !== UPLOAD_ERR_OK) {
        $uploadError = 'Upload failed (error code ' . (int)$_FILES['hero_media']['error'] . '). Please try again.';
    } else {
        $file = $_FILES['hero_media'];
        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($ext === '' || !array_key_exists($ext, $allowedHeroExtensions)) {
            $uploadError = 'That file type isn\'t allowed. Only JPG, PNG, GIF, WEBP images or MP4/WEBM videos can be uploaded.';
        } else {
            // Check the file's real content type — never trust the extension or the
            // browser-supplied Content-Type alone.
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
            if ($finfo) {
                finfo_close($finfo);
            }

            if (!$realMime || !in_array($realMime, $allowedHeroMimes, true)) {
                $uploadError = 'This file\'s content doesn\'t match an allowed image or video format. Upload rejected for security reasons.';
            } else {
                $mediaType = $allowedHeroExtensions[$ext];
                $maxBytes = $mediaType === 'video' ? $maxVideoBytes : $maxImageBytes;

                if ($file['size'] > $maxBytes) {
                    $uploadError = 'File is too large. Max size is ' . ($mediaType === 'video' ? '60MB for videos' : '8MB for images') . '.';
                } elseif ($mediaType === 'image' && @getimagesize($file['tmp_name']) === false) {
                    // A file can pass the MIME check but still not be a genuine, decodable image
                    $uploadError = 'This image file appears to be corrupted or invalid.';
                } else {
                    $uploadDir = __DIR__ . '/uploads/hero/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Never keep the original filename — generate a random one instead
                    $newName = bin2hex(random_bytes(16)) . '.' . $ext;
                    $destination = $uploadDir . $newName;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        chmod($destination, 0644);

                        // Delete the previous hero file so uploads don't pile up
                        $oldStmt = $pdo->prepare("SELECT setting_value FROM cms_content WHERE setting_key = 'home_hero_media'");
                        $oldStmt->execute();
                        $oldFile = $oldStmt->fetchColumn();
                        if ($oldFile) {
                            $oldPath = $uploadDir . basename($oldFile);
                            if (is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }

                        $updMedia = $pdo->prepare("UPDATE cms_content SET setting_value = ? WHERE setting_key = 'home_hero_media'");
                        $updMedia->execute([$newName]);

                        $updType = $pdo->prepare("UPDATE cms_content SET setting_value = ? WHERE setting_key = 'home_hero_media_type'");
                        $updType->execute([$mediaType]);

                        $uploadSuccess = 'Hero background updated — ' . strtoupper($ext) . ' (' . $mediaType . ') uploaded successfully.';
                    } else {
                        $uploadError = 'Could not save the uploaded file. Please try again.';
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group']) && ($_POST['action'] ?? '') !== 'upload_hero_media') {
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

$heroMediaFile = '';
$heroMediaType = '';
foreach ($grouped['home'] as $f) {
    if ($f['setting_key'] === 'home_hero_media') $heroMediaFile = $f['setting_value'];
    if ($f['setting_key'] === 'home_hero_media_type') $heroMediaType = $f['setting_value'];
}

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

                <?php if ($uploadSuccess): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($uploadSuccess) ?>
                    </div>
                <?php endif; ?>

                <?php if ($uploadError): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($uploadError) ?>
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

                    <?php if ($groupKey === 'home'): ?>
                        <div class="settings-card">
                            <h3>Hero Background (Image or Video)</h3>
                            <p class="settings-desc">
                                Replaces the homepage hero background. Allowed types:
                                <strong>JPG, JPEG, PNG, GIF, WEBP</strong> (images) or
                                <strong>MP4, WEBM</strong> (videos) only — max 8MB for images, 60MB for video.
                                Any other file type (.pdf, .js, .xml, .csv, etc.) is rejected automatically.
                            </p>

                            <div class="hero-preview">
                                <?php if ($heroMediaFile && $heroMediaType === 'video'): ?>
                                    <video src="uploads/hero/<?= htmlspecialchars($heroMediaFile) ?>" muted loop autoplay playsinline></video>
                                <?php elseif ($heroMediaFile && $heroMediaType === 'image'): ?>
                                    <img src="uploads/hero/<?= htmlspecialchars($heroMediaFile) ?>" alt="Hero background preview">
                                <?php else: ?>
                                    <div class="hero-preview__empty">
                                        <i class="fa-solid fa-image"></i>
                                        <span>No custom background uploaded yet — the homepage is using its default image.</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="cms.php?tab=home" enctype="multipart/form-data" id="heroUploadForm">
                                <input type="hidden" name="action" value="upload_hero_media">
                                <div class="form-row">
                                    <label for="hero_media">Choose file</label>
                                    <input type="file" id="hero_media" name="hero_media"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm">
                                    <small id="heroFileMsg">Only JPG, PNG, GIF, WEBP, MP4 or WEBM files are accepted.</small>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">Upload / Replace</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

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
                                <?php if (in_array($field['field_type'], ['media', 'hidden'], true)) continue; ?>
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
