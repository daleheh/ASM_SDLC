<?php
// Kết nối cơ sở dữ liệu
include './connectdb/db.php'; // Đảm bảo file kết nối database hoạt động

// Xử lý session (giả lập user login để kiểm tra)
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null; // Giả sử chưa đăng nhập
}

// Khởi tạo giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Giỏ hàng là một mảng
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1; // Tăng số lượng nếu sản phẩm đã có trong giỏ hàng
    } else {
        $_SESSION['cart'][$productId] = ['quantity' => 1]; // Thêm sản phẩm mới
    }
    header("Location: dashboard.php"); // Refresh trang để tránh gửi lại form
    exit();
}

// Xử lý tìm kiếm sản phẩm
$searchQuery = '';
if (isset($_GET['query'])) {
    $searchQuery = trim($_GET['query']);
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("ss", $likeQuery, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Truy vấn mặc định khi không tìm kiếm
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dash.css">
    <title>Dashboard</title>
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
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </nav>
        <div class="user-menu">
            <?php if ($_SESSION['username']): ?>
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
        <h1>Feature products</h1>

        <!-- Hiển thị kết quả tìm kiếm -->
        <?php if ($searchQuery): ?>
            <h2>Result of searching for: "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
        <?php endif; ?>

        <div class="products">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p>Price: <?php echo number_format($row['price']); ?> VND</p>
                        <!-- Form thêm vào giỏ hàng -->
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Add to MyCart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No matching products found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
