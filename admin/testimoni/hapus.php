<?php
// admin/testimoni/hapus.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    db()->prepare('DELETE FROM testimoni WHERE id=?')->execute([$id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Testimoni berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'ID tidak valid.'];
}
header('Location: ' . base_url('admin/testimoni/index.php')); exit;