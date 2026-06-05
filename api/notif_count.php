<?php
// ============================================================
// api/notif_count.php
// Endpoint polling — dipanggil JS setiap 30 detik
// Kembalikan JSON { count: N }
// ============================================================

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/notifikasi.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Tentukan siapa yang sedang login
if (!empty($_SESSION['admin_id'])) {
    echo json_encode(['count' => hitung_notif_belum_dibaca('admin')]);
} elseif (!empty($_SESSION['pelanggan_id'])) {
    echo json_encode(['count' => hitung_notif_belum_dibaca('pelanggan', (int)$_SESSION['pelanggan_id'])]);
} else {
    echo json_encode(['count' => 0]);
}