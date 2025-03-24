<?php 
include("admin function.php");

$admin_email = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;
if (!$admin_email || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin_email)) {
    header("Location: admin login.php");
    exit();
}

$sql = mysqli_query($conn, "SELECT COUNT(*) AS 'COUNT', orderDate FROM `ordered products` GROUP BY orderDate");

$saleMonth = mysqli_query($conn, "SELECT COUNT(*) AS orderCount, orderMonth FROM `ordered products` GROUP BY orderMonth");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Insight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #1a237e;
            --header-bg: #212529;
            --table-header-bg: #0d6efd;
        }

        body {
            display: flex;
            background: #f8f9fa;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            min-height: 100vh;
            background: #1a237e;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: #fff !important;
            padding: 0.5rem;
            margin:0 0 6px 0;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
        }
        
        .header {
            background: var(--header-bg);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header i {
            font-size: 1.75rem;
            margin-right: 1rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            height: 500px;
            width: 100%;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            width: 100%;
        }
        
        .table {
            margin-bottom: 0;
            width: 100%;
        }
        
        .table thead {
            background: var(--table-header-bg);
            color: white;
        }
        
        .table th {
            padding: 1rem;
            font-weight: 600;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        .collapse-content {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
            margin-top: 1rem;
        }

        .product-table {
            margin-top: 1rem;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn {
            padding: 0.5rem 1rem;
            min-width: 120px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn i {
            transition: transform 0.3s;
        }

        .btn.collapsed i {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 1rem 0.5rem;
            }
            
            .nav-link span {
                display: none;
            }
            
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            
            .header {
                padding: 1rem;
            }
            
            .table-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php echo htmlspecialchars(sidebar(), ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="main-content">
        <div class="header">
            <i class="fas fa-chart-line"></i>
            <h2 class="m-0">Sales Insight</h2>
        </div>

        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>

        <?php
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $order_date = mysqli_real_escape_string($conn, $data['orderDate']);
                $products_sql = mysqli_query($conn, sprintf("SELECT * FROM `ordered products` WHERE orderDate='%s' AND `Status` = 'Completed'", $order_date));
                $total_num_prod = mysqli_num_rows($products_sql);
                
                $products = [];
                $total_amount = 0;
                while ($row = mysqli_fetch_assoc($products_sql)) {
                    $products[] = $row;
                    $total_amount += floatval($row['price']);
                }
        ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders Placed</th>
                        <th>Amount Collected</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($data['orderDate'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($total_num_prod, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>$<?php echo number_format($total_amount, 2); ?></td>
                        <td>
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $data['orderDate'])), ENT_QUOTES, 'UTF-8'); ?>" 
                                    aria-expanded="false">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse" id="collapse-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $data['orderDate'])), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="collapse-content">
                    <h5 class="mb-3">Orders for <?php echo htmlspecialchars($data['orderDate'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <div class="product-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Price</th>
                                    <th>User ID</th>
                                    <th>User Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($products) > 0) {
                                    foreach ($products as $product) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['productID'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>$<?php echo number_format(floatval($product['price']), 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['userID'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($product['userContact'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php 
        if (mysqli_num_rows($saleMonth) > 0) {
            $monthDataArray = [];
            while ($monthData = mysqli_fetch_assoc($saleMonth)) {
                $monthDataArray[] = [
                    'orderMonth' => ucfirst(strtolower($monthData['orderMonth'])), // Normalize to "January", "February", etc.
                    'orderCount' => $monthData['orderCount']
                ];
            }

            $monthOrder = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
                'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8,
                'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
            ];

            // Sort by month order
            usort($monthDataArray, function($a, $b) use ($monthOrder) {
                return $monthOrder[$a['orderMonth']] - $monthOrder[$b['orderMonth']];
            });

            $monthArr = array_map(function($item) {
                return $item['orderMonth'];
            }, $monthDataArray);

            $salesArr = array_map(function($item) {
                return (int)$item['orderCount'];
            }, $monthDataArray);
        } else {
            $monthArr = [];
            $salesArr = [];
        }
        ?>

        // Initialize line chart with monthly sales data
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthArr); ?>,
                datasets: [{
                    label: 'Monthly Sales',
                    data: <?php echo json_encode($salesArr); ?>,
                    backgroundColor: 'rgba(82, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    fill: true, 
                    tension: 0.3 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    </script>
</body>
</html>