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

// Kiểm tra login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

// Lấy dữ liệu
$data = json_decode(file_get_contents('php://input'), true);
$comment_id = (int)($data['comment_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Kiểm tra comment có tồn tại và thuộc về user
$stmt = $con->prepare("DELETE FROM comment WHERE com_id = ? AND user_id = ?");
$stmt->bind_param("ii", $comment_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Đã xóa bình luận']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể xóa bình luận']);
}
