<?php
require('../config/db.php'); 
require('../inc/essentials.php'); 

// Lấy danh sách người dùng student
if (isset($_POST['get_users'])) {
    $query = "SELECT user_id, username, email, full_name, role, created_at, status 
              FROM users WHERE role = 'student'";
    $res = mysqli_query($con, $query);

    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='7' class='text-center'>Không có người dùng nào</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $created_at = date('d-m-Y H:i', strtotime($row['created_at']));
        $role_badge = "<span class='badge bg-primary'>Student</span>";

        // Xử lý trạng thái
        if ($row['status'] == 1) {
            $status = "<button onClick='toggle_status({$row['user_id']}, 0)' class='btn btn-dark btn-sm shadow-none'>Đang hoạt động</button>";
        } else {
            $status = "<button onClick='toggle_status({$row['user_id']}, 1)' class='btn btn-danger btn-sm shadow-none'>Bị vô hiệu</button>";
        }

        $del_btn = "<button onClick='remove_user({$row['user_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";

        $data .= "
            <tr>
                <td>{$i}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$role_badge}</td>
                <td>{$created_at}</td>
                <td>{$status}</td>
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}

// Tìm kiếm người dùng theo tên
if (isset($_POST['search_user'])) {
    $frm_data = filteration($_POST);

    $query = "SELECT user_id, username, email, full_name, role, created_at, status 
              FROM users WHERE role = 'student' AND full_name LIKE ?";
    $res = select($query, ["%{$frm_data['name']}%"], 's');

    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='7' class='text-center'>Không tìm thấy người dùng</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $created_at = date('d-m-Y H:i', strtotime($row['created_at']));
        $role_badge = "<span class='badge bg-primary'>Student</span>";

        if ($row['status'] == 1) {
            $status = "<button onClick='toggle_status({$row['user_id']}, 0)' class='btn btn-dark btn-sm shadow-none'>Đang hoạt động</button>";
        } else {
            $status = "<button onClick='toggle_status({$row['user_id']}, 1)' class='btn btn-danger btn-sm shadow-none'>Bị vô hiệu</button>";
        }

        $del_btn = "<button onClick='remove_user({$row['user_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";

        $data .= "
            <tr>
                <td>{$i}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$role_badge}</td>
                <td>{$created_at}</td>
                <td>{$status}</td>
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}

// Bật/tắt trạng thái tài khoản
if (isset($_POST['toggle_status'])) {
    $user_id = $_POST['toggle_status'];
    $value = $_POST['value'];

    $query = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $value, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['remove_user']) && isset($_POST['user_id'])) {
    function sanitize_filename($str) {
    return preg_replace('/[^A-Za-z0-9_\-]/', '_', $str);
}
    $user_id = $_POST['user_id'];

    mysqli_begin_transaction($con);

    try {
        // Lấy username
        $query_username = "SELECT username FROM users WHERE user_id = ?";
        $stmt_user = mysqli_prepare($con, $query_username);
        mysqli_stmt_bind_param($stmt_user, 'i', $user_id);
        mysqli_stmt_execute($stmt_user);
        $res_user = mysqli_stmt_get_result($stmt_user);
        $row_user = mysqli_fetch_assoc($res_user);
        $username = $row_user['username'];
        mysqli_stmt_close($stmt_user);

        // Truy vấn lấy tất cả tài liệu đã upload
        $query_documents = "SELECT d.file_path_subject, s.name AS subject_name, m.name AS major_name
                            FROM documents d
                            JOIN users u ON d.user_id = u.user_id 
                            JOIN subjects s ON d.subject_id = s.subject_id  
                            JOIN majors m ON m.major_id = s.major_id      
                            WHERE d.user_id = ?";
        $stmt_documents = mysqli_prepare($con, $query_documents);
        mysqli_stmt_bind_param($stmt_documents, 'i', $user_id);
        mysqli_stmt_execute($stmt_documents);
        $res_documents = mysqli_stmt_get_result($stmt_documents);

        while ($row_doc = mysqli_fetch_assoc($res_documents)) {
            $file_path = $row_doc['file_path_subject'];
            $subject = sanitize_filename(strtoupper(trim($row_doc['subject_name'])));
            $major = sanitize_filename(strtoupper(trim($row_doc['major_name'])));
            $file_name = basename($file_path);

            $path_by_user = "C:/xampp/htdocs/stocument/backend/webup/uploads_by_username/$username/$file_name";
            $path_by_subject = "C:/xampp/htdocs/stocument/backend/webup/uploads_by_subject/$major/$subject/$file_name";

            if (file_exists($path_by_user)) unlink($path_by_user);
            if (file_exists($path_by_subject)) unlink($path_by_subject);
        }
        mysqli_stmt_close($stmt_documents);

        // Xóa documents
        $stmt_delete_documents = mysqli_prepare($con, "DELETE FROM documents WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt_delete_documents, 'i', $user_id);
        mysqli_stmt_execute($stmt_delete_documents);
        mysqli_stmt_close($stmt_delete_documents);

        // Xóa comment
        $stmt_delete_comments = mysqli_prepare($con, "DELETE FROM comment WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt_delete_comments, 'i', $user_id);
        mysqli_stmt_execute($stmt_delete_comments);
        mysqli_stmt_close($stmt_delete_comments);

        // Xóa câu hỏi
        $stmt_delete_queries = mysqli_prepare($con, "DELETE FROM user_queries WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt_delete_queries, 'i', $user_id);
        mysqli_stmt_execute($stmt_delete_queries);
        mysqli_stmt_close($stmt_delete_queries);

        // Xóa user
        $stmt_delete_user = mysqli_prepare($con, "DELETE FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt_delete_user, 'i', $user_id);
        mysqli_stmt_execute($stmt_delete_user);
        mysqli_stmt_close($stmt_delete_user);

        // Commit nếu mọi thứ OK
        mysqli_commit($con);
        echo 1;
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo 0;
    }
}
?>
