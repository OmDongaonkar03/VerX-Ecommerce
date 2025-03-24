<?php
include("admin function.php");

$admin_email = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;
if (!$admin_email || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin_email)) {
    header("Location: admin login.php");
    exit();
}

$sql = mysqli_query($conn, "SELECT * FROM `signup` WHERE `Status` = 'Pending'");

$entries = mysqli_query($conn, "SELECT COUNT(*) AS req, date FROM `signup` GROUP BY date");

$safe_admin_email = mysqli_real_escape_string($conn, $admin_email);
$privileges_query = mysqli_query($conn, sprintf("SELECT privileges FROM `admin details` WHERE `admin_email` = '%s'", $safe_admin_email));
$privileges_data = mysqli_fetch_assoc($privileges_query);
$privileges = explode(",", htmlspecialchars($privileges_data['privileges'] ?? '', ENT_QUOTES, 'UTF-8'));

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Pending Requests</title>
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
       
       .message-cell {
           max-width: 300px;
           white-space: normal;
           word-wrap: break-word;
       }
       
       .table tbody tr:hover {
           background-color: #f8f9fa;
       }

       .chart-container {
           background: white;
           border-radius: 12px;
           box-shadow: 0 4px 6px rgba(0,0,0,0.1);
           padding: 1.5rem;
           margin-bottom: 2rem;
           height: 400px;
       }
   </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <?php echo htmlspecialchars(sidebar(), ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="main-content" id="main">
        <div class="header">
            <h2 class="m-0"><i class="fa fa-hourglass"></i> Pending Requests</h2>
        </div>
        
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
        
        <div class="mt-4 mb-4 px-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search..." aria-label="Search" name="search" id="searchInput">
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
                        <th>Date</th>
                        <?php if (in_array('edit', $privileges)) { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="display">
                    <?php
                    if (mysqli_num_rows($sql) > 0) {
                        while ($data = mysqli_fetch_assoc($sql)) {
                            $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                            $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                            $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                            $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                            $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                            $date = htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $contact; ?></td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $password; ?></td>
                            <td><?php echo $date; ?></td>
                            <?php if (in_array('edit', $privileges)) { ?>
                                <td class="d-flex gap-1">
                                    <button type="button" class="btn btn-success mt-2" onclick="action('<?php echo urlencode($id); ?>', 'accept')">Accept</button>
                                    <button type="button" class="btn btn-danger mt-2" onclick="action('<?php echo urlencode($id); ?>', 'reject')">Reject</button>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan=\"" . (in_array('edit', $privileges) ? 7 : 6) . "\">No pending requests found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            let param = "request_search";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&input=" + word, true);
            xhttp.send();
        }
        
        function action(id, action) {
            let safeId = encodeURIComponent(id);
            let safeAction = encodeURIComponent(action);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("display").innerHTML = this.responseText;
                }
            };
            let param = "user_action";
            xhttp.open("GET", "admin_ajax.php?param=" + param + "&action=" + safeAction + "&user=" + safeId, true);
            xhttp.send();
        }
    </script>
    
    <script>
        <?php 
        if (mysqli_num_rows($entries) > 0) {
            $dateArr = [];
            $dateCount = [];
            
            while ($dateData = mysqli_fetch_assoc($entries)) {
                $dateArr[] = htmlspecialchars($dateData['date'], ENT_QUOTES, 'UTF-8');
                $dateCount[] = (int)$dateData['req'];
            }
            
            array_multisort(array_map('strtotime', $dateArr), SORT_ASC, $dateArr, $dateCount);
        } else {
            $dateArr = [];
            $dateCount = [];
        }
        ?>
        
        // Initialize chart with request data
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dateArr); ?>,
                datasets: [{
                    label: 'Request Data',
                    data: <?php echo json_encode($dateCount); ?>,
                    backgroundColor: 'rgba(82, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>