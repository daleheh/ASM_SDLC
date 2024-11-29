<?php
include '../connectdb/db.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];  // Lọc và chuyển đổi 'id' thành kiểu số nguyên

    // Kiểm tra tồn tại sản phẩm
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        $_SESSION['error'] = "Product does not exist!";
        header("Location: index.php");
        exit;
    }

    // Xóa sản phẩm khỏi cơ sở dữ liệu
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "An error occurred while deleting the product!";
    }

    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid product ID!";
    header("Location: index.php");
    exit;
}