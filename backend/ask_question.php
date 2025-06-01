<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// CORS cấu hình đúng khi dùng credentials:
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origin = 'http://localhost:5173';

if ($origin === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Xử lý OPTIONS request trước
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Chỉ POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để gửi câu hỏi']);
    exit;
}

// Đọc JSON từ client
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['name'], $data['email'], $data['subject'], $data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu bắt buộc.']);
    exit;
}

$name = trim($data['name']);
$email = trim($data['email']);
$subject = trim($data['subject']);
$message = trim($data['message']);
$datetime = date('Y-m-d H:i:s');

// Nếu bạn muốn gán user_id từ session:
$user_id = $_SESSION['user_id'];

// Tạo câu query: nếu user_id là NULL, bạn có thể INSERT NULL hoặc 0
$stmt = $con->prepare("
    INSERT INTO user_queries (user_id, name, email, subject, message, datentime)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "isssss",
    $user_id,
    $name,
    $email,
    $subject,
    $message,
    $datetime
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Gửi câu hỏi thành công!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi lưu vào cơ sở dữ liệu.']);
}
exit;
