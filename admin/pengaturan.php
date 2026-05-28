<?php
// admin/pengaturan.php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$active_menu = 'pengaturan';
$errors      = [];
$success     = '';

$admin = db()->prepare('SELECT * FROM admin WHERE id = ?');
$admin->execute([$_SESSION['admin_id']]);
$admin = $admin->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama']       ?? '');
    $pw_lama    = $_POST['pw_lama']         ?? '';
    $pw_baru    = $_POST['pw_baru']         ?? '';
    $pw_konfirm = $_POST['pw_konfirm']      ?? '';

    if (!$nama) $errors[] = 'Nama wajib diisi.';

    if ($pw_baru !== '') {
        // Mendukung plain-text (development) dan password_hash (produksi)
        $pw_lama_valid = password_verify($pw_lama, $admin['password']) || $pw_lama === $admin['password'];
        if (!$pw_lama_valid)          $errors[] = 'Password lama salah.';
        if (strlen($pw_baru) < 6)     $errors[] = 'Password baru minimal 6 karakter.';
        if ($pw_baru !== $pw_konfirm) $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        if ($pw_baru !== '') {
            $hash = password_hash($pw_baru, PASSWORD_BCRYPT);
            db()->prepare('UPDATE admin SET nama = ?, password = ? WHERE id = ?')
               ->execute([$nama, $hash, $admin['id']]);
        } else {
            db()->prepare('UPDATE admin SET nama = ? WHERE id = ?')
               ->execute([$nama, $admin['id']]);
        }
        $_SESSION['admin_nama'] = $nama;
        $success = 'Pengaturan berhasil disimpan.';

        $admin = db()->prepare('SELECT * FROM admin WHERE id = ?');
        $admin->execute([$_SESSION['admin_id']]);
        $admin = $admin->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengaturan – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Pengaturan <em>Akun</em></h1>
  </div>

  <div class="admin-body">

    <?php if ($errors): ?>
    <div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="flash-msg flash-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="panel" style="max-width:540px;">
      <div class="panel-title">Profil Admin</div>
      <form method="post">
        <div class="form-group">
          <label>Nama Tampil</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required />
        </div>
        <div class="form-group">
          <label>Username (tidak bisa diubah)</label>
          <input type="text" value="<?= htmlspecialchars($admin['username']) ?>" disabled
                 style="background:var(--sand);color:var(--muted);" />
        </div>

        <div class="panel-title" style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--sand);">
          Ganti Password
          <small style="font-weight:300;font-size:.78rem;color:var(--muted);">(kosongkan jika tidak ingin mengganti)</small>
        </div>

        <div class="form-group">
          <label>Password Lama</label>
          <input type="password" name="pw_lama" autocomplete="current-password" />
        </div>
        <div class="form-group">
          <label>Password Baru</label>
          <input type="password" name="pw_baru" autocomplete="new-password" />
        </div>
        <div class="form-group">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="pw_konfirm" autocomplete="new-password" />
        </div>

        <div style="display:flex;gap:.8rem;margin-top:1rem;">
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>