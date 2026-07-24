<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

// Get Cart Count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $qty) { $cart_count += $qty; }
}

// Get Config
$config = [];
$settings_res = $conn->query("SELECT * FROM settings");
if ($settings_res) {
    while($row = $settings_res->fetch_assoc()) {
        $config[$row['setting_key']] = $row['setting_value'];
    }
}
$store_name = $config['store_name'] ?? 'ElectroShop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " | " . $store_name : $store_name; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="header-top">
        <div class="container">
            <div><?php echo htmlspecialchars($config['store_email'] ?? ''); ?> | <?php echo htmlspecialchars($config['store_phone'] ?? ''); ?></div>
            <div>Free Shipping on Orders Over $100!</div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-bolt"></i> <?php echo htmlspecialchars($store_name); ?>
            </a>
            
            <form action="shop.php" method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search for products, brands..." class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit" class="search-btn"><i class="fa-solid fa-search"></i></button>
            </form>
            
            <div class="header-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="action-item">
                        <i class="fa-solid fa-user"></i>
                        Profile
                    </a>
                <?php else: ?>
                    <a href="login.php" class="action-item">
                        <i class="fa-solid fa-user"></i>
                        Login
                    </a>
                <?php endif; ?>
                
                <a href="cart.php" class="action-item">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Cart
                    <?php if($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
    
    <div class="navbar">
        <div class="container">
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="shop.php">All Products</a>
                <?php
                $cats = $conn->query("SELECT * FROM categories WHERE status='Active' LIMIT 5");
                if($cats):
                    while($c = $cats->fetch_assoc()):
                ?>
                    <a href="shop.php?category=<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></a>
                <?php 
                    endwhile;
                endif; 
                ?>
            </nav>
        </div>
    </div>
</header>
<main class="main-wrapper">

