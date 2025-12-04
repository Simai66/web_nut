<aside class="sidebar">
    <div class="sidebar-header">
        <div class="app-brand">
            <i class="fa-solid fa-shirt"></i> Clothery
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line menu-icon"></i> ภาพรวม (Dashboard)
            </a>
        </li>
        <li>
            <a href="pos.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register menu-icon"></i> ขายหน้าร้าน (POS)
            </a>
        </li>
        <li>
            <a href="products.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-box menu-icon"></i> จัดการสินค้า
            </a>
        </li>
        <li>
            <a href="orders.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cart-shopping menu-icon"></i> รายการขาย (Orders)
            </a>
        </li>
        <li style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <a href="logout.php" class="menu-item" style="color: var(--danger);">
                <i class="fa-solid fa-right-from-bracket menu-icon"></i> ออกจากระบบ
            </a>
        </li>
    </ul>
</aside>