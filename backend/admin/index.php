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
$total_users = count_user();
$total_active = count_active();  
$total_download = get_total_downloads();
$total_document = total_documents();
$total_question = count_user_questions($con);
$total_comment = count_comments($con);
$total_inactive = count_inactive_30_days($con);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị | Chia Sẻ Tài Liệu</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="css/common.css">
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
                <i class="fas fa-chart-line menu-item active"></i> Tổng quan
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
                    <p>Xin chào, <strong><?php echo $admin_name; ?></strong></p>
                </div>
            </div>
        </div>

        <div id="dashboard" class="tab-content active">
            <div class="dashboard">
             
                <div class="card stat-card users">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Tổng số người dùng</p>
                </div>
             
                <div class="card stat-card documents">
                    <i class="fas fa-file-alt"></i>
                    <h3><?php echo $total_document; ?></h3>
                    <p>Tổng số tài liệu</p>
                </div>
                
                <div class="card stat-card downloads">
                    <i class="fas fa-download"></i>
                    <h3><?php echo $total_download; ?></h3>
                    <p>Tổng lượt tải xuống</p>
                </div>
               
                <div class="card stat-card active-users">
                    <i class="fas fa-user-check"></i>
                    <h3><?php echo $total_active; ?></h3>
                    <p>Người dùng hoạt động</p>
                </div>
            </div>

            <div class="dashboard">
                <div class="card stat-card">
                    <i class="fas fa-star"></i>
                    <h3>500</h3>
                    <p>Tổng số lượt đánh giá</p>
                </div>
                <div class="card stat-card">
                    <i class="fas fa-question"></i>
                    <h3><?php echo $total_question; ?></h3>
                    <p>Tổng số câu hỏi của người dùng</p>
                </div>
                <div class="card stat-card">
                    <i class="fas fa-comments"></i>
                    <h3><?php echo $total_comment ?></h3>
                    <p>Tổng số bình luận của người dùng</p>
                </div>
                <div class="card stat-card">
                    <i class="fas fa-user-times"></i>
                    <h3><?php echo $total_inactive ?></h3>
                    <p>Tổng người dùng không hoạt động trong 30 ngày</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
