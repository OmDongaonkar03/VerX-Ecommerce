<?php
include("admin function.php");

$admin_email = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;
if (!$admin_email || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin_email)) {
    header("Location: admin login.php");
    exit();
}

$sql = mysqli_query($conn, "SELECT * FROM `products`");

$safe_admin_email = mysqli_real_escape_string($conn, $admin_email);
$privileges_query = sprintf("SELECT privileges FROM `admin details` WHERE `admin_email` = '%s'", $safe_admin_email);
$privileges_result = mysqli_query($conn, $privileges_query);
$privileges_data = mysqli_fetch_assoc($privileges_result);
$privileges = explode(",", htmlspecialchars($privileges_data['privileges'], ENT_QUOTES, 'UTF-8'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Product Details</title>
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
           transition: all 0.3s;
       }
       
       .sidebar.collapsed {
           width: 60px;
       }
       
       .sidebar.collapsed .nav-link span,
       .sidebar.collapsed h3 span {
           display: none;
       }
       
       .nav-link {
           color: #fff !important;
           padding: 0.5rem;
           margin:0 0 6px 0;
           border-radius: 8px;
           transition: all 0.3s;
           display: flex;
           align-items: center;
           white-space: nowrap;
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
           transition: all 0.3s;
       }
       
       .main-content.expanded {
           margin-left: 60px;
       }
       
       .header {
           background: #212529;
           color: white;
           padding: 1.5rem;
           border-radius: 12px;
           margin-bottom: 2rem;
           display: flex;
           align-items: center;
           justify-content: space-between;
       }
       
       .table-container {
           background: white;
           border-radius: 12px;
           box-shadow: 0 4px 6px rgba(0,0,0,0.1);
           padding: 1.5rem;
           overflow-x: auto;
       }
       
       .table thead {
           background: #0d6efd;
           color: white;
       }
       
       .table th {
           padding: 1rem;
           font-weight: 600;
           white-space: nowrap;
       }
       
       .table td {
           padding: 1rem;
           vertical-align: middle;
       }
       
       .table tbody tr:hover {
           background-color: #f8f9fa;
       }
       
       .description-cell {
           max-width: 200px;
           overflow: hidden;
           text-overflow: ellipsis;
           white-space: nowrap;
       }
   </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <?php 
        echo htmlspecialchars(sidebar(), ENT_QUOTES, 'UTF-8'); 
        ?>
    </div>

    <div class="main-content" id="main">
        <div class="header">
            <h2 class="m-0">Product Details</h2>
        </div>

        <div class="mb-4 px-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search..." 
                       aria-label="Search" name="search" id="searchInput">
            </div>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Colors</th>
                        <th>Status</th>
                        <?php if (in_array('edit', $privileges)) { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="display">
                    <?php
                    if (mysqli_num_rows($sql) > 0) {
                        while ($data = mysqli_fetch_assoc($sql)) {
                            $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                            $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                            $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
                            $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                            $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
                            $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
                            $quantity = htmlspecialchars($data['quantity'], ENT_QUOTES, 'UTF-8');
                            $colors = implode(", ", array_map(function($color) {
                                return htmlspecialchars(trim($color), ENT_QUOTES, 'UTF-8');
                            }, explode(",", $data['colors'])));
                            $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?php echo $productID; ?></td>
                        <td><?php echo $productName; ?></td>
                        <td><?php echo $category; ?></td>
                        <td><?php echo $price; ?></td>
                        <td><?php echo $discount; ?></td>
                        <td class="description-cell"><?php echo $description; ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td><?php echo $colors; ?></td>
                        <td><?php echo $status; ?></td>
                        <?php if (in_array('edit', $privileges)) { ?>
                            <td>
                                <button type="button" class="btn btn-danger mt-2" 
                                        onclick="remove_prod(<?php echo urlencode($productID); ?>)">Remove</button>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', search);
        
        function search() {
            let word = encodeURIComponent(document.getElementById("searchInput").value);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "product_search";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&input=" + word, true);
            xhttp.send();
        }
        
        function remove_prod(product) {
            let safeProduct = encodeURIComponent(product);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "remove_product";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&product=" + safeProduct, true);
            xhttp.send();
        }
    </script>
</body>
</html>