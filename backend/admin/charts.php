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

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị | Chia Sẻ Tài Liệu</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

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
                <i class="fas fa-chart-bar menu-item active"></i> Thống kê & Báo cáo
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
        <a href="javascript:void(0)" 
        onclick="printCharts()" 
        class="btn btn-sm rounded-pill btn-primary" 
        style="margin-bottom: 10px; border-radius: 5px; text-decoration: none;">
        🖨️ In biểu đồ
        </a>
        <div class="chart-container" style="padding: 20px; width: 100%; box-sizing: border-box;">
         <h3>Biểu đồ thống kê số lượt downloads.</h3>
    </div>
       <div id="myfirstchart" style="height: 250px"></d>
    </div>
         <h3>Tổng số lượt truy cập hàng tuần.</h3>
         <div id="mysecondchart" style="height: 250px"></d>
         </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script>
    let downloads_data = [];
    let visits_data = [];

    window.onload = function () {
        const today = new Date();

        for (let i = 0; i < 7; i++) {
            let date = new Date(today);
            date.setDate(today.getDate() - i); 

            let formattedDate = date.toISOString().split('T')[0]; 
            downloads_data.push({
                year: formattedDate,
                value: Math.floor(Math.random() * 100) + 1 
            });

            visits_data.push({
                year: formattedDate,
                value: Math.floor(Math.random() * 200) + 1 
            });
        }
        downloads_data.reverse();
        visits_data.reverse();
        new Morris.Area({
            element: 'myfirstchart',
            data: downloads_data,
            xkey: 'year',
            ykeys: ['value'],
            labels: ['Số lượt tải'],
            lineColors: ['#0b7285'],
            parseTime: false
        });
        new Morris.Line({
            element: 'mysecondchart',
            data: visits_data,
            xkey: 'year',
            ykeys: ['value'],
            labels: ['Số lượt truy cập'],
            lineColors: ['#0b7285'],
            parseTime: false
        });
    };

    function printCharts() {
        const chartStyles = `
            <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
            <style>
                body { font-family: sans-serif; padding: 20px; }
                h2 { color: #0b7285; }
            </style>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"><\/script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"><\/script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"><\/script>
        `;
        const printWindow = window.open('', '', 'width=900,height=600');
        printWindow.document.write('<html><head><title>In biểu đồ</title>');
        printWindow.document.write(chartStyles);
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Biểu đồ thống kê số lượt downloads</h2>');
        printWindow.document.write('<div id="chart1" style="height: 250px;"></div>');
        printWindow.document.write('<h2>Tổng số lượt truy cập hàng tuần</h2>');
        printWindow.document.write('<div id="chart2" style="height: 250px;"></div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function () {
            const scriptContent = `
                new Morris.Area({
                    element: 'chart1',
                    data: ${JSON.stringify(downloads_data)},
                    xkey: 'year',
                    ykeys: ['value'],
                    labels: ['Số lượt tải'],
                    lineColors: ['#0b7285'],
                    parseTime: false
                });
                new Morris.Line({
                    element: 'chart2',
                    data: ${JSON.stringify(visits_data)},
                    xkey: 'year',
                    ykeys: ['value'],
                    labels: ['Số lượt truy cập'],
                    lineColors: ['#0b7285'],
                    parseTime: false
                });

                setTimeout(() => { window.print(); }, 1000);
            `;
            const scriptTag = printWindow.document.createElement('script');
            scriptTag.innerHTML = scriptContent;
            printWindow.document.body.appendChild(scriptTag);
        };
    }
</script>
</body>
</html>
