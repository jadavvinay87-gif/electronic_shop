<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$products = $conn->query("
    SELECT p.*, c.name as cat_name, b.name as brand_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN brands b ON p.brand_id = b.id 
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--background); color: var(--gray); font-weight: 600;}
        .action-btn { padding: 0.4rem 0.8rem; color: #fff; border-radius: 4px; background: var(--danger); font-size: 0.9rem; }
        .add-btn { background: var(--primary); padding: 0.5rem 1rem; color:#fff; border-radius: 6px; float: right;}
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="topvar" style="padding: 1.5rem 2rem; background:#fff; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                <h2 style="margin:0;">Products</h2>
                <a href="#" class="add-btn">+ Add Product</a>
            </div>
            <div class="content-wrapper" style="padding: 2rem;">
                <div style="background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td>[Img]</td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td>
                                    <a href="#" class="action-btn" onclick="return confirm('Delete this product?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($products->num_rows == 0): ?>
                            <tr><td colspan="9" style="text-align:center; padding:2rem;">No products found in the database.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
