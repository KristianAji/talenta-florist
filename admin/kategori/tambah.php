<?php
// admin/kategori/tambah.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
$active_menu = 'kategori';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $slug   = trim($_POST['slug']   ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);

    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if (!$slug) $errors[] = 'Slug wajib diisi.';

    if (empty($errors)) {
        try {
            db()->prepare('INSERT INTO kategori (slug,nama,urutan) VALUES (?,?,?)')->execute([$slug,$nama,$urutan]);
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Kategori berhasil ditambahkan.'];
            header('Location: /admin/kategori/index.php'); exit;
        } catch (\Exception $e) {
            $errors[] = 'Slug sudah digunakan atau terjadi kesalahan.';
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Kategori – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head><body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Tambah <em>Kategori</em></h1>
    <a href="<?= base_url('admin/kategori/index.php') ?>" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="admin-body">
    <?php if ($errors): ?><div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
    <div class="panel" style="max-width:540px;">
      <div class="panel-title">Form Kategori</div>
      <form method="post">
        <div class="form-group">
          <label>Nama Kategori *</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama']??'') ?>" required id="inp-nama" />
        </div>
        <div class="form-group">
          <label>Slug (huruf kecil, tanpa spasi) *</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($_POST['slug']??'') ?>" required id="inp-slug" placeholder="cth: rangkaian" />
        </div>
        <div class="form-group">
          <label>Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= (int)($_POST['urutan']??0) ?>" min="0" />
        </div>
        <div style="display:flex;gap:.8rem;margin-top:1rem;">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="<?= base_url('admin/kategori/index.php') ?>" class="btn btn-outline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="<?= base_url('js/admin.js') ?>"></script>
<script>
// Auto-slug dari nama
document.getElementById('inp-nama').addEventListener('input', function() {
  document.getElementById('inp-slug').value = this.value.toLowerCase()
    .replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');
});
</script>
</body></html>