<?php
require_once 'db.php';
header('Content-Type: application/json');

// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://localhost:5173') {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Xử lý OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Lấy path từ query string
$path = $_GET['path'] ?? '';

if (empty($path)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu đường dẫn tài liệu']);
    exit;
}

$filepath = 'webup/uploads_by_subject/' . $path ;

// Lấy doc_id từ path
$sql = "
    SELECT c.*, u.full_name, u.username
    FROM comment c
    JOIN documents d ON c.doc_id = d.doc_id
    JOIN users u ON c.user_id = u.user_id
    WHERE d.file_path_subject = ?
    ORDER BY c.created_at DESC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $filepath);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'id' => $row['com_id'],
        'text' => $row['context'],
        'user' => $row['full_name'] ?: $row['username'],
        'user_id' => $row['user_id'], 
        'created_at' => $row['created_at'],
    ];
}

echo json_encode(['success' => true, 'comments' => $comments]);
