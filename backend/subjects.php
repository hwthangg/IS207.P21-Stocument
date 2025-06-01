<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

require "db.php";

$major_id = intval($_GET['major_id']);

$stmt = $con->prepare("SELECT subject_id, name FROM subjects WHERE major_id = ?");
$stmt->bind_param("i", $major_id);
$stmt->execute();
$res = $stmt->get_result();

$subjects = [];
while ($row = $res->fetch_assoc()) {
  $subjects[] = $row;
}

echo json_encode(['success' => true, 'subjects' => $subjects]);
exit;
