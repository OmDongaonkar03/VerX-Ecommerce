<?php
include("config.php");
session_start();

if (isset($_SESSION['user_email'])) {
    $email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
    $sql = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email));
    
    if ($sql && mysqli_num_rows($sql) > 0) {
        $data = mysqli_fetch_assoc($sql);
    } else {
        $data = [];
        error_log("No user found with email: " . $email);
    }
    
    $userID = isset($data['id']) ? mysqli_real_escape_string($conn, $data['id']) : 0;
    $result = mysqli_query($conn, sprintf("SELECT COUNT(*) AS total FROM `atcproduct` WHERE `userID` = '%s'", $userID));
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        $row = ['total' => 0];
    }
} else {
    $data = [];
    $row = ['total' => 0];
}

$notification = mysqli_query($conn, "SELECT * FROM `user_notification` ORDER BY `timestamp` DESC");
if (!$notification) {
    error_log("Notification query failed: " . mysqli_error($conn));
    $notification = false; 
}

function navbar() {
    global $row, $notification;
	
    echo '
    <!-- Custom CSS for navbar -->
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
        
        .navbar {
            box-shadow: var(--shadow);
            background-color: white !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .nav-link i {
            margin-right: 5px;
        }
        
        .notification-item:hover {
            background-color: rgba(79, 70, 229, 0.06) !important;
        }
        
        .cart-badge {
            position: relative;
        }
        
        .cart-badge::after {
            content: "' . htmlspecialchars($row['total'], ENT_QUOTES, 'UTF-8') . '";
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: var(--secondary);
            color: white;
            font-size: 10px;
            padding: 0 6px;
            border-radius: 10px;
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        .footer-logo {
            font-size: 44px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 15px;
            display: inline-block;
            text-decoration: none;
        }
        
        .footer-logo span {
            color: var(--secondary);
        }
    </style>
    
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a href="index.php" class="footer-logo mt-2">Ver<span>X</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">VerX</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3 gap-3">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="index.php">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productpage.php">
                                <i class="fa fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cart-badge" href="addtocart.php">
                                <i class="fa fa-shopping-cart"></i> Cart
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wishlist.php">
                                <i class="fas fa-heart"></i> Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="AboutUs.php">
                                <i class="fa fa-info-circle"></i> About Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contactUs.php">
                                <i class="fa fa-phone"></i> Contact Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <div class="nav-link" role="button" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <i class="fa fa-bell"></i> Notification
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="userpage.php">
                                <i class="fa fa-user"></i> User
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="fa fa-bell me-2"></i> Notifications
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 350px; overflow-y: auto;">';
    
    if ($notification && mysqli_num_rows($notification) > 0) {
        while ($data_notification = mysqli_fetch_assoc($notification)) {
            echo
            '<a href="' . htmlspecialchars($data_notification['notification_link'], ENT_QUOTES, 'UTF-8') . '" class="text-decoration-none">
                <div class="notification-item border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-top">
                        <h6 class="mb-2 text-dark">' . htmlspecialchars($data_notification['notification_title'], ENT_QUOTES, 'UTF-8') . '</h6>
                        <small class="text-muted ms-2">' . htmlspecialchars($data_notification['timestamp'], ENT_QUOTES, 'UTF-8') . '</small>
                    </div>
                    <p class="text-muted mb-0">' . htmlspecialchars($data_notification['notification_detail'], ENT_QUOTES, 'UTF-8') . '</p>
                </div>
            </a>';
        }
    } else {
        echo '<div class="p-4 text-center text-muted">
            <i class="fa fa-bell-slash fa-3x mb-3 text-secondary"></i>
            <p>No notifications yet</p>
        </div>';
    }
    
    echo '
                </div>
            </div>
        </div>
    </div>';
}

function footer() {
    echo '
    <!-- Custom CSS for footer -->
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
        
        .modern-footer {
            background-color: var(--light);
            padding: 60px 0 0;
            color: var(--dark);
            border-top: 1px solid var(--border);
        }
        
        .footer-heading {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            display: inline-block;
        }
        
        .footer-heading::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 3px;
            background-color: var(--secondary);
            border-radius: 10px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }
        
        .footer-links a:hover {
            color: var(--primary);
            transform: translateX(3px);
        }
        
        .footer-links a::before {
            content: "›";
            margin-right: 6px;
            color: var(--primary);
            font-weight: bold;
            opacity: 0;
            transition: var(--transition);
        }
        
        .footer-links a:hover::before {
            opacity: 1;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: rgba(79, 70, 229, 0.1);
            border-radius: 50%;
            color: var(--primary);
            transition: var(--transition);
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
        }
        
        .footer-bottom {
            padding: 20px 0;
            margin-top: 40px;
            background-color: rgba(0, 0, 0, 0.02);
            border-top: 1px solid var(--border);
        }
        
        .newsletter-input {
            border: 2px solid var(--border);
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            padding: 10px 15px;
            transition: var(--transition);
        }
        
        .newsletter-input:focus {
            border-color: var(--primary);
            box-shadow: none;
        }
        
        .newsletter-button {
            background-color: var(--primary);
            border: none;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            color: white;
            padding: 0 20px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .newsletter-button:hover {
            background-color: var(--primary-light);
        }
        
        .about-text {
            line-height: 1.7;
            margin-bottom: 20px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 15px;
            display: inline-block;
        }
        
        .footer-logo span {
            color: var(--secondary);
        }
        
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .payment-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 30px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .copyright-text {
            margin-bottom: 0;
            font-size: 14px;
        }
        
        .divider {
            width: 100%;
            height: 1px;
            background-color: var(--border);
            margin: 30px 0;
        }
        
        @media (max-width: 767px) {
            .footer-heading {
                margin-top: 20px;
            }
        }
    </style>
    
    <footer class="modern-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-logo">Ver<span>X</span></div>
                    <p class="about-text">We are more than just a clothing brand. VerX is a lifestyle, committed to providing high-quality, stylish, and sustainable fashion for individuals who dare to express themselves.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-heading">Quick Links</h5>
                    <div class="row">
                        <div class="col-6">
                            <ul class="footer-links">
                                <li><a href="#">Size Guide</a></li>
                                <li><a href="#">Shipping Info</a></li>
                                <li><a href="#">Returns Policy</a></li>
                                <li><a href="#">Contact Us</a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="footer-links">
                                <li><a href="#">FAQs</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Terms of Service</a></li>
                                <li><a href="#">Our Story</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="divider d-md-none"></div>
                    
                    <h5 class="footer-heading d-md-none">We Accept</h5>
                    <div class="payment-methods d-md-none">
                        <div class="payment-icon"><i class="fab fa-cc-visa text-primary"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-mastercard text-danger"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-amex text-info"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-paypal text-primary"></i></div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-12 mb-4">
                    <h5 class="footer-heading">Stay Connected</h5>
                    <p>Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
                    
                    <div class="input-group mb-3">
                        <input type="email" class="form-control newsletter-input" placeholder="Your email address" aria-label="Email Address">
                        <button class="btn newsletter-button" type="button"><i class="fas fa-paper-plane me-1"></i> Subscribe</button>
                    </div>
                    
                    <div class="divider d-none d-md-block"></div>
                    
                    <h5 class="footer-heading d-none d-md-block">We Accept</h5>
                    <div class="payment-methods d-none d-md-flex">
                        <div class="payment-icon"><i class="fab fa-cc-visa text-primary"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-mastercard text-danger"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-amex text-info"></i></div>
                        <div class="payment-icon"><i class="fab fa-cc-paypal text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom text-center">
            <div class="container">
                <p class="copyright-text">© 2024 VerX. All Rights Reserved. Designed with <i class="fas fa-heart text-danger"></i> for fashion lovers.</p>
            </div>
        </div>
    </footer>';
}
?>