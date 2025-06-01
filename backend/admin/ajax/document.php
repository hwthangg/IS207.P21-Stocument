<?php
require('../config/db.php'); 
require('../inc/essentials.php'); 

//lay ten tai lieu
if (isset($_POST['get_documents'])) {
    $query = "SELECT * FROM documents 
                JOIN users ON documents.user_id = users.user_id
                JOIN subjects ON documents.subject_id = subjects.subject_id
                ";
    $res = mysqli_query($con, $query);

    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='6' class='text-center'>Không có tài liệu nào</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $uploaded_at = date('d-m-Y H:i', strtotime($row['uploaded_at']));
        $download_count = $row['download_count'] ?? 0;
    
        $del_btn = "<button onClick='remove_document({$row['doc_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";
    
        $data .= "
            <tr>
                <td>{$i}</td>
                <td>{$row['title']}</td>
                <td>{$row['name']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['username']}</td>
                <td>{$download_count}</td>
                <td>{$uploaded_at}</td>
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}

//tim kiem ten tai lieu hoac ma mon
if (isset($_POST['search_document'])) {
    $frm_data = filteration($_POST);
    $search = "%{$frm_data['title']}%";

    $query = "SELECT * FROM documents 
              JOIN users ON documents.user_id = users.user_id
            JOIN subjects ON documents.subject_id = subjects.subject_id
              WHERE documents.title LIKE ? OR subjects.name LIKE ?
              ORDER BY documents.uploaded_at DESC";

    $res = select($query, [$search, $search], 'ss');

    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='8' class='text-center'>Không tìm thấy tài liệu nào</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $uploaded_at = date('d-m-Y H:i', strtotime($row['uploaded_at']));
        $download_count = $row['download_count'] ?? 0;

        $del_btn = "<button onClick='remove_document({$row['doc_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";

        $data .= "
            <tr id='doc-row-{$row['doc_id']}'>
                <td>{$i}</td>
                <td>{$row['title']}</td>
                <td>{$row['name']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['username']}</td>
                <td>{$download_count}</td>
                <td>{$uploaded_at}</td>
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}

// Xoá tài liệu
if (isset($_POST['delete_document'])) {
    $frm_data = filteration($_POST);
    $doc_id = $frm_data['doc_id'];

    function safe_dir_name($str) {
        $str = trim($str);
        $str = str_replace(['\\', '/'], '-', $str);
        return $str;
    }

    $query = "SELECT d.file_path_subject, u.username, s.name AS subject_name, m.name AS major_name
              FROM documents d 
              JOIN users u ON d.user_id = u.user_id 
              JOIN subjects s ON d.subject_id = s.subject_id
              JOIN majors m ON m.major_id = s.major_id
              WHERE d.doc_id = ?";
    
    $res = select($query, [$doc_id], 'i');

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $file_path = $row['file_path_subject'];
        $username = $row['username'];
        $major_dir = safe_dir_name($row['major_name']);     // Giữ nguyên tên có dấu, chỉ thay \ /
        $subject_dir = safe_dir_name($row['subject_name']);
        $file_name = basename($file_path);

        // Đường dẫn file vật lý đầy đủ
        $path_by_user = "C:/xampp/htdocs/stocument/backend/webup/uploads_by_username/$username/$file_name";
        $path_by_subject = "C:/xampp/htdocs/stocument/backend/webup/uploads_by_subject/$major_dir/$subject_dir/$file_name";

        // Xóa file nếu tồn tại
        $deleted_from_user = file_exists($path_by_user) ? unlink($path_by_user) : true;
        $deleted_from_subject = file_exists($path_by_subject) ? unlink($path_by_subject) : true;

        if ($deleted_from_user && $deleted_from_subject) {
            // Xoá comment
            $delete_comments_query = "DELETE FROM comment WHERE doc_id = ?";
            $stmt = $con->prepare($delete_comments_query);
            $stmt->bind_param('i', $doc_id);
            $stmt->execute();
            $stmt->close();

            // Xoá bookmark (nếu có)
            $delete_bookmark_query = "DELETE FROM bookmarks WHERE doc_id = ?";
            $stmt = $con->prepare($delete_bookmark_query);
            $stmt->bind_param('i', $doc_id);
            $stmt->execute();
            $stmt->close();

            // Xoá bản ghi chính
            $delete_query = "DELETE FROM documents WHERE doc_id = ?";
            $stmt = $con->prepare($delete_query);
            $stmt->bind_param('i', $doc_id);
            $success = $stmt->execute();
            $stmt->close();

           
        } else {
            
        }
    } else {
         
    }
}
?>