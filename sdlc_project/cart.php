<?php
// Kết nối cơ sở dữ liệu
include './connectdb/db.php';
session_start();

// Giả lập giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý cập nhật số lượng sản phẩm trong giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $productId => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$productId]); // Xóa sản phẩm nếu số lượng là 0
        } else {
            $_SESSION['cart'][$productId]['quantity'] = (int)$quantity;
        }
    }
    header("Location: cart.php"); // Refresh trang để cập nhật giỏ hàng
    exit();
}

// Lấy thông tin chi tiết sản phẩm từ giỏ hàng
$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $productIds = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($productIds)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']]['quantity'];
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $cartItems[] = $row;
        $totalPrice += $row['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dash.css">
    <title>MyCart</title>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="logo">
            <a href="dashboard.php">Home</a>
        </div>
        <nav class="menu">
            <a href="#">Pant</a>
            <a href="#">Shirt</a>
            <a href="#">Hat</a>
            <a href="#">Watch</a>
            <form class="search-bar" action="dashboard.php" method="GET">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit">Search</button>
            </form>
        </nav>
        <div class="user-menu">
            <?php if (isset($_SESSION['username']) && $_SESSION['username']): ?>
                <a href="profile.php">Profile (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="cart.php">My Cart (<?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?>)</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>MyCart</h1>

        <?php if (!empty($cartItems)): ?>
            <form method="POST" action="cart.php">
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                                </td>
                                <td><?php echo number_format($item['price']); ?> VND</td>
                                <td><?php echo number_format($item['subtotal']); ?> VND</td>
                                <td>
                                    <!-- Xóa sản phẩm bằng cách đặt số lượng = 0 -->
                                    <button type="submit" name="update_cart" value="1">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h3>Total bill: <?php echo number_format($totalPrice); ?> VND</h3>
                <button type="submit" name="update_cart" value="1">Update the cart</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty now.</p>
            <a href="dashboard.php">Continue to shop</a>
        <?php endif; ?>
    </div>
</body>
</html>
