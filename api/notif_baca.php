<?php
// ============================================================
// api/notif_baca.php
// Tandai semua notif sebagai dibaca (dipanggil saat buka inbox)
// ============================================================

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/notifikasi.php';

header('Content-Type: application/json');

if (!empty($_SESSION['admin_id'])) {
    tandai_semua_dibaca('admin');
    echo json_encode(['ok' => true]);
} elseif (!empty($_SESSION['pelanggan_id'])) {
    tandai_semua_dibaca('pelanggan', (int)$_SESSION['pelanggan_id']);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(401);
    echo json_encode(['ok' => false, 'pesan' => 'Tidak terautentikasi']);
}