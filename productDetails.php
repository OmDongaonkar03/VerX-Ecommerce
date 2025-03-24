<?php
include_once 'function.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
$userdata = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
$userdata = mysqli_fetch_assoc($userdata);
$userID = mysqli_real_escape_string($conn, $userdata['id']);
$userName = htmlspecialchars($userdata['name'], ENT_QUOTES, 'UTF-8');

if (isset($_GET['param'])) {
    $id = filter_var(base64_decode($_GET['param']), FILTER_VALIDATE_INT);
    if ($id === false) {
        exit; // Invalid product ID
    }
    $id = mysqli_real_escape_string($conn, $id);
    $sql = mysqli_query($conn, sprintf("SELECT * FROM `products` WHERE `productID` = '%s'", $id));
    $data = mysqli_fetch_assoc($sql);

    if ($data) {
        $name = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
        $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
        $image1 = htmlspecialchars(substr($data["product_Image"], 3), ENT_QUOTES, 'UTF-8');
		$image2 = htmlspecialchars(substr($data["product_image2"], 3), ENT_QUOTES, 'UTF-8');
		$image3 = htmlspecialchars(substr($data["product_image3"], 3), ENT_QUOTES, 'UTF-8');
		$image4 = htmlspecialchars(substr($data["product_image4"], 3), ENT_QUOTES, 'UTF-8');
        $quantity = htmlspecialchars($data['quantity'], ENT_QUOTES, 'UTF-8');
        $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
        $color = array_map('trim', explode(",", htmlspecialchars($data['colors'], ENT_QUOTES, 'UTF-8')));
    } else {
        exit; // Product not found
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ATCrequest'])) {
        $check = mysqli_query($conn, sprintf(
            "SELECT * FROM `atcproduct` WHERE `productID` = '%s' AND `userID` = '%s'",
            $id, $userID
        ));
        if (mysqli_num_rows($check) > 0) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Notice</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Product Already In Cart
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("cartToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        } else {
            $selectedcolor = mysqli_real_escape_string($conn, $_POST['Selectedcolors']);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($quantity === false || $quantity > intval($data['quantity'])) {
                echo '
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="quantityToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Invalid quantity selected
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var toastEl = document.getElementById("quantityToast");
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                </script>';
            } else {
                $quantity = mysqli_real_escape_string($conn, $quantity);
                $ATC_query = sprintf(
                    "INSERT INTO `atcproduct`(`productID`, `productName`, `productCategory`, `productPrice`, `productColor`, `productQuantity`, `productImage`, `userID`) 
                    VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                    $id, $name, $category, $price, $selectedcolor, $quantity, $image1, $userID
                );

                date_default_timezone_set("Asia/Kolkata");
                $current_time = date("Y/m/d H:i");
                $notification = mysqli_query($conn, sprintf(
                    "INSERT INTO `notifications`(`title`, `detail`, `timestamp`) 
                    VALUES ('%s added %s in Cart', '%s added %s in Cart price: %s × %s', '%s')",
                    $userName, $name, $userName, $name, $price, $quantity, $current_time
                ));

                if (mysqli_query($conn, $ATC_query)) {
                    header("Location: addtocart.php");
                    exit();
                } else {
                    echo '
                    <div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">Error</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Failed to add to cart: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . '
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
    }
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verx Product Detail</title>
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

        .breadcrumb-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }

        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: var(--primary-light);
        }

        .breadcrumb-item.active {
            color: var(--dark);
            font-weight: 500;
        }

        .product-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-top: 1rem;
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1);
        }

		.gallery-container {
			background: white;
			padding: 2rem;
			border-radius: var(--border-radius);
			height: 100%;
		}
		
		.main-image-container {
			display: flex;
			justify-content: center;
			align-items: center;
			border-radius: var(--border-radius);
			overflow: hidden;
			background-color: #f8f9fa;
			height: 450px;
			box-shadow: var(--shadow);
			transition: var(--transition);
		}
		
		.main-product-image {
			max-height: 100%;
			max-width: 100%;
			object-fit: contain;
			transition: transform 0.5s ease;
		}
		
		.main-image-container:hover .main-product-image {
			transform: scale(1.05);
		}
		
		.thumbnail-gallery {
			margin-top: 1rem;
		}
		
		.thumbnail {
			cursor: pointer;
			border-radius: var(--border-radius);
			overflow: hidden;
			border: 2px solid transparent;
			transition: all 0.3s ease;
			height: 80px;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: #f8f9fa;
		}
		
		.thumbnail img {
			max-height: 100%;
			max-width: 100%;
			object-fit: contain;
		}
		
		.thumbnail:hover {
			border-color: var(--primary-light);
			transform: translateY(-2px);
		}
		
		.thumbnail.active {
			border-color: var(--primary);
			box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
		}
		
		@media (max-width: 992px) {
			.main-image-container {
				height: 400px;
			}
		}
		
		@media (max-width: 768px) {
			.main-image-container {
				height: 350px;
			}
			.thumbnail {
				height: 60px;
			}
		}
		
		@media (max-width: 576px) {
			.gallery-container {
				padding: 1rem;
			}
			.main-image-container {
				height: 300px;
			}
			.thumbnail {
				height: 50px;
			}
		}
		
		/* Thumbnail Navigation */
		.thumbnail-navigation {
			margin-top: 1.5rem;
		}
		
		.thumbnail {
			cursor: pointer;
			border-radius: var(--border-radius);
			overflow: hidden;
			border: 2px solid transparent;
			transition: all 0.3s ease;
			height: 80px;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: #f8f9fa;
		}
		
		.thumbnail img {
			max-height: 100%;
			max-width: 100%;
			object-fit: contain;
		}
		
		.thumbnail:hover {
			border-color: var(--primary-light);
			transform: translateY(-2px);
		}
		
		.thumbnail.active {
			border-color: var(--primary);
			box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
		}
		
        .product-info {
            padding: 2.5rem;
        }

        .product-title {
            font-weight: 700;
            color: var(--dark);
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .product-category {
            display: inline-block;
            padding: 0.4rem 1rem;
            background-color: #EEF2FF;
            color: var(--primary);
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .price-container {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .price-tag {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-right: 1rem;
        }

        .original-price {
            text-decoration: line-through;
            color: #9CA3AF;
            font-size: 1.3rem;
        }

        .discount-badge {
            background: var(--secondary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            margin-left: 1rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .product-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .spec-item {
            background: var(--light);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--border);
        }

        .spec-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            border-color: var(--primary-light);
        }

        .spec-icon {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .spec-label {
            font-size: 0.85rem;
            color: #6B7280;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .spec-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .color-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .color-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .color-checkbox-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
            margin-right: 0.5rem;
        }

        .color-checkbox {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .color-badge {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            background-color: white;
            border: 2px solid var(--border);
            transition: var(--transition);
            font-weight: 500;
        }

        .color-checkbox:checked + .color-badge {
            border-color: var(--primary);
            background-color: #EEF2FF;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.2);
        }

        .color-checkbox:focus + .color-badge {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .color-badge:hover {
            transform: translateY(-3px);
            border-color: var(--primary-light);
        }

        .quantity-selector {
            max-width: 150px;
            margin-top: 0.5rem;
        }

        .quantity-selector .btn {
            background-color: white;
            border-color: var(--border);
            color: var(--dark);
            font-weight: 600;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: var(--transition);
        }

        .quantity-selector .btn:hover {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .quantity-selector input {
            text-align: center;
            font-weight: 600;
            border-left: none;
            border-right: none;
            border-color: var(--border);
        }

        .product-details {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .product-details:hover {
            box-shadow: var(--shadow);
        }

        .product-details h5 {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .product-details h5 i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .product-description {
            color: #4B5563;
            line-height: 1.7;
            font-size: 1.05rem;
        }

        .btn-action {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            font-size: 1rem;
        }

        .btn-cart {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-cart:hover {
            background: var(--primary-light);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }

        .btn-buy {
            background: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }

        .btn-buy:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: #EF4444;
            border-color: #EF4444;
        }

        .btn-danger:hover {
            background: #DC2626;
            border-color: #DC2626;
        }

        .stock-status {
            display: inline-flex;
            align-items: center;
            border-radius: 30px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .in-stock {
            background-color: #ECFDF5;
            color: var(--secondary);
            border: 1px solid #D1FAE5;
        }

        .low-stock {
            background-color: #FEF3C7;
            color: #D97706;
            border: 1px solid #FDE68A;
        }

        .out-of-stock {
            background-color: #FEE2E2;
            color: #EF4444;
            border: 1px solid #FECACA;
        }

        .guarantees {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .guarantee-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            transition: var(--transition);
            flex: 1;
            min-width: 200px;
        }

        .guarantee-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .guarantee-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #EEF2FF;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .guarantee-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .product-tabs {
            margin-top: 3rem;
        }

        .nav-tabs {
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-item {
            margin-right: 1rem;
        }

        .nav-tabs .nav-link {
            color: #6B7280;
            font-weight: 500;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 1rem 1.5rem;
            transition: var(--transition);
        }

        .nav-tabs .nav-link:hover, 
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background-color: transparent;
        }

        .tab-content {
            padding: 1.5rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        @media (max-width: 992px) {
            .product-info {
                padding: 2rem;
            }
        }


        @media (max-width: 576px) {
            .product-info {
                padding: 1.5rem;
            }
			.gallery-container {
				padding: 1rem;
			}
			.thumbnail {
				height: 50px;
			}
            .product-specs {
                grid-template-columns: 1fr;
            }
            .price-container {
                flex-wrap: wrap;
            }
            .discount-badge {
                margin-left: 0;
                margin-top: 0.5rem;
            }
            .section-title {
                flex-direction: column;
                align-items: flex-start;
            }
            .product-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div>
        <?php navbar(); ?>
    </div>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <div class="breadcrumb-section">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="productpage.php">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $name; ?></li>
                </ol>
            </nav>
        </div>

        <div class="product-card">
            <div class="row g-0">
                <!-- Product Gallery -->
                <div class="col-lg-6">
                    <div class="gallery-container">
                        <div id="productCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="<?php echo $image1; ?>" class="d-block w-100" alt="<?php echo $name; ?> - Image 1">
                                </div>
                                <div class="carousel-item">
                                    <img src="<?php echo $image2; ?>" class="d-block w-100" alt="<?php echo $name; ?> - Image 2">
                                </div>
                                <div class="carousel-item">
                                    <img src="<?php echo $image3; ?>" class="d-block w-100" alt="<?php echo $name; ?> - Image 3">
                                </div>
                                <div class="carousel-item">
                                    <img src="<?php echo $image4; ?>" class="d-block w-100" alt="<?php echo $name; ?> - Image 4">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        
                        <!-- Thumbnail Navigation -->
                        <div class="thumbnail-navigation mt-3">
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="thumbnail active" onclick="changeSlide(0)">
                                        <img src="<?php echo $image1; ?>" class="img-fluid" alt="Thumbnail 1">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="thumbnail" onclick="changeSlide(1)">
                                        <img src="<?php echo $image2; ?>" class="img-fluid" alt="Thumbnail 2">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="thumbnail" onclick="changeSlide(2)">
                                        <img src="<?php echo $image3; ?>" class="img-fluid" alt="Thumbnail 3">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="thumbnail" onclick="changeSlide(3)">
                                        <img src="<?php echo $image4; ?>" class="img-fluid" alt="Thumbnail 4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Information -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <span class="product-category"><?php echo $category; ?></span>
                        <h1 class="product-title"><?php echo $name; ?></h1>
                        
                        <!-- Price Section -->
                        <div class="price-container">
                            <span class="price-tag">₹<?php echo $price; ?></span>
                            <?php if ($discount > 0): ?>
                                <span class="original-price">₹<?php echo number_format(floatval($price) / (1 - floatval($discount)/100), 2); ?></span>
                                <span class="discount-badge">
                                    <i class="fas fa-bolt me-2"></i>
                                    <?php echo $discount; ?>% OFF
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Stock Status -->
                        <?php
                        $stockStatusClass = '';
                        $stockStatusText = '';
                        if (intval($quantity) > 10) {
                            $stockStatusClass = 'in-stock';
                            $stockStatusText = 'In Stock';
                        } elseif (intval($quantity) > 0) {
                            $stockStatusClass = 'low-stock';
                            $stockStatusText = 'Low Stock';
                        } else {
                            $stockStatusClass = 'out-of-stock';
                            $stockStatusText = 'Out of Stock';
                        }
                        ?>
                        <div class="stock-status <?php echo $stockStatusClass; ?>">
                            <i class="fas <?php echo intval($quantity) > 0 ? 'fa-check-circle' : 'fa-times-circle'; ?> me-2"></i>
                            <?php echo $stockStatusText; ?> (<?php echo $quantity; ?> units)
                        </div>

                        <form method="POST" id="productForm" class="product-form mt-4">
                            <!-- Color Options -->
                            <div class="color-section">
                                <h3 class="section-title"><i class="fas fa-palette"></i> Available Colors</h3>
                                <div class="color-options">
                                    <?php foreach ($color as $c): ?>
                                    <label class="color-checkbox-wrapper">
                                        <input type="radio" name="Selectedcolors" value="<?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?>" class="color-checkbox" required>
                                        <span class="color-badge">
                                            <?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="mt-4">
                                <h3 class="section-title"><i class="fas fa-cubes"></i> Quantity</h3>
                                <div class="input-group quantity-selector">
                                    <button type="button" class="btn" onclick="updateQuantity(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $quantity; ?>" readonly>
                                    <button type="button" class="btn" onclick="updateQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Product Guarantees -->
                            <div class="guarantees">
                                <div class="guarantee-item">
                                    <div class="guarantee-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="guarantee-text">Free Shipping</div>
                                </div>
                                <div class="guarantee-item">
                                    <div class="guarantee-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="guarantee-text">30-Day Returns</div>
                                </div>
                                <div class="guarantee-item">
                                    <div class="guarantee-icon">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div class="guarantee-text">Warranty Included</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-3 mt-4">
                                <?php if ($status == 'active'): ?>
                                    <button type="button" class="btn btn-action btn-buy" onclick="BuyNow()">
                                        <i class="fas fa-bolt me-2"></i>Buy Now
                                    </button>
                                    <button type="submit" class="btn btn-action btn-cart" name="ATCrequest">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-action btn-danger" disabled>
                                        <i class="fas fa-times me-2"></i>Currently Unavailable
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs Section -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                        <i class="fas fa-info-circle me-2"></i>Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">
                        <i class="fas fa-list me-2"></i>Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">
                        <i class="fas fa-truck me-2"></i>Shipping & Returns
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="product-description">
                        <?php echo $description; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Product ID</th>
                                    <td><?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td><?php echo $category; ?></td>
                                </tr>
                                <tr>
                                    <th>Available Colors</th>
                                    <td><?php echo implode(", ", array_map('htmlspecialchars', $color)); ?></td>
                                </tr>
                                <tr>
                                    <th>Stock Status</th>
                                    <td><?php echo $status == 'active' ? 'Available' : 'Unavailable'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                    <div>
                        <h5>Shipping Policy</h5>
                        <p>Free shipping on all orders over $50. Standard delivery takes 3-5 business days.</p>
                        <h5 class="mt-4">Return Policy</h5>
                        <p>Return any item within 30 days of delivery for a full refund. Items must be in original condition with tags attached.</p>
                        <h5 class="mt-4">Warranty</h5>
                        <p>All products come with a 1-year manufacturer's warranty. This covers defects in materials and workmanship under normal use conditions.</p>
                        <h5 class="mt-4">International Shipping</h5>
                        <p>We ship to most countries worldwide. International shipping typically takes 7-14 business days. Additional customs fees may apply.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div>
        <?php footer(); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
        // Quantity update function
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let newQuantity = parseInt(quantityInput.value) + change;
            const maxQuantity = <?php echo intval($quantity); ?>;
            
            if (newQuantity < 1) {
                newQuantity = 1;
            } else if (newQuantity > maxQuantity) {
                newQuantity = maxQuantity;
            }
            
            quantityInput.value = newQuantity;
        }
        
        // Function to change slides via thumbnails
        function changeSlide(slideIndex) {
            const carousel = document.getElementById('productCarousel');
            bootstrap.Carousel.getInstance(carousel).to(slideIndex);
        }
        
        // Initialize carousel and thumbnail syncing
        document.addEventListener('DOMContentLoaded', function() {
            const carouselEl = document.getElementById('productCarousel');
            const carousel = new bootstrap.Carousel(carouselEl, {
                interval: false
            });
            
            carouselEl.addEventListener('slid.bs.carousel', function() {
                const activeSlideIndex = [...carouselEl.querySelectorAll('.carousel-item')]
                    .findIndex(slide => slide.classList.contains('active'));
                document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
                    thumb.classList.toggle('active', index === activeSlideIndex);
                });
            });
        });
        
        function BuyNow() {
            const form = document.getElementById('productForm');
            const selectedColor = form.querySelector('input[name="Selectedcolors"]:checked');
            const quantity = document.getElementById('quantity').value;
            const productId = '<?php echo urlencode(base64_encode($id)); ?>';
            
            if (selectedColor) {
                const encodedColor = btoa(selectedColor.value); // Base64 encode color
                const encodedQuantity = btoa(quantity); // Base64 encode quantity
                const url = `buynow.php?param=${productId}&color=${encodeURIComponent(encodedColor)}&quantity=${encodeURIComponent(encodedQuantity)}`;
                window.location.href = url;
            } else {
                const toast = new bootstrap.Toast(document.createElement('div'));
                document.body.appendChild(toast._element);
                toast._element.innerHTML = `
                    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Please select a color
                        </div>
                    </div>`;
                toast.show();
            }
        }
    </script>
</body>
</html>