<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $phone, $address, $user_id);
    if($stmt->execute()) {
        $_SESSION['user_name'] = $name;
        $msg = "Profile updated successfully!";
    }
}

$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC");

$page_title = "My Account";
require '../includes/header.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 4rem;">
    <h1 class="page-title" style="margin-bottom: 2rem;">My Account</h1>
    
    <?php if(isset($_GET['order_success'])): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> Thank you! Your order has been successfully placed.
    </div>
    <?php endif; ?>
    
    <?php if(isset($msg)): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo $msg; ?>
    </div>
    <?php endif; ?>
    
    <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items:start;">
        
        <!-- Profile Menu & Edit -->
        <div class="card">
            <div style="text-align:center; margin-bottom:2rem;">
                <div style="width:80px; height:80px; background:var(--primary); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; margin:0 auto 1rem;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h3 style="font-size:1.25rem; font-weight:600;"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p style="color:var(--text-light); font-size:0.9rem;"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                <div class="form-group">
                    <label>Default Shipping Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <button type="submit" name="update_profile" class="btn-primary" style="width:100%;">Save Changes</button>
            </form>
            
            <a href="logout.php" style="display:block; text-align:center; margin-top:2rem; color:var(--danger); font-weight:500;">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
        
        <!-- Order History -->
        <div class="card">
            <h3 style="margin-bottom:1.5rem; font-size:1.25rem;">Order History</h3>
            
            <?php if($orders->num_rows > 0): ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($o = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $o['id']; ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                            <td><strong>$<?php echo number_format($o['total_amount'], 2); ?></strong></td>
                            <td>
                                <?php
                                $s = $o['order_status'];
                                $c = 'var(--text-light)';
                                if($s == 'Completed') $c = 'var(--success)';
                                if($s == 'Processing') $c = 'var(--primary)';
                                if($s == 'Cancelled') $c = 'var(--danger)';
                                ?>
                                <span style="font-weight:600; color:<?php echo $c; ?>;"><?php echo $s; ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding: 3rem 0; color:var(--text-light);">
                <div style="font-size:3rem; margin-bottom:1rem;"><i class="fa-solid fa-box-open"></i></div>
                <p>You haven't placed any orders yet.</p>
                <a href="shop.php" style="color:var(--primary); font-weight:500; margin-top:1rem; display:inline-block;">Start Shopping</a>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php require '../includes/footer.php'; ?>

