<?php 
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) { header('Location: shop.php'); exit; }
$product_id = (int)$_GET['id'];

require '../config/db.php';

$stmt = $conn->prepare("
    SELECT p.*, c.name as cat_name, b.name as brand_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN brands b ON p.brand_id = b.id 
    WHERE p.id = ? AND p.status = 'Active'
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) { header('Location: shop.php'); exit; }
$product = $result->fetch_assoc();

$page_title = $product['name'];
require '../includes/header.php'; 
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 4rem;">
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
        
        <!-- Product Image -->
        <div style="background:var(--surface); border-radius:var(--radius); padding:2rem; border:1px solid var(--border); box-shadow:var(--shadow-sm); display:flex; justify-content:center;">
            <?php if($product['main_image']): ?>
                <img src="<?php echo htmlspecialchars(getProductImageSrc($product['main_image']), ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height:500px; object-fit:contain;">
            <?php else: ?>
                <div style="height:400px; display:flex; align-items:center; justify-content:center; font-size:5rem; color:var(--border);">
                    <i class="fa-solid fa-image"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Details -->
        <div>
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem; font-size:0.875rem;">
                <span class="badge" style="background:#e2e8f0; color:var(--text);"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                <span style="color:var(--text-light);"><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($product['brand_name']); ?></span>
            </div>
            
            <h1 style="font-size:2.5rem; font-weight:800; line-height:1.2; margin-bottom:1rem;"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div style="font-size:2rem; font-weight:700; color:var(--primary); margin-bottom:1.5rem;">
                <?php if($product['discount_price'] > 0): ?>
                    <span style="color:var(--text-light); text-decoration:line-through; font-size:1.25rem; margin-right:1rem; font-weight:500;">$<?php echo number_format($product['price'], 2); ?></span>
                    $<?php echo number_format($product['discount_price'], 2); ?>
                <?php else: ?>
                    $<?php echo number_format($product['price'], 2); ?>
                <?php endif; ?>
            </div>
            
            <p style="color:var(--text-light); margin-bottom:2rem; line-height:1.8; font-size:1.05rem;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
            
            <form action="cart.php" method="POST" style="background:var(--surface); padding:1.5rem; border-radius:var(--radius); border:1px solid var(--border); box-shadow:var(--shadow-sm); margin-bottom:2rem;">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">
                
                <div style="display:flex; align-items:flex-end; gap:1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem;">Quantity</label>
                        <input type="number" name="qty" value="1" min="1" max="<?php echo $product['quantity']; ?>" style="width:80px; padding:0.75rem; border:1px solid var(--border); border-radius:var(--radius-sm); outline:none;">
                    </div>
                    <button type="submit" class="btn-primary" style="flex-grow:1; padding:0.875rem;" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-cart-plus"></i> <?php echo $product['quantity'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                    </button>
                </div>
                
                <?php if($product['quantity'] <= 5 && $product['quantity'] > 0): ?>
                    <div style="margin-top:1rem; color:var(--accent); font-size:0.875rem; font-weight:500;">
                        <i class="fa-solid fa-fire"></i> Hurry! Only <?php echo $product['quantity']; ?> left in stock.
                    </div>
                <?php endif; ?>
            </form>
            
            <?php if($product['specifications'] || $product['warranty']): ?>
            <div style="border-top:1px solid var(--border); padding-top:2rem;">
                <h3 style="margin-bottom:1rem; font-size:1.25rem;">Product Details</h3>
                <?php if($product['warranty']): ?>
                <div style="display:flex; gap:0.5rem; align-items:center; margin-bottom:1rem; color:var(--text-light);">
                    <i class="fa-solid fa-shield-halved"></i> <strong>Warranty:</strong> <?php echo htmlspecialchars($product['warranty']); ?>
                </div>
                <?php endif; ?>
                
                <?php if($product['specifications']): ?>
                <div style="color:var(--text-light); line-height:1.6; font-size:0.95rem;">
                    <?php echo nl2br(htmlspecialchars($product['specifications'])); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>

