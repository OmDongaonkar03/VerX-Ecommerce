<?php
	include("admin function.php");
	if (!isset($_SESSION["admin_email"])) {
		header("Location: admin login.php");
		exit();
	}
	$sql = mysqli_query($conn,"SELECT * FROM `atcproduct`");
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Cart Products</title>
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
       
       .toggle-sidebar {
           background: none;
           border: none;
           color: white;
           font-size: 1.2rem;
           cursor: pointer;
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
         <?php sidebar() ?>
    </div>

    <div class="main-content" id="main">
        <div class="header">
            <h2 class="m-0"><i class="fas fa-shopping-cart me-2"></i>Cart Products</h2>
        </div>

		<div class="mb-4 px-3">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Search..." aria-label="Search" name="search" id="searchInput">
			</div>
		</div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Price</th>
                        <th>User ID</th>
                    </tr>
                </thead>
                <tbody id="display">
                    <?php
                    if (mysqli_num_rows($sql) > 0) {
                        while ($data = mysqli_fetch_assoc($sql)) {
                    ?>
                    <tr>
                        <td><?php echo $data['productID']; ?></td>
                        <td><?php echo $data['productPrice']; ?></td>
                        <td><?php echo $data['userID']; ?></td>
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
            let word = document.getElementById("searchInput").value;
            
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "cart_search";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&input=" + word, true);
            xhttp.send();
        }
	</script>
</body>
</html>