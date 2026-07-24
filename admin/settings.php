<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = "Settings";

$msg = '';

// Handle Admin Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $hashed, $_SESSION['admin_id']);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $email, $_SESSION['admin_id']);
    }
    
    if($stmt->execute()) {
        $_SESSION['admin_name'] = $username;
        $msg = "Profile updated successfully.";
    } else {
        $msg = "Error updating profile.";
    }
}

// Handle Store Settings Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $settings = ['store_name', 'store_email', 'store_phone', 'currency', 'store_address'];
    foreach($settings as $key) {
        if(isset($_POST[$key])) {
            $val = trim($_POST[$key]);
            $stmt = $conn->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
            $stmt->bind_param("ss", $val, $key);
            $stmt->execute();
        }
    }
    $msg = "Store settings updated successfully.";
}

// Get Admin Data
$admin_id = $_SESSION['admin_id'];
$admin_data = $conn->query("SELECT * FROM admins WHERE id = $admin_id")->fetch_assoc();

// Get Settings Data
$settings_res = $conn->query("SELECT * FROM settings");
$config = [];
while($row = $settings_res->fetch_assoc()) {
    $config[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/admin_header.php'; ?>
            
            <div class="content-wrapper">
                
                <?php if($msg): ?>
                <div class="alert badge success" style="margin-bottom: 1.5rem; display:block; padding:1rem; border-radius:8px;">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $msg; ?>
                </div>
                <?php endif; ?>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">
                    
                    <!-- Admin Profile -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Admin Profile</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password (leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h3>General Store Settings</h3>
                        </div>
                        <div style="padding: 1.5rem;">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Store Name</label>
                                    <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($config['store_name'] ?? ''); ?>" required>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                    <div class="form-group">
                                        <label>Support Email</label>
                                        <input type="email" name="store_email" class="form-control" value="<?php echo htmlspecialchars($config['store_email'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Support Phone</label>
                                        <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($config['store_phone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Currency Symbol</label>
                                    <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($config['currency'] ?? '$'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Physical Address</label>
                                    <textarea name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($config['store_address'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div> <!-- End Content Wrapper -->
            <?php include '../includes/admin_footer.php'; ?>
        </div>
    </div>
</body>
</html>
