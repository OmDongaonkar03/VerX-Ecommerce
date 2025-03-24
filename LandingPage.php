<?php
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerX - Modern Fashion for Every Style</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
	
	<link rel="icon" href="https://i.ibb.co/YZ1GwrD/Screenshot-2025-01-07-205133-removebg-preview.png" type="image/x-icon" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }
        .newsletter-section {
            background-color: #f1f3f5;
        }
        .product-card {
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .login-modal-body {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .social-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        a {
            text-decoration: none;
        }
		.toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">VerX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-">
                    <li class="nav-item">
                        <a class="nav-link text-primary" href="signup.php">
                            <i class="fas fa-user me-2"></i>Sign-Up
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="display-4 mb-4">Modern Fashion <br><span class="text-primary">For Every Style</span></h1>
                    <p class="lead text-muted mb-4">Elevate your wardrobe with VerX - where comfort meets cutting-edge design. Sustainable, stylish, and always ahead of the curve.</p>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="img-fluid rounded shadow" alt="VerX Fashion Collection">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Collections -->
    <section class="py-5" id="collections">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up" data-aos-duration="800">Our Collections</h2>
            <div class="row">
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                    <div class="card product-card h-100">
                        <img src="https://www.snitch.co.in/cdn/shop/files/dfd1340e2976273e55ae259c16a349d1.jpg?v=1733139247" 
                             class="card-img-top" alt="Casual Wear">
                        <div class="card-body">
                            <h5 class="card-title">Casual Essentials</h5>
                            <p class="card-text text-muted">Comfortable everyday wear for the modern individual.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">$49.99</span>
                                <span class="badge bg-primary">New</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    <div class="card product-card h-100">
                        <img src="https://cdn.shopify.com/s/files/1/0420/7073/7058/files/e3102811d5cb676f0951b89d990d7248.jpg?v=1732601799&width=600" 
                             class="card-img-top" alt="Professional Wear">
                        <div class="card-body">
                            <h5 class="card-title">Magestic Match</h5>
                            <p class="card-text text-muted">Sophisticated clothing for the workplace.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">$79.99</span>
                                <span class="badge bg-success">Best Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                    <div class="card product-card h-100">
                        <img src="https://www.snitch.co.in/cdn/shop/files/d46409a4e00da36e6ff934bca86fb44d.jpg?v=1733139219" 
                             class="card-img-top" alt="Active Wear">
                        <div class="card-body">
                            <h5 class="card-title">Active Lifestyle</h5>
                            <p class="card-text text-muted">Performance-driven clothing for fitness.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">$59.99</span>
                                <span class="badge bg-warning text-dark">Sale</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    <div class="card product-card h-100">
                        <img src="https://www.snitch.co.in/cdn/shop/files/0bb1602ee9ce4a3ee1867e663822f2c9.jpg?v=1733139195" 
                             class="card-img-top" alt="Accessories">
                        <div class="card-body">
                            <h5 class="card-title">Look-Up Accessories</h5>
                            <p class="card-text text-muted">Stylish accessories for every occasion.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">$29.99</span>
                                <span class="badge bg-info">Trending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Size Guide Section -->
    <section class="bg-light py-5" id="size-guide">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6" data-aos="fade-right" data-aos-duration="1000">
                    <h2 class="mb-4">Find Your Perfect Fit</h2>
                    <p class="lead text-muted mb-4">
                        Our comprehensive size guide helps you find the perfect fit every time. 
                        We provide detailed measurements and fit recommendations for every body type.
                    </p>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                        View Size Guide
                    </button>
                </div>
                <div class="col-md-6" data-aos="fade-left" data-aos-duration="1000">
                    <img src="https://9thson.com/wp-content/uploads/2020/08/Men_Round_Updated_with_5XL_2.jpg" 
                         class="img-fluid rounded shadow" alt="Size Guide">
                </div>
            </div>
        </div>
    </section>

    <!-- Size Guide Modal -->
    <div class="modal fade" id="sizeGuideModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">VerX Size Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Measuring Tips</h6>
                            <ul class="list-unstyled">
                                <li>• Use a flexible measuring tape</li>
                                <li>• Wear minimal clothing</li>
                                <li>• Stand straight with arms at sides</li>
                                <li>• Measure at the fullest part of each area</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Size Conversion</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>US</th>
                                        <th>UK</th>
                                        <th>EU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>XS</td>
                                        <td>6</td>
                                        <td>34</td>
                                    </tr>
                                    <tr>
                                        <td>S</td>
                                        <td>8</td>
                                        <td>36</td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>10</td>
                                        <td>38</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>12</td>
                                        <td>40</td>
                                    </tr>
                                    <tr>
                                        <td>XL</td>
                                        <td>14</td>
                                        <td>42</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sustainability Section -->
    <section class="py-5" id="sustainability">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up" data-aos-duration="800">Our Commitment to Sustainability</h2>
            <div class="d-flex flex-row flex-wrap justify-content-center gap-4">
                <div class="card" style="width: 18rem;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1499971442178-8c10fdf5f6ac?q=80&w=1891&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="card-img-top" alt="...">
                    <div class="card-body">
                        <p class="card-text">At VerX, <b>transparency is our promise.</b> We meticulously track every step of our production process, ensuring that each garment is created under fair, safe, and ethical working conditions. Our factories are regularly audited to maintain the highest standards of worker welfare and environmental responsibility.</p>
                    </div>
                </div>
                <div class="card" style="width: 18rem;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1580287486990-b08ff7d0dfca?q=80&w=1891&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="card-img-top" alt="...">
                    <div class="card-body">
                        <p class="card-text">We believe in <b>empowering workers</b> through fair wages, safe working environments, and comprehensive benefits. Our partnerships with manufacturing facilities prioritize worker rights, provide ongoing training programs, and ensure that every individual involved in creating our clothing is treated with dignity and respect.</p>
                    </div>
                </div>
                <div class="card" style="width: 18rem;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1524275539700-cf51138f679b?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="card-img-top" alt="...">
                    <div class="card-body">
                        <p class="card-text">Our commitment to <b>sustainability goes beyond design.</b> We source materials responsibly, focusing on organic, recycled, and low-impact fabrics. From responsibly harvested cotton to innovative recycled polyester, each material is chosen to minimize environmental footprint while maintaining the highest quality standards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-duration="800">
                    <h5>VerX Fashion</h5>
                    <p>Modern, sustainable fashion for the conscious individual.</p>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Collections</a></li>
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Sustainability</a></li>
                    </ul>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    <h5>Connect With Us</h5>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 VerX Fashion. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
	
	<script>
	
		var toastElement = document.querySelector('.toast');
        var toast = new bootstrap.Toast(toastElement, {
            delay: 20000 // Auto-hide after 5 seconds
        });
        toast.show();
			
		document.addEventListener('DOMContentLoaded', function() {
			getLocation();
		});
		
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition, showError);
			} else {
				alert("Geolocation is not supported by this browser.");
			}
		}
	
		function showPosition(position) {
			document.cookie = `latitude=${position.coords.latitude};expires=Fri, 31 Dec 2025 23:59:59 GMT;`;
			document.cookie = `longitude=${position.coords.longitude};expires=Fri, 31 Dec 2025 23:59:59 GMT;`;
		}
	
		function showError(error) {
			switch(error.code) {
				case error.PERMISSION_DENIED:
					alert("Please allow location access to login");
					break;
				case error.POSITION_UNAVAILABLE:
					alert("Location information is unavailable.");
					break;
				case error.TIMEOUT:
					alert("The request to get user location timed out.");
					break;
				case error.UNKNOWN_ERROR:
					alert("An unknown error occurred.");
					break;
			}
		}
	</script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 100,
        });
    </script>
</body>
</html>