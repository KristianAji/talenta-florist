<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/base_path.php';

header('Content-Type: application/json');

if (empty($_SESSION['pelanggan_id'])) {
    echo json_encode(['ok' => false]);
    exit;
}

$pid = (int) $_SESSION['pelanggan_id'];

$notifs = db()->prepare("
    SELECT * FROM notifikasi
    WHERE penerima = 'pelanggan' AND pelanggan_id = ? AND dibaca = 0
    ORDER BY dibuat_pada DESC
    LIMIT 10
");
$notifs->execute([$pid]);
$rows = $notifs->fetchAll(PDO::FETCH_ASSOC);

$count = db()->prepare("
    SELECT COUNT(*) FROM notifikasi
    WHERE penerima = 'pelanggan' AND pelanggan_id = ? AND dibaca = 0
");
$count->execute([$pid]);
$jumlah = (int) $count->fetchColumn();

$result = array_map(fn($n) => [
    'pesan'  => $n['pesan'],
    'link'   => $n['link'] ?? '',
    'dibaca' => (bool) $n['dibaca'],
    'waktu'  => date('d M Y, H:i', strtotime($n['dibuat_pada'])),
], $rows);

echo json_encode([
    'ok'     => true,
    'count'  => $jumlah,
    'notifs' => $result,
]);