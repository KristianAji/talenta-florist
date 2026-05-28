<?php
// logout.php

// 1. Mulai session untuk bisa mengakses data yang akan dihapus
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kosongkan semua data session (menghapus status login admin maupun pelanggan)
$_SESSION = [];

// 3. Hapus cookie session di browser pengguna (untuk keamanan ekstra)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 4. Hancurkan session sepenuhnya dari server XAMPP
session_destroy();

// 5. Lemparkan pengunjung kembali ke halaman login
header('Location: login.php');
exit;
?>
