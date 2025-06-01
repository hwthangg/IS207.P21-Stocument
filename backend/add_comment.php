<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Lấy dữ liệu từ body JSON
$data = json_decode(file_get_contents("php://input"), true);
$path = trim($data['path'] ?? '');
$context = trim($data['context'] ?? '');

if (!isset($_SESSION['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để bình luận']);
    exit;
}

if (empty($path) || empty($context)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$filepath = 'webup/uploads_by_subject/' . $path ;
// Truy vấn doc_id từ bảng documents
$doc_stmt = $con->prepare("SELECT doc_id FROM documents WHERE file_path_subject = ?");
$doc_stmt->bind_param("s", $filepath);
$doc_stmt->execute();
$doc_result = $doc_stmt->get_result();
$doc_row = $doc_result->fetch_assoc();
$doc_id = $doc_row['doc_id'];

// Thêm bình luận với doc_id 
$stmt = $con->prepare("INSERT INTO comment (user_id, doc_id, context, created_at) VALUES (?,?,?,NOW()) ");

$stmt->bind_param("iis", $_SESSION['user_id'], $doc_id, $context);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Bình luận đã được thêm.',
        'comment_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm bình luận.'
    ]);
}
