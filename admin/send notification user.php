<?php
include("admin function.php");

// Assuming admin authentication is required
if (!isset($_SESSION["admin_email"])) {
    header("Location: admin login.php");
    exit();
}

// Fetch stats
$group_query = mysqli_query($conn, "SELECT * FROM `user_notification` GROUP BY `time`");
$group_num = mysqli_num_rows($group_query);

$user_query = mysqli_query($conn, "SELECT `id` FROM `signup`");
$user_num = mysqli_num_rows($user_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate title
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    if (empty($title)) {
        $msg = htmlspecialchars("Notification title cannot be empty.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg'); window.location.href = window.location.href;</script>";
        exit;
    }
    $title = mysqli_real_escape_string($conn, $title);

    // Sanitize and validate detail
    $detail = filter_var($_POST['detail'], FILTER_SANITIZE_STRING);
    if (empty($detail)) {
        $msg = htmlspecialchars("Notification detail cannot be empty.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg'); window.location.href = window.location.href;</script>";
        exit;
    }
    $detail = mysqli_real_escape_string($conn, $detail);

    // Sanitize and validate link
    $link = filter_var($_POST['link'], FILTER_SANITIZE_URL);
    if (!preg_match("/^https?:\/\/[a-zA-Z0-9-._~:\/?#[\]@!$&'()*+,;=]+$/", $link)) {
        $msg = htmlspecialchars("Invalid URL format.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg'); window.location.href = window.location.href;</script>";
        exit;
    }
    $link = mysqli_real_escape_string($conn, $link);

    // Set timestamp
    date_default_timezone_set("Asia/Kolkata");
    $current_time = date("Y/m/d H:i");
    $current_time = mysqli_real_escape_string($conn, $current_time);

    // Insert notification using sprintf
    $query = sprintf(
        "INSERT INTO `user_notification` (`notification_title`, `notification_detail`, `notification_link`, `timestamp`) VALUES ('%s', '%s', '%s', '%s')",
        $title,
        $detail,
        $link,
        $current_time
    );
    $sql = mysqli_query($conn, $query);

    if ($sql) {
        $msg = htmlspecialchars("Notification Sent Successfully", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg'); window.location.href = window.location.href;</script>";
    } else {
        $error = htmlspecialchars("Failed To Send Notification: " . mysqli_error($conn), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$error'); window.location.href = window.location.href;</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications - VerX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .page-wrapper {
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .admin-card {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-bottom: none;
            padding: 1.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .required-field::after {
            content: "*";
            color: #dc3545;
            margin-left: 4px;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .icon-circle {
            transform: scale(1.1);
        }

        .quick-tips {
            background: #fff;
            border-left: 4px solid var(--warning-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .template-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .template-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .notification-preview {
            background: white;
            border-left: 4px solid var(--primary-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 1rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .template-btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-container {
                flex-direction: column;
                gap: 1rem;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Stats Row -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body p-4">
                                <div class="icon-circle bg-white text-primary">
                                    <i class="fas fa-bell fa-lg"></i>
                                </div>
                                <h5 class="fw-bold">Today's Notifications</h5>
                                <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($group_num, ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p class="mt-2 mb-0 opacity-75">Total notifications sent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body p-4">
                                <div class="icon-circle bg-white text-success">
                                    <i class="fas fa-users fa-lg"></i>
                                </div>
                                <h5 class="fw-bold">Active Users</h5>
                                <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($user_num, ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p class="mt-2 mb-0 opacity-75">Currently active users</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card admin-card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0 fw-bold">Send Notification</h3>
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                                <i class="fas fa-bell me-2"></i>Notification Management
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" id="notificationForm" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="notificationTitle" class="form-label required-field fw-bold">
                                    Notification Title
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="notificationTitle" 
                                       name="title" 
                                       placeholder="Enter notification title" 
                                       required>
                                <div class="invalid-feedback">Please enter a notification title.</div>
                            </div>

                            <div class="mb-4">
                                <label for="notificationDetail" class="form-label required-field fw-bold">
                                    Notification Detail
                                </label>
                                <textarea class="form-control" 
                                          id="notificationDetail" 
                                          name="detail" 
                                          rows="4" 
                                          placeholder="Enter notification details" 
                                          required></textarea>
                                <div class="invalid-feedback">Please enter notification details.</div>
                            </div>

                            <div class="mb-4">
                                <label for="notificationlink" class="form-label required-field fw-bold">
                                    Notification Link
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="notificationlink" 
                                       name="link" 
                                       placeholder="Enter notification redirect link" 
                                       required>
                                <div class="invalid-feedback">Please enter a notification link.</div>
                            </div>

                            <!-- Quick Tips Section -->
                            <div class="quick-tips">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-lightbulb me-2 text-warning"></i>Quick Tips
                                </h6>
                                <ul class="mb-0">
                                    <li class="mb-2">Keep titles concise and attention-grabbing</li>
                                    <li class="mb-2">Use clear and specific language in details</li>
                                    <li>Avoid using all caps in notifications</li>
                                </ul>
                            </div>

                            <!-- Quick Templates -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Quick Templates</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" 
                                            class="template-btn btn btn-outline-secondary" 
                                            onclick="fillTemplate('New Product Alert', 'Check out our latest products added to the store!', 'https://example.com/products')">
                                        <i class="fas fa-tag me-2"></i>New Product
                                    </button>
                                    <button type="button" 
                                            class="template-btn btn btn-outline-secondary" 
                                            onclick="fillTemplate('Special Offer', 'Don\'t miss out on our limited time special offers!', 'https://example.com/offers')">
                                        <i class="fas fa-percentage me-2"></i>Special Offer
                                    </button>
                                    <button type="button" 
                                            class="template-btn btn btn-outline-secondary" 
                                            onclick="fillTemplate('Important Update', 'We have some important updates about our services.', 'https://example.com/updates')">
                                        <i class="fas fa-info-circle me-2"></i>Update
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="notification-preview" id="preview">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-eye me-2"></i>Preview
                                </h6>
                                <div class="preview-content">
                                    <h5 id="previewTitle" class="mb-2">Your notification title will appear here</h5>
                                    <p id="previewDetail" class="mb-2 text-muted">Your notification details will appear here</p>
                                    <small id="previewLink" class="text-primary">Link will appear here</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 btn-container">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Notification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.getElementById('notificationForm');
        const titleInput = document.getElementById('notificationTitle');
        const detailInput = document.getElementById('notificationDetail');
        const linkInput = document.getElementById('notificationlink');
        const previewTitle = document.getElementById('previewTitle');
        const previewDetail = document.getElementById('previewDetail');
        const previewLink = document.getElementById('previewLink');

        function updatePreview() {
            previewTitle.textContent = titleInput.value || 'Your notification title will appear here';
            previewDetail.textContent = detailInput.value || 'Your notification details will appear here';
            previewLink.textContent = linkInput.value || 'Link will appear here';
        }

        titleInput.addEventListener('input', updatePreview);
        detailInput.addEventListener('input', updatePreview);
        linkInput.addEventListener('input', updatePreview);

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            if (form.checkValidity()) {
                form.submit();
            } else {
                form.classList.add('was-validated');
            }
        });

        function fillTemplate(title, detail, link) {
            titleInput.value = title;
            detailInput.value = detail;
            linkInput.value = link;
            updatePreview();
        }

        // Reset preview on form reset
        form.addEventListener('reset', function() {
            setTimeout(updatePreview, 0);
        });
    </script>
</body>
</html>