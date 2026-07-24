<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

if(empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Calculate totals
$subtotal = 0;
$ids = array_keys($_SESSION['cart']);
$id_list = implode(',', $ids);
$items_res = $conn->query("SELECT * FROM products WHERE id IN ($id_list)");
$cart_items = [];

while($item = $items_res->fetch_assoc()) {
    $pid = $item['id'];
    $qty = $_SESSION['cart'][$pid];
    $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
    $subtotal += ($price * $qty);
    $item['qty'] = $qty;
    $item['calc_price'] = $price;
    $cart_items[] = $item;
}

// Process Order
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];
    
    if(empty($address)) {
        $error = "Shipping address is required.";
    } else {
        $conn->begin_transaction();
        try {
            // Create Order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idss", $user_id, $subtotal, $payment_method, $address);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Insert Items and deduct stock
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_stock = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            
            foreach($cart_items as $ci) {
                $stmt_item->bind_param("iiid", $order_id, $ci['id'], $ci['qty'], $ci['calc_price']);
                $stmt_item->execute();
                
                $stmt_stock->bind_param("ii", $ci['qty'], $ci['id']);
                $stmt_stock->execute();
            }
            
            $conn->commit();
            
            // Clear Cart
            unset($_SESSION['cart']);
            
            header("Location: profile.php?order_success=1");
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred while placing your order. Please try again.";
        }
    }
}

$page_title = "Checkout";
require '../includes/header.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 4rem;">
    <h1 class="page-title" style="margin-bottom: 2rem;">Secure Checkout</h1>
    
    <?php if($error): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>
    
    <div style="display:grid; grid-template-columns: 2fr 1.2fr; gap: 3rem;">
        
        <div>
            <form method="POST" id="checkoutForm">
                
                <div class="card" style="margin-bottom:2rem;">
                    <h3 style="margin-bottom:1.5rem; font-size:1.25rem;">Shipping Information</h3>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea name="address" class="form-control" rows="4" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                </div>
                
                <div class="card" style="margin-bottom:2rem;">
                    <h3 style="margin-bottom:1.5rem; font-size:1.25rem;">Payment Method</h3>
                    
                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <label style="display:flex; align-items:center; gap:1rem; padding:1rem; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer;">
                            <input type="radio" name="payment_method" value="Credit Card" checked>
                            <div style="flex-grow:1;">
                                <div style="font-weight:600;">Credit Card</div>
                                <div style="font-size:0.85rem; color:var(--text-light);">Secure payment via Stripe</div>
                            </div>
                            <i class="fa-brands fa-cc-visa" style="font-size:1.5rem; color:#1a1f71;"></i>
                            <i class="fa-brands fa-cc-mastercard" style="font-size:1.5rem; color:#eb001b;"></i>
                        </label>
                        
                        <label style="display:flex; align-items:center; gap:1rem; padding:1rem; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer;">
                            <input type="radio" name="payment_method" value="PayPal">
                            <div style="flex-grow:1;">
                                <div style="font-weight:600;">PayPal</div>
                                <div style="font-size:0.85rem; color:var(--text-light);">Pay with your PayPal account</div>
                            </div>
                            <i class="fa-brands fa-paypal" style="font-size:1.5rem; color:#003087;"></i>
                        </label>
                        
                        <label style="display:flex; align-items:center; gap:1rem; padding:1rem; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer;">
                            <input type="radio" name="payment_method" value="Cash on Delivery">
                            <div style="flex-grow:1;">
                                <div style="font-weight:600;">Cash on Delivery</div>
                                <div style="font-size:0.85rem; color:var(--text-light);">Pay when you receive the items</div>
                            </div>
                            <i class="fa-solid fa-money-bill-1" style="font-size:1.5rem; color:var(--success);"></i>
                        </label>
                    </div>
                </div>
                
                <button type="submit" name="place_order" class="btn-primary" style="width:100%; font-size:1.2rem; padding:1rem;">
                    Place Order - $<?php echo number_format($subtotal, 2); ?>
                </button>
            </form>
        </div>
        
        <div>
            <div class="card" style="position:sticky; top:100px;">
                <h3 style="margin-bottom:1.5rem; font-size:1.25rem;">Order Summary</h3>
                
                <div style="margin-bottom:2rem; max-height:300px; overflow-y:auto; padding-right:0.5rem;">
                    <?php foreach($cart_items as $ci): ?>
                    <div style="display:flex; gap:1rem; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border);">
                        <?php if($ci['main_image']): ?>
                            <img src="<?php echo htmlspecialchars(getProductImageSrc($ci['main_image']), ENT_QUOTES); ?>" alt="" style="width:50px; height:50px; object-fit:contain; background:#f8fafc; border-radius:4px;">
                        <?php endif; ?>
                        <div style="flex-grow:1;">
                            <div style="font-size:0.9rem; font-weight:500; display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;"><?php echo htmlspecialchars($ci['name']); ?></div>
                            <div style="color:var(--text-light); font-size:0.8rem;">Qty: <?php echo $ci['qty']; ?></div>
                        </div>
                        <div style="font-weight:600; font-size:0.9rem;">
                            $<?php echo number_format($ci['calc_price'] * $ci['qty'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color:var(--text-light);">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; color:var(--text-light); padding-bottom:1.5rem; border-bottom:1px solid var(--border);">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:1.25rem; font-weight:700;">
                    <span>Total</span>
                    <span style="color:var(--primary);">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php require '../includes/footer.php'; ?>

