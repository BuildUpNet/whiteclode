<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$success = '';
$error = '';

function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item';
}

function uniqueCategorySlug($pdo, $base, $excludeId = null)
{
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM categories WHERE slug = ?" . ($excludeId ? " AND id != ?" : "");
        $stmt = $pdo->prepare($sql);
        $params = $excludeId ? [$slug, $excludeId] : [$slug];
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;
        $slug = $base . '-' . $i;
        $i++;
    }
}

function uniqueSubcategorySlug($pdo, $base, $categoryId, $excludeId = null)
{
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM subcategories WHERE slug = ? AND category_id = ?" . ($excludeId ? " AND id != ?" : "");
        $stmt = $pdo->prepare($sql);
        $params = $excludeId ? [$slug, $categoryId, $excludeId] : [$slug, $categoryId];
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;
        $slug = $base . '-' . $i;
        $i++;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_category' || $action === 'edit_category') {
            $id     = (int)($_POST['id'] ?? 0);
            $name   = trim($_POST['name'] ?? '');
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if ($name === '') {
                $error = 'Category name is required.';
            } else {
                $slug = uniqueCategorySlug($pdo, slugify($name), $action === 'edit_category' ? $id : null);

                if ($action === 'add_category') {
                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, status) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $slug, $status]);
                    $success = 'Category added successfully.';
                } else {
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, status = ? WHERE id = ?");
                    $stmt->execute([$name, $slug, $status, $id]);
                    $success = 'Category updated successfully.';
                }
            }
        } elseif ($action === 'delete_category') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Category deleted (its sub-categories were removed too).';
        } elseif ($action === 'add_subcategory' || $action === 'edit_subcategory') {
            $id         = (int)($_POST['id'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $name       = trim($_POST['name'] ?? '');
            $status     = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if ($name === '' || $categoryId <= 0) {
                $error = 'Sub-category name and parent category are required.';
            } else {
                $slug = uniqueSubcategorySlug($pdo, slugify($name), $categoryId, $action === 'edit_subcategory' ? $id : null);

                if ($action === 'add_subcategory') {
                    $stmt = $pdo->prepare("INSERT INTO subcategories (category_id, name, slug, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$categoryId, $name, $slug, $status]);
                    $success = 'Sub-category added successfully.';
                } else {
                    $stmt = $pdo->prepare("UPDATE subcategories SET category_id = ?, name = ?, slug = ?, status = ? WHERE id = ?");
                    $stmt->execute([$categoryId, $name, $slug, $status, $id]);
                    $success = 'Sub-category updated successfully.';
                }
            }
        } elseif ($action === 'delete_subcategory') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Sub-category deleted successfully.';
        }
    } catch (PDOException $e) {
        $error = 'Something went wrong. Please try again.';
    }
}

// Fetch data
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$subStmt = $pdo->query("
    SELECT s.*, c.name AS category_name
    FROM subcategories s
    JOIN categories c ON c.id = s.category_id
    ORDER BY c.name, s.name
");
$subcategories = $subStmt->fetchAll();

$pageTitle = 'Categories & Sub-Categories';
$pageSubtitle = 'Organize your tours into categories';
$activePage = 'categories.php';
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
    <title>Categories · Admin</title>
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

                <!-- ===================== CATEGORIES ===================== -->
                <div class="settings-card">
                    <div class="card-header" style="margin-bottom:16px;">
                        <h3>Categories</h3>
                        <button type="button" class="btn-primary btn-sm" onclick="openCategoryModal('add')">
                            <i class="fa-solid fa-plus"></i> Add Category
                        </button>
                    </div>

                    <div class="booking-table-wrapper">
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Sub-Categories</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr><td colspan="5" class="empty-row">No categories yet — add your first one.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                        $subCount = 0;
                                        foreach ($subcategories as $s) {
                                            if ($s['category_id'] == $cat['id']) $subCount++;
                                        }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cat['name']) ?></td>
                                        <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                        <td><?= $subCount ?></td>
                                        <td>
                                            <span class="status <?= $cat['status'] ? 'confirmed' : 'cancelled' ?>">
                                                <?= $cat['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button type="button" class="icon-btn" title="Edit"
                                                onclick="openCategoryModal('edit', <?= (int)$cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>', <?= (int)$cat['status'] ?>)">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form method="POST" action="categories.php" style="display:inline;"
                                                onsubmit="return confirm('Delete this category? Its sub-categories will be deleted too.');">
                                                <input type="hidden" name="action" value="delete_category">
                                                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                                <button type="submit" class="icon-btn icon-btn-danger" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ===================== SUB-CATEGORIES ===================== -->
                <div class="settings-card">
                    <div class="card-header" style="margin-bottom:16px;">
                        <h3>Sub-Categories</h3>
                        <button type="button" class="btn-primary btn-sm"
                            onclick="openSubcategoryModal('add')" <?= empty($categories) ? 'disabled title="Add a category first"' : '' ?>>
                            <i class="fa-solid fa-plus"></i> Add Sub-Category
                        </button>
                    </div>

                    <div class="booking-table-wrapper">
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($subcategories)): ?>
                                    <tr><td colspan="5" class="empty-row">No sub-categories yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($subcategories as $sub): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sub['name']) ?></td>
                                        <td><?= htmlspecialchars($sub['category_name']) ?></td>
                                        <td><code><?= htmlspecialchars($sub['slug']) ?></code></td>
                                        <td>
                                            <span class="status <?= $sub['status'] ? 'confirmed' : 'cancelled' ?>">
                                                <?= $sub['status'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button type="button" class="icon-btn" title="Edit"
                                                onclick='openSubcategoryModal("edit", <?= (int)$sub["id"] ?>, <?= (int)$sub["category_id"] ?>, "<?= htmlspecialchars(addslashes($sub["name"])) ?>", <?= (int)$sub["status"] ?>)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form method="POST" action="categories.php" style="display:inline;"
                                                onsubmit="return confirm('Delete this sub-category?');">
                                                <input type="hidden" name="action" value="delete_subcategory">
                                                <input type="hidden" name="id" value="<?= (int)$sub['id'] ?>">
                                                <button type="submit" class="icon-btn icon-btn-danger" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </section>
    </div>

    <!-- ===================== CATEGORY MODAL ===================== -->
    <div class="modal-overlay" id="categoryModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3 id="categoryModalTitle">Add Category</h3>
                <button type="button" class="modal-close" onclick="closeModal('categoryModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="categories.php">
                <input type="hidden" name="action" id="categoryAction" value="add_category">
                <input type="hidden" name="id" id="categoryId" value="">

                <div class="form-row">
                    <label for="categoryName">Category Name</label>
                    <input type="text" id="categoryName" name="name" required maxlength="150">
                </div>

                <div class="form-row">
                    <label for="categoryStatus">Status</label>
                    <select id="categoryStatus" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="categorySubmitBtn">Add Category</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('categoryModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== SUB-CATEGORY MODAL ===================== -->
    <div class="modal-overlay" id="subcategoryModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3 id="subcategoryModalTitle">Add Sub-Category</h3>
                <button type="button" class="modal-close" onclick="closeModal('subcategoryModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="categories.php">
                <input type="hidden" name="action" id="subcategoryAction" value="add_subcategory">
                <input type="hidden" name="id" id="subcategoryId" value="">

                <div class="form-row">
                    <label for="subcategoryParent">Category</label>
                    <select id="subcategoryParent" name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="subcategoryName">Sub-Category Name</label>
                    <input type="text" id="subcategoryName" name="name" required maxlength="150">
                </div>

                <div class="form-row">
                    <label for="subcategoryStatus">Status</label>
                    <select id="subcategoryStatus" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="subcategorySubmitBtn">Add Sub-Category</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('subcategoryModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        function openCategoryModal(mode, id, name, status) {
            document.getElementById('categoryModalTitle').textContent = mode === 'add' ? 'Add Category' : 'Edit Category';
            document.getElementById('categorySubmitBtn').textContent = mode === 'add' ? 'Add Category' : 'Update Category';
            document.getElementById('categoryAction').value = mode === 'add' ? 'add_category' : 'edit_category';
            document.getElementById('categoryId').value = mode === 'edit' ? id : '';
            document.getElementById('categoryName').value = mode === 'edit' ? name : '';
            document.getElementById('categoryStatus').value = mode === 'edit' ? status : '1';
            openModal('categoryModal');
        }

        function openSubcategoryModal(mode, id, categoryId, name, status) {
            document.getElementById('subcategoryModalTitle').textContent = mode === 'add' ? 'Add Sub-Category' : 'Edit Sub-Category';
            document.getElementById('subcategorySubmitBtn').textContent = mode === 'add' ? 'Add Sub-Category' : 'Update Sub-Category';
            document.getElementById('subcategoryAction').value = mode === 'add' ? 'add_subcategory' : 'edit_subcategory';
            document.getElementById('subcategoryId').value = mode === 'edit' ? id : '';
            document.getElementById('subcategoryName').value = mode === 'edit' ? name : '';
            document.getElementById('subcategoryStatus').value = mode === 'edit' ? status : '1';
            if (mode === 'edit') {
                document.getElementById('subcategoryParent').value = categoryId;
            }
            openModal('subcategoryModal');
        }

        // Close modal when clicking outside the box
        document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    overlay.classList.remove('open');
                }
            });
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(function (m) {
                    m.classList.remove('open');
                });
            }
        });
    </script>
</body>

</html>
