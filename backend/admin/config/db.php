<?php
$con = new mysqli("localhost", "root", "", "web_sharing");
if ($con->connect_error) {
    die("Kết nối thất bại: " . $con->connect_error);
}

function filteration($data) {
    foreach ($data as $key => $value) {
         $value = trim($value);
         $value = stripslashes($value);
         $value = strip_tags($value);
         $value = htmlspecialchars($value);
         $data[$key] = $value;
    }
    return $data;
 }

 function selectAll($table){
     $con = $GLOBALS['con'];
     $res = mysqli_query($con, "SELECT * FROM $table");
     return $res;
 }

 function select($sql, $values, $datatypes) {
     $con = $GLOBALS['con'];
     if ($stmt = mysqli_prepare($con, $sql)) {
         mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
         if (mysqli_stmt_execute($stmt))
         {
             $result = mysqli_stmt_get_result($stmt);
             mysqli_stmt_close($stmt);
             return $result;
         }
         else {
             die("Truy vấn không thể thực thi - Select");
         }
         mysqli_stmt_close($stmt);
         
     }
     else{
         die("Không thể chuẩn bị truy vấn - Select");
     }
 }

 function update($sql, $values, $datatypes) {
     $con = $GLOBALS['con'];
     if ($stmt = mysqli_prepare($con, $sql)) {
         mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
         if (mysqli_stmt_execute($stmt))
         {
             $result = mysqli_stmt_affected_rows($stmt);
             mysqli_stmt_close($stmt);
             return $result;
         }
         else {
             die("Truy vấn không thể thực thi - Update");
         }
         mysqli_stmt_close($stmt);
         
     }
     else{
         die("Không thể chuẩn bị truy vấn - Update");
     }
 }

 function insert($sql, $values, $datatypes) {
     $con = $GLOBALS['con'];
     if ($stmt = mysqli_prepare($con, $sql)) {
         mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
         if (mysqli_stmt_execute($stmt))
         {
             $result = mysqli_stmt_affected_rows($stmt);
             mysqli_stmt_close($stmt);
             return $result;
         }
         else {
             die("Truy vấn không thể thực thi - Insert");
         }
         mysqli_stmt_close($stmt);
         
     }
     else{
         die("Không thể chuẩn bị truy vấn - Insert");
     }
 }

 function delete($sql, $values, $datatypes) {
     $con = $GLOBALS['con'];
     if ($stmt = mysqli_prepare($con, $sql)) {
         mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
         if (mysqli_stmt_execute($stmt))
         {
             $result = mysqli_stmt_affected_rows($stmt);
             mysqli_stmt_close($stmt);
             return $result;
         }
         else {
             die("Truy vấn không thể thực thi - Delete");
         }
         mysqli_stmt_close($stmt);
         
     }
     else{
         die("Không thể chuẩn bị truy vấn - Delete");
     }
 }

 function count_user() {
    global $con;
    $query = "SELECT COUNT(*) AS total FROM users WHERE role = 'student'";
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['total'];
    }

    return 0;
}

function count_active() {
    global $con;
    $query = "SELECT COUNT(*) AS total FROM users WHERE role = 'student' and status ='1'";
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['total'];
    }
    return 0;
}
function get_admin_name($user_id) {
    global $con;
    if (!$user_id) return "Người dùng";
    $query = "SELECT full_name FROM users WHERE user_id = ? AND role = 'admin'";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {   
            return $row['full_name'];
        }
    }
    return "Người dùng";
}

function get_total_downloads() {
    $query = "SELECT SUM(download_count) AS total_downloads FROM documents";
    $res = mysqli_query($GLOBALS['con'], $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        return $row['total_downloads'] ?? 0;
    }

    return 0; 
}

function total_documents(){

    $query = "SELECT COUNT(*) AS total_documents FROM documents";
    $res = mysqli_query($GLOBALS['con'], $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        return $row['total_documents'] ?? 0;
    }

    return 0;
}

function count_user_questions($con) {
    $sql = "SELECT COUNT(*) as total FROM user_queries";
    $result = $con->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'];
    }

    return 0;
}
function count_comments($con) {
    $sql = "SELECT COUNT(*) as total FROM comment";
    $result = $con->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'];
    }
    return 0;
}
function count_inactive_30_days() {
    global $con;
    $query = "SELECT COUNT(*) AS total FROM users 
              WHERE role = 'student' 
              AND (last_login IS NULL OR last_login < NOW() - INTERVAL 30 DAY)";
              
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['total'];
    }
    return 0;
}

?>