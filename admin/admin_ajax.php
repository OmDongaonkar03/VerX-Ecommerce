<?php
include("admin function.php");

// Validate and sanitize session email
$admin = isset($_SESSION["admin_email"]) ? filter_var($_SESSION["admin_email"], FILTER_SANITIZE_EMAIL) : null;
if (!$admin || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin)) {
    header("Location: admin login.php");
    exit();
}

// Secure admin details query
$safe_admin = mysqli_real_escape_string($conn, $admin);
$privileges_query = sprintf("SELECT privileges FROM `admin details` WHERE `admin_email` = '%s'", $safe_admin);
$privileges_result = mysqli_query($conn, $privileges_query);
$privileges_data = mysqli_fetch_assoc($privileges_result);
$privileges = explode(",", htmlspecialchars($privileges_data['privileges'], ENT_QUOTES, 'UTF-8'));

// Parameters
$work = isset($_GET['param']) ? htmlspecialchars($_GET['param'], ENT_QUOTES, 'UTF-8') : '';

// User search on admin side
if ($work == 'user_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input']));
        $sql = sprintf("SELECT * FROM `signup` WHERE `Status`='Accepted' AND (`name` LIKE '%%%s%%' OR `email` LIKE '%%%s%%' OR `id` LIKE '%%%s%%')",
            $search, $search, $search);
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                echo "<tr>";
                echo "<td>$id</td>";
                echo "<td>$name</td>";
                echo "<td>$contact</td>";
                echo "<td>$email</td>";
                echo "<td>$password</td>";
                if (in_array('edit', $privileges)) {
                    echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"terminate_user(" . urlencode($id) . ")\">Terminate</button></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"6\">No users found.</td></tr>";
        }
    }
    exit;
}

// Terminate user from admin panel
if ($work == 'terminate_user') {
    if (isset($_GET['user']) && preg_match("/^[0-9]+$/", $_GET['user'])) {
        $user = mysqli_real_escape_string($conn, $_GET['user']);
        $terminate = mysqli_query($conn, sprintf("UPDATE `signup` SET `Status`='Pending' WHERE `id` = '%s'", $user));

        if ($terminate) {
            $sql = mysqli_query($conn, "SELECT * FROM `signup` WHERE `Status`='Accepted'");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                    $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                    $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                    $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$name</td>";
                    echo "<td>$contact</td>";
                    echo "<td>$email</td>";
                    echo "<td>$password</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"terminate_user(" . urlencode($id) . ")\">Terminate</button></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"6\">No accepted users found.</td></tr>";
            }
        } else {
            $sql = mysqli_query($conn, "SELECT * FROM `signup` WHERE `Status`='Accepted'");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                    $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                    $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                    $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$name</td>";
                    echo "<td>$contact</td>";
                    echo "<td>$email</td>";
                    echo "<td>$password</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"terminate_user(" . urlencode($id) . ")\">Terminate</button></td>";
                    }
                    echo "</tr>";
                }
            }
        }
    }
    exit;
}

// Product search on admin side
if ($work == 'product_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input']));
        $sql = sprintf("SELECT * FROM `products` WHERE `productName` LIKE '%%%s%%' OR `category` LIKE '%%%s%%' OR `description` LIKE '%%%s%%' OR `productID` LIKE '%%%s%%'",
            $search, $search, $search, $search);
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
                $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
                $quantity = htmlspecialchars($data['quantity'], ENT_QUOTES, 'UTF-8');
                $colors = implode(", ", array_map(function($color) {
                    return htmlspecialchars(trim($color), ENT_QUOTES, 'UTF-8');
                }, explode(",", $data['colors'])));
                $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                echo "<tr>";
                echo "<td>$productID</td>";
                echo "<td>$productName</td>";
                echo "<td>$category</td>";
                echo "<td>$price</td>";
                echo "<td>$discount</td>";
                echo "<td class=\"description-cell\">$description</td>";
                echo "<td>$quantity</td>";
                echo "<td>$colors</td>";
                echo "<td>$status</td>";
                if (in_array('edit', $privileges)) {
                    echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"remove_prod(" . urlencode($productID) . ")\">Remove</button></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"10\" class=\"text-center\">No products found.</td></tr>";
        }
    }
    exit;
}

// Product remove from admin side
if ($work == 'remove_product') {
    if (isset($_GET['product']) && preg_match("/^[0-9]+$/", $_GET['product'])) {
        $product = mysqli_real_escape_string($conn, $_GET['product']);
        $remove_product = mysqli_query($conn, sprintf("DELETE FROM `products` WHERE `productID` = '%s'", $product));

        if ($remove_product) {
            $sql = mysqli_query($conn, "SELECT * FROM `products`");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                    $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                    $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
                    $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                    $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
                    $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
                    $quantity = htmlspecialchars($data['quantity'], ENT_QUOTES, 'UTF-8');
                    $colors = implode(", ", array_map(function($color) {
                        return htmlspecialchars(trim($color), ENT_QUOTES, 'UTF-8');
                    }, explode(",", $data['colors'])));
                    $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$productID</td>";
                    echo "<td>$productName</td>";
                    echo "<td>$category</td>";
                    echo "<td>$price</td>";
                    echo "<td>$discount</td>";
                    echo "<td class=\"description-cell\">$description</td>";
                    echo "<td>$quantity</td>";
                    echo "<td>$colors</td>";
                    echo "<td>$status</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"remove_prod(" . urlencode($productID) . ")\">Remove</button></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"10\" class=\"text-center\">No products found.</td></tr>";
            }
        } else {
            $sql = mysqli_query($conn, "SELECT * FROM `products`");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                    $productName = htmlspecialchars($data['productName'], ENT_QUOTES, 'UTF-8');
                    $category = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
                    $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                    $discount = htmlspecialchars($data['discount'], ENT_QUOTES, 'UTF-8');
                    $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
                    $quantity = htmlspecialchars($data['quantity'], ENT_QUOTES, 'UTF-8');
                    $colors = implode(", ", array_map(function($color) {
                        return htmlspecialchars(trim($color), ENT_QUOTES, 'UTF-8');
                    }, explode(",", $data['colors'])));
                    $status = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$productID</td>";
                    echo "<td>$productName</td>";
                    echo "<td>$category</td>";
                    echo "<td>$price</td>";
                    echo "<td>$discount</td>";
                    echo "<td class=\"description-cell\">$description</td>";
                    echo "<td>$quantity</td>";
                    echo "<td>$colors</td>";
                    echo "<td>$status</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-danger mt-2\" onclick=\"remove_prod(" . urlencode($productID) . ")\">Remove</button></td>";
                    }
                    echo "</tr>";
                }
            }
        }
    }
    exit;
}

// Cart search on admin side
if ($work == 'cart_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input']));
        $sql = sprintf("SELECT * FROM `atcproduct` WHERE `productID` LIKE '%%%s%%' OR `userID` LIKE '%%%s%%'",
            $search, $search);
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                $productPrice = htmlspecialchars($data['productPrice'], ENT_QUOTES, 'UTF-8');
                $userID = htmlspecialchars($data['userID'], ENT_QUOTES, 'UTF-8');
                echo "<tr>";
                echo "<td>$productID</td>";
                echo "<td>$productPrice</td>";
                echo "<td>$userID</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"3\">No cart items found.</td></tr>";
        }
    }
    exit;
}

// Order products search on admin side
if ($work == 'orders_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input']));
        $sql = sprintf("SELECT * FROM `ordered products` WHERE (`address` LIKE '%%%s%%' OR `city` LIKE '%%%s%%' OR `state` LIKE '%%%s%%' OR `productID` LIKE '%%%s%%') AND `Status` != 'Completed'",
            $search, $search, $search, $search);
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                $userID = htmlspecialchars($data['userID'], ENT_QUOTES, 'UTF-8');
                $userContact = htmlspecialchars($data['userContact'], ENT_QUOTES, 'UTF-8');
                $address = htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8');
                $city = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
                $state = htmlspecialchars($data['state'], ENT_QUOTES, 'UTF-8');
                $pinCode = htmlspecialchars($data['pinCode'], ENT_QUOTES, 'UTF-8');
                $orderTime = htmlspecialchars($data['orderTime'], ENT_QUOTES, 'UTF-8');
                echo "<tr>";
                echo "<td>$productID</td>";
                echo "<td>$price</td>";
                echo "<td>$userID</td>";
                echo "<td>$userContact</td>";
                echo "<td>$address</td>";
                echo "<td>$city</td>";
                echo "<td>$state</td>";
                echo "<td>$pinCode</td>";
                if (in_array('edit', $privileges)) {
                    echo "<td><button type=\"button\" class=\"btn btn-success mt-2\" onclick=\"complete_order('" . urlencode($productID) . "', '" . urlencode($orderTime) . "')\">Completed</button></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"9\">No pending orders found.</td></tr>";
        }
    }
    exit;
}

// Update order status to Completed
if ($work == 'orders_completed') {
    if (isset($_GET['product']) && isset($_GET['orderTime'])) {
        $product = mysqli_real_escape_string($conn, urldecode($_GET['product']));
        $ordertime = mysqli_real_escape_string($conn, urldecode($_GET['orderTime']));
        $updt_status = mysqli_query($conn, sprintf("UPDATE `ordered products` SET `Status` = 'Completed' WHERE `productID` = '%s' AND `orderTime` = '%s'", $product, $ordertime));

        if ($updt_status) {
            $sql = mysqli_query($conn, "SELECT * FROM `ordered products` WHERE `Status` != 'Completed'");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                    $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                    $userID = htmlspecialchars($data['userID'], ENT_QUOTES, 'UTF-8');
                    $userContact = htmlspecialchars($data['userContact'], ENT_QUOTES, 'UTF-8');
                    $address = htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8');
                    $city = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
                    $state = htmlspecialchars($data['state'], ENT_QUOTES, 'UTF-8');
                    $pinCode = htmlspecialchars($data['pinCode'], ENT_QUOTES, 'UTF-8');
                    $orderTime = htmlspecialchars($data['orderTime'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$productID</td>";
                    echo "<td>$price</td>";
                    echo "<td>$userID</td>";
                    echo "<td>$userContact</td>";
                    echo "<td>$address</td>";
                    echo "<td>$city</td>";
                    echo "<td>$state</td>";
                    echo "<td>$pinCode</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-success mt-2\" onclick=\"complete_order('" . urlencode($productID) . "', '" . urlencode($orderTime) . "')\">Completed</button></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"9\">No pending orders.</td></tr>";
            }
        } else {
            $sql = mysqli_query($conn, "SELECT * FROM `ordered products` WHERE `Status` != 'Completed'");
            if (mysqli_num_rows($sql) > 0) {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $productID = htmlspecialchars($data['productID'], ENT_QUOTES, 'UTF-8');
                    $price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
                    $userID = htmlspecialchars($data['userID'], ENT_QUOTES, 'UTF-8');
                    $userContact = htmlspecialchars($data['userContact'], ENT_QUOTES, 'UTF-8');
                    $address = htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8');
                    $city = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
                    $state = htmlspecialchars($data['state'], ENT_QUOTES, 'UTF-8');
                    $pinCode = htmlspecialchars($data['pinCode'], ENT_QUOTES, 'UTF-8');
                    $orderTime = htmlspecialchars($data['orderTime'], ENT_QUOTES, 'UTF-8');
                    echo "<tr>";
                    echo "<td>$productID</td>";
                    echo "<td>$price</td>";
                    echo "<td>$userID</td>";
                    echo "<td>$userContact</td>";
                    echo "<td>$address</td>";
                    echo "<td>$city</td>";
                    echo "<td>$state</td>";
                    echo "<td>$pinCode</td>";
                    if (in_array('edit', $privileges)) {
                        echo "<td><button type=\"button\" class=\"btn btn-success mt-2\" onclick=\"complete_order('" . urlencode($productID) . "', '" . urlencode($orderTime) . "')\">Completed</button></td>";
                    }
                    echo "</tr>";
                }
            }
        }
    }
    exit;
}

// Search user requests on admin side
if ($work == 'request_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input'] ?? ''));
        $sql = mysqli_query($conn, sprintf("SELECT * FROM `signup` WHERE `Status` = 'Pending' AND (`name` LIKE '%%%s%%' OR `email` LIKE '%%%s%%' OR `id` LIKE '%%%s%%')",
            $search, $search, $search));

        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                $date = htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8');
                echo '
                <tr>
                    <td>' . $id . '</td>
                    <td>' . $name . '</td>
                    <td>' . $contact . '</td>
                    <td>' . $email . '</td>
                    <td>' . $password . '</td>
                    <td>' . $date . '</td>';
                if (in_array('edit', $privileges)) {
                    echo '
                    <td class="d-flex gap-1">
                        <button type="button" class="btn btn-success mt-2" onclick="action(\'' . urlencode($id) . '\', \'accept\')">Accept</button>
                        <button type="button" class="btn btn-danger mt-2" onclick="action(\'' . urlencode($id) . '\', \'reject\')">Reject</button>
                    </td>';
                }
                echo '</tr>';
            }
        }
    }
    exit;
}

// Accept or reject user request to access website
if ($work == 'user_action') {
    if (isset($_GET['action']) && isset($_GET['user']) && preg_match("/^[0-9]+$/", $_GET['user'])) {
        $action = $_GET['action'] === 'accept' ? 'Accepted' : 'Rejected'; // Restrict to valid actions
        $user = mysqli_real_escape_string($conn, $_GET['user']);
        
        if ($action === 'Accepted') {
            $accept = mysqli_query($conn, sprintf("UPDATE `signup` SET `Status`='Accepted' WHERE `id` = '%s'", $user));
        } else {
            $reject = mysqli_query($conn, sprintf("DELETE FROM `signup` WHERE `id` = '%s'", $user));
        }
        
        $sql = mysqli_query($conn, "SELECT * FROM `signup` WHERE `Status` = 'Pending'");
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $id = htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
                $contact = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');
                $date = htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8');
                echo '
                <tr>
                    <td>' . $id . '</td>
                    <td>' . $name . '</td>
                    <td>' . $contact . '</td>
                    <td>' . $email . '</td>
                    <td>' . $password . '</td>
                    <td>' . $date . '</td>';
                if (in_array('edit', $privileges)) {
                    echo '
                    <td class="d-flex gap-1">
                        <button type="button" class="btn btn-success mt-2" onclick="action(\'' . urlencode($id) . '\', \'accept\')">Accept</button>
                        <button type="button" class="btn btn-danger mt-2" onclick="action(\'' . urlencode($id) . '\', \'reject\')">Reject</button>
                    </td>';
                }
                echo '</tr>';
            }
        }
    }
    exit;
}

if ($work == 'msg_search') {
    if (isset($_GET['input'])) {
        $search = mysqli_real_escape_string($conn, urldecode($_GET['input'] ?? ''));
        $sql = mysqli_query($conn, sprintf("SELECT * FROM `connect request` WHERE `userName` LIKE '%%%s%%' OR `userEmail` LIKE '%%%s%%'",
            $search, $search));
        
        if (mysqli_num_rows($sql) > 0) {
            while ($data = mysqli_fetch_assoc($sql)) {
                $userName = htmlspecialchars($data['userName'], ENT_QUOTES, 'UTF-8');
                $userEmail = htmlspecialchars($data['userEmail'], ENT_QUOTES, 'UTF-8');
                $userMSG = htmlspecialchars($data['userMSG'], ENT_QUOTES, 'UTF-8');
                echo '
                <tr>
                    <td>' . $userName . '</td>
                    <td>' . $userEmail . '</td>
                    <td class="message-cell">' . $userMSG . '</td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="3">No messages found matching your search.</td></tr>';
        }
    }
    exit;
}

if ($work == 'add_product') {
    
}
?>