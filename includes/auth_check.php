<?php
// includes/auth_check.php
// ─────────────────────────────────────────────────────────────
// Sertakan di baris PERTAMA setiap file di folder admin/.
// Jika belum login → redirect ke login.php secara otomatis.
//
// Cara pakai (dari admin/dashboard.php):
//   require_once __DIR__ . '/../includes/auth_check.php';
//
// Cara pakai (dari admin/produk/index.php):
//   require_once __DIR__ . '/../../includes/auth_check.php';
// ─────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load base_path jika belum di-load sebelumnya
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/base_path.php';
}

// Cek session login
if (empty($_SESSION['admin_id'])) {
    $ref = urlencode($_SERVER['REQUEST_URI'] ?? '');
    header('Location: ' . base_url('login.php') . '?ref=' . $ref);
    exit;
}