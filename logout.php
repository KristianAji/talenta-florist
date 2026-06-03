<?php
if (session_status() === PHP_SESSION_NONE) session_start();

unset(
    $_SESSION['pelanggan_id'],
    $_SESSION['pelanggan_nama'],
    $_SESSION['admin_id'],
    $_SESSION['admin_nama']
);

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: login.php');
exit;