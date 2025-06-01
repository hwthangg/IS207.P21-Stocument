<?php
$con = new mysqli("localhost", "root", "", "web_sharing");
if ($con->connect_error) {
    die("Kết nối thất bại: " . $con->connect_error);
}