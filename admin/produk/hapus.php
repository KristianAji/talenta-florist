<?php
// admin/produk/hapus.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $variasi = db()->prepare('SELECT gambar FROM variasi WHERE produk_id=?');
    $variasi->execute([$id]);
    foreach ($variasi->fetchAll() as $v) {
        if ($v['gambar'] && file_exists(__DIR__ . '/../../' . $v['gambar'])) {
            unlink(__DIR__ . '/../../' . $v['gambar']);
        }
    }

    db()->prepare('DELETE FROM produk WHERE id=?')->execute([$id]);
    $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Produk berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'ID produk tidak valid.'];
}

header('Location: ' . base_url('admin/produk/index.php'));
exit;