<?php
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    $errors = [];
    if (empty($name) || strlen($name) < 2 || strlen($name) > 50) {
        $errors[] = "Name must be between 2 and 50 characters.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid email is required.";
        }
    }
    if (empty($message) || strlen($message) < 10 || strlen($message) > 500) {
        $errors[] = "Message must be between 10 and 500 characters.";
    }

    if (empty($errors)) {
        // Secure the inputs
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $message = mysqli_real_escape_string($conn, $message);

        // Insert into database
        $sql = mysqli_query($conn, sprintf(
            "INSERT INTO `connect request` (`userName`, `userEmail`, `userMSG`) VALUES ('%s', '%s', '%s')",
            $name, $email, $message
        ));

        if ($sql) {
            // Success toast
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto"><i class="fas fa-check-circle me-2"></i>Success</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Your message has been sent successfully!
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toastEl = document.getElementById("successToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    setTimeout(() => document.querySelector("form").reset(), 2000); // Reset form after 2s
                });
            </script>';
        } else {
            // Error toast with MySQL error
            echo '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger text-white">
                        <strong class="me-auto"><i class="fas fa-exclamation-circle me-2"></i>Error</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Failed to send message: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . '
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
    } else {
        // Validation error toast
        echo '
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="validationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-warning text-dark">
                    <strong class="me-auto"><i class="fas fa-exclamation-triangle me-2"></i>Warning</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ' . htmlspecialchars(implode(" ", $errors), ENT_QUOTES, 'UTF-8') . '
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var toastEl = document.getElementById("validationToast");
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        </script>';
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerX - Contact Us</title>
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
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            color: var(--dark);
        }
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        .contact-header:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--primary);
            margin: 15px auto 0;
            border-radius: 3px;
        }
        .contact-header p {
            color: #6B7280;
            margin-top: 15px;
            font-size: 1.1rem;
        }
        .contact-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 40px;
            border: 1px solid var(--border);
            transition: var(--transition);
        }
        .contact-card:hover {
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 12px 15px;
            transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .form-control::placeholder {
            color: #9CA3AF;
        }
        .btn-submit {
            background-color: var(--primary);
            border: none;
            border-radius: var(--border-radius);
            padding: 12px 30px;
            font-weight: 600;
            transition: var(--transition);
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .character-count {
            color: #6B7280;
            font-size: 0.85rem;
            text-align: right;
            margin-top: 5px;
        }
        .contact-info {
            margin-top: 40px;
            padding: 30px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .contact-info-item i {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 15px;
        }
        .toast {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        .toast-header {
            padding: 12px 15px;
        }
        .toast-body {
            padding: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div>
        <?php navbar(); ?>
    </div>

    <div class="container py-5">
        <!-- Page Header -->
        <div class="contact-header">
            <h1 class="fw-bold"><i class="fas fa-envelope-open-text me-2"></i> Contact Us</h1>
            <p>We'd love to hear from you! Share your thoughts, questions, or feedback below.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <form method="POST" id="contactForm">
                        <div class="mb-4">
                            <label for="name" class="form-label">Your Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-primary"></i></span>
                                <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter your name" value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Type your message here..." required oninput="updateCharCount(this);"><?php echo isset($message) ? htmlspecialchars($message, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                            <div class="character-count"><span id="charCount">0</span>/500 characters</div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-submit"><i class="fas fa-paper-plane me-2"></i> Send Message</button>
                        </div>
                    </form>
                </div>
                
                <!-- Contact Info Section -->
                <div class="contact-info">
                    <h4 class="mb-4 fw-bold text-center">Other Ways to Reach Us</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Our Location</h6>
                                    <p class="mb-0">VERX</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Phone Number</h6>
                                    <p class="mb-0">+91 (PQR) ABC-WXYZ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Email Address</h6>
                                    <p class="mb-0">support@verx.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Working Hours</h6>
                                    <p class="mb-0">Mon-Fri: 9AM - 5PM</p>
                                </div>
                            </div>
                        </div>
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
        // Character counter for message field
        function updateCharCount(textarea) {
            const charCount = textarea.value.length;
            document.getElementById('charCount').textContent = charCount;
            
            // Visual feedback
            const charCountElement = document.querySelector('.character-count');
            if (charCount > 450) {
                charCountElement.style.color = '#DC2626';
            } else if (charCount > 400) {
                charCountElement.style.color = '#F59E0B';
            } else {
                charCountElement.style.color = '#6B7280';
            }
        }
        
        // Initialize character count on page load
        document.addEventListener('DOMContentLoaded', function() {
            const messageField = document.getElementById('message');
            if (messageField.value) {
                updateCharCount(messageField);
            }
            
            // Add subtle animation to form fields on focus
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.closest('.mb-4')?.classList.add('was-validated');
                });
            });
        });
    </script>
</body>
</html>