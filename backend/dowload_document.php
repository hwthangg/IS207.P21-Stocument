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

$input = json_decode(file_get_contents('php://input'), true);
$path = $input['path'] ?? '';

if (!$path) {
    echo json_encode(['success' => false, 'message' => 'Thiếu đường dẫn.']);
    exit;
}

$cleanPath = 'webup/uploads_by_subject/' . $path;

$stmt = $con->prepare("UPDATE documents SET download_count = download_count + 1 WHERE file_path_subject = ?");
$stmt->bind_param("s", $cleanPath);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật.']);
}
