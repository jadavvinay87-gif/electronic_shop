<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) { header('Location: profile.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if($check->get_result()->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            if($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $name;
                header("Location: profile.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

$page_title = "Register";
require '../includes/header.php';
?>

<div class="container" style="display:flex; justify-content:center; padding: 4rem 0;">
    <div class="card" style="width:100%; max-width:500px;">
        <h1 style="text-align:center; margin-bottom:0.5rem; font-size:1.75rem;">Create Account</h1>
        <p style="text-align:center; color:var(--text-light); margin-bottom:2rem;">Join us and start shopping securely.</p>
        
        <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%; justify-content:center; margin-top:1rem; padding:0.875rem;">
                Create Account
            </button>
        </form>
        
        <div style="text-align:center; margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
            Already have an account? <a href="login.php" style="color:var(--primary); font-weight:600;">Sign in</a>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

