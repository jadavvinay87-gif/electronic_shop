<?php 
$page_title = "Home";
require '../includes/header.php'; 

// Fetch Featured Products
$featured = $conn->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.status = 'Active' LIMIT 4");

// Fetch Latest Products
$latest = $conn->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'Active' ORDER BY p.id DESC LIMIT 8");
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Discover the Next Generation of Tech</h1>
            <p class="hero-subtitle">Upgrade your lifestyle with our premium selection of smartphones, laptops, and smart accessories. Unleash innovation today.</p>
            <a href="shop.php" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 2.5rem;">Shop the Collection</a>
        </div>
    </div>
</section>

<div class="container">
    
    <!-- Featured Products -->
    <?php if($featured && $featured->num_rows > 0): ?>
    <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <a href="shop.php" class="section-link">View All <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="product-grid">
        <?php while($p = $featured->fetch_assoc()): ?>
        <div class="product-card">
            <?php if($p['discount_price'] > 0): ?>
                <div class="product-badge">SALE</div>
            <?php endif; ?>
            
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <?php if($p['main_image']): ?>
                    <img src="<?php echo htmlspecialchars(getProductImageSrc($p['main_image']), ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-image">
                <?php else: ?>
                    <div class="product-image" style="background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:3rem;">
                        <i class="fa-solid fa-image"></i>
                    </div>
                <?php endif; ?>
            </a>
            
            <div class="product-info">
                <div class="product-category"><?php echo htmlspecialchars($p['cat_name']); ?></div>
                <a href="product.php?id=<?php echo $p['id']; ?>" class="product-title"><?php echo htmlspecialchars($p['name']); ?></a>
                <div class="product-price">
                    <?php if($p['discount_price'] > 0): ?>
                        <span style="color:var(--text-light); text-decoration:line-through; font-size:0.9rem; font-weight:400; margin-right:0.5rem;">$<?php echo number_format($p['price'], 2); ?></span>
                        $<?php echo number_format($p['discount_price'], 2); ?>
                    <?php else: ?>
                        $<?php echo number_format($p['price'], 2); ?>
                    <?php endif; ?>
                </div>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="add-to-cart-btn">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- Latest Arrivals -->
    <div class="section-header" style="margin-top: 4rem;">
        <h2 class="section-title">Latest Arrivals</h2>
        <a href="shop.php" class="section-link">Explore New <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="product-grid">
        <?php while($p = $latest->fetch_assoc()): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $p['id']; ?>">
                <?php if($p['main_image']): ?>
                    <img src="<?php echo htmlspecialchars(getProductImageSrc($p['main_image']), ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-image">
                <?php else: ?>
                    <div class="product-image" style="background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:3rem;">
                        <i class="fa-solid fa-image"></i>
                    </div>
                <?php endif; ?>
            </a>
            
            <div class="product-info">
                <div class="product-category"><?php echo htmlspecialchars($p['cat_name']); ?></div>
                <a href="product.php?id=<?php echo $p['id']; ?>" class="product-title"><?php echo htmlspecialchars($p['name']); ?></a>
                <div class="product-price">
                    <?php if($p['discount_price'] > 0): ?>
                        <span style="color:var(--text-light); text-decoration:line-through; font-size:0.9rem; font-weight:400; margin-right:0.5rem;">$<?php echo number_format($p['price'], 2); ?></span>
                        $<?php echo number_format($p['discount_price'], 2); ?>
                    <?php else: ?>
                        $<?php echo number_format($p['price'], 2); ?>
                    <?php endif; ?>
                </div>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="add-to-cart-btn">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</div>

<?php require '../includes/footer.php'; ?>

