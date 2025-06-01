<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origin = 'http://localhost:5173';

if ($origin === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Trả về nhanh nếu là preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
$path = trim($input['path'] ?? '');

if (!isset($_SESSION['user_id']) || empty($path)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$user_id = $_SESSION['user_id'];
$filepath = 'webup/uploads_by_subject/' . $path;

// Lấy doc_id
$stmt = $con->prepare("SELECT doc_id FROM documents WHERE file_path_subject = ?");
$stmt->bind_param("s", $filepath);
$stmt->execute();
$result = $stmt->get_result();
$doc = $result->fetch_assoc();
$doc_id = $doc['doc_id'] ?? null;

if (!$doc_id) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài liệu']);
    exit;
}

// Kiểm tra đã bookmark chưa
$checkStmt = $con->prepare("SELECT 1 FROM bookmarks WHERE user_id = ? AND doc_id = ?");
$checkStmt->bind_param("ii", $user_id, $doc_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Đã bookmark => xóa
    $delStmt = $con->prepare("DELETE FROM bookmarks WHERE user_id = ? AND doc_id = ?");
    $delStmt->bind_param("ii", $user_id, $doc_id);
    $delStmt->execute();
    echo json_encode(['success' => true, 'bookmarked' => false]);
} else {
    // Chưa bookmark => thêm
    $addStmt = $con->prepare("INSERT INTO bookmarks (user_id, doc_id, bookmarked_at) VALUES (?, ?, NOW())");
    $addStmt->bind_param("ii", $user_id, $doc_id);
    $addStmt->execute();
    echo json_encode(['success' => true, 'bookmarked' => true]);
}
