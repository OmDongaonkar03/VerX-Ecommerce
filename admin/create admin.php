<?php
include("admin function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Sanitize and validate email
    $email = filter_var($_POST['adminEmail'], FILTER_SANITIZE_EMAIL);
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $msg = htmlspecialchars("Invalid email format.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        exit;
    }

    // Validate passwords
    $password = $_POST['adminPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    if (empty($password) || $password !== $confirmPassword) {
        $msg = htmlspecialchars("Passwords must match and cannot be empty.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);

    // Sanitize and validate privileges
    if (!isset($_POST['privileges']) || !is_array($_POST['privileges'])) {
        $msg = htmlspecialchars("At least one privilege must be selected.", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        exit;
    }
    $allowed_privileges = ['read', 'edit', 'notification', 'add_editProduct', 'create_admin', 'all'];
    $privileges = array_intersect($_POST['privileges'], $allowed_privileges); // Only allow valid privileges
    $ticks = mysqli_real_escape_string($conn, implode(',', $privileges));

    // Escape email for SQL
    $email = mysqli_real_escape_string($conn, $email);

    // Use sprintf for safer query construction
    $query = sprintf(
        "INSERT INTO `admin details` (`admin_password`, `admin_email`, `privileges`) VALUES ('%s', '%s', '%s')",
        $hashedPassword,
        $email,
        $ticks
    );
    $sql = mysqli_query($conn, $query);

    if ($sql) {
        $msg = htmlspecialchars("Admin Details Added Successfully", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
        echo "<script>window.location.href = window.location.href</script>";
    } else {
        $msg = htmlspecialchars("Failed To Add Admin Details", ENT_QUOTES, 'UTF-8');
        echo "<script>alert('$msg')</script>";
        echo "<script>window.location.href = window.location.href</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Create Admin</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
		body {
			display: flex;
			background: #f8f9fa;
		}
		
		.sidebar {
			min-height: 100vh;
			background: #1a237e;
			width: 250px;
			position: fixed;
			left: 0;
			top: 0;
			padding: 1.5rem;
			z-index: 1000;
			transition: all 0.3s;
		}
		
		.sidebar.collapsed {
			width: 60px;
		}
		
		.sidebar.collapsed .nav-link span,
		.sidebar.collapsed h3 span {
			display: none;
		}
		
		.nav-link {
			color: #fff !important;
			padding: 0.5rem;
			margin: 0 0 6px 0;
			border-radius: 8px;
			transition: all 0.3s;
			display: flex;
			align-items: center;
			white-space: nowrap;
		}
		
		.nav-link:hover {
			background: rgba(255,255,255,0.15);
			transform: translateX(5px);
		}
		
		.nav-link i {
			margin-right: 12px;
			width: 20px;
			font-size: 1.1rem;
		}
		
		.main-content {
			margin-left: 250px;
			flex: 1;
			padding: 2rem;
			transition: all 0.3s;
		}
		
		.main-content.expanded {
			margin-left: 60px;
		}
		
		.header {
			background: #212529;
			color: white;
			padding: 1.5rem;
			border-radius: 12px;
			margin-bottom: 2rem;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .card-title {
            color: #0d6efd;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #212529;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            color: #495057;
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
        }

        .privileges-card .form-check {
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .privileges-card .form-check:hover {
            background: #f8f9fa;
        }

        .privileges-card .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-right: 0.75rem;
        }
   </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
		<?php sidebar() ?>
    </div>
    <div class="main-content" id="main">
        <div class="header">
            <h2 class="m-0"><i class="fas fa-user-shield me-2"></i> Create Admin</h2>
        </div>
        
        <div class="container">
            <form method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Admin Credentials</h5>
                                
                                <div class="mb-3">
                                    <label for="adminEmail" class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" id="adminEmail" name="adminEmail" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid email address.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="adminPassword" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                                    <div class="invalid-feedback">
                                        Please provide a password.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    <div class="invalid-feedback">
                                        Passwords must match.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card privileges-card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Admin Privileges</h5>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="readPrivilege" name="privileges[]" value="read">
                                    <label class="form-check-label" for="readPrivilege">
                                        Read Access
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editPrivilege" name="privileges[]" value="edit">
                                    <label class="form-check-label" for="editPrivilege">
                                        Edit Access
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="notificationPrivilege" name="privileges[]" value="notification">
                                    <label class="form-check-label" for="notificationPrivilege">
                                        Send Notifications
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="add_editProductPrivilege" name="privileges[]" value="add_editProduct">
                                    <label class="form-check-label" for="add_editProductPrivilege">
                                        Add/Edit Products
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createAdminPrivilege" name="privileges[]" value="create_admin">
                                    <label class="form-check-label" for="createAdminPrivilege">
                                        Create Admin
                                    </label>
                                </div>
								
								<div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allPrivilege" name="privileges[]" value="all">
                                    <label class="form-check-label" for="allPrivilege">
                                        All Privileges
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event){
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        const password = document.getElementById('adminPassword');
                        const confirmPassword = document.getElementById('confirmPassword');
						
                        if (password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity('Passwords must match');
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            confirmPassword.setCustomValidity('');
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })();
		document.getElementById('allPrivilege').addEventListener('change', function () {
			const checkboxes = document.querySelectorAll('input[name="privileges[]"]');
			checkboxes.forEach(checkbox => {
				checkbox.checked = this.checked;
			});
		});
    </script>
</body>
</html>