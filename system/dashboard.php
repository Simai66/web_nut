<?php
require_once 'db.php';
checkLogin();

// Get Stats
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT SUM(total_amount) as total_sales, COUNT(*) as total_orders FROM orders WHERE DATE(order_date) = ?");
$stmt->execute([$today]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as low_stock FROM products WHERE stock < 10");
$stmt->execute();
$low_stock = $stmt->fetch()['low_stock'];

// Get Recent Orders
$stmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.total_amount, c.customer_name 
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_date DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();

// Get Daily Sales for Current Month (for Chart)
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

$chart_stmt = $pdo->prepare("
    SELECT DATE(order_date) as sale_date, SUM(total_amount) as daily_total 
    FROM orders 
    WHERE DATE(order_date) BETWEEN ? AND ?
    GROUP BY DATE(order_date)
    ORDER BY sale_date ASC
");
$chart_stmt->execute([$current_month_start, $current_month_end]);
$daily_sales = $chart_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fill in missing days with 0
$chart_labels = [];
$chart_data = [];
$period = new DatePeriod(
    new DateTime($current_month_start),
    new DateInterval('P1D'),
    new DateTime($current_month_end . ' +1 day')
);

foreach ($period as $date) {
    $date_str = $date->format('Y-m-d');
    $chart_labels[] = $date->format('d M'); // Format: 01 Jan
    $chart_data[] = $daily_sales[$date_str] ?? 0;
}

// Get Best Selling Products
$best_stmt = $pdo->query("
    SELECT p.product_name, SUM(oi.quantity) as total_qty, SUM(oi.price_each * oi.quantity) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    GROUP BY oi.product_id
    ORDER BY total_qty DESC
    LIMIT 5
");
$best_sellers = $best_stmt->fetchAll();

// Get Peak Sales Hour
$peak_stmt = $pdo->query("
    SELECT HOUR(order_date) as hour_of_day, COUNT(*) as order_count
    FROM orders
    GROUP BY HOUR(order_date)
    ORDER BY order_count DESC
    LIMIT 1
");
$peak_hour_data = $peak_stmt->fetch();
$peak_hour = $peak_hour_data ? $peak_hour_data['hour_of_day'] : null;
$peak_hour_display = $peak_hour !== null ? sprintf("%02d:00 - %02d:00", $peak_hour, $peak_hour + 1) : '-';

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Clothery System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 1024px) {

            .dashboard-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .best-seller-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .best-seller-item:last-child {
            border-bottom: none;
        }

        .rank-badge {
            width: 24px;
            height: 24px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            margin-right: 1rem;
        }

        .best-seller-info {
            flex: 1;
        }

        .best-seller-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--secondary-color);
        }

        .best-seller-stats {
            font-size: 0.85rem;
            color: var(--text-light);
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
                    <input type="text" class="search-input" placeholder="ค้นหาสินค้า, ออเดอร์...">
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
                    <h1 class="page-title">ภาพรวมร้านค้า</h1>
                    <a href="pos.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> สร้างรายการขายใหม่
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-light"><i class="fa-solid fa-dollar-sign"></i></div>
                        <div class="stat-info">
                            <h3>ยอดขายวันนี้</h3>
                            <div class="stat-value">฿<?php echo number_format($stats['total_sales'] ?? 0, 2); ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-green-light"><i class="fa-solid fa-bag-shopping"></i></div>
                        <div class="stat-info">
                            <h3>ออเดอร์วันนี้</h3>
                            <div class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-purple-light"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-info">
                            <h3>ช่วงเวลาขายดี</h3>
                            <div class="stat-value" style="font-size: 1.25rem;"><?php echo $peak_hour_display; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-orange-light"><i class="fa-solid fa-box-open"></i></div>
                        <div class="stat-info">
                            <h3>สินค้าใกล้หมด</h3>
                            <div class="stat-value"><?php echo $low_stock; ?></div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Sales Chart -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-header" style="margin-bottom: 1.5rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 600;">ยอดขายประจำเดือน <?php echo date('F Y'); ?></h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- Best Sellers -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="card-header" style="margin-bottom: 1rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 600;">สินค้าขายดี 🏆</h3>
                        </div>
                        <div>
                            <?php if (count($best_sellers) > 0): ?>
                                <?php foreach ($best_sellers as $index => $item): ?>
                                    <div class="best-seller-item">
                                        <div class="rank-badge"><?php echo $index + 1; ?></div>
                                        <div class="best-seller-info">
                                            <div class="best-seller-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                            <div class="best-seller-stats">ขายแล้ว <?php echo $item['total_qty']; ?> ชิ้น</div>
                                        </div>
                                        <div style="font-weight: 600; color: var(--primary-color);">
                                            ฿<?php echo number_format($item['total_revenue'], 0); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--text-light); text-align: center; padding: 2rem;">ยังไม่มีข้อมูลการขาย</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 600;">รายการขายล่าสุด</h3>
                        <a href="orders.php" style="color: var(--primary-color); font-size: 0.85rem;">ดูทั้งหมด</a>
                    </div>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>รหัสสั่งซื้อ</th>
                                    <th>ลูกค้า</th>
                                    <th>วันที่</th>
                                    <th>ยอดรวม</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><span style="font-family:monospace;">#<?php echo $order['order_id']; ?></span></td>
                                        <td><?php echo $order['customer_name'] ? $order['customer_name'] : 'Walk-in Customer'; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td style="font-weight:600;">฿<?php echo number_format($order['total_amount'], 2); ?></td>
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
        // Search Functionality
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

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            family: 'Inter',
                            size: 13
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return '฿' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#e2e8f0',
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            callback: function(value) {
                                return '฿' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            maxTicksLimit: 10
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>