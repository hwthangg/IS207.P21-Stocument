<?php
// test.php

header('Access-Control-Allow-Origin: *');           // Cho phép mọi domain
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');  // Các phương thức được phép
header('Access-Control-Allow-Headers: Content-Type, Authorization'); 

// Nếu là pre-flight request thì trả về 200 OK và kết thúc
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$response = [
    'status'  => 'success',
    'message' => 'Đây là phản hồi JSON thử nghiệm',
    'data'    => [
        'id'   => 1,
        'name' => 'Test item'
    ]
];


echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);