<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');
// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://localhost:5173') {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Xử lý OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $input = trim($data["username_or_email"] ?? '');
    $password = $data["password"] ?? '';

    // Kiểm tra bỏ trống
    if (empty($input)) {
        $response['message'] = "Vui lòng nhập email hoặc tên đăng nhập.";
        echo json_encode($response);
        exit;
    }

    if (empty($password)) {
        $response['message'] = "Vui lòng nhập mật khẩu.";
        echo json_encode($response);
        exit;
    }

    // Tiếp tục kiểm tra tài khoản
    $sql = "SELECT * FROM users WHERE role = 'student' AND (username = ? OR email = ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user["status"] == '0') {
            $response['message'] = "Tài khoản đã bị khóa!";
        } elseif ($user["is_verified"] == '0') {
            $response['message'] = "Tài khoản chưa xác thực!";
        } elseif (password_verify($password, $user["password"])) {
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_id"] = $user["user_id"];

            $update_sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("i", $user['user_id']);
            $update_stmt->execute();

            $response['success'] = true;
            $response['message'] = "Đăng nhập thành công!";
            $response['user'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'full_name' => $user['full_name']
            ];
        } else {
            $response['message'] = "Sai mật khẩu!";
        }
    } else {
        $response['message'] = "Tài khoản không tồn tại!";
    }

    echo json_encode($response);
    exit;
}
