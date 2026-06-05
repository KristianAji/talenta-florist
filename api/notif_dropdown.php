<?php
// api/notif_dropdown.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['ok' => false]);
    exit;
}

// Ambil 10 notifikasi yang BELUM DIBACA
$notifs = db()->query("
    SELECT n.*, p.nama AS nama_pelanggan
    FROM notifikasi n
    LEFT JOIN pelanggan p ON p.id = n.pelanggan_id
    WHERE n.penerima = 'admin' AND n.dibaca = 0
    ORDER BY n.dibuat_pada DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Hitung yang belum dibaca
$count = db()->query("
    SELECT COUNT(*) FROM notifikasi
    WHERE penerima = 'admin' AND dibaca = 0
")->fetchColumn();

// Format data
$result = array_map(fn($n) => [
    'pesan'          => $n['pesan'],
    'link'           => $n['link'] ?? '',
    'dibaca'         => (bool) $n['dibaca'],
    'nama_pelanggan' => $n['nama_pelanggan'] ?? '',
    'waktu'          => date('d M Y, H:i', strtotime($n['dibuat_pada'])),
], $notifs);

echo json_encode([
    'ok'     => true,
    'count'  => (int) $count,
    'notifs' => $result,
]);