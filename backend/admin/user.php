<?php  
require('inc/scripts.php');
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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị | Chia Sẻ Tài Liệu</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="scripts/user.js"></script>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php">
                <img src="images/logo.png" alt="Logo" style="width: 100%; height: 12%; margin-bottom: 10px;">
            </a>
            <h2>Quản Trị</h2>
            <p>Hệ thống chia sẻ tài liệu</p>
        </div>
        <div class="sidebar-menu">
            <div onclick="window.location.href='index.php'" class="menu-item">
                <i class="fas fa-chart-line"></i> Tổng quan
            </div>
            <div onclick="window.location.href='user.php'" class="menu-item" >
                <i class="fas fa-users menu-item active"></i> Quản lý người dùng
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
                <i class="fas fa-question"></i> Câu hỏi của người dùng
            </div>
            <div onclick="window.location.href='information.php'" class="menu-item">
                <i class="fas fa-cogs"></i> Thông tin
            </div>
            <div onclick="window.location.href='logout.php'" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Trang Quản Trị</h1>
            <div class="profile">
                <img src="images/IMG_1747381878.webp" alt="Admin Avatar">
                <div>
                    <p>Xin chào, <strong><?php echo $admin_name ?></strong></p>
                </div>
            </div>
        </div>
        <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
            <h4 class="fw-bold">NGƯỜI DÙNG</h4>
  
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                    
                        <div class="text-end mb-4">
                            <input type="text" oninput="search_user(this.value)" class="form-control w-25 ms-auto" id="search" placeholder="Tìm kiếm người dùng">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover border text-center" style="min-width: 1600px; width: auto;">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col" class="text-nowrap">STT</th>
                                        <th scope="col" width="20%">Họ và tên</th>
                                        <th scope="col" class="text-nowrap ">Tên người dùng</th>                                                                                           
                                        <th scope="col" class="text-nowrap">Email</th>
                                        <th scope="col" class="text-nowrap">Chức quyền</th>
                                        <th scope="col" class="text-nowrap">Ngày đăng ký</th>
                                        <th scope="col" class="text-nowrap">Khóa tài khoản</th>
                                        <th scope="col" class="text-nowrap">Xoá tài khoản</th>
                                    </tr>
                                </thead>   
                                <tbody id="users-data">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
        </div>

        
        </body>
</html>