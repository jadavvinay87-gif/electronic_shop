<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $name = trim($_POST['name']);
    $stmt = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    header("Location: brands.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM brands WHERE id = $id");
    header("Location: brands.php");
    exit;
}

$brands = $conn->query("SELECT * FROM brands ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Brands | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--light-gray); color: var(--dark); }
        .form-inline { display: flex; gap: 1rem; margin-bottom: 2rem; background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow); }
        .action-btn { padding: 0.4rem 0.8rem; color: #fff; border-radius: 4px; background: var(--danger); font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="topvar">
                <h2>Brands</h2>
            </div>
            <div class="content-pad">
                
                <form method="POST" class="form-inline">
                    <div style="flex: 3;">
                        <input type="text" name="name" placeholder="Brand Name (e.g. Samsung, Apple)" required style="width:100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                    <div>
                        <button type="submit" name="add_brand" class="btn">Add Brand</button>
                    </div>
                </form>

                <div style="background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Brand Name</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $brands->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="brands.php?delete=<?php echo $row['id']; ?>" class="action-btn" onclick="return confirm('Delete this brand?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($brands->num_rows == 0): ?>
                            <tr><td colspan="4" style="text-align:center;">No brands found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
