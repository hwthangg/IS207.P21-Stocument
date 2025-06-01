<?php  
require('config/db.php');
require('inc/scripts.php');


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="scripts/information.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                <i class="fas fa-cogs menu-item active"></i> Thông tin
            </div>
            <div onclick="window.location.href='logout.php'" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </div>
        </div>
    </div>
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
        <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
            <h4 class="fw-bold">THÔNG TIN</h4>
          <!-- General settings section -->

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Cài đặt chung</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#general-s">
                                <i class="bi bi-pencil-square"></i> Chỉnh sửa
                            </button>
                        </div>
                        <h6 class="card-subtitle mb-1 fw-bold">Tiêu đề trang web</h6>
                        <p class="card-text" id="site_title"></p>
                        <h6 class="card-subtitle mb-1 fw-bold">Giới thiệu</h6>
                        <p class="card-text" id="site_about"></p>
                    </div>
                </div>

                <!-- General settings modal -->

                <div class="modal fade" id="general-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="general_s_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cài đặt chung</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tiêu đề trang web</label>
                                        <input type="text" name="site_title" id="site_title_inp" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Giới thiệu</label>
                                        <textarea name="site_about" id="site_about_inp" class="form-control shadow-none" rows="6" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="site_title.value = general_data.site_title, site_about.value =  general_data.site_about" class="btn btn-danger me-2 shadow-none" data-bs-dismiss="modal">HỦY</button>
                                    <button type="submit" class="btn btn-success text-white shadow-none">LƯU</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <!-- Contact details section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Cài đặt Liên hệ</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#contacts-s">
                                <i class="bi bi-pencil-square"></i> Chỉnh sửa
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Địa chỉ</h6>
                                    <p class="card-text" id="address"></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Liên kết Google Map</h6>
                                    <p class="card-text" id="gmap"></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Các số điện thoại</h6>
                                    <p class="card-text mb-1" ><i class="bi bi-telephone-fill"></i><span id="pn1"></span></p>
                                    <p class="card-text" ><i class="bi bi-telephone-fill"></i><span id="pn2"></span></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Email</h6>
                                    <p class="card-text" id="email"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Liên kết mạng xã hội</h6>
                                    <p class="card-text mb-1" ><i class="bi bi-facebook me-1"></i><span id="fb"></span></p>
                                    <p class="card-text mb-1" ><i class="bi bi-instagram me-1"></i><span id="insta"></span></p>
                                    <p class="card-text" ><i class="bi bi-twitter me-1"></i><span id="tw"></span></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Vị trí</h6>
                                    <iframe id="iframe" class="border p-2 w-100" loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>

                <!-- Contacts details modal -->
                <div class="modal fade" id="contacts-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <form id="contacts_s_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cài đặt Liên hệ</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid p-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Địa chỉ</label>
                                                    <input type="text" name="address" id="address_inp" class="form-control shadow-none" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Liên kết Google Map</label>
                                                    <textarea name="gmap" id="gmap_inp" class="form-control shadow-none" rows="6" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Các số điện thoại (với mã quốc gia)</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i> </span>
                                                        <input type="number" name="pn1" id="pn1_inp" class="form-control shadow-none" required>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                                        <input type="number" name="pn2" id="pn2_inp" class="form-control shadow-none" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <textarea name="email" id="email_inp" class="form-control shadow-none" rows="6" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Liên kết mạng xã hội</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i class="bi bi-facebook"></i> </span>
                                                        <input type="text" name="fb" id="fb_inp" class="form-control shadow-none" required>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                                        <input type="text" name="insta" id="insta_inp" class="form-control shadow-none" required>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                                                        <input type="text" name="tw" id="tw_inp" class="form-control shadow-none" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                        <label class="form-label fw-bold">Vị trí</label>
                                                        <input type="text" name="iframe" id="iframe_inp" class="form-control shadow-none" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="contacts_inp(contacts_data)" class="btn btn-danger shadow-none" data-bs-dismiss="modal">HỦY</button>

                                    <button type="submit" class="btn btn-success text-white shadow-none">LƯU</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Management Team section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Đội ngũ quản lý</h5>
                            <button type="button" class="btn btn-success shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#team-s">
                                <i class="bi bi-plus-square me-2"></i> Thêm
                            </button>
                        </div>
                        <div class="row" id="team-data">
                            
                        </div> 
                    </div>
                </div> 
                <!-- Management Team modal -->

                <div class="modal fade" id="team-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="team_s_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Thêm thành viên</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tên</label>
                                        <input type="text" name="member_name" id="member_name_inp" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ảnh đại diện</label>
                                        <input type="file" name="member_picture" id="member_picture_inp" accept=".jpg, .png, /webp, .jpeg" class="form-control shadow-none" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="document.getElementById('member_name_inp').value=''; document.getElementById('member_picture_inp').value='';" class="btn btn-danger me-2 shadow-none" data-bs-dismiss="modal">HỦY</button>
                                    <button type="submit" class="btn btn-success text-white shadow-none">LƯU</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


            </body>
            </html>

