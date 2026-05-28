<?php
// admin/kategori/edit.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
$active_menu = 'kategori';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$kat = db()->prepare('SELECT * FROM kategori WHERE id=?');
$kat->execute([$id]);
$kat = $kat->fetch();
if (!$kat) { header('Location: ' . base_url('admin/kategori/index.php')); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $slug   = trim($_POST['slug']   ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);
    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if (!$slug) $errors[] = 'Slug wajib diisi.';
    if (empty($errors)) {
        try {
            db()->prepare('UPDATE kategori SET slug=?,nama=?,urutan=? WHERE id=?')->execute([$slug,$nama,$urutan,$id]);
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Kategori berhasil diperbarui.'];
            header('Location: /admin/kategori/index.php'); exit;
        } catch (\Exception $e) { $errors[] = 'Slug sudah digunakan.'; }
    }
}
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Kategori – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head><body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Edit <em>Kategori</em></h1>
    <a href="<?= base_url('admin/kategori/index.php') ?>" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="admin-body">
    <?php if ($errors): ?><div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
    <div class="panel" style="max-width:540px;">
      <div class="panel-title">Edit Kategori</div>
      <form method="post">
        <div class="form-group"><label>Nama Kategori *</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($kat['nama']) ?>" required /></div>
        <div class="form-group"><label>Slug *</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($kat['slug']) ?>" required /></div>
        <div class="form-group"><label>Urutan</label>
          <input type="number" name="urutan" value="<?= $kat['urutan'] ?>" min="0" /></div>
        <div style="display:flex;gap:.8rem;margin-top:1rem;">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="<?= base_url('admin/kategori/index.php') ?>" class="btn btn-outline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="<?= base_url('js/admin.js') ?>"></script>
</body></html>