<?php
include('function.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$parameter = isset($_GET['param']) ? $_GET['param'] : '';
$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);

// Get user data
$user = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
$Userdata = mysqli_fetch_assoc($user);

// User Data
$Userid = mysqli_real_escape_string($conn, $Userdata['id']);
$Username = htmlspecialchars($Userdata['name'], ENT_QUOTES, 'UTF-8');
$Useremail = htmlspecialchars($Userdata['email'], ENT_QUOTES, 'UTF-8');
$Usercontact = htmlspecialchars($Userdata['contact'], ENT_QUOTES, 'UTF-8');
$Userstatus = htmlspecialchars($Userdata['Status'], ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['address'], $_POST['city'], $_POST['state'], $_POST['pinCode'])) {
        $userAddress = mysqli_real_escape_string($conn, $_POST['address']);
        $userCity = mysqli_real_escape_string($conn, $_POST['city']);
        $userState = mysqli_real_escape_string($conn, $_POST['state']);
        $userPinCode = filter_var($_POST['pinCode'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 100000, 'max_range' => 999999]]);
        
        if ($userPinCode === false || empty($userState) || !in_array($userState, ['Maharashtra', 'Delhi', 'Goa'])) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="fieldsToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Please provide valid shipping details (6-digit pin code and valid state).
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("fieldsToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        } else {
            $userPinCode = mysqli_real_escape_string($conn, $userPinCode);
            $orderMonth = date("F");
            $orderDate = date("Y-m-d");
            $orderTime = date("Y-m-d H:i:s");

            if ($parameter != 'requestATC') {
                // Single Product Order
                $productID = filter_var(base64_decode($parameter), FILTER_VALIDATE_INT);
                if ($productID === false) {
                    exit; // Invalid product ID
                }
                $productID = mysqli_real_escape_string($conn, $productID);
                $product = mysqli_query($conn, sprintf("SELECT * FROM `products` WHERE `productID` = '%s'", $productID));
                if ($Productdata = mysqli_fetch_assoc($product)) {
                    $productName = htmlspecialchars($Productdata['productName'], ENT_QUOTES, 'UTF-8');
                    $quantity = filter_var(base64_decode($_GET['quantity']), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    $selectedColor = base64_decode($_GET['color']);
                    $productPrice = floatval($Productdata['price']);
                    $totalPrice = $productPrice * $quantity;
                    $availableQuantity = intval($Productdata['quantity']);
                    $availableColors = array_map('trim', explode(",", $Productdata['colors']));

                    if ($quantity === false || $quantity > $availableQuantity || empty($selectedColor) || !in_array($selectedColor, $availableColors)) {
                        echo '
                        <div class="toast-container position-fixed bottom-0 end-0 p-3">
                            <div id="invalidToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <strong class="me-auto">Error</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Invalid quantity or color selection. Quantity: ' . htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') . ', Stock: ' . $availableQuantity . ', Color: ' . htmlspecialchars($selectedColor, ENT_QUOTES, 'UTF-8') . '
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var toastEl = document.getElementById("invalidToast");
                                var toast = new bootstrap.Toast(toastEl);
                                toast.show();
                            });
                        </script>';
                    } else {
                        $quantity = mysqli_real_escape_string($conn, $quantity);
                        $selectedColor = mysqli_real_escape_string($conn, $selectedColor);
                        $insertQuery = sprintf(
                            "INSERT INTO `ordered products` (`userID`, `userName`, `userEmail`, `userContact`, `productID`, `productName`, `quantity`, `color`, `price`, `totalPrice`, `address`, `city`, `state`, `pinCode`, `orderMonth`, `orderDate`, `orderTime`) 
                            VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                            $Userid, $Username, $Useremail, $Usercontact, $productID, $productName, $quantity, $selectedColor, $productPrice, $totalPrice, $userAddress, $userCity, $userState, $userPinCode, $orderMonth, $orderDate, $orderTime
                        );
                        if (mysqli_query($conn, $insertQuery)) {
                            echo '
                            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                                <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header">
                                        <strong class="me-auto">Success</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Order placed successfully!
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var toastEl = document.getElementById("successToast");
                                    var toast = new bootstrap.Toast(toastEl);
                                    toast.show();
                                    setTimeout(() => window.location.href="index.php", 2000);
                                });
                            </script>';
                        }
                    }
                }
            } else {
                // Multiple Product Order (Cart) - No changes here since it uses cart data
                $ATC_products = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $Userid));
                $success = true;
                while ($ATCdata = mysqli_fetch_assoc($ATC_products)) {
                    $productID = mysqli_real_escape_string($conn, $ATCdata['productID']);
                    $productName = htmlspecialchars($ATCdata['productName'], ENT_QUOTES, 'UTF-8');
                    $quantity = mysqli_real_escape_string($conn, $ATCdata['productQuantity']);
                    $selectedColor = mysqli_real_escape_string($conn, $ATCdata['productColor']);
                    $productPrice = floatval($ATCdata['productPrice']);
                    $totalPrice = $productPrice * $quantity;

                    $insertQuery = sprintf(
                        "INSERT INTO `ordered products` (`userID`, `userName`, `userEmail`, `userContact`, `productID`, `productName`, `quantity`, `color`, `price`, `totalPrice`, `address`, `city`, `state`, `pinCode`, `orderMonth`, `orderDate`, `orderTime`) 
                        VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                        $Userid, $Username, $Useremail, $Usercontact, $productID, $productName, $quantity, $selectedColor, $productPrice, $totalPrice, $userAddress, $userCity, $userState, $userPinCode, $orderMonth, $orderDate, $orderTime
                    );
                    if (!mysqli_query($conn, $insertQuery)) {
                        $success = false;
                    }
                }
                if ($success) {
                    mysqli_query($conn, sprintf("DELETE FROM `atcproduct` WHERE `userID` = '%s'", $Userid));
                    echo '
                    <div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">Success</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Order placed successfully!
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var toastEl = document.getElementById("successToast");
                            var toast = new bootstrap.Toast(toastEl);
                            toast.show();
                            setTimeout(() => window.location.href="index.php", 2000);
                        });
                    </script>';
                } else {
                    echo '
                    <div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">Error</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Failed to place order: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . '
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var toastEl = document.getElementById("errorToast");
                            var toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        });
                    </script>';
                }
            }
        }
    } else {
        echo '
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="fieldsToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    All fields are required.
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var toastEl = document.getElementById("fieldsToast");
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        </script>';
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verx - Complete Your Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', sans-serif;
            color: var(--dark);
        }
        
        .checkout-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid var(--border);
        }
        
        .form-control, .form-select {
            padding: 0.8rem 1rem;
            border-radius: var(--border-radius);
            border: 2px solid var(--border);
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .btn-submit {
            padding: 1rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: var(--border-radius);
            background: var(--primary);
            color: white;
            transition: var(--transition);
            border: none;
        }
        
        .btn-submit:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        
        .product-summary {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .product-summary:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .total-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary);
            border-radius: 3px;
        }
        
        .user-info {
            padding: 0.75rem;
            background: rgba(79, 70, 229, 0.05);
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            color: var(--dark);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--primary);
        }
        
        .checkout-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            margin-bottom: -1rem;
            position: relative;
        }
        
        .checkout-header h2 {
            margin: 0;
            font-weight: 700;
        }
        
        .checkout-header p {
            margin: 0;
            opacity: 0.8;
        }
        
        .checkout-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            margin-right: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .order-card {
            border-left: 4px solid var(--secondary);
        }
        
        .product-image-placeholder {
            width: 60px;
            height: 60px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-right: 1rem;
        }
        
        .divider {
            height: 1px;
            background: var(--border);
            margin: 1.5rem 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div>
        <?php navbar(); ?>
    </div>

    <div class="container py-5">
        <!-- Page Header -->
        <div class="checkout-header mb-4">
            <h2><i class="fas fa-shopping-bag me-2"></i> Complete Your Purchase</h2>
            <p>Please provide your shipping information to proceed</p>
        </div>
        
        <div class="row">
            <!-- Shipping Information Form -->
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h3 class="section-title">
                        <span class="checkout-step">1</span>
                        Personal Information
                    </h3>
                    
                    <!-- User Details -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="user-info">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo $Username; ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="user-info">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo $Useremail; ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="user-info">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value"><?php echo $Usercontact; ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="user-info">
                                <div class="info-label">User ID</div>
                                <div class="info-value"><?php echo htmlspecialchars($Userid, ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <h3 class="section-title">
                        <span class="checkout-step">2</span>
                        Shipping Address
                    </h3>
                    
                    <form method="POST">
                        <div class="row g-4">
                            <!-- Address Information -->
                            <div class="col-12">
                                <label class="form-label">Street Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Enter your complete street address" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" placeholder="Enter city" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <select class="form-select" name="state" required>
                                    <option value="" selected disabled>Select State</option>
                                    <option value="Maharashtra">Maharashtra</option>
                                    <option value="Delhi">Delhi</option>
                                    <option value="Goa">Goa</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Pin Code</label>
                                <input type="number" class="form-control" name="pinCode" placeholder="Enter 6-digit pin code" min="100000" max="999999" required>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" name="order_req" class="btn btn-submit w-100">
                                    <i class="fas fa-lock me-2"></i>Complete Purchase
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-lg-4">
                <div class="checkout-card order-card">
                    <h4 class="section-title">
                        <i class="fas fa-receipt me-2"></i>
                        Order Summary
                    </h4>
                    
                    <?php
                    $total_order_price = 0;
                    
                    if ($parameter != 'requestATC') {
                        $productID = filter_var(base64_decode($parameter), FILTER_VALIDATE_INT);
                        if ($productID !== false) {
                            $productID = mysqli_real_escape_string($conn, $productID);
                            $product = mysqli_query($conn, sprintf("SELECT * FROM `products` WHERE `productID` = '%s'", $productID));
                            if ($Productdata = mysqli_fetch_assoc($product)) {
                                $productName = htmlspecialchars($Productdata['productName'], ENT_QUOTES, 'UTF-8');
                                $quantity = filter_var(base64_decode($_GET['quantity']), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                                $selectedColor = htmlspecialchars(base64_decode($_GET['color']), ENT_QUOTES, 'UTF-8');
                                $productPrice = floatval($Productdata['price']);
                                $total_price = $productPrice * $quantity;
                                $availableQuantity = intval($Productdata['quantity']);
                                $availableColors = array_map('trim', explode(",", $Productdata['colors']));
                                if ($quantity !== false && $quantity <= $availableQuantity && !empty($selectedColor) && in_array($selectedColor, $availableColors)) {
                                    $total_order_price += $total_price;
                                    ?>
                                    <div class="product-summary d-flex align-items-center">
                                        <div class="product-image-placeholder">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo $productName; ?></h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">
                                                    <span class="badge bg-light text-dark me-2">Qty: <?php echo htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <span class="badge bg-light text-dark"><?php echo $selectedColor; ?></span>
                                                </small>
                                                <span class="fw-bold text-primary">$<?php echo htmlspecialchars($productPrice, ENT_QUOTES, 'UTF-8'); ?> × <?php echo htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Subtotal</span>
                                                <span class="total-price">$<?php echo htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    } else {
                        $ATC_products = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $Userid));
                        if (mysqli_num_rows($ATC_products) > 0) {
                            while ($ATCdata = mysqli_fetch_assoc($ATC_products)) {
                                $item_total = floatval($ATCdata['productPrice']) * intval($ATCdata['productQuantity']);
                                $total_order_price += $item_total;
                                ?>
                                <div class="product-summary d-flex align-items-center">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($ATCdata['productName'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">
                                                <span class="badge bg-light text-dark">Qty: <?php echo htmlspecialchars($ATCdata['productQuantity'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </small>
                                            <span class="fw-bold text-primary">$<?php echo htmlspecialchars($ATCdata['productPrice'], ENT_QUOTES, 'UTF-8'); ?> × <?php echo htmlspecialchars($ATCdata['productQuantity'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal</span>
                                            <span class="total-price">$<?php echo htmlspecialchars($item_total, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                    
                    <div class="divider"></div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Order Total</span>
                        <span class="total-price">$<?php echo htmlspecialchars($total_order_price, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info mb-0 d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Shipping costs will be calculated based on your location.</small>
                        </div>
                    </div>
                </div>
                
                <div class="checkout-card mt-4">
                    <h5 class="mb-3"><i class="fas fa-shield-alt me-2 text-success"></i> Secure Checkout</h5>
                    <p class="small text-muted mb-0">Your payment information is processed securely. We do not store credit card details nor have access to your credit card information.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div>
        <?php footer(); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>