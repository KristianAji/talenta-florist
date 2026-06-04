<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $telepon  = trim($_POST['telepon']  ?? '');
    $alamat   = trim($_POST['alamat']   ?? '');

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Field bertanda bintang (*) wajib diisi!';
    } else {
        try {
            $stmtCek = db()->prepare("SELECT id FROM pelanggan WHERE username = ? OR email = ? LIMIT 1");
            $stmtCek->execute([$username, $email]);
            if ($stmtCek->fetch()) {
                $error = 'Username atau Email sudah terdaftar! Silakan gunakan yang lain.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                db()->prepare("INSERT INTO pelanggan (nama,username,email,password,telepon,alamat) VALUES (?,?,?,?,?,?)")
                   ->execute([$nama,$username,$email,$hash,$telepon,$alamat]);
                $success = 'Pendaftaran berhasil! Silakan <a href="login.php">login</a> untuk melanjutkan.';
            }
        } catch (\Exception $e) {
            $error = 'Kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar – Talenta Florist</title>
  <link rel="stylesheet" href="css/daftar.css" />
</head>
<body>
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>

  <nav>
    <a class="logo" href="index.php">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="katalog.php">Katalog</a></li>
      <li><a href="pesan.php">Cara Pesan</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <a class="btn btn-primary btn-sm" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>

  <div class="page-wrap">
    <div class="card" style="max-width:520px;">
      <p class="card-eyebrow">Bergabung dengan kami</p>
      <h1 class="card-title">Buat <em>Akun</em> Baru</h1>

      <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <form method="POST" onsubmit="return validasiForm()">
        <div class="form-group">
          <label>Nama Lengkap *</label>
          <input type="text" name="nama" placeholder="cth: Maria Tumembow" required
                 value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Username *</label>
            <input type="text" name="username" id="username" placeholder="tanpa spasi" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
          </div>
          <div class="form-group">
            <label>Password * <span style="font-size:.65rem;color:var(--muted);text-transform:none;letter-spacing:0;">(min. 6 karakter)</span></label>
            <input type="password" name="password" id="password" placeholder="••••••" required />
          </div>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" placeholder="cth: maria@email.com" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>
        <div class="form-group">
          <label>No. Telepon / WhatsApp</label>
          <input type="text" name="telepon" placeholder="cth: 08123456789"
                 value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>" />
        </div>
        <div class="form-group">
          <label>Alamat Lengkap</label>
          <textarea name="alamat" placeholder="Jl. Contoh No. 1, Tomohon..."><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-submit">Daftar Sekarang</button>
      </form>

      <div class="divider-or">atau</div>
      <div class="link-row">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
    </div>
  </div>

  <footer>
    <p>© <?= date('Y') ?> <strong>Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script>
    function validasiForm() {
      const pw = document.getElementById('password').value;
      const un = document.getElementById('username').value;
      if (pw.length < 6)        { alert('Password harus minimal 6 karakter!'); return false; }
      if (un.includes(' '))     { alert('Username tidak boleh menggunakan spasi!'); return false; }
      return true;
    }
  </script>
</body>
</html>