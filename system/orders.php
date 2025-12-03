<?php
require_once 'db.php';
checkLogin();

// Get Orders with Customer Name
$stmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.total_amount, c.customer_name 
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Clothery System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="top-header">
                <div class="header-search">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="ค้นหาเลขที่สั่งซื้อ...">
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
                    <h1 class="page-title">รายการขายทั้งหมด</h1>
                    <a href="pos.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> สร้างรายการขายใหม่
                    </a>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>รหัสสั่งซื้อ</th>
                                    <th>วันที่/เวลา</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดรวม</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><span style="font-family: monospace; font-weight: 600;">#<?php echo $order['order_id']; ?></span></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo $order['customer_name'] ? $order['customer_name'] : 'Walk-in Customer'; ?></td>
                                        <td>฿<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><span class="badge badge-success">สำเร็จ</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
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
    </script>
</body>

</html>