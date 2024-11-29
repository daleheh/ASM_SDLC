<?php
include '../connectdb/db.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Chèn sản phẩm mới vào cơ sở dữ liệu
    $sql = "INSERT INTO products (name, description, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $name, $description, $price); // "ssd" = string, string, double

    if ($stmt->execute()) {
        echo "Product added successfully!";
        header("Location: index.php");
        exit;
    } else {
        echo "An error occurred while adding the product!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Product</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>
    <h1>Create New Product</h1>
    <form action="create.php" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required><br>

        <button type="submit">Create</button>
    </form>
    <a href="dashboard.php">Back</a>
</body>
</html>