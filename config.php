<?php
// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

// Database configuration
$servername = "localhost";
$username = "root"; 
$password = "";
$database = "verx";


$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    error_log("Connection failed: " . mysqli_connect_error()); // Log instead of display
    header("Location: /error.php"); // Redirect to a generic error page
    exit();
}

mysqli_set_charset($conn, "utf8mb4"); 
?>