<?php
// admin/kategori/hapus.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    // Cek apakah masih ada produk
    $jml = db()->prepare('SELECT COUNT(*) FROM produk WHERE kategori_id=?');
    $jml->execute([$id]);
    if ($jml->fetchColumn() > 0) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Kategori tidak bisa dihapus karena masih memiliki produk.'];
    } else {
        db()->prepare('DELETE FROM kategori WHERE id=?')->execute([$id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Kategori berhasil dihapus.'];
    }
} else {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'ID tidak valid.'];
}
header('Location: ' . base_url('admin/kategori/index.php'));
exit;