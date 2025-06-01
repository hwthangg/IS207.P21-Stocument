<?php
require_once 'vendor/autoload.php';
require_once 'db.php';
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
$client = new Google_Client();
$client->setClientId('587881704783-n6sievmk8aeq3v03g6dijgbq50ge2kj1.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/stocument/backend/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $google_account = $oauth->userinfo->get();

        $email = $google_account->email;
        $name = $google_account->name;
        $google_id = $google_account->id;

        // Kiểm tra người dùng
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            // Tạo user mới
            $username = explode("@", $email)[0];
            $role = 'student';
            $status = 1;
            $is_verified = 1;
            $last_login = date("Y-m-d H:i:s");
            $password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO users (username, email, full_name, password, role, last_login, status, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssii", $username, $email, $name, $password, $role, $last_login, $status, $is_verified);
            $stmt->execute();
            $user_id = $stmt->insert_id;
         
        } else {
            $user = $result->fetch_assoc();
            $user_id = $user['user_id'];
            $username = $user['username'];
            $name = $user['full_name'];
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $name;
        $_SESSION['role'] = 'student';
        // Gửi dữ liệu về frontend qua URL
        $query = http_build_query([
            'googleLogin' => 'success',
            'user_id' => $user_id,
            'username' => $username,
            'full_name' => $name,
            'role' => 'student'
        ]);

        header("Location: http://localhost:5173/google-auth?$query");
        exit;
    }
}

header("Location: http://localhost:5173/google-auth?googleLogin=failed");
exit;
