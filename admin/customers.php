<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = "Manage Customers";

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: customers.php");
    exit;
}

$customers = $conn->query("
    SELECT u.*, COUNT(o.id) as total_orders, SUM(o.total_amount) as total_spent 
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    GROUP BY u.id 
    ORDER BY u.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/admin_header.php'; ?>
            
            <div class="content-wrapper">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Customer Info</th>
                                    <th>Contact</th>
                                    <th>Total Orders</th>
                                    <th>Total Spent</th>
                                    <th>Joined Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $customers->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600; color:var(--text);"><?php echo htmlspecialchars($row['name']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-size:0.875rem;"><?php echo htmlspecialchars($row['email']); ?></div>
                                        <div style="font-size:0.75rem; color:var(--text-light);"><?php echo htmlspecialchars($row['phone']); ?></div>
                                    </td>
                                    <td><span class="badge info"><?php echo $row['total_orders']; ?> Orders</span></td>
                                    <td><strong>$<?php echo number_format((float)$row['total_spent'], 2); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div style="display:flex; gap:0.5rem;">
                                            <a href="customers.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-icon" onclick="return confirm('Are you sure you want to delete this customer? All their orders will be deleted as well.');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($customers->num_rows == 0): ?>
                                <tr><td colspan="6" style="text-align:center; padding:3rem;">No customers found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php include '../includes/admin_footer.php'; ?>
        </div>
    </div>
</body>
</html>
