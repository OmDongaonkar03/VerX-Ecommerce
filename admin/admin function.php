<?php
include("../config.php");
session_start();
function sidebar(){
    global $conn;
	$admin_email = $_SESSION["admin_email"];
    echo '
        <div class="sidebar p-3">
            <h3 class="text-white mb-1 d-flex align-items-center">
                <a class="nav-link" href="admin panel.php">
                    VerX Admin
                </a>
                <div class="position-relative ms-auto mt-2">
                    <h4>
                    <a href="notification.php" class="text-white text-decoration-none">
                        <i class="fas fa-bell"></i>
                    </a>
                    </h4>
                </div>
            </h3>
            <nav class="nav flex-column">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-users"></i>
                    User Details
                </a>
                <a class="nav-link" href="products.php">
                    <i class="fas fa-box"></i>
                    Products
                </a>
                <a class="nav-link" href="products by category.php">
                    <i class="fas fa-tags"></i>
                    Products By Category
                </a>
                <a class="nav-link" href="cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    Cart Items
                </a>
                <a class="nav-link" href="orders.php">
                    <i class="fas fa-clipboard-list"></i>
                    Orders
                </a>
                <a class="nav-link" href="sales.php">
                    <i class="fas fa-chart-line"></i>
                    Sales Insights
                </a>
                <a class="nav-link" href="requests.php">
                    <i class="fa fa-hourglass"></i>
                    Access Request
                </a>
                <a class="nav-link" href="messages.php">
                    <i class="fas fa-comments"></i>
                    Messages
                </a>';

    $privileges_query = mysqli_query($conn, "SELECT privileges FROM `admin details` WHERE `admin_email` = '$admin_email'");
    $privileges_data = mysqli_fetch_assoc($privileges_query);
    $privileges = explode(",", $privileges_data['privileges']);
    
    if (in_array('add_editProduct', $privileges)) {
        echo '
            <a class="nav-link" href="AddProduct.php">
                <i class="fas fa-plus-circle"></i>
                Add Products
            </a>
            <a class="nav-link" href="EditProduct.php">
                <i class="fas fa-edit"></i>
                Edit Products
            </a>';
    }

			if (in_array('notification', $privileges)) {
                echo 
				'<a class="nav-link" href="send notification user.php">
                    <i class="fas fa-bell"></i>
                    Send Notification
                </a>';
			}
			if (in_array('create_admin', $privileges)) {
				echo 
				'<a class="nav-link" href="create admin.php">
                    <i class="fas fa-user-shield"></i>
                    Create Admin
                </a>';
			}
			echo '
                <a class="nav-link mt-auto" href="../LandingPage.php" style="padding: 10px; background-color:red; border-radius: 5px;">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </nav>
        </div>
    ';
}
?>