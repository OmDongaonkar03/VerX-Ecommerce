<?php
	include_once('function.php');
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X Clothing | Our Story</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1a2e;
            --secondary-color: #4831d4;
            --accent-color: #ccf381;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .about-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .about-card:hover {
            transform: scale(1.02);
        }

        .about-image {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .values-section {
            padding: 50px 0;
        }

        .value-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar Placeholder (replace with your existing navbar) -->
    <?php
        include_once('function.php');
        navbar();
    ?>

    <!-- Hero Section -->
    <section class="hero-section text-white">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Redefining Modern Fashion</h1>
            <p class="lead">Where Style Meets Sustainability</p>
        </div>
    </section>

    <!-- About Story Section -->
    <div class="container py-5">
        <div class="about-card shadow-lg">
            <div class="row g-0">
                <div class="col-md-6">
                    <img src="images\aboutus img.jpeg" class="about-image" alt="X Clothing Story">
                </div>
                <div class="col-md-6 p-5 d-flex align-items-center">
                    <div>
                        <h2 class="mb-4">Our Journey</h2>
                        <p class="text-muted">
                            VerX emerged from a bold vision to transform fashion. Founded in 2018 by design visionaries, we're more than a clothing brand—we're a movement challenging traditional fashion paradigms.
                        </p>
                        <p class="text-muted">
                            Our mission is simple: create clothing that empowers individuals, respects the planet, and pushes the boundaries of design and sustainability.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <section class="values-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <i class="fas fa-leaf value-icon"></i>
                    <h3>Sustainability</h3>
                    <p>Committed to eco-friendly materials and ethical production.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-palette value-icon"></i>
                    <h3>Innovation</h3>
                    <p>Continuously pushing design boundaries and exploring new techniques.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-users value-icon"></i>
                    <h3>Community</h3>
                    <p>Building a global network of conscious, style-forward individuals.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Placeholder (replace with your existing footer) -->
    <?php
        footer();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>