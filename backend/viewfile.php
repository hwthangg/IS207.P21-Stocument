<?php
// CORS cho React
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
header('Content-Length: ' . filesize($fullPath));
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
$baseDir = realpath(__DIR__ . '/../webup/uploads_by_subject');

if (!isset($_GET['path'])) {
    http_response_code(400);
    exit('Thiếu đường dẫn file');
}

$requestedPath = $_GET['path'];
$fullPath = realpath($baseDir . '/' . $requestedPath);

// Kiểm tra bảo mật
if ($fullPath === false || strpos($fullPath, $baseDir) !== 0 || !is_file($fullPath)) {
    http_response_code(404);
    exit('Tệp không tồn tại hoặc không hợp lệ');
}

// Lấy đúng kiểu file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Bắt buộc phải là PDF
if ($mime !== 'application/pdf') {
    http_response_code(415);
    exit('Không phải file PDF');
}

readfile($fullPath);
exit;
