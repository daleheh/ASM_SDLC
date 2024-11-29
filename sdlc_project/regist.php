<?php
include('./connectdb/db.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = 'customer'; // Tài khoản mặc định là khách hàng

    // Kiểm tra xem username có trùng lặp không
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu tên đăng nhập đã tồn tại
        $message = "Username already exists! Please choose another name.";
    } else {
        // Nếu không trùng, thêm tài khoản vào cơ sở dữ liệu
        $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);

        // Không mã hóa mật khẩu để đơn giản hóa (bạn nên thêm mã hóa trong thực tế)
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $message = "Đăng ký thành công! Bạn có thể đăng nhập.";
        } else {
            $message = "An error occurred while registering. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registration</title>
</head>
<body>
    <h1>Registration</h1>
    <?php if (isset($message)): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>