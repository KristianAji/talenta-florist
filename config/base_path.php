<?php
// config/base_path.php
// ─────────────────────────────────────────────────────────────
// Deteksi BASE_PATH secara otomatis.
//
// Jika project di: localhost/talenta-florist/
//   → BASE_PATH = '/talenta-florist'
//
// Jika project di: domain.com/ (root)
//   → BASE_PATH = ''
//
// Tidak perlu ubah apapun saat pindah server / hosting.
// ─────────────────────────────────────────────────────────────

if (!defined('BASE_PATH')) {
    $docRoot    = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $scriptFile = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');

    // Cari relative path dari document root ke script yang sedang berjalan
    if (str_starts_with($scriptFile, $docRoot)) {
        $rel = substr($scriptFile, strlen($docRoot));
    } else {
        $rel = '/' . basename($scriptFile);
    }

    // Ambil komponen pertama path — itulah nama subfolder project
    $parts = explode('/', trim($rel, '/'));

    // Kalau ada subfolder dan folder itu benar-benar exist → pakai sebagai BASE
    if (count($parts) >= 2 && is_dir($docRoot . '/' . $parts[0])) {
        define('BASE_PATH', '/' . $parts[0]);
    } else {
        define('BASE_PATH', '');
    }
}

// ── HELPER FUNCTIONS ─────────────────────────────────────────

if (!function_exists('base_url')) {
    /**
     * Buat URL lengkap dari root project.
     *
     * Contoh (project di /talenta-florist/):
     *   base_url('admin/admin.css')    → /talenta-florist/admin/admin.css
     *   base_url('index.php')          → /talenta-florist/index.php
     *   base_url('image/foto.jpg')     → /talenta-florist/image/foto.jpg
     */
    function base_url(string $path = ''): string {
        return BASE_PATH . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect ke path dalam project, lalu exit.
     *
     * Contoh:
     *   redirect('login.php');              → Location: /talenta-florist/login.php
     *   redirect('admin/dashboard.php');   → Location: /talenta-florist/admin/dashboard.php
     */
    function redirect(string $path): void {
        header('Location: ' . base_url($path));
        exit;
    }
}