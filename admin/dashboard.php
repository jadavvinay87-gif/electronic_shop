<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$page_title = "Dashboard Overview";

// Get key metrics
$total_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_customers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as c FROM orders WHERE payment_status = 'Paid'")->fetch_assoc()['c'] ?? 0;

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

// Low stock products
$low_stock = $conn->query("SELECT * FROM products WHERE quantity <= 5 ORDER BY quantity ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/admin_header.php'; ?>
            
            <div class="content-wrapper">
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>Total Revenue</h3>
                            <div class="value">$<?php echo number_format((float)$total_revenue, 2); ?></div>
                        </div>
                        <div class="stat-icon icon-blue">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>Total Orders</h3>
                            <div class="value"><?php echo $total_orders; ?></div>
                        </div>
                        <div class="stat-icon icon-green">
                            <i class="fa-solid fa-shopping-cart"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>Total Products</h3>
                            <div class="value"><?php echo $total_products; ?></div>
                        </div>
                        <div class="stat-icon icon-yellow">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-content">
                            <h3>Customers</h3>
                            <div class="value"><?php echo $total_customers; ?></div>
                        </div>
                        <div class="stat-icon icon-purple">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                    
                    <!-- Recent Orders -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Orders</h3>
                            <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $recent_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'warning';
                                            if($row['order_status'] == 'Completed') $status_class = 'success';
                                            if($row['order_status'] == 'Cancelled') $status_class = 'danger';
                                            if($row['order_status'] == 'Processing') $status_class = 'info';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo $row['order_status']; ?></span>
                                        </td>
                                        <td><strong>$<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if($recent_orders->num_rows == 0): ?>
                                    <tr><td colspan="5" style="text-align:center;">No recent orders.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Low Stock Alert</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $low_stock->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;"><?php echo htmlspecialchars(substr($row['name'], 0, 25)) . '...'; ?></div>
                                            <div style="font-size:0.75rem; color:var(--text-light);">$<?php echo number_format($row['price'], 2); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge danger"><?php echo $row['quantity']; ?> left</span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if($low_stock->num_rows == 0): ?>
                                    <tr><td colspan="2" style="text-align:center; padding: 2rem;">Stock levels are healthy.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div> <!-- Content Wrapper -->
            
<?php include '../includes/admin_footer.php'; ?>
