<?php
session_start();
require_once 'db.php';

// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origin = 'http://localhost:5173';

if ($origin === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json');

// Xử lý preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy doc_id từ JSON
$input = json_decode(file_get_contents("php://input"), true);
$doc_id = $input['doc_id'] ?? null;

if (!$doc_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu doc_id.']);
    exit;
}

// Lấy thông tin tài liệu
$stmt = $con->prepare("SELECT file_path_subject, file_path_username, user_id FROM documents WHERE doc_id = ?");
$stmt->bind_param("i", $doc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Tài liệu không tồn tại.']);
    exit;
}

$doc = $result->fetch_assoc();
if ($doc['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xoá tài liệu này.']);
    exit;
}

// Xoá comment liên quan
$con->query("DELETE FROM comment WHERE doc_id = " . intval($doc_id));

// Xoá bookmark liên quan
$con->query("DELETE FROM bookmarks WHERE doc_id = " . intval($doc_id));

// Xoá file vật lý
$filesToDelete = [$doc['file_path_subject'], $doc['file_path_username']];
foreach ($filesToDelete as $file) {
    $path = __DIR__ . '/' . $file;
    if (is_file($path)) {
        @unlink($path);
    }
}

// Xoá bản ghi chính
$deleteStmt = $con->prepare("DELETE FROM documents WHERE doc_id = ?");
$deleteStmt->bind_param("i", $doc_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đã xoá tài liệu.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xoá tài liệu.']);
}
