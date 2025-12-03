<?php
require_once 'db.php';
checkLogin();

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $product_name = $_POST['product_name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $stmt = $pdo->prepare("INSERT INTO products (product_name, category, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_name, $category, $price, $stock]);
        header("Location: products.php");
        exit();
    } elseif ($_POST['action'] == 'edit') {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $stmt = $pdo->prepare("UPDATE products SET product_name = ?, category = ?, price = ?, stock = ? WHERE product_id = ?");
        $stmt->execute([$product_name, $category, $price, $stock, $product_id]);
        header("Location: products.php");
        exit();
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit();
}

// Get Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Clothery System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced Table Styles */
        .table th {
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
            font-size: 1rem;
        }

        .table tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            padding: 0.35em 0.8em;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            width: 450px;
            max-width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-search">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="ค้นหาสินค้า...">
                </div>
                <div class="header-profile">
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION['fullname']; ?></span>
                        <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
                    </div>
                    <div class="user-avatar">AD</div>
                </div>
            </header>

            <div class="page-content">
                <div class="page-header">
                    <h1 class="page-title">จัดการสินค้า</h1>
                    <button class="btn btn-primary" onclick="document.getElementById('addProductModal').style.display='flex'">
                        <i class="fa-solid fa-plus"></i> เพิ่มสินค้าใหม่
                    </button>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th style="width: 60px;">รูปภาพ</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>หมวดหมู่</th>
                                    <th>ราคา</th>
                                    <th>สต็อกคงเหลือ</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?php echo $p['product_id']; ?></td>
                                        <td>
                                            <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo $p['product_name']; ?></div>
                                            <div style="font-size: 0.85rem; color: var(--text-light);"><?php echo $p['category']; ?></div>
                                        </td>
                                        <td><?php echo $p['category']; ?></td>
                                        <td>฿<?php echo number_format($p['price'], 2); ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td>
                                            <?php if ($p['stock'] == 0): ?>
                                                <span class="badge badge-danger">สินค้าหมด</span>
                                            <?php elseif ($p['stock'] < 10): ?>
                                                <span class="badge badge-warning">ใกล้หมด</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">พร้อมขาย</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline btn-sm" onclick='openEditModal(<?php echo json_encode($p); ?>)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <a href="?delete=<?php echo $p['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-bottom:1.5rem; font-size:1.5rem; font-weight:700;">เพิ่มสินค้าใหม่</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="product_name" class="form-control" required placeholder="เช่น เสื้อยืดสีขาว">
                </div>
                <div class="form-group"><label class="form-label">หมวดหมู่</label><input type="text" name="category" class="form-control" required placeholder="เช่น เสื้อผ้า"></div>
                <div class="row" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div class="form-group"><label class="form-label">ราคา (บาท)</label><input type="number" name="price" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">จำนวนสต็อก</label><input type="number" name="stock" class="form-control" required></div>
                </div>
                <div style="display:flex; gap:1rem; margin-top:2rem;">
                    <button type="button" class="btn btn-outline" style="flex:1;" onclick="document.getElementById('addProductModal').style.display='none'">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;">บันทึกสินค้า</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-bottom:1.5rem; font-size:1.5rem; font-weight:700;">แก้ไขสินค้า</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="product_id" id="edit_product_id">
                <div class="form-group">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                </div>
                <div class="form-group"><label class="form-label">หมวดหมู่</label><input type="text" name="category" id="edit_category" class="form-control" required></div>
                <div class="row" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div class="form-group"><label class="form-label">ราคา (บาท)</label><input type="number" name="price" id="edit_price" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">จำนวนสต็อก</label><input type="number" name="stock" id="edit_stock" class="form-control" required></div>
                </div>
                <div style="display:flex; gap:1rem; margin-top:2rem;">
                    <button type="button" class="btn btn-outline" style="flex:1;" onclick="document.getElementById('editProductModal').style.display='none'">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Search
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Open Edit Modal
        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_product_name').value = product.product_name;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock;

            document.getElementById('editProductModal').style.display = 'flex';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>

</html>