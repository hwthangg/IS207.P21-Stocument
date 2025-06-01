<?php
session_start();
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");


// Kiểm tra user đã đăng nhập chưa
if (isset($_SESSION['username'])) {
    // Trả về JSON với tên user
    echo json_encode([
        "name" => $_SESSION['username']
    ]);
} else {
    // Nếu chưa đăng nhập trả về lỗi hoặc tên rỗng
    http_response_code(401);
    echo json_encode([
        "error" => "Chưa đăng nhập"
    ]);
}
?>
