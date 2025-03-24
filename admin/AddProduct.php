<?php
include("admin function.php");
if (!isset($_SESSION["admin_email"])) {
    header("Location: admin login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check all required fields
    if (isset($_POST['productName']) && isset($_POST['category']) && isset($_POST['price']) && 
        isset($_POST['discount']) && isset($_POST['description']) && isset($_FILES['productImage']) && 
        isset($_POST['status']) && isset($_POST['colors']) && isset($_POST['quantity'])) {

        // Generate product ID
        $productID = rand(1000, 100000);

        // Sanitize and validate product name
        $productName = filter_var($_POST['productName'], FILTER_SANITIZE_STRING);
        if (empty($productName)) {
            $msg = htmlspecialchars("Product name cannot be empty.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $productName = mysqli_real_escape_string($conn, $productName);

        // Validate category
        $allowed_categories = ['men', 'women', 'kids', 'accessories'];
        $category = $_POST['category'];
        if (!in_array($category, $allowed_categories)) {
            $msg = htmlspecialchars("Invalid category selected.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $category = mysqli_real_escape_string($conn, $category);

        // Validate price
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) {
            $msg = htmlspecialchars("Price must be a valid positive number.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }

        // Validate discount
        $discount = filter_var($_POST['discount'], FILTER_VALIDATE_FLOAT);
        if ($discount === false || $discount < 0 || $discount > 100) {
            $msg = htmlspecialchars("Discount must be between 0 and 100.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }

        // Sanitize description
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        if (empty($description)) {
            $msg = htmlspecialchars("Description cannot be empty.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $description = mysqli_real_escape_string($conn, $description);

        // Validate status
        $allowed_statuses = ['active', 'inactive', 'out_of_stock'];
        $status = $_POST['status'];
        if (!in_array($status, $allowed_statuses)) {
            $msg = htmlspecialchars("Invalid status selected.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $status = mysqli_real_escape_string($conn, $status);

        // Validate quantity
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        if ($quantity === false || $quantity < 0) {
            $msg = htmlspecialchars("Quantity must be a valid non-negative integer.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }

        // Sanitize and validate colors
        if (!is_array($_POST['colors']) || empty($_POST['colors'])) {
            $msg = htmlspecialchars("At least one color must be provided.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $sanitized_colors = array_map(function($color) {
            return mysqli_real_escape_string($GLOBALS['conn'], trim(filter_var($color, FILTER_SANITIZE_STRING)));
        }, $_POST['colors']);
        $colors = implode(',', $sanitized_colors);

        // File upload handling
        $uploaddir = '../uploads/';

        // Validate and upload main image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $uploadfile = $uploaddir . basename($_FILES['productImage']['name']);
        if ($_FILES['productImage']['size'] > $max_size || !in_array($_FILES['productImage']['type'], $allowed_types)) {
            $msg = htmlspecialchars("Invalid main image file type or size (max 5MB, JPEG/PNG/GIF only).", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
            exit;
        }
        $uploadfile = mysqli_real_escape_string($conn, $uploadfile);

        // Handle additional images
        $image2 = '';
        $image3 = '';
        $image4 = '';
        foreach (['productImage2', 'productImage3', 'productImage4'] as $key => $field) {
            if (isset($_FILES[$field]['name']) && !empty($_FILES[$field]['name'])) {
                $file = $uploaddir . basename($_FILES[$field]['name']);
                if ($_FILES[$field]['size'] <= $max_size && in_array($_FILES[$field]['type'], $allowed_types)) {
                    $file = mysqli_real_escape_string($conn, $file);
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $file)) {
                        ${"image" . ($key + 2)} = $file;
                    } else {
                        $msg = htmlspecialchars("Failed to upload additional image " . ($key + 2) . ".", ENT_QUOTES, 'UTF-8');
                        echo "<script>alert('$msg')</script>";
                        exit;
                    }
                } else {
                    $msg = htmlspecialchars("Invalid additional image " . ($key + 2) . " file type or size.", ENT_QUOTES, 'UTF-8');
                    echo "<script>alert('$msg')</script>";
                    exit;
                }
            }
        }

        // Upload main image and insert into database
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadfile)) {
            $query = sprintf(
                "INSERT INTO `products` (
                    `productID`, `productName`, `category`, `price`, `discount`, 
                    `description`, `product_Image`, `status`, `quantity`, `colors`, 
                    `product_image2`, `product_image3`, `product_image4`
                ) VALUES (
                    '%d', '%s', '%s', %.2f, %.2f, '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s'
                )",
                $productID, $productName, $category, $price, $discount,
                $description, $uploadfile, $status, $quantity, $colors,
                $image2, $image3, $image4
            );

            $sql = mysqli_query($conn, $query);

            if ($sql) {
                // Add notification
                date_default_timezone_set("Asia/Kolkata");
                $current_time = date("Y/m/d H:i");
                $notification_query = sprintf(
                    "INSERT INTO `notifications` (`title`, `detail`, `timestamp`) VALUES ('%s', '%s', '%s')",
                    mysqli_real_escape_string($conn, "New Product Added"),
                    mysqli_real_escape_string($conn, "new product ($productName) has been added in the product page"),
                    mysqli_real_escape_string($conn, $current_time)
                );
                mysqli_query($conn, $notification_query);

                $msg = htmlspecialchars("Product Added Successfully", ENT_QUOTES, 'UTF-8');
                echo "<script>alert('$msg')</script>";
            } else {
                $error = htmlspecialchars("Database insertion failed: " . mysqli_error($conn), ENT_QUOTES, 'UTF-8');
                echo "<script>alert('$error')</script>";
            }
        } else {
            $msg = htmlspecialchars("File upload failed. Check folder permissions.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
        }
    } else {
        $msg = htmlspecialchars("All required fields must be filled out.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Add product- VerX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --primary-light: #534bae;
            --primary-dark: #000051;
            --secondary-color: #f4f6f9;
        }

        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: var(--primary-color);
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding: 1.5rem;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
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
            opacity: 0.85;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
            opacity: 1;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            opacity: 1;
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
            width: calc(100% - 250px);
        }

        .main-content.expanded {
            margin-left: 60px;
            width: calc(100% - 60px);
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

        /* Rest of the styles remain the same */
        .admin-card {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-radius: 15px;
            border: none;
            background: white;
            margin: 20px 0 0 140px;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
            background: var(--primary-color);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 0.7rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.15);
        }

        .image-preview {
            width: 180px;
            height: 180px;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            transition: all 0.3s;
        }

        .image-preview:hover {
            border-color: var(--primary-light);
            transform: translateY(-5px);
        }

        .btn-primary {
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #e0e0e0;
            border: none;
            color: #333;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background-color: #d0d0d0;
            transform: translateY(-2px);
        }

        .color-input-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .add-color-btn, .remove-color-btn {
            font-size: 1.4rem;
            transition: all 0.3s;
        }

        .add-color-btn:hover, .remove-color-btn:hover {
            transform: scale(1.1);
        }

        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color:#0D6EFD;
            color: white;
            border: none;
            border-radius: 8px 0 0 8px;
        }

        .badge {
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar .nav-link span,
            .sidebar h3 span {
                display: none;
            }
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
        }
    </style>
</head>
<body>
	<div class="sidebar" id="sidebar">
		<?php sidebar() ?>
    </div>
    <div class="container-fluid py-4 d-flex justify-content-center align-items-center">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card admin-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Add New Product</h3>
                        <span class="badge bg-light text-primary">Product Management</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="productForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label required-field">Product Name</label>
                                    <input type="text" class="form-control" id="productName" name="productName" required>
                                    <div class="invalid-feedback">Please enter a product name.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory" class="form-label required-field">Product Category</label>
                                    <select class="form-select" id="productCategory" name="category" required>
                                        <option value="" selected disabled>Select Category</option>
                                        <option value="men">Men's Clothing</option>
                                        <option value="women">Women's Clothing</option>
                                        <option value="kids">Kids Clothing</option>
                                        <option value="accessories">Accessories</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a category.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productPrice" class="form-label required-field">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid price.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productDiscount" class="form-label">Discount Percentage</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="productDiscount" name="discount" min="0" max="100" value="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="productDescription" class="form-label required-field">Product Description</label>
                                <textarea class="form-control" id="productDescription" name="description" rows="4" required></textarea>
                                <div class="invalid-feedback">Please enter a product description.</div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label required-field">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                                <div class="invalid-feedback">Please enter a valid quantity.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Available Colors</label>
                                <div id="colorInputsContainer">
                                    <div class="color-input-container d-flex align-items-center gap-2">
                                        <input type="text" class="form-control" name="colors[]" placeholder="Enter color name" required>
                                        <i class="fas fa-plus-circle add-color-btn" onclick="addColorInput()"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Product Images</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Image 1*</label>
                                            <input type="file" class="form-control" name="productImage" accept="image/*" onchange="previewImage(this, 'preview1')" required>
                                            <div id="preview1" class="image-preview"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Image 2</label>
                                            <input type="file" class="form-control" name="productImage2" accept="image/*" onchange="previewImage(this, 'preview2')">
                                            <div id="preview2" class="image-preview"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Image 3</label>
                                            <input type="file" class="form-control" name="productImage3" accept="image/*" onchange="previewImage(this, 'preview3')">
                                            <div id="preview3" class="image-preview"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Image 4</label>
                                            <input type="file" class="form-control" name="productImage4" accept="image/*" onchange="previewImage(this, 'preview4')">
                                            <div id="preview4" class="image-preview"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productStatus" class="form-label required-field">Product Status</label>
                                    <select class="form-select" id="productStatus" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" class="btn btn-secondary">Reset Form</button>
                                <button type="submit" class="btn btn-primary">Add Product</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="EditProduct.php" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Existing Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        function addColorInput() {
            const container = document.getElementById('colorInputsContainer');
            const newInput = document.createElement('div');
            newInput.className = 'color-input-container d-flex align-items-center gap-2 mt-2';
            newInput.innerHTML = `
                <input type="text" class="form-control" name="colors[]" placeholder="Enter color name" required>
                <i class="fas fa-minus-circle remove-color-btn" onclick="removeColorInput(this)"></i>
            `;
            container.appendChild(newInput);
        }

        function removeColorInput(element) {
            element.parentElement.remove();
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.backgroundImage = '';
            }
        }
    </script>
</body>
</html>