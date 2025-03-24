<?php
include('function.php');

$email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
$sql = sprintf("SELECT * FROM `signup` WHERE `email` = '%s'", $email);
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    if ($data = mysqli_fetch_assoc($result)) {
        $id = $data['id'];
        $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
        $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
        $password = $data['password'];
        $status = $data['Status'];
        $dp = htmlspecialchars($data['Profile photo'], ENT_QUOTES, 'UTF-8');
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            background-color: #F3F4F6;
            color: var(--dark);
        }
        
        .card {
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card {
            position: relative;
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            height: 80px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .profile-img-container {
            position: relative;
            margin-top: -50px;
            z-index: 1;
        }
        
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 4px solid var(--light);
            box-shadow: var(--shadow);
        }
        
        .edit-profile-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: var(--light);
            color: var(--primary);
            border: 2px solid var(--light);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .edit-profile-btn:hover {
            background-color: var(--primary);
            color: var(--light);
        }
        
        .user-badge {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
        }
        
        .info-card {
            padding: 20px;
        }
        
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .info-value {
            color: #6B7280;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: var(--border-radius);
            transition: var(--transition);
            padding: 10px 20px;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            border-radius: var(--border-radius);
            transition: var(--transition);
            padding: 10px 20px;
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #0DA271;
            border-color: #0DA271;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: #EF4444;
            border-color: #EF4444;
            border-radius: var(--border-radius);
            transition: var(--transition);
            padding: 10px 20px;
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background-color: #DC2626;
            border-color: #DC2626;
            transform: translateY(-2px);
        }
        
        .btn-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-icon i {
            transition: var(--transition);
        }
        
        .btn-icon:hover i {
            transform: translateX(3px);
        }
        
        .option-section {
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 16px;
            border: 1px solid var(--border);
        }
        
        .option-header {
            cursor: pointer;
            transition: var(--transition);
            padding: 15px;
            background-color: var(--light);
            color: var(--dark);
            font-weight: 600;
        }
        
        .option-header:hover {
            background-color: #F3F4F6;
        }
        
        .option-header .fa {
            transition: var(--transition);
        }
        
        .option-header .fa-chevron-up {
            transform: rotate(180deg);
        }
        
        .change-form {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: var(--light);
        }
        
        .change-form.active {
            max-height: 300px;
            padding: 15px;
        }
        
        .change-form .form-content {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .change-form.active .form-content {
            opacity: 1;
        }
        
        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            padding: 10px 15px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }
        
        .current-value {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            padding: 8px 12px;
            border-radius: var(--border-radius);
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow);
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            background-color: white;
            opacity: 0.8;
        }
        
        .modal-footer {
            border-top: 1px solid var(--border);
        }
    </style>
  </head>
<body>
    <div>
        <?php navbar(); ?>
    </div>
    <div class="container py-5">
        <div class="row g-4">
            <!-- Left column - Profile Photo -->
            <div class="col-lg-4">
                <div class="card profile-card">
                    <div class="profile-header"></div>
                    <div class="card-body text-center">
                        <div class="profile-img-container mx-auto">
                            <img src="<?php echo $dp; ?>" alt="Profile Picture" class="profile-img rounded-circle">
                            <button class="edit-profile-btn" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                        <h4 class="mt-3 mb-1"><?php echo $name; ?></h4>
                        <div class="d-flex justify-content-center mt-4">
                            <a href="LandingPage.php" class="btn btn-danger btn-icon">
                                <i class="fa fa-sign-out"></i>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right column - User Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="m-0">Personal Information</h5>
                    </div>
                    <div class="card-body info-card">
                        <!-- User ID -->
                        <div class="row info-row align-items-center">
                            <div class="col-sm-4 col-md-3">
                                <p class="info-label mb-0">User ID</p>
                            </div>
                            <div class="col-sm-8 col-md-9">
                                <p class="info-value mb-0"><?php echo $id; ?></p>
                            </div>
                        </div>
                        
                        <!-- Full Name -->
                        <div class="row info-row align-items-center">
                            <div class="col-sm-4 col-md-3">
                                <p class="info-label mb-0">Full Name</p>
                            </div>
                            <div class="col-sm-8 col-md-9">
                                <p class="info-value mb-0"><?php echo $name; ?></p>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="row info-row align-items-center">
                            <div class="col-sm-4 col-md-3">
                                <p class="info-label mb-0">Email</p>
                            </div>
                            <div class="col-sm-8 col-md-9">
                                <p class="info-value mb-0"><?php echo $email; ?></p>
                            </div>
                        </div>
                        
                        <!-- Contact -->
                        <div class="row info-row align-items-center">
                            <div class="col-sm-4 col-md-3">
                                <p class="info-label mb-0">Contact</p>
                            </div>
                            <div class="col-sm-8 col-md-9">
                                <p class="info-value mb-0"><?php echo $contact; ?></p>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button class="btn btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#changeDetailsModal">
                                    <span>Edit Profile Details</span>
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Picture Modal -->
    <div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilePicModalLabel">Change Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="profilePicForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profilePicInput" class="form-label">Select new profile picture</label>
                            <input type="file" class="form-control" id="profilePicInput" name="profile_pic" accept="image/*" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Upload Picture</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Details Modal -->
    <div class="modal fade" id="changeDetailsModal" tabindex="-1" aria-labelledby="changeDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeDetailsModalLabel">Edit Profile Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Name Section -->
                    <div class="option-section">
                        <div class="option-header d-flex justify-content-between align-items-center" data-section="name">
                            <span>Change Name</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <form class="change-form" id="nameForm">
                            <div class="form-content">
                                <div class="current-value">
                                    <i class="fas fa-user me-2"></i>Current Name: <span id="currentName"><?php echo $name; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Name</label>
                                    <input type="text" class="form-control" placeholder="Enter new name" name="newName" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
    
                    <!-- Email Section -->
                    <div class="option-section">
                        <div class="option-header d-flex justify-content-between align-items-center" data-section="email">
                            <span>Change Email</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <form class="change-form" id="emailForm" method="POST">
                            <div class="form-content">
                                <div class="current-value">
                                    <i class="fas fa-envelope me-2"></i>Current Email: <?php echo $email; ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Email</label>
                                    <input type="email" class="form-control" placeholder="Enter new email" name="newEmail" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="email_update_req">Save Changes</button>
                            </div>
                        </form>
                    </div>
    
                    <!-- Contact Section -->
                    <div class="option-section">
                        <div class="option-header d-flex justify-content-between align-items-center" data-section="contact">
                            <span>Change Contact</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <form class="change-form" id="contactForm">
                            <div class="form-content">
                                <div class="current-value">
                                    <i class="fas fa-phone me-2"></i>Current Contact: <span id="currentContact"><?php echo $contact; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Contact</label>
                                    <input type="tel" class="form-control" placeholder="Enter new contact" name="newContact" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
    
                    <!-- Password Section -->
                    <div class="option-section">
                        <div class="option-header d-flex justify-content-between align-items-center" data-section="password">
                            <span>Change Password</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <form class="change-form" id="passwordForm">
                            <div class="form-content">
                                <div class="mb-3">
                                    <label class="form-label">Old Password</label>
                                    <input type="password" class="form-control" name="oldPass" placeholder="Enter old password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="newPass" placeholder="Enter new password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <?php footer(); ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion functionality
            const optionHeaders = document.querySelectorAll('.option-header');
            
            optionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const section = this.dataset.section;
                    const form = document.getElementById(section + 'Form');
                    const icon = this.querySelector('.fa');
                    const isCurrentlyActive = form.classList.contains('active');
                    
                    // Close all forms
                    document.querySelectorAll('.change-form').forEach(form => {
                        form.classList.remove('active');
                    });
                    
                    // Reset all icons
                    document.querySelectorAll('.option-header .fa').forEach(icon => {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    });
                    
                    // If the clicked form wasn't active, open it
                    if (!isCurrentlyActive) {
                        form.classList.add('active');
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                });
            });

            // Helper function to show toast
            function showToast(message, isSuccess = false) {
                const toastHTML = `
                    <div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto">${isSuccess ? 'Success' : 'Error'}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                ${message}
                            </div>
                        </div>
                    </div>`;
                document.body.insertAdjacentHTML('beforeend', toastHTML);
                const toastEl = document.querySelector('.toast:last-child');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                setTimeout(() => toastEl.remove(), 3000);
            }

            // Name Update
            document.getElementById('nameForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update_name');

                fetch('ajaxreq.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                    if (data.success) {
                        document.getElementById('currentName').textContent = data.newValue;
                        document.querySelector('.profile-card .card-body h4').textContent = data.newValue;
                        document.querySelectorAll('.info-value')[1].textContent = data.newValue;
                        this.reset();
                    }
                })
                .catch(error => {
                    showToast('An error occurred while updating the name', false);
                    console.error('Error:', error);
                });
            });

            // Contact Update
            document.getElementById('contactForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update_contact');

                fetch('ajax_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                    if (data.success) {
                        document.getElementById('currentContact').textContent = data.newValue;
                        document.querySelectorAll('.info-value')[3].textContent = data.newValue;
                        this.reset();
                    }
                })
                .catch(error => {
                    showToast('An error occurred while updating the contact', false);
                    console.error('Error:', error);
                });
            });

            // Password Update
            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update_password');

                fetch('ajax_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                    if (data.success) {
                        this.reset();
                    }
                })
                .catch(error => {
                    showToast('An error occurred while updating the password', false);
                    console.error('Error:', error);
                });
            });

            // Profile Picture Update
            document.getElementById('profilePicForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update_profile_pic');

                fetch('ajax_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                    if (data.success) {
                        document.querySelector('.profile-img').src = data.newValue;
                        this.reset();
                        bootstrap.Modal.getInstance(document.getElementById('profilePicModal')).hide();
                    }
                })
                .catch(error => {
                    showToast('An error occurred while updating the profile picture', false);
                    console.error('Error:', error);
                });
            });

            // Email Update (placeholder - not implemented)
            document.getElementById('emailForm').addEventListener('submit', function(e) {
                e.preventDefault();
                showToast('This feature is not developed yet! Sorry', false);
            });
        });
    </script>
</body>
</html>