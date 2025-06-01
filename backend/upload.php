<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origin = 'http://localhost:5173';
if ($origin === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Credentials: true");
}

if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn cần đăng nhập để tải tài liệu.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Nhận dữ liệu POST
$major_id = $_POST['major_id'] ?? '';
$subject_id = $_POST['subject_id'] ?? '';
$files = $_FILES['files'] ?? null;

if (!$major_id || !$subject_id || !$files || !isset($files['name']) || count($files['name']) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng nhập đầy đủ thông tin và chọn tệp hợp lệ.'
    ]);
    exit;
}

// Lấy tên ngành và môn
$stmt1 = $con->prepare("SELECT name FROM majors WHERE major_id = ?");
$stmt1->bind_param("i", $major_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
if (!$result1->num_rows) {
    echo json_encode(['success' => false, 'message' => 'Ngành không tồn tại']);
    exit;
}
$major_name = $result1->fetch_assoc()['name'];

$stmt2 = $con->prepare("SELECT name FROM subjects WHERE subject_id = ? AND major_id = ?");
$stmt2->bind_param("ii", $subject_id, $major_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
if (!$result2->num_rows) {
    echo json_encode(['success' => false, 'message' => 'Môn học không tồn tại trong ngành này']);
    exit;
}
$subject_name = $result2->fetch_assoc()['name'];

// Chuẩn bị thư mục
$dirSubject = "webup/uploads_by_subject/" . $major_name . "/" . $subject_name;
$dirUsername = "webup/uploads_by_username/" . $username;

@mkdir($dirSubject, 0777, true);
@mkdir($dirUsername, 0777, true);

$uploaded = [];

for ($i = 0; $i < count($files['name']); $i++) {
    $originalName = basename($files['name'][$i]);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if ($ext !== 'pdf') {
        continue; // skip non-pdf files
    }

    $tmpPath = $files['tmp_name'][$i];
    if (!is_uploaded_file($tmpPath)) continue;

    // Tạo tên file duy nhất nếu trùng
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $finalName = $originalName;
    $count = 1;

    while (file_exists("$dirSubject/$finalName")) {
        $finalName = $baseName . "($count).pdf";
        $count++;
    }

    $pathSubject = "$dirSubject/$finalName";
    $pathUsername = "$dirUsername/$finalName";

    if (move_uploaded_file($tmpPath, $pathSubject)) {
        copy($pathSubject, $pathUsername);

        // Ghi vào DB
        $stmt = $con->prepare("INSERT INTO documents (title, major_id, subject_id, file_path_subject, file_path_username, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siissi", $baseName, $major_id, $subject_id, $pathSubject, $pathUsername, $user_id);
        $stmt->execute();

        $uploaded[] = $finalName;
    }
}

// Kết quả
if (!empty($uploaded)) {
    echo json_encode([
        'success' => true,
        'message' => 'Tải lên thành công',
        'files' => $uploaded
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không có tệp nào được tải lên'
    ]);
}
?>
