<?php
include '../connectdb/db.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Kiểm tra nếu có 'id' trong URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];  // Lọc và chuyển đổi 'id' thành kiểu số nguyên

    // Lấy thông tin sản phẩm cần sửa
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "Product does not exist!";
        exit;
    }

    // Xử lý cập nhật sản phẩm
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = (float)$_POST['price'];

        // Cập nhật thông tin vào cơ sở dữ liệu
        $update_sql = "UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssdi", $name, $description, $price, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product update successful!";
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "An error occurred while updating the product!";
        }
    }
} else {
    $_SESSION['error'] = "Invalid product ID!";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>
    <h1>Edit Product</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="edit.php?id=<?php echo $product['id']; ?>" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required><?php echo $product['description']; ?></textarea><br>

        <label for="price">Price:</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>