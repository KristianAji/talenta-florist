<?php
// ============================================================
// includes/auth_pelanggan.php
// Sertakan di SEMUA halaman publik yang wajib login:
//   index.php, katalog.php, detail.php, tentang.php,
//   pesan.php, kontak.php, dll.
//
// Aturan akses:
//   ✅ Pelanggan yang sudah login     → boleh masuk
//   ✅ Admin yang sudah login         → boleh masuk (preview website)
//   ❌ Tamu (belum login sama sekali) → redirect ke login.php
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/base_path.php';
}

// Boleh akses jika sudah login sebagai pelanggan ATAU admin
$sudah_login = !empty($_SESSION['pelanggan_id']) || !empty($_SESSION['admin_id']);

if (!$sudah_login) {
    // Belum login → simpan URL tujuan lalu redirect ke login
    $ref = urlencode($_SERVER['REQUEST_URI'] ?? '');
    header('Location: ' . base_url('login.php') . '?ref=' . $ref);
    exit;
}

// ── Helper functions ─────────────────────────────────────────

if (!function_exists('is_pelanggan')) {
    /** Cek apakah yang login adalah pelanggan */
    function is_pelanggan(): bool {
        return !empty($_SESSION['pelanggan_id']);
    }
}

if (!function_exists('is_admin_browsing')) {
    /** Cek apakah admin sedang preview halaman publik */
    function is_admin_browsing(): bool {
        return !empty($_SESSION['admin_id']) && empty($_SESSION['pelanggan_id']);
    }
}