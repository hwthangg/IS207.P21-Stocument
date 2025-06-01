<?php
// CORS headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

$baseDir = realpath(__DIR__ . '/webup/uploads_by_subject');
$current = isset($_GET['path']) ? $_GET['path'] : '';
$currentPath = realpath($baseDir . '/' . $current);

// Bảo vệ path truy cập ngoài thư mục gốc
if ($currentPath === false || strpos($currentPath, $baseDir) !== 0) {
    echo json_encode(['success' => false, 'message' => 'Truy cập không hợp lệ']);
    exit;
}

// Breadcrumb
$breadcrumbs = [];
if ($current !== '') {
    $parts = explode('/', $current);
    $path = '';
    foreach ($parts as $part) {
        $path .= ($path === '' ? '' : '/') . $part;
        $breadcrumbs[] = [
            'label' => $part,
            'path' => $path
        ];
    }
}

// Danh sách folder + file
$items = scandir($currentPath);
$folders = [];
$files = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $fullPath = $currentPath . '/' . $item;
    if (is_dir($fullPath)) {
        $folders[] = [
            'name' => $item,
            'type' => 'folder',
            'path' => $current ? $current . '/' . $item : $item
        ];
    } else {
        $files[] = [
            'name' => $item,
            'type' => 'file',
            'path' => $current ? $current . '/' . $item : $item
        ];
    }
}

// ✅ Quay lại
$parent = dirname($current);
$backPath = $parent === '.' ? '' : $parent;

echo json_encode([
    'success' => true,
    'path' => $current,
    'back' => $backPath,
    'breadcrumbs' => $breadcrumbs,
    'documents' => array_merge($folders, $files)
]);
exit;
