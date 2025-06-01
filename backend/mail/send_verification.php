<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

function sendVerificationEmail($email, $fullName, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';          // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'stocument@gmail.com';   
        $mail->Password   = 'xfuqvorjrgxvwyss';       
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';
        // Recipients
        $mail->setFrom('stocument@gmail.com', 'Stocument');
        $mail->addAddress($email, $fullName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Xác minh tài khoản của bạn';
        $verifyUrl = "http://localhost:5173/?verifyToken=" . urlencode($token);
        $mail->Body    = "
            <h2>Chào $fullName,</h2>
            <p>Cảm ơn bạn đã đăng ký. Vui lòng nhấn vào nút bên dưới để xác minh tài khoản:</p>
            <a href='$verifyUrl' style='padding:10px 20px; background:#28a745; color:#fff; text-decoration:none;'>Xác minh tài khoản</a>
            <p>Nếu bạn không đăng ký, vui lòng bỏ qua email này.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Không gửi được email. Lỗi: {$mail->ErrorInfo}";
    }
}
