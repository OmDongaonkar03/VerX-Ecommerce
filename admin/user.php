<?php
include("admin function.php");

$admin_email = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;

if (!$admin_email || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin_email)) {
    header("Location: admin login.php");
    exit();
}

$sql = sprintf("SELECT * FROM `signup` WHERE `Status`='%s'", 
    mysqli_real_escape_string($conn, "Accepted"));
$result = mysqli_query($conn, $sql);

$safe_admin_email = mysqli_real_escape_string($conn, $admin_email);
$admin_details_query = sprintf("SELECT * FROM `admin details` WHERE `admin_email` = '%s'", $safe_admin_email);
$admin_result = mysqli_query($conn, $admin_details_query);
$admin_data = mysqli_fetch_assoc($admin_result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>User Details</title>
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
           margin:0 0 6px 0;
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
       
       .table-container {
           background: white;
           border-radius: 12px;
           box-shadow: 0 4px 6px rgba(0,0,0,0.1);
           padding: 1.5rem;
       }
       
       .table thead {
           background: #0d6efd;
           color: white;
       }
       
       .table th {
           padding: 1rem;
           font-weight: 600;
       }
       
       .table td {
           padding: 1rem;
           vertical-align: middle;
       }
       
       .table tbody tr:hover {
           background-color: #f8f9fa;
       }
   </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <?php 
        echo htmlspecialchars(sidebar(), ENT_QUOTES, 'UTF-8'); 
        ?>
    </div>

    <div class="main-content" id="main">
        <div class="header">
            <h2 class="m-0"><i class="fas fa-users me-2"></i>User Details</h2>
        </div>

        <div class="mb-4 px-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search..." 
                       aria-label="Search" name="search" id="searchInput">
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Password</th>
                        <?php 
                        $safe_admin_email = mysqli_real_escape_string($conn, $admin_email);
                        $privileges_query = sprintf("SELECT privileges FROM `admin details` WHERE `admin_email` = '%s'", 
                            $safe_admin_email);
                        $privileges_result = mysqli_query($conn, $privileges_query);
                        $privileges_data = mysqli_fetch_assoc($privileges_result);
                        $privileges = explode(",", htmlspecialchars($privileges_data['privileges'], ENT_QUOTES, 'UTF-8'));
                        if (in_array('edit', $privileges)) { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="display">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                            $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                            $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                            $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                            $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $contact; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $password; ?></td>
                        <?php if (in_array('edit', $privileges)) { ?>
                            <td>
                                <button type="button" class="btn btn-danger mt-2" 
                                        onclick="terminate_user(<?php echo urlencode($id); ?>)">Terminate</button>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', search);
        
        function search() {
            let word = encodeURIComponent(document.getElementById("searchInput").value);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "user_search";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&input=" + word, true);
            xhttp.send();
        }
        
        function terminate_user(user) {
            let safeUser = encodeURIComponent(user);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "terminate_user";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&user=" + safeUser, true);
            xhttp.send();
        }
    </script>
</body>
</html>