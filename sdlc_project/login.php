<?php
include('./connectdb/db.php');
session_start();
// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra xem tài khoản có tồn tại trong cơ sở dữ liệu không
    $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Nếu tìm thấy tài khoản, kiểm tra mật khẩu
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu
        if ($password == $user['password']) {  // Ở đây bạn có thể thay thế bằng password_hash để bảo mật hơn
            session_start();
            $_SESSION['user'] = $user;
            
            // Điều hướng đến trang index.php cho admin và customer
            if ($user['role'] == 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "The account does not existed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sign In</title>
</head>
<body>
    <h1>Login</h1>
    <form action="" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="regist.php">Regist</a></p>
</body>
</html>