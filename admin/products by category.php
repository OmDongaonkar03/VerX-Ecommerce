<?php 
include("admin function.php");

$admin_email = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;
if (!$admin_email || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin_email)) {
    header("Location: admin login.php");
    exit();
}

$sql = mysqli_query($conn, "SELECT COUNT(*) AS 'COUNT', category FROM products GROUP BY category");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products by Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex;
            background: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: #1a237e;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding: 1.5rem;
            z-index: 1000;
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
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
        }
        
        .header {
            background: #212529;
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        
        .header i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin: 0 auto 1rem auto;
            max-width: 1200px;
        }
        
        .table {
            margin-bottom: 0;
            width: 100%;
        }
        
        .table thead {
            background: #0d6efd;
            color: white;
        }
        
        .table th, .table td {
            padding: 1rem;
            vertical-align: middle;
            text-align: left;
        }
        
        .table th {
            font-weight: 600;
            border: none;
        }
        
        .table td {
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th:nth-child(1) {
            width: 40%;
        }
        
        .table th:nth-child(2) {
            width: 30%;
        }
        
        .table th:nth-child(3) {
            width: 30%;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn {
            min-width: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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
        }

        .product-table .table {
            margin-bottom: 0;
        }

        .product-table thead {
            background: #e9ecef;
            color: #212529;
        }

        .product-table th {
            font-weight: 600;
            white-space: nowrap;
        }

        .product-table td {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-active {
            color: #198754;
            font-weight: 500;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: 500;
        }

        .product-table tbody tr:last-child td {
            border-bottom: none;
        }

        .btn i {
            transition: transform 0.3s;
        }

        .btn.collapsed i {
            transform: rotate(180deg);
        }

        /* Animation for collapse */
        .collapse {
            transition: all 0.3s ease;
        }

        /* Toast container styling */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php echo htmlspecialchars(sidebar(), ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="main-content">
        <div class="header">
            <i class="fas fa-box"></i>
            <h2 class="m-0">Products by Category</h2>
        </div>

        <!-- Toast Container -->
        <div class="toast-container" aria-live="polite" aria-atomic="true"></div>

        <?php
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $category = mysqli_real_escape_string($conn, $data['category']);
                $products_sql = sprintf("SELECT * FROM products WHERE category='%s'", $category);
                $products_result = mysqli_query($conn, $products_sql);
        ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($data['COUNT'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-<?php echo htmlspecialchars(strtolower($data['category']), ENT_QUOTES, 'UTF-8'); ?>" 
                                    aria-expanded="false">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="collapse" id="collapse-<?php echo htmlspecialchars(strtolower($data['category']), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="collapse-content">
                    <h5 class="mb-3">Products in <?php echo htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <div class="product-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Colors</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($products_result) > 0) {
                                    while ($product = mysqli_fetch_assoc($products_result)) {
                                        $productID = htmlspecialchars($product['productID'], ENT_QUOTES, 'UTF-8');
                                        $productName = htmlspecialchars($product['productName'], ENT_QUOTES, 'UTF-8');
                                        $price = htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8');
                                        $discount = htmlspecialchars($product['discount'], ENT_QUOTES, 'UTF-8');
                                        $description = htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8');
                                        $quantity = htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8');
                                        $colors = implode(", ", array_map(function($color) {
                                            return htmlspecialchars(trim($color), ENT_QUOTES, 'UTF-8');
                                        }, explode(",", $product['colors'])));
                                        $status = htmlspecialchars($product['status'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr>
                                    <td><?php echo $productID; ?></td>
                                    <td><?php echo $productName; ?></td>
                                    <td>$ <?php echo $price; ?></td>
                                    <td><?php echo $discount; ?>%</td>
                                    <td><?php echo $description; ?></td>
                                    <td><?php echo $quantity; ?></td>
                                    <td><?php echo $colors; ?></td>
                                    <td><?php echo $status; ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center">No products found in this category</td>
                                </tr>
                                <?php
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
        } else {
            echo "<script>showToast('No categories found.', 'danger');</script>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>