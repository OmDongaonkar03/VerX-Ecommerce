<?php
include('config.php');

session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['name']) && isset($_POST['contact']) && isset($_POST['email']) && isset($_POST['password'])) {
        $userID = rand(1000, 100000);
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Name validation - letters, spaces, and some special characters
        if (!preg_match('/^[a-zA-Z\s\'-]{2,50}$/', $name)) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="nameToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid name format
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("nameToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        }
        // Contact validation - numbers only, appropriate length
        else if (!preg_match('/^[0-9]{9,15}$/', $contact)) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="contactToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Invalid contact number
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("contactToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            </script>';
        }
        // Email validation - standard email format
        else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
        // Password validation - alphanumeric with some special characters
        else if (!preg_match('/^[a-zA-Z0-9@#$%^&*!_.-]{6,50}$/', $password)) {
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="passwordToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Password must be at least 6 characters with letters, numbers, and special characters
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
            // Sanitize and escape inputs
            $name = mysqli_real_escape_string($conn, $name);
            $contact = mysqli_real_escape_string($conn, $contact);
            $email = mysqli_real_escape_string($conn, $email);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $dp = 'uploads/user.png';
            date_default_timezone_set("Asia/Kolkata");
            $current_time = date("Y/m/d H:i");
            $current_date = date("d/m/Y");

            // Check if email exists
            $checkSQL = sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email);
            $result = mysqli_query($conn, $checkSQL);
            $rows = mysqli_num_rows($result);

            if ($rows == 0) {
                // Insert new user
                $insertSQL = sprintf(
                    "INSERT INTO `signup`(`id`, `name`, `contact`, `email`, `password`, `Profile photo`, `Status`, `date`) 
                    VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 'Pending', '%s')",
                    $userID, $name, $contact, $email, $hashed_password, $dp, $current_date
                );
                mysqli_query($conn, $insertSQL);

                // Insert notification
                $notifSQL = sprintf(
                    "INSERT INTO `notifications`(`title`, `detail`, `timestamp`) 
                    VALUES ('New user Access Request', '%s has sent a request to access the website', '%s')",
                    $name, $current_time
                );
                mysqli_query($conn, $notifSQL);

                $_SESSION["user_email"] = $email;
                header("Location: pending_registration.php");
                exit;
            } else {
                echo '
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="duplicateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Email Already In Use
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var toastEl = document.getElementById("duplicateToast");
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
    <title>Sign Up to VerX</title>
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
    }

    .signup-wrapper {
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

    .signup-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
        border: none;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }

    .signup-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .signup-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 7px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }

    .signup-card-body {
        padding: 2.5rem 2rem;
    }

    .signup-title {
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

    .btn {
        height: 54px;
        font-weight: 600;
        border-radius: var(--border-radius);
        letter-spacing: 0.5px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .btn-primary {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        border: none;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        background: linear-gradient(45deg, var(--primary-light), var(--primary));
    }

    .btn-secondary {
        background: #f3f4f6;
        color: var(--dark);
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        background: #e5e7eb;
    }

    .btn::after {
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

    .btn:hover::after {
        animation: sheen 1s forwards;
    }

    @keyframes sheen {
        100% {
            opacity: 1;
            transform: rotateZ(60deg) translate(1em, -9em);
        }
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

    .decoration {
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

    .content-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6B7280;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    /* Improved responsive styles */
    @media (min-width: 992px) {
        .signup-wrapper {
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
        .signup-card-body {
            padding: 2rem 1.5rem;
        }
        
        .brand-logo {
            font-size: 3rem;
        }
        
        .decoration-1, .decoration-2 {
            display: none;
        }
    }
  </style>
  <body>
    <!-- Decoration Elements -->
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>
    
    <div class="signup-wrapper">
        <div class="container">
            <div class="content-container">
                <div class="brand-container">
                    <div class="brand-logo">VerX</div>
                    <p class="brand-description">
                        Premium clothing that empowers individuals, respects the planet, and pushes the boundaries of design.
                    </p>
                </div>
            
                <div class="signup-card">
                    <div class="signup-card-body">
                        <h2 class="signup-title">Create Account</h2>
                        
                        <form method="POST">
                            <!-- Hidden fields for coordinates -->
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="lng">
                            
                            <!-- Name input -->
                            <div class="form-outline position-relative">
                                <label class="form-label" for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe" />
                                <i class="input-icon fas fa-user mt-3"></i>
                            </div>
            
                            <!-- Contact input -->
                            <div class="form-outline position-relative">
                                <label class="form-label" for="contact">Contact Number</label>
                                <input type="number" id="contact" name="contact" class="form-control" required placeholder="Enter your phone number" />
                                <i class="input-icon fas fa-phone mt-1"></i>
                                <div class="form-text">Numbers only, between 9-15 digits</div>
                            </div>
                            
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
                                <i class="input-icon fas fa-eye password-toggle" id="togglePassword"></i>
                                <div class="form-text">Minimum 6 characters including letters, numbers, and special characters</div>
                            </div>
                            
                            <!-- Button group -->
                            <div class="d-flex justify-content-between gap-3 mt-4 mb-3">
                                <button type="button" onclick="resetForm()" class="btn btn-secondary flex-grow-1">
                                    <i class="fas fa-redo-alt me-2"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-user-plus me-2"></i> Sign Up
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="account-links">
                        <div class="mb-2">
                            Already have an account? <a href="login.php">Sign In</a>
                        </div>
                        <div>
                            Are you an admin? <a href="admin/admin login.php">Admin Login</a>
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
        
        // Reset form function
        function resetForm() {
            document.getElementById('name').value = '';
            document.getElementById('contact').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        }
    </script>
  </body>
</html>