<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/pdf");


// Lấy path file từ query string
if (!isset($_GET['path'])) {
    http_response_code(400);
    echo "Missing 'path' parameter";
    exit;
}

// Tránh dò thư mục: chỉ cho phép những thư mục con hợp lệ
$relative = $_GET['path'];
// Loại bỏ các dấu .. để chống path traversal
$clean = str_replace(['..', '\\'], '', $relative);

// Xây dựng đường dẫn vật lý đến file
$baseDir = __DIR__ . '/webup/uploads_by_subject/';
$file = realpath($baseDir . $clean);

// Kiểm tra file có tồn tại và nằm trong đúng folder
if (!$file || strpos($file, realpath($baseDir)) !== 0 || !is_file($file)) {
    http_response_code(404);
    echo "File not found";
    exit;
}
// Xác định MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
// Đọc và trả file
readfile($file);

exit;
