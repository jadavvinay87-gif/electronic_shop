<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-bolt"></i>
        <span>ElectroAdmin</span>
    </div>
    <ul class="sidebar-menu">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard</a>
        </li>
        <li><a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cart-shopping"></i> Orders</a>
        </li>
        <li><a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-box-open"></i> Products</a>
        </li>
        <li><a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-tags"></i> Categories</a>
        </li>
        <li><a href="brands.php" class="<?php echo ($current_page == 'brands.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-star"></i> Brands</a>
        </li>
        <li><a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Customers</a>
        </li>
        <li><a href="settings.php" class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gear"></i> Settings</a>
        </li>
        <li style="margin-top: 2rem;">
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
    </ul>
</div>
