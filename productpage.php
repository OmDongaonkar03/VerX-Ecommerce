<?php 
include_once('function.php');

$userEmail = mysqli_real_escape_string($conn, $_SESSION["user_email"]);
$userdata = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $userEmail));
$userdata = mysqli_fetch_assoc($userdata);
$userID = $userdata['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['prodID'])) {
        $prod = filter_var($_POST['prodID'], FILTER_VALIDATE_INT);
        if ($prod === false) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="prodErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid product ID
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("prodErrorToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        } else {
            $encodedID = urlencode(base64_encode($prod));
            header("Location: productDetails.php?param=$encodedID");
            exit();
        }
    }
    
    if (isset($_POST['wishID'])) {
        $wish_prod = filter_var($_POST['wishID'], FILTER_VALIDATE_INT);
        if ($wish_prod === false) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="wishErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid wishlist product ID
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("wishErrorToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        } else {
            $wish_prod = mysqli_real_escape_string($conn, $wish_prod);
            $prod_sql = mysqli_query($conn, sprintf("SELECT * FROM `products` WHERE `productID` = '%s'", $wish_prod));
            $product_data = mysqli_fetch_assoc($prod_sql);

            if ($product_data) {
                $wishlist_add = mysqli_query($conn, sprintf(
                    "INSERT INTO `wishlist`(`userId`, `productID`, `productName`, `productPrice`, `productImage`, `productCategory`) 
                    VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                    mysqli_real_escape_string($conn, $userID),
                    $wish_prod,
                    mysqli_real_escape_string($conn, $product_data['productName']),
                    mysqli_real_escape_string($conn, $product_data['price']),
                    mysqli_real_escape_string($conn, $product_data['product_Image']),
                    mysqli_real_escape_string($conn, $product_data['category'])
                ));

                if ($wishlist_add) {
                    header("Location: wishlist.php");
                    exit();
                } else {
                    echo '
                    <div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div id="wishFailToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">Error</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Something went wrong adding to wishlist
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var toastEl = document.getElementById("wishFailToast");
                            var toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        });
                    </script>';
                }
            } else {
                echo '
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="prodNotFoundToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Product not found
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var toastEl = document.getElementById("prodNotFoundToast");
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                </script>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VerX Product Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #6366F1;
            --secondary: #10B981;
            --dark: #1F2937;
            --light: #F9FAFB;
            --border: #E5E7EB;
            --border-radius: 12px;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            color: var(--dark);
        }

        .search-container {
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .search-input {
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .search-btn {
            background-color: var(--primary);
            border: none;
            border-radius: var(--border-radius);
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .search-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .filter-section {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 20px;
        }

        .filter-section h5 {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary);
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group h6 {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
            color: var(--dark);
            font-weight: 500;
        }

        .price-input {
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            padding: 0.5rem;
        }

        .filter-btn {
            background-color: var(--primary);
            border: none;
            border-radius: var(--border-radius);
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
        }
		.reset-btn {
            border: none;
            border-radius: var(--border-radius);
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
        }

        .filter-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            border: none;
			max-height:800px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            height: 400px;
            object-fit: cover;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            width: 100%;
            transition: var(--transition);
        }
		
        .product-details {
            padding: 1.5rem;
        }

        .product-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .product-category {
            display: inline-block;
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            margin-bottom: 1rem;
        }

        .product-description {
            color: #6B7280;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--dark);
            margin-right: 0.75rem;
        }

        .product-discount {
            background-color: #FEF2F2;
            color: #EF4444;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
        }

        .product-status {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-in-stock {
            color: var(--secondary);
        }
        
        .status-out-stock {
            color: #EF4444;
        }

        .product-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .view-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            flex-grow: 1;
            margin-right: 0.5rem;
            transition: var(--transition);
        }

        .view-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .wishlist-btn {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: var(--border-radius);
            padding: 0.6rem;
            transition: var(--transition);
        }
		.wishlist-done {
            padding: 0.6rem;
			border:none;
			background-color:white;
        }

        .wishlist-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .wishlist-btn.active {
            background-color: #FEF2F2;
            color: #EF4444;
            border-color: #EF4444;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }

        .pagination-btn {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: var(--border-radius);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .pagination-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .pagination-btn.active {
            background-color: var(--primary);
            color: white;
        }

        .shipping-info {
            display: flex;
            align-items: center;
            color: var(--secondary);
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .shipping-info i {
            margin-right: 0.4rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php navbar() ?>

    <!-- Search Section -->
    <div class="container mt-4">
        <div class="search-container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <input class="form-control search-input me-2" type="search" name="search" id="searchInput" placeholder="Search for products..." aria-label="Search">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Filter Section -->
            <div class="col-lg-3 col-md-4">
                <div class="filter-section">
                    <h5>Filters</h5>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h6>Category</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="men" onchange="category()">
                            <label class="form-check-label" for="mens">Mens</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="women" onchange="category()">
                            <label class="form-check-label" for="womens">Womens</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="kids" onchange="category()">
                            <label class="form-check-label" for="kids">Kids</label>
                        </div>
                    </div>

                    <!-- Discount Filter -->
                    <div class="filter-group">
                        <h6>Discount</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discount" id="10" onchange="discountSelected(this)">
                            <label class="form-check-label" for="discount10">10% or more</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discount" id="20" onchange="discountSelected(this)">
                            <label class="form-check-label" for="discount20">20% or more</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discount" id="30" onchange="discountSelected(this)">
                            <label class="form-check-label" for="discount30">30% or more</label>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <h6>Price Range</h6>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="min" class="form-control price-input" placeholder="Min">
                            <span>-</span>
                            <input type="number" id="max" class="form-control price-input" placeholder="Max">
                        </div>
                    </div>
                    
                    <button type="button" class="btn filter-btn mb-2" onclick="pricefilter()">Apply Filters</button>
                    <button type="button" class="btn reset-btn btn-secondary mb-2" onclick="reset()">Reset</button>
                </div>
            </div>

            <!-- Products Section -->
            <div class="col-lg-9 col-md-8">
                <div class="row" id="display">
                    <?php
                    $sql = mysqli_query($conn, "SELECT * FROM `products`");
                    if (mysqli_num_rows($sql) > 0) {
                        while ($data = mysqli_fetch_assoc($sql)) {
                            $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                            $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                            $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
                            $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                            $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
                            $productImage = htmlspecialchars(substr($data['product_Image'], 3), ENT_QUOTES, 'UTF-8');
                            $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
                            $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="product-card h-100" id="product_card">
                            <img src="<?php echo $productImage; ?>" alt="product" class="product-img">
                            <div class="product-details">
                                <span class="product-category"><?php echo $category; ?></span>
                                <h5 class="product-name"><?php echo $productName; ?></h5>
                                <p class="product-description"><?php echo $description; ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="product-price">₹<?php echo $price; ?></span>
                                    <span class="product-discount"><?php echo $discount; ?>% off</span>
                                </div>
                                <div class="shipping-info">
                                    <i class="fas fa-truck"></i> Free shipping
                                </div>
                                <div class="mt-3">
                                    <span class="product-status status-in-stock">
                                        <i class="fas fa-check-circle"></i> <?php echo $status; ?>
                                    </span>
                                </div>
                                <div class="product-buttons">
                                    <form method="POST">
                                        <button class="view-btn" type="submit" name="prodID" value="<?php echo $productID; ?>">
                                            View Details
                                        </button>
                                        <?php
                                        $check_wish = mysqli_query($conn, sprintf("SELECT * FROM `wishlist` WHERE `productID` = '%s'", $productID));
                                        if (mysqli_num_rows($check_wish) > 0) {
                                        ?>
                                            <button class="wishlist-done" disabled style="color: red;">
                                                <i class="fa-solid fa-heart fa-lg"></i>
                                            </button>
                                        <?php
                                        } else {
                                        ?>
                                            <button class="wishlist-btn" type="submit" name="wishID" value="<?php echo $productID; ?>">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        <?php
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php footer(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', search);
        
        function search() {
            let word = document.getElementById("searchInput").value;
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "search";
            xhttp.open("GET", "ajaxreq.php?param=" + param + "&input=" + word, true);
            xhttp.send();
        }
        
        function reset() {
            document.getElementById('max').value = '';
            document.getElementById('min').value = '';
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "reset";
            xhttp.open("GET", "ajaxreq.php?param=" + param, true);
            xhttp.send();
        }
        
        function discountSelected(element) {
            let discount = element.id;
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "discount";
            xhttp.open("GET", "ajaxreq.php?param=" + param + "&input=" + discount, true);
            xhttp.send();
        }
        
        function category() {
            let selectedCategories = [];
        
            document.querySelectorAll(".form-check-input").forEach(checkbox => {
                if (checkbox.checked) {
                    selectedCategories.push(checkbox.id);
                }
            });
        
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
        
            let param = "category";
            let input = encodeURIComponent(JSON.stringify(selectedCategories));
            xhttp.open("GET", "ajaxreq.php?param=" + param + "&input=" + input, true);
            xhttp.send();
        }
        
        function pricefilter() {
            let max = document.getElementById('max').value || 0;
            let min = document.getElementById('min').value || 1000000000000;
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            
            let param = "price";
            xhttp.open("GET", "ajaxreq.php?param=" + param + "&min=" + min + "&max=" + max, true);
            xhttp.send();
        }
    </script>
</body>
</html>