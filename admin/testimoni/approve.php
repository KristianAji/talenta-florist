<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    db()->prepare('UPDATE testimoni SET aktif=1 WHERE id=?')->execute([$id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Testimoni berhasil disetujui.'];
} else {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'ID tidak valid.'];
}
header('Location: ' . base_url('admin/testimoni/index.php'));
exit;