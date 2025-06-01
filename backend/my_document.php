<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// CORS cho frontend React
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origin = 'http://localhost:5173';

if ($origin === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Credentials: true");
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Truy vấn tài liệu của người dùng
$stmt = $con->prepare("
    SELECT doc_id, title, file_path_subject, download_count, uploaded_at
    FROM documents
    WHERE user_id = ?
    ORDER BY uploaded_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];

while ($row = $result->fetch_assoc()) {
    $documents[] = [
        'doc_id' => $row['doc_id'],
        'title' => $row['title'],
        'file_path_subject' => str_replace('webup/uploads_by_subject/', '', $row['file_path_subject']),
        'download_count' => $row['download_count'],
        'uploaded_at' => date('c', strtotime($row['uploaded_at']))
    ];
}

echo json_encode([
    'success' => true,
    'documents' => $documents
]);
