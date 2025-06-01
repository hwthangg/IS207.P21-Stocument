<?php
session_start();


$error = '';
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Truy vấn kiểm tra tài khoản admin
    $sql = "SELECT * FROM users WHERE username=? AND role='admin' LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Kiểm tra tài khoản/mật khẩu
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống quản trị | Chia sẻ tài liệu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .login-form input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: border 0.3s;
        }
        .login-form input:focus {
            border-color: #3498db;
            outline: none;
        }
        .login-form button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-form button:hover {
            background-color: #2980b9;
        }
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fadbd8;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Hệ thống quản trị</h1>
            <p>Đăng nhập để tiếp tục</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
