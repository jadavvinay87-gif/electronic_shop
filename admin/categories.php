<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = "Manage Categories";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("INSERT INTO categories (name, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $status);
    $stmt->execute();
    header("Location: categories.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: categories.php");
    exit;
}

$categories = $conn->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/admin_header.php'; ?>
            
            <div class="content-wrapper">
                <div style="display:flex; justify-content:space-between; margin-bottom: 1.5rem;">
                    <div></div>
                    <button class="btn btn-primary" onclick="showModal('addCategoryModal')">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Products Count</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $categories->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                    <td>
                                        <span class="badge info"><?php echo $row['product_count']; ?> Products</span>
                                    </td>
                                    <td>
                                        <?php if($row['status'] == 'Active'): ?>
                                            <span class="badge success">Active</span>
                                        <?php else: ?>
                                            <span class="badge warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:0.5rem;">
                                            <a href="categories.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete this category?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($categories->num_rows == 0): ?>
                                <tr><td colspan="5" style="text-align:center; padding:3rem;">No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php include '../includes/admin_footer.php'; ?>

            <!-- Add Modal -->
            <div id="addCategoryModal" class="modal">
                <div class="modal-content" style="max-width:400px;">
                    <form method="POST">
                        <div class="modal-header">
                            <h3>Add New Category</h3>
                            <button type="button" class="close-btn" onclick="hideModal('addCategoryModal')"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Laptops">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" onclick="hideModal('addCategoryModal')">Cancel</button>
                            <button type="submit" name="add_category" class="btn btn-primary">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
