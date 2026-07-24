<?php
session_start();
require_once '../config/db.php';

// Initialize cart if empty
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = (int)$_POST['product_id'];
    
    if ($action == 'add') {
        $qty = (int)$_POST['qty'];
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
        header("Location: cart.php");
        exit;
    }
    
    if ($action == 'update') {
        $qty = (int)$_POST['qty'];
        if($qty > 0) {
            $_SESSION['cart'][$product_id] = $qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: cart.php");
        exit;
    }
    
    if ($action == 'remove') {
        unset($_SESSION['cart'][$product_id]);
        header("Location: cart.php");
        exit;
    }
}

$page_title = "Shopping Cart";
require '../includes/header.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 4rem;">
    <h1 class="page-title" style="margin-bottom: 2rem;">Your Shopping Cart</h1>
    
    <?php if(empty($_SESSION['cart'])): ?>
    <div style="background:var(--surface); padding:4rem 2rem; border-radius:var(--radius); border:1px solid var(--border); box-shadow:var(--shadow-sm); text-align:center;">
        <div style="font-size:4rem; color:var(--text-light); margin-bottom:1rem;"><i class="fa-solid fa-cart-arrow-down"></i></div>
        <h2 style="margin-bottom:0.5rem;">Your cart is empty</h2>
        <p style="color:var(--text-light); margin-bottom: 2rem;">Looks like you haven't added anything to your cart yet.</p>
        <a href="shop.php" class="btn-primary">Start Shopping</a>
    </div>
    
    <?php else: ?>
    
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Cart Items -->
        <div>
            <div class="card" style="padding:0; overflow:hidden;">
                <table class="table" style="margin-bottom:0;">
                    <thead style="background:var(--bg);">
                        <tr>
                            <th colspan="2">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        $ids = array_keys($_SESSION['cart']);
                        $id_list = implode(',', $ids);
                        $items = $conn->query("SELECT * FROM products WHERE id IN ($id_list)");
                        
                        while($item = $items->fetch_assoc()):
                            $pid = $item['id'];
                            $qty = $_SESSION['cart'][$pid];
                            $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                            $item_total = $price * $qty;
                            $subtotal += $item_total;
                        ?>
                        <tr>
                            <td style="width:80px; padding:1rem;">
                                <?php if($item['main_image']): ?>
                                    <img src="<?php echo htmlspecialchars(getProductImageSrc($item['main_image']), ENT_QUOTES); ?>" alt="" style="width:60px; height:60px; object-fit:contain;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="product.php?id=<?php echo $pid; ?>" style="font-weight:600;"><?php echo htmlspecialchars($item['name']); ?></a>
                            </td>
                            <td>$<?php echo number_format($price, 2); ?></td>
                            <td>
                                <form method="POST" style="display:flex; align-items:center; gap:0.5rem;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                    <input type="number" name="qty" value="<?php echo $qty; ?>" min="1" style="width:60px; padding:0.4rem; border:1px solid var(--border); border-radius:4px;">
                                    <button type="submit" style="background:none; border:none; color:var(--primary); cursor:pointer;"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            </td>
                            <td style="font-weight:600;">$<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                    <button type="submit" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:1.1rem;"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1.5rem;">
                <a href="shop.php" style="color:var(--text-light); font-weight:500;"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div>
            <div class="card">
                <h3 style="margin-bottom:1.5rem; font-size:1.25rem;">Order Summary</h3>
                
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; color:var(--text-light); padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:2rem; font-size:1.25rem; font-weight:700;">
                    <span>Total</span>
                    <span style="color:var(--primary);">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <a href="checkout.php" class="btn-primary" style="display:block; width:100%; font-size:1.1rem;">Proceed to Checkout</a>
            </div>
        </div>
        
    </div>
    
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>

