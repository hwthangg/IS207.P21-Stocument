<?php  
require('inc/essentials.php');
require('config/db.php');
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Kiểm tra SESSION
if (!isset($_SESSION['user_id'])) {
    die("Lỗi: Không tìm thấy user_id trong session.");
}

$admin_id = $_SESSION['user_id'];
$admin_name = get_admin_name($admin_id);


if(isset($_GET['seen']))
{
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all')
    {
        $q = "UPDATE `user_queries` SET `seen` =?";
        $values = [1];
        if(update($q,$values,'i'))
        {
            alert('success','Đánh dấu tất cả là đã đọc');
        }
        else{
            alert('error','Thao tác thất bại');
        }
    }
    else
    {
        $q = "UPDATE `user_queries` SET `seen` =? WHERE `sr_no`=?";
        $values = [1,$frm_data['seen']];
        if(update($q,$values,'ii'))
        {
            alert('success','Đánh dấu là đã đọc');
        }
        else{
            alert('error','Thao tác thất bại');
        }
    }
}

if(isset($_GET['del']))
{
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all')
    {
        $q = "DELETE FROM `user_queries`";
        if(mysqli_query($con,$q))
        {
            alert('success','Đã xóa tất cả dữ liệu!');
        }
        else{
            alert('error','Thao tác thất bại');
        }
    }
    else
    {
        $q = "DELETE FROM `user_queries` WHERE `sr_no`=?";
        $values = [$frm_data['del']];
        if(delete($q,$values,'i'))
        {
            alert('success','Dữ liệu đã được xóa');
        }
        else{
            alert('error','Thao tác thất bại');
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị | Chia Sẻ Tài Liệu</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>
<body>  

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php">
                <img src="images/logo.png" alt="Logo" style="width: 100%; height: 100%; margin-bottom: 10px;">
            </a>
            <h2>Quản Trị</h2>
            <p>Hệ thống chia sẻ tài liệu</p>
        </div>
        <div class="sidebar-menu">
            <div onclick="window.location.href='index.php'" class="menu-item">
                <i class="fas fa-chart-line"></i> Tổng quan
            </div>
            <div onclick="window.location.href='user.php'" class="menu-item" >
                <i class="fas fa-users"></i> Quản lý người dùng
            </div>
            <div onclick="window.location.href='document.php'" class="menu-item" >
                <i class="fas fa-file-alt"></i> Quản lý tài liệu
            </div>
            <div onclick="window.location.href='comment.php'" class="menu-item">
                <i class="fas fa-comments"></i> Bình luận của người dùng
            </div>
            <div onclick="window.location.href='charts.php'" class="menu-item">
                <i class="fas fa-chart-bar"></i> Thống kê & Báo cáo
            </div>
            <div onclick="window.location.href='question.php'" class="menu-item">
                <i class="fas fa-question menu-item active"></i> Câu hỏi của người dùng
            </div>
            <div onclick="window.location.href='information.php'" class="menu-item">
                <i class="fas fa-cogs"></i> Thông tin
            </div>
            <div onclick="window.location.href='logout.php'" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </div>
        </div>
    </div>
    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
            <h4 class="fw-bold">CÂU HỎI CỦA NGƯỜI DÙNG</h4>
          
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4">
                            <a href="?seen=all" class="btn btn-success rounded-pill shadow-none btn-sm me-2">
                            <i class="bi bi-check2-all"></i> Đánh dấu tất cả là đã đọc
                            </a>
                            <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm">
                                <i class="bi bi-trash"></i> Xóa tất cả
                            </a>    
                        </div>
                        <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
                            <table class="table table-hover border" style = "min-width: 1500px; width: auto;">
                                <thead class="stickcy-top">
                                    <tr class="bg-dark text-light">
                                        <th scope="col">STT</th>
                                        <th scope="col" style="width: 200px;">Họ và tên người dùng</th>
                                        <th scope="col">Email</th>
                                        <th scope="col" width="15%">Chủ đề</th>
                                        <th scope="col" width="30%">Nội dung câu hỏi</th>
                                        <th scope="col">Ngày gửi</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thread>   
                                <tbody>
                                    <?php
                                        $q = "SELECT * FROM `user_queries` ORDER BY `sr_no` DESC";
                                        $data = mysqli_query($con,$q);
                                        $i=1;

                                        while($row = mysqli_fetch_assoc($data))
                                        {
                                            $seen='';
                                            if($row['seen']!=1)
                                            {
                                                $seen = "<a href='?seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary'>Đánh dấu là đã đọc</a> <br>";
                                            }
                                            $seen.="<a href='?del=$row[sr_no]' class='btn btn-sm rounded-pill btn-danger mt-2'>Xóa</a>";
                                            $date = date('Y-m-d', strtotime($row['datentime']));
                                            echo<<<query
                                                <tr>
                                                    <td>$i</td>
                                                    <td>$row[name]</td>
                                                    <td>$row[email]</td>
                                                    <td>$row[subject]</td>
                                                    <td>$row[message]</td>
                                                    <td>$date</td>
                                                    <td>$seen</td>
                                                </tr>
                                            query;
                                            $i++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 

            </div>
        </div>
    </div>
    <?php require('inc/scripts.php'); ?>
</body>
</html>