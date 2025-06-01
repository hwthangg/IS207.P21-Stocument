<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
require "db.php";

$res = $con->query("SELECT major_id, name FROM majors ORDER BY name");
$majors = [];
while ($r = $res->fetch_assoc()) $majors[] = $r;
echo json_encode(['success'=>true, 'majors'=>$majors]);
