<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

function sendPasswordResetEmail($email, $fullName, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'stocument@gmail.com';   
        $mail->Password   = 'xfuqvorjrgxvwyss';  
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('stocument@gmail.com', 'Stocument');
        $mail->addAddress($email, $fullName);

        $resetUrl = "http://localhost:5173/?resetToken=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Yêu cầu khôi phục mật khẩu';
        $mail->Body    = "
            <p>Xin chào <strong>$fullName</strong>,</p>
            <p>Bạn đã yêu cầu đặt lại mật khẩu. Nhấn vào liên kết dưới đây để tiếp tục:</p>
            <a href='$resetUrl'>Đặt lại mật khẩu</a>
            <p>Liên kết có hiệu lực trong 30 phút.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
