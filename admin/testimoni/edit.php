<?php
// admin/testimoni/edit.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';
$active_menu = 'testimoni';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$t  = db()->prepare('SELECT * FROM testimoni WHERE id=?');
$t->execute([$id]); $t = $t->fetch();
if (!$t) { header('Location: ' . base_url('admin/testimoni/index.php')); exit; }
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']  ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');
    $rating = min(5, max(1, (int)($_POST['rating'] ?? 5)));
    $aktif  = isset($_POST['aktif']) ? 1 : 0;
    if (!$nama)  $errors[] = 'Nama wajib diisi.';
    if (!$pesan) $errors[] = 'Pesan wajib diisi.';
    if (empty($errors)) {
        db()->prepare('UPDATE testimoni SET nama=?,pesan=?,rating=?,aktif=? WHERE id=?')->execute([$nama,$pesan,$rating,$aktif,$id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Testimoni berhasil diperbarui.'];
        header('Location: ' . base_url('admin/testimoni/index.php')); exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Testimoni – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head><body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Edit <em>Testimoni</em></h1>
    <a href="<?= base_url('admin/testimoni/index.php') ?>" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="admin-body">
    <?php if ($errors): ?><div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
    <div class="panel" style="max-width:600px;">
      <div class="panel-title">Edit Testimoni</div>
      <form method="post">
        <div class="form-group"><label>Nama *</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($t['nama']) ?>" required /></div>
        <div class="form-group"><label>Pesan *</label>
          <textarea name="pesan" required><?= htmlspecialchars($t['pesan']) ?></textarea></div>
        <div class="form-row">
          <div class="form-group"><label>Rating</label>
            <select name="rating">
              <?php for ($r=5;$r>=1;$r--): ?>
              <option value="<?= $r ?>" <?= $t['rating']==$r?'selected':'' ?>><?= str_repeat('★',$r) ?> (<?= $r ?>)</option>
              <?php endfor; ?>
            </select></div>
          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem;">
            <label class="form-check">
              <input type="checkbox" name="aktif" value="1" <?= $t['aktif']?'checked':'' ?> /> Tampilkan
            </label></div>
        </div>
        <div style="display:flex;gap:.8rem;margin-top:1rem;">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a href="<?= base_url('admin/testimoni/index.php') ?>" class="btn btn-outline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="<?= base_url('js/admin.js') ?>"></script>
</body></html>