<?php
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Sanitize and validate email
        $userEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="emailToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid email format
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("emailToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        }
        // Validate password
        else if (!preg_match('/^[a-zA-Z0-9@#$%^&*!_.-]{3,50}$/', $_POST['password'])) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="passwordToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid password format
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("passwordToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        } else {
            // Escape inputs for SQL safety
            $userEmail = mysqli_real_escape_string($conn, $userEmail);
            $userPassword = mysqli_real_escape_string($conn, $_POST['password']);

            // Secure SQL query
            $query = sprintf("SELECT * FROM `signup` WHERE `email` = '%s' AND `password` = '%s'", $userEmail, $userPassword);
            $sql = mysqli_query($conn, $query);

            if (mysqli_num_rows($sql) > 0) {
                $data = mysqli_fetch_assoc($sql);
                $status = $data['Status'];

                $_SESSION["user_email"] = $userEmail;
                if ($status == 'Accepted') {
                    header("Location: index.php");
                    exit();
                } else {
                    header("Location: pending_registration.php");
                    exit();
                }
            } else {
                echo '
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="loginToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Invalid Email or Password
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var toastEl = document.getElementById("loginToast");
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                </script>';
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In to VerX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  </head>
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
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: linear-gradient(135deg, #f5f7fa 0%, #e4ecfa 100%);
        padding: 2rem 0;
		overflow:hidden;
		
    }

    .login-wrapper {
        width: 100%;
        max-width: 1200px;
    }

    .brand-container {
        position: relative;
        z-index: 2;
        margin-bottom: 2rem;
    }

    .brand-logo {
        font-size: 5rem;
        font-weight: 800;
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        text-shadow: 0px 5px 15px rgba(99, 102, 241, 0.3);
        margin-bottom: 1.5rem;
        display: inline-block;
        position: relative;
    }

    .brand-logo::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 6px;
        background: var(--secondary);
        border-radius: 3px;
    }

    .brand-description {
        color: var(--dark);
        font-size: 1.1rem;
        line-height: 1.7;
        max-width: 90%;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .login-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
        border: none;
        width: 100%;
        max-width: 450px;
		max-height:650px;
        margin: 0 auto;
    }

    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .login-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 7px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }

    .login-card-body {
        padding: 2.5rem 2rem;
    }

    .login-title {
        font-weight: 700;
        color: var(--dark);
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .form-outline {
        position: relative;
        margin-bottom: 1.75rem;
    }

    .form-control {
        height: 54px;
        border-radius: var(--border-radius);
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 2px solid var(--border);
        transition: var(--transition);
        box-shadow: none;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-icon {
        position: absolute;
        top: 50%;
        right: 1rem;
        transform: translateY(-50%);
        color: #6B7280;
        transition: var(--transition);
    }

    .form-control:focus + .input-icon {
        color: var(--primary);
    }

    .password-toggle {
        cursor: pointer;
    }

    .btn-primary {
        height: 54px;
        font-weight: 600;
        border-radius: var(--border-radius);
        letter-spacing: 0.5px;
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        border: none;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        background: linear-gradient(45deg, var(--primary-light), var(--primary));
    }

    .btn-primary::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        bottom: -50%;
        left: -50%;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0));
        transform: rotateZ(60deg) translate(-5em, 7.5em);
        opacity: 0;
        transition: opacity 0.5s;
    }

    .btn-primary:hover::after {
        animation: sheen 1s forwards;
    }

    @keyframes sheen {
        100% {
            opacity: 1;
            transform: rotateZ(60deg) translate(1em, -9em);
        }
    }

    .link-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .remember-me input {
        margin-right: 0.5rem;
    }

    .forgot-password {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .forgot-password:hover {
        color: var(--primary-light);
        text-decoration: underline;
    }

    .account-links {
        text-align: center;
        padding: 1.25rem;
        background-color: #F8FAFC;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }

    .account-links a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .account-links a:hover {
        color: var(--primary-light);
        text-decoration: underline;
    }

    .login-decoration {
        position: fixed;
        z-index: 1;
    }

    .decoration-1 {
        top: 20%;
        left: 10%;
        width: 300px;
        height: 300px;
        background: linear-gradient(45deg, rgba(99, 102, 241, 0.1), rgba(16, 185, 129, 0.05));
        border-radius: 50%;
        filter: blur(40px);
    }

    .decoration-2 {
        bottom: 10%;
        right: 5%;
        width: 250px;
        height: 250px;
        background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(99, 102, 241, 0.05));
        border-radius: 50%;
        filter: blur(40px);
    }

    .social-login {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: white;
        border: 2px solid var(--border);
        color: var(--dark);
        font-size: 1.25rem;
        transition: var(--transition);
    }

    .social-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .social-btn.google:hover {
        color: #DB4437;
        border-color: #DB4437;
    }

    .social-btn.facebook:hover {
        color: #4267B2;
        border-color: #4267B2;
    }

    .social-btn.apple:hover {
        color: #000;
        border-color: #000;
    }

    .or-divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
        color: #6B7280;
    }

    .or-divider::before, .or-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .or-divider span {
        padding: 0 1rem;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .content-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .form-check-input {
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
        margin-left: 0.25rem;
    }

    /* Improved responsive styles */
    @media (min-width: 992px) {

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .content-container {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 3rem;
        }
        
        .brand-container {
            text-align: left;
            max-width: 450px;
            margin-bottom: 0;
        }
        
        .brand-description {
            margin: 0;
        }
    }

    @media (max-width: 991px) {
		body{
			overflow:auto;
		}
        .brand-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .brand-logo {
            font-size: 4rem;
        }
        
        .brand-logo::after {
            left: 50%;
            transform: translateX(-50%);
        }
    }

    @media (max-width: 768px) {
		body{
			overflow:auto;
		}
        .brand-logo {
            font-size: 3.5rem;
        }
        
        .decoration-1, .decoration-2 {
            opacity: 0.5;
            width: 200px;
            height: 200px;
        }
    }

    @media (max-width: 576px) {
		body{
			overflow:auto;
		}
        .login-card-body {
            padding: 2rem 1.5rem;
        }
        
        .brand-logo {
            font-size: 3rem;
        }
        
        .link-section {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .decoration-1, .decoration-2 {
            display: none;
        }
    }
  </style>
  <body>
    <!-- Decoration Elements -->
    <div class="login-decoration decoration-1"></div>
    <div class="login-decoration decoration-2"></div>
    
    <div class="login-wrapper">
        <div class="container">
            <div class="content-container">
                <div class="brand-container">
                    <div class="brand-logo">VerX</div>
                    <p class="brand-description">
                        Premium clothing that empowers individuals, respects the planet, and pushes the boundaries of design.
                    </p>
                </div>
            
                <div class="login-card">
                    <div class="login-card-body">
                        <h2 class="login-title">Welcome Back</h2>
                        
                        <form method="POST">
                            <!-- Hidden fields for coordinates -->
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="lng">
                            
                            <!-- Email input -->
                            <div class="form-outline position-relative">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required placeholder="your@email.com" />
                                <i class="input-icon fas fa-envelope mt-3"></i>
                            </div>
            
                            <!-- Password input -->
                            <div class="form-outline position-relative">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••" />
                                <i class="input-icon fas fa-eye password-toggle mt-3" id="togglePassword"></i>
                            </div>
                            
                            <div class="link-section">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label">Remember me</label>
                                </div>
                                <a href="#" class="forgot-password">Forgot password?</a>
                            </div>
                            
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary">
                                Sign In <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                        
                        <div class="or-divider">
                            <span>OR</span>
                        </div>
                        
                        <div class="social-login">
                            <a href="#" class="social-btn google">
                                <i class="fab fa-google"></i>
                            </a>
                            <a href="#" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-btn apple">
                                <i class="fab fa-apple"></i>
                            </a>
                        </div>
                        <div class="mb-2 mt-3 d-flex justify-content-center">
                            Don't have an account? <a href="signup.php">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
        
        // Get geolocation if browser supports it
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('lat').value = position.coords.latitude;
                document.getElementById('lng').value = position.coords.longitude;
            });
        }
    </script>
  </body>
</html>