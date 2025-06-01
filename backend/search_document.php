<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
$baseDir = realpath(__DIR__ . '/webup/uploads_by_subject');
$current = isset($_POST['path']) ? $_POST['path'] : '';
$query   = isset($_POST['query']) ? trim($_POST['query']) : '';

$currentPath = realpath($baseDir . '/' . $current);

// Bảo vệ truy cập không hợp lệ
if (!$currentPath || strpos($currentPath, $baseDir) !== 0) {
    echo json_encode(['folders' => [], 'files' => []]);
    exit;
}

$folders = [];
$files = [];

/**
 * Duyệt đệ quy thư mục để tìm kiếm tên có chứa từ khóa
 */
function searchRecursive($path, $relative, $query, &$folders, &$files) {
    $items = scandir($path);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        $relativePath = ($relative !== '' ? $relative . '/' : '') . $item;
        $encodedPath = urlencode($relativePath);

        // Nếu có từ khóa trong tên file/thư mục
        if (stripos($item, $query) !== false) {
            if (is_dir($fullPath)) {
                $folders[] = [
                    'icon' => '📁',
                    'name' => $item,
                    'url'  => 'document.php?path=' . $encodedPath
                ];
            } elseif (is_file($fullPath)) {
                $files[] = [
                    'icon' => '📄',
                    'name' => $item,
                    'url'  => 'viewfile.php?path=' . $encodedPath
                ];
            }
        }

        // Đệ quy nếu là thư mục
        if (is_dir($fullPath)) {
            searchRecursive($fullPath, $relativePath, $query, $folders, $files);
        }
    }
}

// Gọi hàm tìm kiếm
searchRecursive($currentPath, $current, $query, $folders, $files);

// Trả kết quả JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'folders' => $folders,
    'files'   => $files
], JSON_UNESCAPED_UNICODE);
