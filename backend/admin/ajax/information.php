<?php
require('../config/db.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin:*');           // Cho phép mọi domain
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');  // Các phương thức được phép
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Header client có thể gửi


if (isset($_GET['get_general'])) {
    $res = select("SELECT site_title, site_about FROM settings WHERE sr_no = ?", [1], "i");
    $data = mysqli_fetch_assoc($res);
    echo json_encode($data);
    exit;
}

if (isset($_POST['upd_general'])) {
    // Lấy dữ liệu từ $_POST an toàn
    $site_title = trim($_POST['site_title'] ?? '');
    $site_about = trim($_POST['site_about'] ?? '');

    if ($site_title === '' || $site_about === '') {
        echo json_encode(['success' => false, 'msg' => 'Vui lòng nhập đầy đủ thông tin.']);
        exit;
    }

    $q = "UPDATE `settings` SET `site_title` = ?, `site_about` = ? WHERE `sr_no` = ?";
    $values = [$site_title, $site_about, 1];

    $res = update($q, $values, 'ssi');

    if ($res === 1) {
        echo json_encode(['success' => true,'msg' => 'Cập nhật thành công.']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Không có thay đổi hoặc lỗi xảy ra.']);
    }

    exit;
}

if (isset($_GET['get_contacts'])) {
    $res = select("SELECT * FROM contact_details WHERE sr_no = ?", [1], "i");
    $data = mysqli_fetch_assoc($res);
    echo json_encode($data);
    exit;
}

if (isset($_POST['upd_contacts'])) {
    // Lấy dữ liệu an toàn từ $_POST
    $address = trim($_POST['address'] ?? '');
    $gmap = trim($_POST['gmap'] ?? '');
    $pn1 = trim($_POST['pn1'] ?? '');
    $pn2 = trim($_POST['pn2'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fb = trim($_POST['fb'] ?? '');
    $insta = trim($_POST['insta'] ?? '');
    $tw = trim($_POST['tw'] ?? '');
    $iframe = trim($_POST['iframe'] ?? '');

    // Có thể kiểm tra bắt buộc nếu cần, ví dụ:
    if ($address === '' || $email === '') {
        echo json_encode(['success' => false, 'msg' => 'Vui lòng nhập đầy đủ địa chỉ và email.']);
        exit;
    }
    $q = "UPDATE `contact_details` SET 
            `address` = ?, `gmap` = ?, `pn1` = ?, `pn2` = ?, 
            `email` = ?, `fb` = ?, `insta` = ?, `tw` = ?, `iframe` = ? 
          WHERE `sr_no` = ?";
    $values = [$address, $gmap, $pn1, $pn2, $email, $fb, $insta, $tw, $iframe, 1];

    $res = update($q, $values, 'sssssssssi');

    if ($res !== 1) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Không có thay đổi hoặc lỗi xảy ra.']);
    }
    exit;
}
    //them thanh vien
    if (isset($_POST['add_member'])) {
    $name = trim($_POST['member_name']);

    // Kiểm tra có file upload không
    if (!isset($_FILES['member_picture']) || $_FILES['member_picture']['error'] != 0) {
        echo json_encode(['success' => false, 'msg' => 'Vui lòng chọn ảnh hợp lệ']);
        exit;
    }

    $img = $_FILES['member_picture'];
    $img_name = basename($img['name']);
    $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array(strtolower($img_ext), $allowed_exts)) {
        echo json_encode(['success' => false, 'msg' => 'Định dạng ảnh không hợp lệ']);
        exit;
    }

    // Tạo tên ảnh duy nhất và di chuyển
    $new_img_name = 'IMG_' . time() . ".$img_ext";
    $target_path = __DIR__ . "/../images/" . $new_img_name;
    $db_path = "images/" . $new_img_name; // đường dẫn dùng trong HTML

    if (move_uploaded_file($img['tmp_name'], $target_path)) {
        $q = "INSERT INTO team_details (name, picture) VALUES (?, ?)";
        $res = insert($q, [$name, $db_path], 'ss');
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Tải ảnh lên thất bại']);
    }

    exit;
}
    //load thanh vien
  if (isset($_POST['get_members'])) {
    $res = selectAll('team_details');
    while ($row = mysqli_fetch_assoc($res)) {
        echo <<<data
        <div class="col-md-2 mb-3" style="width: 300px;">
            <div class="card bg-dark text-white">
                <img src="$row[picture]" class="card-img" >
                <div class="card-img-overlay text-end">
                    <button type="button" onclick="rem_member($row[sr_no])" class="btn btn-danger btn-sm shadow-none">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </div>
                <p class="card-text text-center px-3 py-2">$row[name]</p>
            </div>
        </div>
        data;
    }
    exit;
}

if (isset($_POST['rem_member'])) {
    $data = filteration($_POST);
    // Lấy đường dẫn ảnh của thành viên trước khi xóa
    $res = select("SELECT picture FROM team_details WHERE sr_no = ?", [$data['sr_no']], 'i');

    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $image_path = __DIR__ . "/../" . $row['picture'];

        // Xóa bản ghi trong database
        $del_res = delete("DELETE FROM team_details WHERE sr_no = ?", [$data['sr_no']], 'i');

        if ($del_res === 1) {
            // Nếu xóa DB thành công, tiến hành xóa file ảnh
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            echo 'success';
        } else {
            echo 'error';
        }

    } else {
        echo 'error';
    }

    exit;
}
