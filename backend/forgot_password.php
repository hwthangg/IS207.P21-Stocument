<?php
session_start();
require_once 'db.php';
require_once 'mail/send_password_reset.php';

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

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email không hợp lệ.']);
    exit;
}

$stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND is_verified = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $reset_token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", time() + 1800);

    $update = $con->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE user_id = ?");
    $update->bind_param("ssi", $reset_token, $expiry, $user['user_id']);
    $update->execute();

    sendPasswordResetEmail($email, $user['full_name'], $reset_token);
    echo json_encode(['success' => true, 'message' => 'Email khôi phục đã được gửi. Vui lòng kiểm tra hộp thư.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy email hoặc tài khoản chưa xác minh.']);
}
