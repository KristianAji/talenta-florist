<?php
// admin/pelanggan/hapus.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . base_url('admin/pelanggan/index.php'));
    exit;
}

// Verifikasi CSRF token
$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Akses tidak sah.'];
    header('Location: ' . base_url('admin/pelanggan/index.php'));
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id) {
    db()->prepare('DELETE FROM pelanggan WHERE id=?')->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Pelanggan berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'ID tidak valid.'];
}

header('Location: ' . base_url('admin/pelanggan/index.php'));
exit;