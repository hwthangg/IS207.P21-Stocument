<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra tồn tại dữ liệu đầu vào
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username=? AND role='admin'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: index.php");
            exit();
        } else {
            echo "Sai tài khoản hoặc mật khẩu!";
        }
    } else {
        echo "Thiếu thông tin đăng nhập!";
    }
} else {
    echo "Phương thức gửi dữ liệu không hợp lệ!";
}
