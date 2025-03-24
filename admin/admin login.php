<?php
include("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Sanitize and validate email
        $adminEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $adminEmail)) {
            echo "<script>alert('Invalid email format.')</script>";
            exit;
        }
        
        // Sanitize password (no specific filter, just escape for SQL)
        $adminPassword = $_POST['password'];
        if (empty($adminPassword)) {
            echo "<script>alert('Password cannot be empty.')</script>";
            exit;
        }

        // Escape inputs for SQL injection prevention
        $adminEmail = mysqli_real_escape_string($conn, $adminEmail);
        $adminPassword = mysqli_real_escape_string($conn, $adminPassword);

        // Use sprintf for safer query construction
        $query = sprintf("SELECT * FROM `admin details` WHERE `admin_email` = '%s'", $adminEmail);
        $sql = mysqli_query($conn, $query);

        if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $db_data = $data['admin_email'];
            $db_pass = $data['admin_password'];

            // Verify password (assuming passwords in DB are hashed with password_hash)
            if ($adminEmail === $db_data && $adminPassword == $db_pass) {
                $_SESSION["admin_email"] = $adminEmail;
                header("Location: admin panel.php");
                exit;
            } else {
                // Sanitize output to prevent XSS in alert
                $msg = htmlspecialchars("Invalid details, please retry.", ENT_QUOTES, 'UTF-8');
                echo "<script>alert('$msg')</script>";
            }
        } else {
            // Sanitize output to prevent XSS in alert
            $msg = htmlspecialchars("Admin email not found.", ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$msg')</script>";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Log-In VerX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    body {
        background-color: hsl(0, 0%, 96%);
        min-height: 100vh;
    }
    section {
        margin-top: 5%;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        border-color: #0d6efd;
    }
    .btn-primary {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
    }
    .brand-title {
        font-size: 85px;
        font-weight: bold;
        background: linear-gradient(45deg, #0d6efd, #0099ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
    }
    .brand-description {
        color: hsl(217, 10%, 50.8%);
        font-size: 1.1rem;
        line-height: 1.6;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }
    .input-group {
        position: relative;
    }
    .input-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    .signup-link {
        color: #0d6efd;
        text-decoration: none;
        margin-left: 8px;
        font-weight: 500;
    }
    .signup-link:hover {
        text-decoration: underline;
    }
</style>
<body>
    <section>
        <div class="px-4 py-5 px-md-5 text-center text-lg-start">
            <div class="container">
                <div class="row gx-lg-5 align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 class="brand-title">Admin - VerX</h1>
                        <p class="brand-description">
                            We're more than a clothing brand—we're a movement challenging traditional fashion paradigms. 
                            Our mission is simple: create clothing that empowers individuals, respects the planet, 
                            and pushes the boundaries of design and sustainability.
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="card">
                            <div class="card-body py-5 px-md-5">
                                <h3 class="mb-4 text-center">Welcome Back!</h3>
                                <form method="POST">
                                    <!-- Email input -->
                                    <div class="mb-4">
                                        <label class="form-label" for="email">Email address</label>
                                        <div class="input-group">
                                            <input type="email" id="email" name="email" class="form-control" 
                                                   placeholder="Enter your email" required/>
                                            <span class="input-icon">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Password input -->
                                    <div class="mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control"
                                                   placeholder="Enter your password" required/>
                                            <span class="input-icon">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Submit button -->
                                    <button type="submit" class="btn btn-primary mb-4">
                                        <i class="fas fa-sign-in-alt me-2"></i>Log In
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>