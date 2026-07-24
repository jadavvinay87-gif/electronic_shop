</main>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="index.php" class="logo" style="color:#fff; margin-bottom:1rem;">
                    <i class="fa-solid fa-bolt"></i> <?php echo htmlspecialchars($config['store_name'] ?? 'ElectroShop'); ?>
                </a>
                <p style="color:#94a3b8; margin-bottom:1rem; font-size:0.9rem;">
                    <?php echo htmlspecialchars($config['store_address'] ?? '123 Tech Lane, Silicon Valley, CA 94025'); ?>
                </p>
                <p style="color:#94a3b8; font-size:0.9rem;">
                    <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($config['store_phone'] ?? ''); ?><br>
                    <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($config['store_email'] ?? ''); ?>
                </p>
            </div>
            
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop All</a></li>
                    <li><a href="cart.php">Shopping Cart</a></li>
                    <li><a href="login.php">My Account</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Customer Service</h4>
                <ul class="footer-links">
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Returns & Exchanges</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Newsletter</h4>
                <p style="color:#94a3b8; font-size:0.9rem; margin-bottom:1rem;">Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
                <form style="display:flex;">
                    <input type="email" placeholder="Enter your email" style="padding:0.75rem; width:100%; border:none; border-radius:4px 0 0 4px; outline:none;">
                    <button type="button" class="btn-primary" style="border-radius:0 4px 4px 0; padding:0.75rem 1rem;">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($config['store_name'] ?? 'ElectroShop'); ?>. All Rights Reserved.
        </div>
    </div>
</footer>
</body>
</html>

