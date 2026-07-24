<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--background); color: var(--gray); font-weight: 600;}
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="topvar" style="padding: 1.5rem 2rem; background:#fff; border-bottom:1px solid #eee;">
                <h2 style="margin:0;">Orders Management</h2>
            </div>
            <div class="content-wrapper" style="padding: 2rem;">
                <div style="background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6" style="text-align:center; padding:2rem;">No orders have been placed yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
