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
// Chỉ cho phép GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

// Kiểm tra user đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Truy vấn các mục bookmark của user, sắp theo thời gian bookmark giảm dần
$stmt = $con->prepare("
    SELECT 
      d.doc_id, 
      d.title, 
      d.file_path_subject, 
      d.download_count, 
      d.uploaded_at,
      b.bookmarked_at
    FROM documents AS d
    INNER JOIN bookmarks AS b 
      ON d.doc_id = b.doc_id 
    WHERE b.user_id = ?
    ORDER BY b.bookmarked_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookmarks = [];

while ($row = $result->fetch_assoc()) {
    $bookmarks[] = [
        'doc_id' => (int)$row['doc_id'],
        'title' => $row['title'],
        // Nếu muốn giữ nguyên path gốc, comment dòng str_replace:
        'file_path_subject' => str_replace('webup/uploads_by_subject/', '', $row['file_path_subject']),
        'bookmarked_at' => date('c', strtotime($row['bookmarked_at']))
    ];
}

echo json_encode([
    'success' => true,
    'bookmarks' => $bookmarks
]);
exit;
