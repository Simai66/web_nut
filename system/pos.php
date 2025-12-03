<?php
require_once 'db.php';
checkLogin();

// Get Products for POS
$stmt = $pdo->query("SELECT * FROM products ORDER BY product_name");
$products = $stmt->fetchAll();

// Get Categories
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get Customers
$cust_stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_name");
$customers = $cust_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS | Clothery System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern POS Styles */
        :root {
            --pos-bg: #f8fafc;
            --card-bg: #ffffff;
            --primary-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        body {
            background-color: var(--pos-bg);
            overflow: hidden;
            /* Prevent body scroll */
        }

        .pos-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Left Side: Product Area */
        .product-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            padding-right: 0;
        }

        .pos-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
            flex-shrink: 0;
        }

        .search-bar-container {
            position: relative;
            width: 400px;
            max-width: 100%;
        }

        .search-bar-container i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-bar-container input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid var(--border-color);
            border-radius: 99px;
            background: var(--bg-body);
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .search-bar-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .category-bar {
            padding: 1rem 2rem;
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            background: white;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
            scrollbar-width: none;
            /* Firefox */
        }

        .category-bar::-webkit-scrollbar {
            display: none;
            /* Chrome/Safari */
        }

        .cat-chip {
            padding: 0.5rem 1.25rem;
            border-radius: 99px;
            background: var(--bg-body);
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .cat-chip:hover {
            background: #e2e8f0;
        }

        .cat-chip.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .products-scroll-area {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: var(--pos-bg);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .pos-product-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid transparent;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .pos-product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-light);
        }

        .card-img-wrapper {
            height: 160px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .card-img-wrapper i {
            font-size: 3.5rem;
            color: #cbd5e1;
        }

        .stock-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .stock-badge.low {
            color: var(--danger);
            background: #fef2f2;
        }

        .card-content {
            padding: 1rem;
        }

        .card-category {
            font-size: 0.75rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .card-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Right Side: Cart Area */
        .cart-sidebar {
            width: 420px;
            background: white;
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100vh;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .cart-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .cart-count {
            background: var(--primary-color);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-item-row {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            animation: slideIn 0.2s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .item-img {
            width: 60px;
            height: 60px;
            background: #f1f5f9;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .item-price {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-color);
            transition: all 0.1s;
        }

        .qty-btn:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .qty-display {
            font-weight: 600;
            width: 20px;
            text-align: center;
        }

        .item-total {
            font-weight: 700;
            font-size: 1rem;
            color: var(--secondary-color);
            text-align: right;
        }

        .cart-footer {
            padding: 1.5rem;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .summary-row.total {
            color: var(--secondary-color);
            font-weight: 800;
            font-size: 1.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
            margin-bottom: 1.5rem;
        }

        .checkout-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
    </style>
</head>

<body>
    <div class="pos-container">
        <!-- Main Product Area -->
        <div class="product-area">
            <!-- Header -->
            <div class="pos-header">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--secondary-color);">Point of Sale</h1>
                    <p style="color: var(--text-light); font-size: 0.9rem;">
                        <a href="dashboard.php" style="display:inline-flex; align-items:center; gap:0.5rem; color:var(--text-light); hover:var(--primary-color);">
                            <i class="fa-solid fa-arrow-left"></i> กลับหน้าหลัก
                        </a>
                    </p>
                </div>
                <div class="search-bar-container">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="ค้นหาสินค้า (ชื่อ, บาร์โค้ด)...">
                </div>
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="text-align:right;">
                        <div style="font-weight:600;"><?php echo $_SESSION['fullname']; ?></div>
                        <div style="font-size:0.8rem; color:var(--text-light);"><?php echo ucfirst($_SESSION['role']); ?></div>
                    </div>
                    <div style="width:40px; height:40px; background:var(--primary-light); color:var(--primary-color); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        AD
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="category-bar">
                <div class="cat-chip active" onclick="filterCategory('all', this)">ทั้งหมด</div>
                <?php foreach ($categories as $cat): ?>
                    <div class="cat-chip" onclick="filterCategory('<?php echo htmlspecialchars($cat); ?>', this)">
                        <?php echo htmlspecialchars($cat); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Products Grid -->
            <div class="products-scroll-area">
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <?php
                        $p_json = htmlspecialchars(json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE));
                        $isLowStock = $p['stock'] < 10;
                        $isOutOfStock = $p['stock'] == 0;
                        ?>
                        <div class="pos-product-card" data-category="<?php echo htmlspecialchars($p['category']); ?>" onclick="addToCart(<?php echo $p_json; ?>)">
                            <div class="card-img-wrapper">
                                <i class="fa-solid fa-shirt"></i>
                                <div class="stock-badge <?php echo $isLowStock ? 'low' : ''; ?>">
                                    <?php echo $p['stock']; ?> ชิ้น
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($p['category']); ?></div>
                                <div class="card-title"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                <div class="card-price">฿<?php echo number_format($p['price'], 0); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Cart Sidebar -->
        <div class="cart-sidebar">
            <div class="cart-header">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div class="cart-title">
                        <i class="fa-solid fa-cart-shopping"></i> ตะกร้าสินค้า
                        <span class="cart-count" id="cartCount">0</span>
                    </div>
                    <button onclick="clearCart()" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:0.9rem;">
                        <i class="fa-solid fa-trash"></i> ล้างตะกร้า
                    </button>
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <div class="custom-select-wrapper" style="flex:1; position:relative;">
                            <div class="select-trigger" onclick="toggleCustomerDropdown()" style="padding:0.5rem; border:1px solid var(--border-color); border-radius:0.5rem; cursor:pointer; background:white; display:flex; justify-content:space-between; align-items:center;">
                                <span id="selectedCustomerName">Unknown</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:0.8rem; color:var(--text-light);"></i>
                            </div>
                            <div class="select-dropdown" id="customerDropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; background:white; border:1px solid var(--border-color); border-radius:0.5rem; margin-top:0.25rem; z-index:100; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); max-height:200px; overflow-y:auto;">
                                <div style="padding:0.5rem; position:sticky; top:0; background:white; border-bottom:1px solid var(--border-color);">
                                    <input type="text" id="customerSearch" placeholder="ค้นหาลูกค้า..." style="width:100%; padding:0.25rem 0.5rem; border:1px solid var(--border-color); border-radius:0.25rem;" oninput="filterCustomers(this.value)">
                                </div>
                                <div class="option" onclick="selectCustomer(1, 'Unknown')" style="padding:0.5rem; cursor:pointer; border-bottom:1px solid #f1f5f9;">Unknown</div>
                                <?php foreach ($customers as $c): ?>
                                    <?php if ($c['customer_id'] != 1): ?>
                                        <div class="option customer-option" onclick="selectCustomer(<?php echo $c['customer_id']; ?>, '<?php echo htmlspecialchars($c['customer_name']); ?>')" style="padding:0.5rem; cursor:pointer; border-bottom:1px solid #f1f5f9;"><?php echo htmlspecialchars($c['customer_name']); ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button class="btn btn-outline" style="padding:0.5rem;" onclick="document.getElementById('addCustomerModal').style.display='flex'">
                            <i class="fa-solid fa-user-plus"></i>
                        </button>
                    </div>
                    <input type="hidden" id="selectedCustomerId" value="1">
                </div>
            </div>

            <div class="cart-items-container" id="cartItems">
                <div class="empty-cart">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <p>ยังไม่มีสินค้าในตะกร้า</p>
                    <p style="font-size:0.85rem; margin-top:0.5rem;">เลือกสินค้าจากด้านซ้ายเพื่อเริ่มขาย</p>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-row">
                    <span>ยอดรวมสินค้า</span>
                    <span id="subtotal">฿0.00</span>
                </div>
                <div class="summary-row">
                    <span>ภาษี (7%)</span>
                    <span id="tax">฿0.00</span>
                </div>
                <div class="summary-row total">
                    <span>ยอดสุทธิ</span>
                    <span id="grandTotal" style="color:var(--primary-color);">฿0.00</span>
                </div>
                <button class="checkout-btn" onclick="checkout()">
                    <span>ชำระเงิน</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:white; padding:2rem; border-radius:1rem; width:400px; max-width:90%;">
            <h3 style="margin-bottom:1.5rem;">เพิ่มสมาชิกใหม่</h3>
            <div class="form-group">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" id="newCustName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" id="newCustPhone" class="form-control">
            </div>
            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button class="btn btn-outline" style="flex:1;" onclick="document.getElementById('addCustomerModal').style.display='none'">ยกเลิก</button>
                <button class="btn btn-primary" style="flex:1;" onclick="saveNewCustomer()">บันทึก</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        // Customer Dropdown Logic
        function toggleCustomerDropdown() {
            const dropdown = document.getElementById('customerDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        function selectCustomer(id, name) {
            document.getElementById('selectedCustomerId').value = id;
            document.getElementById('selectedCustomerName').textContent = name;
            document.getElementById('customerDropdown').style.display = 'none';
        }

        function filterCustomers(text) {
            const options = document.querySelectorAll('.customer-option');
            text = text.toLowerCase();
            options.forEach(opt => {
                if (opt.textContent.toLowerCase().includes(text)) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.custom-select-wrapper');
            if (!wrapper.contains(e.target)) {
                document.getElementById('customerDropdown').style.display = 'none';
            }
        });

        // Save New Customer
        function saveNewCustomer() {
            const name = document.getElementById('newCustName').value;
            const phone = document.getElementById('newCustPhone').value;

            if (!name) return alert('กรุณากรอกชื่อ');

            fetch('save_customer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        phone
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Add to dropdown
                        const dropdown = document.getElementById('customerDropdown');
                        const newOption = document.createElement('div');
                        newOption.className = 'option customer-option';
                        newOption.style.padding = '0.5rem';
                        newOption.style.cursor = 'pointer';
                        newOption.style.borderBottom = '1px solid #f1f5f9';
                        newOption.textContent = data.customer_name;
                        newOption.onclick = function() {
                            selectCustomer(data.customer_id, data.customer_name);
                        };
                        dropdown.appendChild(newOption);

                        // Select it
                        selectCustomer(data.customer_id, data.customer_name);

                        // Close modal
                        document.getElementById('addCustomerModal').style.display = 'none';
                        document.getElementById('newCustName').value = '';
                        document.getElementById('newCustPhone').value = '';
                        alert('เพิ่มสมาชิกเรียบร้อย');
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }

        // Filter Function
        function filterCategory(category, btn) {
            document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');

            const products = document.querySelectorAll('.pos-product-card');
            const searchText = document.getElementById('searchInput').value.toLowerCase();

            products.forEach(p => {
                const pCat = p.dataset.category;
                const pName = p.querySelector('.card-title').textContent.toLowerCase();

                const matchesCategory = category === 'all' || pCat === category;
                const matchesSearch = pName.includes(searchText) || pCat.toLowerCase().includes(searchText);

                if (matchesCategory && matchesSearch) {
                    p.style.display = 'block';
                } else {
                    p.style.display = 'none';
                }
            });
        }

        // Search Function
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const activeCatBtn = document.querySelector('.cat-chip.active');
            const activeCategory = activeCatBtn ? (activeCatBtn.textContent === 'ทั้งหมด' ? 'all' : activeCatBtn.textContent) : 'all';
            filterCategory(activeCategory, activeCatBtn);
        });

        // Add to Cart
        function addToCart(product) {
            if (product.stock <= 0) {
                alert('สินค้าหมดสต็อก!');
                return;
            }

            const existingItem = cart.find(item => item.product_id === product.product_id);
            if (existingItem) {
                if (existingItem.qty < product.stock) {
                    existingItem.qty++;
                } else {
                    alert('สินค้าหมดสต็อก (ครบจำนวนที่มีแล้ว)');
                }
            } else {
                cart.push({
                    ...product,
                    qty: 1
                });
            }
            renderCart();
        }

        // Update Quantity
        function updateQty(productId, change) {
            const item = cart.find(i => i.product_id === productId);
            if (!item) return;

            const newQty = item.qty + change;
            if (newQty > 0) {
                if (newQty <= item.stock) {
                    item.qty = newQty;
                } else {
                    alert('สินค้าหมดสต็อก');
                }
            } else {
                // Remove item if qty becomes 0
                cart = cart.filter(i => i.product_id !== productId);
            }
            renderCart();
        }

        // Clear Cart
        function clearCart() {
            if (confirm('ต้องการล้างตะกร้าใช่หรือไม่?')) {
                cart = [];
                renderCart();
            }
        }

        // Render Cart
        function renderCart() {
            const container = document.getElementById('cartItems');
            const countBadge = document.getElementById('cartCount');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <i class="fa-solid fa-basket-shopping"></i>
                        <p>ยังไม่มีสินค้าในตะกร้า</p>
                        <p style="font-size:0.85rem; margin-top:0.5rem;">เลือกสินค้าจากด้านซ้ายเพื่อเริ่มขาย</p>
                    </div>`;
                countBadge.textContent = '0';
                updateTotals();
                return;
            }

            container.innerHTML = cart.map(item => `
                <div class="cart-item-row">
                    <div class="item-img">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <div class="item-details">
                        <div class="item-name">${item.product_name}</div>
                        <div class="item-price">฿${parseFloat(item.price).toFixed(2)}</div>
                        <div class="item-controls">
                            <div class="qty-btn" onclick="updateQty(${item.product_id}, -1)"><i class="fa-solid fa-minus" style="font-size:0.7rem;"></i></div>
                            <div class="qty-display">${item.qty}</div>
                            <div class="qty-btn" onclick="updateQty(${item.product_id}, 1)"><i class="fa-solid fa-plus" style="font-size:0.7rem;"></i></div>
                        </div>
                    </div>
                    <div class="item-total">
                        ฿${(item.price * item.qty).toFixed(2)}
                    </div>
                </div>
            `).join('');

            countBadge.textContent = cart.reduce((sum, item) => sum + item.qty, 0);
            updateTotals();
        }

        // Update Totals
        function updateTotals() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const tax = total * 0.07;
            const grandTotal = total + tax;

            document.getElementById('subtotal').textContent = '฿' + total.toFixed(2);
            document.getElementById('tax').textContent = '฿' + tax.toFixed(2);
            document.getElementById('grandTotal').textContent = '฿' + grandTotal.toFixed(2);
        }

        // Checkout
        function checkout() {
            if (cart.length === 0) return alert('กรุณาเลือกสินค้า');

            if (confirm('ยืนยันการชำระเงิน?')) {
                fetch('save_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            cart: cart,
                            customer_id: document.getElementById('selectedCustomerId').value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ บันทึกรายการขายเรียบร้อย!');
                            cart = [];
                            renderCart();
                            window.location.reload(); // Optional: reload to update stock display
                        } else {
                            alert('❌ เกิดข้อผิดพลาด: ' + data.message);
                        }
                    })
                    .catch(err => alert('Error: ' + err));
            }
        }
    </script>
</body>

</html>