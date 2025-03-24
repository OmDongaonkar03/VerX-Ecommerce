<?php
include('function.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Secure session email
$email = filter_var($_SESSION['user_email'], FILTER_SANITIZE_EMAIL);
$email = mysqli_real_escape_string($conn, $email);
$user = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
$Userdata = mysqli_fetch_assoc($user);
$userid = mysqli_real_escape_string($conn, filter_var($Userdata['id'], FILTER_SANITIZE_NUMBER_INT));

// Secure GET parameter
$work = isset($_GET['param']) ? filter_var($_GET['param'], FILTER_SANITIZE_STRING) : '';

// Wishlist remove all products
if ($work === 'removeAllwish') {
    $remove = mysqli_query($conn, sprintf("DELETE FROM `wishlist` WHERE `userid` = '%s'", $userid));
}

// Wishlist remove single product
if ($work === 'removewish') {
    if (isset($_GET['id'])) {
        $product = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $product = mysqli_real_escape_string($conn, $product);
        $remove = mysqli_query($conn, sprintf("DELETE FROM `wishlist` WHERE `userid` = '%s' AND `productID` = '%s'", $userid, $product));
    }
}

// Remove item from cart
if ($work === 'removeatc') {
    if (isset($_GET['id'])) {
        $product = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $product = mysqli_real_escape_string($conn, $product);
        $delete = mysqli_query($conn, sprintf("DELETE FROM `atcproduct` WHERE `productID` = '%s' AND `userID` = '%s'", $product, $userid));
        
        echo '
        <div class="row g-4" id="display">
            <div class="col-lg-8">
                <div class="cart-container">
                    <div class="cart-header">
                        <h4 class="mb-0">Shopping Cart</h4>';
                        
        $sql = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $userid));
        
        echo '
        </div>
            <div id="cartItems">';    
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $productID = htmlspecialchars(filter_var($data["productID"], FILTER_SANITIZE_NUMBER_INT), ENT_QUOTES, "UTF-8");
                $productName = htmlspecialchars(filter_var($data["productName"], FILTER_SANITIZE_STRING), ENT_QUOTES, "UTF-8");
                $productPrice = htmlspecialchars(filter_var($data["productPrice"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), ENT_QUOTES, "UTF-8");
                $productImage = htmlspecialchars(filter_var($data["productImage"], FILTER_SANITIZE_URL), ENT_QUOTES, "UTF-8");
                $productCategory = htmlspecialchars(filter_var($data["productCategory"], FILTER_SANITIZE_STRING), ENT_QUOTES, "UTF-8");
                $productQuantity = htmlspecialchars(filter_var($data["productQuantity"], FILTER_SANITIZE_NUMBER_INT), ENT_QUOTES, "UTF-8");
        
                echo '
                <div class="product-card animated" style="animation-delay: 0.3s">
                    <div class="product-img-wrapper">
                        <img src="'.$productImage.'" alt="'.$productName.'" class="product-img">
                    </div>
                    <div class="product-details">
                        <a href="#" class="text-decoration-none">
                            <h5 class="product-name">'.$productName.'</h5>
                        </a>
                        <div class="product-meta">
                            <span><i class="fas fa-tag"></i> '.$productCategory.'</span>
                        </div>
                        <p class="product-price mt-2">₹'.$productPrice.'</p>
                    </div>
                    <div class="product-actions d-flex gap-4">
                        <div class="quantity-actions">
                            <div class="quantity-selector">
                                <button type="button" class="quantity-btn" onclick="updateQty('.$productID.', \'sub\')">-</button>
                                <input type="number" class="quantity-input" value="'.$productQuantity.'" min="1" readonly>
                                <button type="button" class="quantity-btn" onclick="updateQty('.$productID.', \'add\')">+</button>
                            </div>  
                        </div>
                        <div class="actions gap-3">
                            <span class="item-total">₹'.(floatval($productPrice) * intval($productQuantity)).'</span>
                            <button type="button" class="remove-btn" onclick="removeItem('.$productID.')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '
                <div id="emptyCart" class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h5>Your cart is empty</h5>
                    <p>Looks like you haven\'t added anything to your cart yet.</p>
                    <a href="productpage.php" class="btn btn-primary px-4 py-2">Continue Shopping</a>
                </div>';
        }
        
        echo '
                    </div>
                </div>
                <a href="productpage.php" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>';
        
        $total_amount = 0;
        $tamt = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $userid));
        $tamt_count = mysqli_num_rows($tamt);
        
        if ($tamt_count > 0) {
            while ($tamt_data = mysqli_fetch_assoc($tamt)) {
                $price = filter_var($tamt_data["productPrice"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $qty = filter_var($tamt_data["productQuantity"], FILTER_SANITIZE_NUMBER_INT);
                $total_amount += floatval($price) * intval($qty);
            }
        }
        
        echo '
            <div class="col-lg-4">
                <div class="summary-card animated" style="animation-delay: 0.4s">
                    <h5 class="summary-title">Order Summary</h5>
                    <div class="summary-row">
                        <span class="summary-label">Subtotal (<span id="itemCount">'.htmlspecialchars($tamt_count, ENT_QUOTES, "UTF-8").'</span> items)</span>
                        <span class="summary-value subtotal">₹'.htmlspecialchars($total_amount, ENT_QUOTES, "UTF-8").'</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value shipping">Free</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-total">
                        <span class="summary-total-label">Total</span>
                        <span class="summary-total-value total">₹'.htmlspecialchars($total_amount, ENT_QUOTES, "UTF-8").'</span>
                    </div>';
        
        if ($tamt_count > 0) {
            echo '
                    <a href="buynow.php?param=requestATC" class="checkout-btn">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                    <div class="mt-4">
                        <div class="d-flex align-items-center justify-content-center gap-2 text-muted">
                            <i class="fas fa-shield-alt"></i>
                            <span class="small">Secure Checkout</span>
                        </div>
                    </div>';
        }
        
        echo '
                </div>
            </div>
        </div>';
    }
}

if ($work === 'qtychange') {
    if (isset($_GET['id']) && isset($_GET['request'])) {
        $product = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $product = mysqli_real_escape_string($conn, $product);
        $request = filter_var($_GET['request'], FILTER_SANITIZE_STRING);
        $request = mysqli_real_escape_string($conn, $request);
        
        if ($request === 'sub') {
            $change = mysqli_query($conn, sprintf("UPDATE `atcproduct` SET `productQuantity` = GREATEST(1, productQuantity - 1) WHERE `productID` = '%s' AND `userID` = '%s'", $product, $userid));
        } else if ($request === 'add') {
            $change = mysqli_query($conn, sprintf("UPDATE `atcproduct` SET `productQuantity` = productQuantity + 1 WHERE `productID` = '%s' AND `userID` = '%s'", $product, $userid));
        }
		
				echo '
		<div class="row g-4" id="display">
			<!-- Cart Items -->
			<div class="col-lg-8">
				<div class="cart-container">
					<div class="cart-header">
						<h4 class="mb-0">Shopping Cart</h4>';
						
		$sql = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $userid));
		
		echo '
		</div>
			<div id="cartItems">';	
		if (mysqli_num_rows($sql) > 0) {
			while ($data = mysqli_fetch_assoc($sql)) {
				$productID = htmlspecialchars($data["productID"], ENT_QUOTES, "UTF-8");
				$productName = htmlspecialchars($data["productName"], ENT_QUOTES, "UTF-8");
				$productPrice = htmlspecialchars($data["productPrice"], ENT_QUOTES, "UTF-8");
				$productImage = htmlspecialchars($data["productImage"], ENT_QUOTES, "UTF-8");
				$productCategory = htmlspecialchars($data["productCategory"], ENT_QUOTES, "UTF-8");
				$productQuantity = htmlspecialchars($data["productQuantity"], ENT_QUOTES, "UTF-8");
		
				echo '
				<div class="product-card animated" style="animation-delay: 0.3s">
					<div class="product-img-wrapper">
						<img src="'.$productImage.'" alt="'.$productName.'" class="product-img">
					</div>
					
					<div class="product-details">
						<a href="#" class="text-decoration-none">
							<h5 class="product-name">'.$productName.'</h5>
						</a>
						<div class="product-meta">
							<span><i class="fas fa-tag"></i> '.$productCategory.'</span>
						</div>
						<p class="product-price mt-2">₹'.$productPrice.'</p>
					</div>
					
					<div class="product-actions d-flex gap-4">
						<div class="quantity-actions">
							<div class="quantity-selector">
								<button type="button" class="quantity-btn" onclick="updateQty('.$productID.', \'sub\')">-</button>
								<input type="number" class="quantity-input" value="'.$productQuantity.'" min="1" readonly>
								<button type="button" class="quantity-btn" onclick="updateQty('.$productID.', \'add\')">+</button>
							</div>  
						</div>
						<div class="actions gap-3">
							<span class="item-total">₹'.(floatval($productPrice) * intval($productQuantity)).'</span>
							<button type="button" class="remove-btn" onclick="removeItem('.$productID.')">
								<i class="fas fa-times"></i>
							</button>
						</div>
					</div>
				</div>';
			}
		} else {
			echo '
				<div id="emptyCart" class="empty-cart">
					<div class="empty-cart-icon">
						<i class="fas fa-shopping-cart"></i>
					</div>
					<h5>Your cart is empty</h5>
					<p>Looks like you haven\'t added anything to your cart yet.</p>
					<a href="productpage.php" class="btn btn-primary px-4 py-2">Continue Shopping</a>
				</div>';
		}
		
		echo '
					</div>
				</div>
				
				<a href="productpage.php" class="continue-shopping">
					<i class="fas fa-arrow-left"></i> Continue Shopping
				</a>
			</div>';
		
		// Order Summary
		$total_amount = 0;
		$tamt = mysqli_query($conn, sprintf("SELECT * FROM `atcproduct` WHERE `userID` = '%s'", $userid));
		$tamt_count = mysqli_num_rows($tamt);
		
		if ($tamt_count > 0) {
			while ($tamt_data = mysqli_fetch_assoc($tamt)) {
				$total_amount += floatval($tamt_data["productPrice"]) * intval($tamt_data["productQuantity"]);
			}
		}
		
		echo '
			<div class="col-lg-4">
				<div class="summary-card animated" style="animation-delay: 0.4s">
					<h5 class="summary-title">Order Summary</h5>
					
					<div class="summary-row">
						<span class="summary-label">Subtotal (<span id="itemCount">'.htmlspecialchars($tamt_count, ENT_QUOTES, "UTF-8").'</span> items)</span>
						<span class="summary-value subtotal">₹'.htmlspecialchars($total_amount, ENT_QUOTES, "UTF-8").'</span>
					</div>
					
					<div class="summary-row">
						<span class="summary-label">Shipping</span>
						<span class="summary-value shipping">Free</span>
					</div>
					
					<div class="summary-divider"></div>
					
					<div class="summary-total">
						<span class="summary-total-label">Total</span>
						<span class="summary-total-value total">₹'.htmlspecialchars($total_amount, ENT_QUOTES, "UTF-8").'</span>
					</div>';
		
		if ($tamt_count > 0) {
			echo '
					<a href="buynow.php?param=requestATC" class="checkout-btn">
						<i class="fas fa-lock"></i> Proceed to Checkout
					</a>
					<div class="mt-4">
						<div class="d-flex align-items-center justify-content-center gap-2 text-muted">
							<i class="fas fa-shield-alt"></i>
							<span class="small">Secure Checkout</span>
						</div>
					</div>';
		}
		
		echo '
				</div>
			</div>
		</div>';
	}
}

$sql = sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email);
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($data['Status'], FILTER_SANITIZE_STRING);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === "POST" && $status === 'Accepted') {
    $response = ['success' => false, 'message' => ''];

    if (isset($_POST['action'])) {
        $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
        
        switch ($action) {
            case 'update_name':
                $updatedName = mysqli_real_escape_string($conn, filter_var($_POST['newName'], FILTER_SANITIZE_STRING));
                if (!preg_match('/^[a-zA-Z\s\'-]{2,50}$/', $updatedName)) {
                    $response['message'] = 'Invalid name format (2-50 letters, spaces, or \' -)';
                } else {
                    $nameSQL = sprintf("UPDATE `signup` SET `name` = '%s' WHERE `id` = '%s'", $updatedName, $id);
                    if (mysqli_query($conn, $nameSQL)) {
                        $response['success'] = true;
                        $response['message'] = 'Name Changed Successfully';
                        $response['newValue'] = htmlspecialchars($updatedName, ENT_QUOTES, 'UTF-8');
                    }
                }
                break;

            case 'update_contact':
                $updatedContact = mysqli_real_escape_string($conn, filter_var($_POST['newContact'], FILTER_SANITIZE_NUMBER_INT));
                if (!preg_match('/^[0-9]{9,15}$/', $updatedContact)) {
                    $response['message'] = 'Invalid contact number (9-15 digits)';
                } else {
                    $contactSQL = sprintf("UPDATE `signup` SET `contact` = '%s' WHERE `id` = '%s'", $updatedContact, $id);
                    if (mysqli_query($conn, $contactSQL)) {
                        $response['success'] = true;
                        $response['message'] = 'Contact Changed Successfully';
                        $response['newValue'] = htmlspecialchars($updatedContact, ENT_QUOTES, 'UTF-8');
                    }
                }
                break;

            case 'update_password':
                $oldPass = mysqli_real_escape_string($conn, filter_var($_POST['oldPass'], FILTER_SANITIZE_STRING));
                $updatedPass = mysqli_real_escape_string($conn, filter_var($_POST['newPass'], FILTER_SANITIZE_STRING));
                if (!preg_match('/^[a-zA-Z0-9@#$%^&*!_.-]{6,50}$/', $updatedPass)) {
                    $response['message'] = 'Password must be 6-50 characters (letters, numbers, @#$%^&*!_.-)';
                } else if ($oldPass !== $data['password']) {
                    $response['message'] = 'Old password is incorrect';
                } else {
                    $hashedPass = password_hash($updatedPass, PASSWORD_DEFAULT);
                    $passSQL = sprintf("UPDATE `signup` SET `password` = '%s' WHERE `id` = '%s'", mysqli_real_escape_string($conn, $hashedPass), $id);
                    if (mysqli_query($conn, $passSQL)) {
                        $response['success'] = true;
                        $response['message'] = 'Password Changed Successfully';
                    }
                }
                break;

            case 'update_profile_pic':
                $uploaddir = 'uploads/';
                if (empty($_FILES['profile_pic']['tmp_name'])) {
                    $response['message'] = 'No file uploaded';
                } else {
                    $fileName = filter_var($_FILES['profile_pic']['name'], FILTER_SANITIZE_STRING);
                    $uploadfile = $uploaddir . basename($fileName);
                    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadfile)) {
                        $uploadfile = mysqli_real_escape_string($conn, $uploadfile);
                        $updatedp = sprintf("UPDATE `signup` SET `Profile photo` = '%s' WHERE `id` = '%s'", $uploadfile, $id);
                        if (mysqli_query($conn, $updatedp)) {
                            $response['success'] = true;
                            $response['message'] = 'Profile Picture Changed Successfully';
                            $response['newValue'] = htmlspecialchars($uploadfile, ENT_QUOTES, 'UTF-8');
                        }
                    } else {
                        $response['message'] = 'Failed to upload file';
                    }
                }
                break;
        }
    }

    echo json_encode($response);
    exit;
}
?>