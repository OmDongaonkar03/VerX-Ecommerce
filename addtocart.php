<?php 
include_once('function.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);

// Get user data
$user = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
$Userdata = mysqli_fetch_assoc($user);

// User Data
$Userid = mysqli_real_escape_string($conn, $Userdata['id']);
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verx - Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #F5F7FA;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Cart Layout */
        .page-title {
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--dark);
        }

        .cart-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 1.5rem;
        }

        /* Product Card */
        .product-card {
            display: flex;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .product-img-wrapper {
            width: 110px;
            height: 110px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #F3F4F6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-img:hover {
            transform: scale(1.05);
        }

        .product-details {
            flex-grow: 1;
            padding: 0 1.5rem;
        }

        .product-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
            transition: color 0.2s;
        }

        .product-name:hover {
            color: var(--primary);
        }

        .product-meta {
            color: #6B7280;
            font-size: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .product-meta span {
            display: inline-flex;
            align-items: center;
        }

        .product-meta i {
            margin-right: 4px;
            font-size: 0.8rem;
        }

        .product-price {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark);
            margin: 0;
        }

        .item-total {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
            white-space: nowrap;
        }

        /* Quantity Selector */
        .quantity-selector {
            display: flex;
            align-items: center;
            background: #F9FAFB;
            border-radius: 8px;
            padding: 0.25rem;
            width: 120px;
            border: 1px solid var(--border);
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: none;
            border-radius: 6px;
            color: var(--dark);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .quantity-btn:hover {
            background: var(--primary-light);
            color: white;
        }

        .quantity-input {
            width: 40px;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 600;
            color: var(--dark);
            -moz-appearance: textfield;
            margin: 0 4px;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .remove-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FEF2F2;
            color: #EF4444;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .remove-btn:hover {
            background: #EF4444;
            color: white;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 3rem 0;
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: #D1D5DB;
            margin-bottom: 1.5rem;
        }

        .empty-cart h5 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: #6B7280;
            margin-bottom: 1.5rem;
        }

        /* Order Summary */
        .summary-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            position: sticky;
            top: 2rem;
        }

        .summary-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-size: 1.25rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .summary-label {
            color: #6B7280;
        }

        .summary-value {
            font-weight: 600;
        }

        .summary-divider {
            border-top: 1px solid var(--border);
            margin: 1rem 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .summary-total-label {
            font-weight: 700;
        }

        .summary-total-value {
            font-weight: 700;
            color: var(--primary);
        }

        /* Promo Code */
        .promo-code {
            margin-bottom: 1.5rem;
        }

        .promo-input {
            display: flex;
            gap: 0.5rem;
        }

        .promo-input input {
            flex-grow: 1;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .promo-input input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .promo-btn {
            background: #F3F4F6;
            border: 1px solid var(--border);
            color: var(--dark);
            font-weight: 600;
            border-radius: 8px;
            padding: 0 1rem;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
        }

        .promo-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* Checkout Button */
        .checkout-btn {
            display: block;
            width: 100%;
            background: var(--primary);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: background 0.2s;
            text-decoration: none;
        }

        .checkout-btn:hover {
            background: var(--primary-light);
            color: white;
        }

        .checkout-btn i {
            margin-right: 0.5rem;
        }

        /* Continue Shopping */
        .continue-shopping {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            margin-top: 1rem;
            transition: color 0.2s;
        }

        .continue-shopping:hover {
            color: var(--primary-light);
        }

        .continue-shopping i {
            margin-right: 0.5rem;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animated {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .product-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-img-wrapper {
                margin-bottom: 1rem;
            }

            .product-details {
                padding: 0;
                margin-bottom: 1rem;
                width: 100%;
            }

            .product-actions {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .quantity-actions {
                display: flex;
                width: 100%;
                gap: 0.5rem;
            }

            .quantity-selector {
                flex-grow: 1;
            }

            .actions {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 767.98px) {
            .summary-card {
                position: static;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar-->
    <?php navbar(); ?>

    <!-- Main Content -->
    <div class="container py-5 animated">
        <h2 class="page-title">Your Shopping Cart</h2>
        
        <div class="row g-4" id="display">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="cart-container">
                    <div class="cart-header">
                        <h4 class="mb-0">Shopping Cart</h4>
                        <?php
                        $sql = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $Userid));
                        ?>
                    </div>
                
                    <div id="cartItems">
                        <?php
                        if (mysqli_num_rows($sql) > 0) {
                            while ($data = mysqli_fetch_assoc($sql)) {
                                $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                                $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                                $productPrice = htmlspecialchars($data['productPrice'], ENT_QUOTES, 'UTF-8');
                                $productImage = htmlspecialchars($data['productImage'], ENT_QUOTES, 'UTF-8');
                                $productCategory = htmlspecialchars($data['productCategory'], ENT_QUOTES, 'UTF-8');
                                $productQuantity = htmlspecialchars($data['productQuantity'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="product-card animated" style="animation-delay: 0.3s">
                            <div class="product-img-wrapper">
                                <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="product-img">
                            </div>
                            
                            <div class="product-details">
                                <a href="#" class="text-decoration-none">
                                    <h5 class="product-name"><?php echo $productName; ?></h5>
                                </a>
                                <div class="product-meta">
                                    <span><i class="fas fa-tag"></i> <?php echo $productCategory; ?></span>
                                </div>
                                <p class="product-price mt-2">₹<?php echo $productPrice; ?></p>
                            </div>
                            
                            <div class="product-actions d-flex gap-4">
                                <div class="quantity-actions">
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn" onclick="updateQty(<?php echo $productID; ?>, 'sub')">-</button>
                                        <input type="number" class="quantity-input" value="<?php echo $productQuantity; ?>" min="1" readonly>
                                        <button type="button" class="quantity-btn" onclick="updateQty(<?php echo $productID; ?>, 'add')">+</button>
                                    </div>  
                                </div>
                                <div class="actions gap-3">
                                    <span class="item-total">₹<?php echo floatval($productPrice) * intval($productQuantity); ?></span>
                                    <button type="button" class="remove-btn" onclick="removeItem(<?php echo $productID; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                        ?>
                            <div id="emptyCart" class="empty-cart">
                                <div class="empty-cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h5>Your cart is empty</h5>
                                <p>Looks like you haven't added anything to your cart yet.</p>
                                <a href="productpage.php" class="btn btn-primary px-4 py-2">Continue Shopping</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
                <a href="productpage.php" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <!-- Order Summary -->
            <?php
            $total_amount = 0;
            $tamt = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $Userid));
            $tamt_count = mysqli_num_rows($tamt);
            if (mysqli_num_rows($tamt) > 0) {
                while ($tamt_data = mysqli_fetch_assoc($tamt)) {
                    $total_amount += floatval($tamt_data['productPrice']) * intval($tamt_data['productQuantity']);
                }
            } else {
                $total_amount = 0;
            }
            ?>
            <div class="col-lg-4">
                <div class="summary-card animated" style="animation-delay: 0.4s">
                    <h5 class="summary-title">Order Summary</h5>
                    
                    <div class="summary-row">
                        <span class="summary-label">Subtotal (<span id="itemCount"><?php echo htmlspecialchars($tamt_count, ENT_QUOTES, 'UTF-8'); ?></span> items)</span>
                        <span class="summary-value subtotal">₹<?php echo htmlspecialchars($total_amount, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value shipping">Free</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-total">
                        <span class="summary-total-label">Total</span>
                        <span class="summary-total-value total">₹<?php echo htmlspecialchars($total_amount, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php if ($tamt_count > 0) { ?>
                    <a href="buynow.php?param=requestATC" class="checkout-btn">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                    <div class="mt-4">
                        <div class="d-flex align-items-center justify-content-center gap-2 text-muted">
                            <i class="fas fa-shield-alt"></i>
                            <span class="small">Secure Checkout</span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php footer(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Remove item
        function removeItem(ID) {
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "ajaxreq.php?param=removeatc&id=" + ID, true);
            xhttp.send();
        }
        
        // Change quantity
        function updateQty(id, req) {
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "ajaxreq.php?param=qtychange&id=" + id + "&request=" + req, true);
            xhttp.send();
        }
    </script>
</body>
</html>