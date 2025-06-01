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

$token = $data['token'] ?? '';
$password = $data['password'] ?? '';

if (!$token || !$password) {
    echo json_encode(['success' => false, 'message' => 'Thiếu token hoặc mật khẩu.']);
    exit;
}

$stmt = $con->prepare("SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Token không hợp lệ.']);
    exit;
}

$row = $result->fetch_assoc();
if (strtotime($row['reset_token_expiry']) < time()) {
    echo json_encode(['success' => false, 'message' => 'Token đã hết hạn.']);
    exit;
}

// Đặt lại mật khẩu
$hashed = password_hash($password, PASSWORD_DEFAULT);
$update = $con->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE user_id = ?");
$update->bind_param("si", $hashed, $row['user_id']);

if ($update->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật mật khẩu.']);
}
