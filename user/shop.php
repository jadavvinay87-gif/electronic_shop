<?php 
$page_title = "Shop";
require '../includes/header.php'; 

$where = "WHERE p.status = 'Active'";
$params = [];
$types = "";

if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $where .= " AND p.category_id = ?";
    $params[] = $_GET['category'];
    $types .= "i";
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $where .= " AND p.name LIKE ?";
    $params[] = "%" . $_GET['q'] . "%";
    $types .= "s";
}

$sql = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id $where ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="page-header">
        <h1 class="page-title">
            <?php 
            if(isset($_GET['q']) && !empty($_GET['q'])) {
                echo "Search Results for '" . htmlspecialchars($_GET['q']) . "'";
            } elseif(isset($_GET['category']) && is_numeric($_GET['category'])) {
                $cat_name = $conn->query("SELECT name FROM categories WHERE id = ".(int)$_GET['category'])->fetch_assoc()['name'];
                echo htmlspecialchars($cat_name);
            } else {
                echo "All Products";
            }
            ?>
        </h1>
        <p style="color:var(--text-light); margin-top:0.5rem;">Showing <?php echo $result->num_rows; ?> results</p>
    </div>

    <?php if($result->num_rows > 0): ?>
    <div class="product-grid">
        <?php while($p = $result->fetch_assoc()): ?>
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
    <?php else: ?>
    <div style="text-align:center; padding: 4rem 0;">
        <div style="font-size:4rem; color:var(--text-light); margin-bottom:1rem;"><i class="fa-solid fa-box-open"></i></div>
        <h2 style="margin-bottom:0.5rem;">No Products Found</h2>
        <p style="color:var(--text-light); margin-bottom: 2rem;">We couldn't find anything matching your criteria.</p>
        <a href="shop.php" class="btn-primary">Clear Filters</a>
    </div>
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>

