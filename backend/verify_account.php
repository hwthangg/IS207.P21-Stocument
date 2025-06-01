<?php
session_start();
require_once 'db.php';

// 1. Thiết lập header CORS ngay lập tức (cho cả OPTIONS và POST)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://localhost:5173') {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    // Nếu bạn muốn cho mọi origin (chỉ test dev), dùng: header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json');

// 2. Xử lý preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight không cần body, trả về 204 No Content
    http_response_code(204);
    exit;
}

// 3. Chỉ chấp nhận POST JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không được hỗ trợ. Vui lòng gửi POST.'
    ]);
    exit;
}

// 4. Đọc raw JSON từ body
$rawBody = file_get_contents('php://input');
$jsonData = json_decode($rawBody, true);

if (!is_array($jsonData) || !isset($jsonData['token'])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu token hoặc JSON không hợp lệ.'
    ]);
    exit;
}

$token = trim($jsonData['token']);
if ($token === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Token rỗng.'
    ]);
    exit;
}

// 5. Tìm user theo verify_token
$stmt = $con->prepare("SELECT user_id, is_verified FROM users WHERE verify_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

$response = [
    'success' => false,
    'message' => ''
];

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userId = (int)$user['user_id'];
    $already = (int)$user['is_verified'];

    if ($already === 1) {
        $response['success'] = true;
        $response['message'] = "Tài khoản đã được xác minh trước đó.";
    } else {
        $update = $con->prepare("
            UPDATE users 
            SET is_verified = 1, verify_token = NULL 
            WHERE user_id = ?
        ");
        $update->bind_param("i", $userId);
        if ($update->execute()) {
            $response['success'] = true;
            $response['message'] = "Xác minh tài khoản thành công!";
        } else {
            $response['message'] = "Lỗi khi cập nhật trạng thái xác minh.";
        }
    }
} else {
    $response['message'] = "Xác minh tài khoản thành công!";
}

echo json_encode($response);
exit;
