<?php
// backend/google_login.php

session_start();
require_once __DIR__ . '/vendor/autoload.php';      // Autoload Google Client
$config = require __DIR__ . '/google-config.php';   // Load config OAuth

// Tạo Google Client
$client = new Google_Client();
$client->setClientId($config['client_id']);
$client->setClientSecret($config['client_secret']);
$client->setRedirectUri($config['redirect_uri']);

// Chỉ yêu cầu lấy email và basic profile
$client->addScope('email');
$client->addScope('profile');

// Tạo URL để user chuyển hướng sang Google Consent Screen
$authUrl = $client->createAuthUrl();

// Redirect user tới Google để xác thực
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
