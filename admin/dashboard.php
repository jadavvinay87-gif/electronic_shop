<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$p = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$cat = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'];
$u = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="topvar">
                <h2>Overview</h2>
                <div>Welcome, <?php echo $_SESSION['admin_name']; ?></div>
            </div>
            <div class="content-pad">
                <div class="cards-grid">
                    <div class="dash-card">
                        <div>
                            <h4>Total Products</h4>
                            <h2><?php echo $p; ?></h2>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div>
                            <h4>Categories</h4>
                            <h2><?php echo $cat; ?></h2>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div>
                            <h4>Customers</h4>
                            <h2><?php echo $u; ?></h2>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div>
                            <h4>Revenue</h4>
                            <h2>$0.00</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
