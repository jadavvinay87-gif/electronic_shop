<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("INSERT INTO categories (name, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $status);
    $stmt->execute();
    header("Location: categories.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: categories.php");
    exit;
}

$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--light-gray); color: var(--dark); }
        .badge { padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.8rem; }
        .badge-active { background: rgba(56, 176, 0, 0.1); color: var(--success); }
        .badge-inactive { background: rgba(239, 35, 60, 0.1); color: var(--danger); }
        .form-inline { display: flex; gap: 1rem; margin-bottom: 2rem; background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow); }
        .action-btn { padding: 0.4rem 0.8rem; color: #fff; border-radius: 4px; background: var(--danger); font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="topvar">
                <h2>Categories</h2>
            </div>
            <div class="content-pad">
                
                <form method="POST" class="form-inline">
                    <div style="flex: 2;">
                        <input type="text" name="name" placeholder="Category Name" required style="width:100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <select name="status" style="width:100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" name="add_category" class="btn">Add Category</button>
                    </div>
                </form>

                <div style="background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 'Active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="categories.php?delete=<?php echo $row['id']; ?>" class="action-btn" onclick="return confirm('Delete this category?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($categories->num_rows == 0): ?>
                            <tr><td colspan="5" style="text-align:center;">No categories found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
