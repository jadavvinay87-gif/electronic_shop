<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = "Manage Orders";

// Update status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['order_status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    header("Location: orders.php");
    exit;
}

$orders = $conn->query("
    SELECT o.*, u.name as customer_name, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Admin</title>
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
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <div style="font-weight:500;"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                        <div style="font-size:0.75rem; color:var(--text-light);"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td><strong>$<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <?php
                                        $s = $row['order_status'];
                                        $cls = 'warning';
                                        if($s == 'Completed') $cls = 'success';
                                        if($s == 'Cancelled') $cls = 'danger';
                                        if($s == 'Processing') $cls = 'info';
                                        ?>
                                        <span class="badge <?php echo $cls; ?>"><?php echo $s; ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-outline btn-sm" onclick="openStatusModal(<?php echo $row['id']; ?>, '<?php echo $row['order_status']; ?>')">
                                            Update
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($orders->num_rows == 0): ?>
                                <tr><td colspan="6" style="text-align:center; padding:3rem;">No orders yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php include '../includes/admin_footer.php'; ?>

            <!-- Status Modal -->
            <div id="statusModal" class="modal">
                <div class="modal-content" style="max-width:400px;">
                    <form method="POST">
                        <div class="modal-header">
                            <h3>Update Order Status</h3>
                            <button type="button" class="close-btn" onclick="hideModal('statusModal')"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="order_id" id="modal_order_id">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="order_status" id="modal_order_status" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" onclick="hideModal('statusModal')">Cancel</button>
                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            function openStatusModal(id, status) {
                document.getElementById('modal_order_id').value = id;
                document.getElementById('modal_order_status').value = status;
                showModal('statusModal');
            }
            </script>
        </div>
    </div>
</body>
</html>
