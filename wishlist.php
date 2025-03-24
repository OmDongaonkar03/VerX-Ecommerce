<?php
include('function.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
$user = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
$Userdata = mysqli_fetch_assoc($user);
$userid = mysqli_real_escape_string($conn, $Userdata['id']);

// Fetch wishlist items for this user only
$wishlist_query = mysqli_query($conn, sprintf("SELECT * FROM `wishlist` WHERE `userID` = '%s'", $userid));
$wishlist_count = mysqli_num_rows($wishlist_query);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VerX Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            background-color: var(--light);
            color: var(--dark);
        }

        .section-title {
            position: relative;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .wishlist-item {
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            margin-bottom: 1.5rem;
            background-color: #fff;
        }
        
        .wishlist-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-3px);
        }

        .remove-wishlist {
            position: absolute;
            right: 15px;
            top: 15px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-wishlist:hover {
            background: #dc3545;
            color: white;
            transform: rotate(90deg);
        }

        .product-img-container {
            overflow: hidden;
            border-radius: var(--border-radius);
            position: relative;
        }

        .product-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            transition: var(--transition);
        }

        .wishlist-item:hover .product-img {
            transform: scale(1.05);
        }

        .discount-badge {
            position: absolute;
            left: 15px;
            top: 15px;
            background: var(--secondary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            z-index: 1;
        }

        .empty-wishlist {
            text-align: center;
            padding: 80px 0;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .empty-wishlist i {
            font-size: 5rem;
            color: var(--border);
            margin-bottom: 1.5rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.5; }
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .product-details {
            padding: 1.5rem;
        }

        .price-tag {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .shipping-info {
            color: var(--secondary);
            font-weight: 500;
        }

        .category-badge {
            display: inline-block;
            background-color: #EEF2FF;
            color: var(--primary);
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 8px;
        }

        .product-id {
            color: #64748B;
            font-size: 0.85rem;
        }

        .view-btn {
            border-radius: 50px;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
            padding: 0.6rem 1rem;
        }

        .action-bar {
            background-color: #fff;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        .page-title {
            color: var(--dark);
            margin: 0;
            font-weight: 700;
        }

        .count-badge {
            background-color: var(--primary-light);
            color: #fff;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.85rem;
            margin-left: 10px;
            font-weight: 500;
        }

        .clear-btn {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div>
        <?php navbar(); ?>
    </div>

    <div class="container my-5" id="display">
        <?php if ($wishlist_count > 0) { ?>
            <div class="action-bar d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <h2 class="page-title">My Wishlist <span class="count-badge"><?php echo htmlspecialchars($wishlist_count, ENT_QUOTES, 'UTF-8'); ?> items</span></h2>
                </div>
                <button class="btn btn-outline-danger clear-btn" type="button" onclick="clearAllProd()">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
            </div>
        
            <div class="row">
                <?php while ($data = mysqli_fetch_assoc($wishlist_query)) { ?>
                <div class="col-md-6 col-lg-4">
                    <div class="wishlist-item">
                        <div class="product-img-container">
                            <img src="<?php echo htmlspecialchars(substr($data['productImage'], 3), ENT_QUOTES, 'UTF-8'); ?>" class="product-img" alt="<?php echo htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <button class="remove-wishlist" type="button" onclick="clearProd(<?php echo filter_var($data['productID'], FILTER_VALIDATE_INT); ?>)">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="product-details">
                            <h5 class="mb-1"><?php echo htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="product-id mb-2">Product ID: <?php echo htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="category-badge">
                                <i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($data['productCategory'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <h4 class="price-tag mb-1">$<?php echo htmlspecialchars($data['productPrice'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p class="shipping-info mb-0"><i class="fas fa-truck me-2"></i>Free Shipping</p>
                                </div>
                                <div>
                                    <?php $encodedID = urlencode(base64_encode($data['productID'])); ?>
                                    <a href="productDetails.php?param=<?php echo $encodedID; ?>" class="btn btn-primary view-btn">
                                        <i class="fas fa-eye me-2"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="empty-wishlist">
                <i class="far fa-heart"></i>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted mb-4">Explore our products and add your favorites to the wishlist!</p>
                <a href="productpage.php" class="btn btn-primary">
                    Browse Products
                </a>
            </div>
        <?php } ?>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="wishlistToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Wishlist</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <div>
        <?php footer(); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function clearProd(prod) {
            if (!Number.isInteger(prod)) return; // Validate product ID
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    const toastEl = document.getElementById('wishlistToast');
                    const toastBody = toastEl.querySelector('.toast-body');
                    const toast = new bootstrap.Toast(toastEl);

                    if (this.status == 200) {
                        toastBody.textContent = 'Item removed from wishlist!';
                        toastEl.classList.remove('bg-danger');
                        toastEl.classList.add('bg-success');
                        toast.show();
                        setTimeout(() => location.reload(), 1500); // Refresh page after 1.5s
                    } else {
                        toastBody.textContent = 'Failed to remove item: ' + this.statusText;
                        toastEl.classList.remove('bg-success');
                        toastEl.classList.add('bg-danger');
                        toast.show();
                    }
                }
            };
            xhttp.open("GET", "ajaxreq.php?param=removewish&id=" + encodeURIComponent(prod), true);
            xhttp.send();
        }
        
        function clearAllProd() {
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    const toastEl = document.getElementById('wishlistToast');
                    const toastBody = toastEl.querySelector('.toast-body');
                    const toast = new bootstrap.Toast(toastEl);

                    if (this.status == 200) {
                        toastBody.textContent = 'Wishlist cleared successfully!';
                        toastEl.classList.remove('bg-danger');
                        toastEl.classList.add('bg-success');
                        toast.show();
                        setTimeout(() => location.reload(), 1500); // Refresh page after 1.5s
                    } else {
                        toastBody.textContent = 'Failed to clear wishlist: ' + this.statusText;
                        toastEl.classList.remove('bg-success');
                        toastEl.classList.add('bg-danger');
                        toast.show();
                    }
                }
            };
            xhttp.open("GET", "ajaxreq.php?param=removeAllwish", true);
            xhttp.send();
        }
    </script>
</body>
</html>