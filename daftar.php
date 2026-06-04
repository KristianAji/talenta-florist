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

    $telepon_len = strlen(preg_replace('/[^0-9]/', '', $telepon));
    if (empty($nama) || empty($username) || empty($email) || empty($password) || empty($telepon)) {
        $error = 'Field bertanda bintang (*) wajib diisi!';
    } elseif ($telepon_len < 11 || $telepon_len > 17) {
        $error = 'Nomor telepon harus antara 11–17 digit angka.';
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
  <style>
    .input-eye-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-eye-wrap input {
      width: 100%;
      padding-right: 2.8rem !important;
    }
    .eye-btn {
      position: absolute;
      right: .75rem;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--muted, #aaa);
      padding: 0;
      display: flex;
      align-items: center;
      transition: color .2s;
      line-height: 1;
    }
    .eye-btn:hover { color: var(--rose, #c06b8a); }
  </style>
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
            <label>Password *</label>
            <div class="input-eye-wrap">
              <input type="password" name="password" id="password"
                     placeholder="min. 6 karakter" required />
              <button type="button" class="eye-btn" id="eyeBtn"
                      onclick="togglePassword()" title="Lihat/sembunyikan password"
                      aria-label="Toggle password visibility">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" placeholder="cth: maria@email.com" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>
        <div class="form-group">
          <label>No. Telepon / WhatsApp *</label>
          <input type="text" name="telepon" id="telepon"
                 placeholder="cth: 08123456789 (min. 11 digit)"
                 minlength="11" maxlength="17" required
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
    <p>&copy; <?= date('Y') ?> <strong>Talenta Florist</strong> &middot; Kota Tomohon, Sulawesi Utara</p>
  </footer>

  <script>
    function validasiForm() {
      const pw  = document.getElementById('password').value;
      const un  = document.getElementById('username').value;
      const tel = document.getElementById('telepon').value.trim();
      if (pw.length < 6)           { alert('Password harus minimal 6 karakter!'); return false; }
      if (un.includes(' '))        { alert('Username tidak boleh menggunakan spasi!'); return false; }
      if (!/^[0-9+\-\s]+$/.test(tel)) { alert('Nomor telepon hanya boleh berisi angka!'); return false; }
      if (tel.length < 11)         { alert('Nomor telepon minimal 11 digit!'); return false; }
      if (tel.length > 17)         { alert('Nomor telepon maksimal 17 digit!'); return false; }
      return true;
    }

    function togglePassword() {
      const input = document.getElementById('password');
      const btn   = document.getElementById('eyeBtn');
      const isHidden = input.type === 'password';

      input.type = isHidden ? 'text' : 'password';

      // Ganti ikon: mata terbuka vs mata dengan garis coret
      btn.innerHTML = isHidden
        ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
             <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8
                      a18.45 18.45 0 0 1 5.06-5.94"/>
             <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8
                      a18.5 18.5 0 0 1-2.16 3.19"/>
             <line x1="1" y1="1" x2="23" y2="23"/>
           </svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
             <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
             <circle cx="12" cy="12" r="3"/>
           </svg>`;
    }
  </script>
</body>
</html>