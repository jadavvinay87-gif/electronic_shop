<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) { header('Location: profile.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            if(isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            } else {
                header("Location: profile.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}

$page_title = "Login";
require '../includes/header.php';
?>

<div class="container" style="display:flex; justify-content:center; padding: 4rem 0;">
    <div class="card" style="width:100%; max-width:450px;">
        <h1 style="text-align:center; margin-bottom:0.5rem; font-size:1.75rem;">Welcome Back</h1>
        <p style="text-align:center; color:var(--text-light); margin-bottom:2rem;">Log in to your account</p>
        
        <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="margin-bottom:0;">Password</label>
                    <a href="#" style="font-size:0.85rem; color:var(--primary);">Forgot password?</a>
                </div>
                <input type="password" name="password" class="form-control" style="margin-top:0.5rem;" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; justify-content:center; margin-top:1rem; padding:0.875rem;">
                Sign In
            </button>
        </form>
        
        <div style="text-align:center; margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
            Don't have an account? <a href="register.php" style="color:var(--primary); font-weight:600;">Create one</a>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

