<?php
require('../config/db.php');


// Lấy danh sách bình luận
if (isset($_POST['get_comments'])) {
    $query = "SELECT comment.*, documents.title, users.username, users.full_name
              FROM comment
              JOIN documents ON comment.doc_id = documents.doc_id
              JOIN users ON comment.user_id = users.user_id
              ORDER BY comment.com_id DESC";

    $res = mysqli_query($con, $query);
    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='6' class='text-center'>Không có bình luận nào</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onClick='remove_comment({$row['com_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";

        $data .= "
            <tr id='comment-row-{$row['com_id']}'>
                <td>{$i}</td>
                <td>{$row['title']}</td>
                <td>{$row['username']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['context']}</td>
                <td>{$row['created_at']}</td>
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}


// Tìm kiếm bình luận
if (isset($_POST['search_comments'])) {
    $frm_data = filteration($_POST);
    $search = "%{$frm_data['query']}%";

    $query = "SELECT comment.*, documents.title, users.username, users.full_name
              FROM comment
              JOIN documents ON comment.doc_id = documents.doc_id
              JOIN users ON comment.user_id = users.user_id
              WHERE comment.context LIKE ?
                 OR documents.title LIKE ?
                 OR users.username LIKE ?
                 OR users.full_name LIKE ?
              ORDER BY comment.com_id DESC";

    $res = select($query, [$search, $search, $search, $search], 'ssss');
    $data = "";
    $i = 1;

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<tr><td colspan='6' class='text-center'>Không tìm thấy bình luận nào</td></tr>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $del_btn = "<button onClick='remove_comment({$row['com_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>";

        $data .= "
            <tr id='comment-row-{$row['com_id']}'>
                <td>{$i}</td>
                <td>{$row['title']}</td>
                <td>{$row['username']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['context']}</td>
                <td>{$row['created_at']}</td>              
                <td>{$del_btn}</td>
            </tr>";
        $i++;
    }

    echo $data;
}

// Xoá bình luận
if (isset($_POST['delete_comment'])) {
    $frm_data = filteration($_POST);
    $com_id = $frm_data['com_id'];

    if (!is_numeric($com_id)) {
        echo "invalid_id";
        exit;
    }

    $query = "DELETE FROM comment WHERE com_id = ?";
    $res = delete($query, [$com_id], 'i');

    if ($res === false) {
        echo "error";
    } elseif ($res === 0) {
        echo "not_found"; // không có gì để xoá
    } else {
        echo "1"; // thành công
    }
}
?>
