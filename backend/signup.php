<?php
require_once 'mail/send_verification.php';

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

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $username   = trim($data["username"] ?? "");
    $email      = trim($data["email"] ?? "");
    $full_name  = trim($data["fullname"] ?? "");
    $password   = $data["password"] ?? "";
    $repassword = $data["repassword"] ?? "";

    if ($password !== $repassword) {
        $response["message"] = "Mật khẩu nhập lại không khớp!";
        echo json_encode($response);
        exit;
    }

    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $response["message"] = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $verify_token = bin2hex(random_bytes(32));

        $sql = "INSERT INTO users (username, email, password, full_name, status, is_verified, verify_token) 
                VALUES (?, ?, ?, ?, 1, 0, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $verify_token);

        if ($stmt->execute()) {
            sendVerificationEmail($email, $full_name, $verify_token);
            $response["success"] = true;
            $response["message"] = "Đăng ký thành công! Vui lòng kiểm tra email để xác nhận tài khoản.";
        } else {
            $response["message"] = "Lỗi khi đăng ký: " . $stmt->error;
        }
    }

    echo json_encode($response);
    exit;
}
