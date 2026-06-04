<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/base_path.php';

if (!empty($_SESSION['admin_id']))    redirect('admin/dashboard.php');
if (!empty($_SESSION['pelanggan_id'])) {
    $ref = $_GET['ref'] ?? '';
    if ($ref && str_starts_with(urldecode($ref), BASE_PATH.'/') && !str_contains($ref,'..')) {
        header('Location: '.urldecode($ref)); exit;
    }
    redirect('index.php');
}

$error = ''; $username_val = ''; $ref_val = $_GET['ref'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']       ?? '';
    $ref      = $_POST['ref']            ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id']; $_SESSION['admin_nama'] = $admin['nama'];
            redirect('admin/dashboard.php');
        }
        $stmt = db()->prepare('SELECT * FROM pelanggan WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $pelanggan = $stmt->fetch();
        if ($pelanggan && password_verify($password, $pelanggan['password'])) {
            session_regenerate_id(true);
            $_SESSION['pelanggan_id'] = $pelanggan['id']; $_SESSION['pelanggan_nama'] = $pelanggan['nama'];
            if ($ref && str_starts_with(urldecode($ref), BASE_PATH.'/') && !str_contains($ref,'..')) {
                header('Location: '.urldecode($ref)); exit;
            }
            redirect('index.php');
        }
        $error = 'Username atau password salah.'; $username_val = $username; $ref_val = $ref;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk – Talenta Florist</title>
  <link rel="stylesheet" href="<?= base_url('css/login.css') ?>" />
</head>
<body class="auth-body">
  <div class="petal"></div><div class="petal"></div><div class="petal"></div>
  <div class="petal"></div><div class="petal"></div>

  <div class="auth-wrap">
    <div class="auth-card">
      <a class="auth-logo" href="#">Talenta <em>Florist</em></a>
      <p class="auth-sub">Selamat datang kembali</p>

      <div class="info-badge">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Login sebagai pelanggan atau admin — sistem mengarahkan otomatis.
      </div>

      <?php if ($error): ?>
      <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="ref" value="<?= htmlspecialchars($ref_val) ?>" />
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 value="<?= htmlspecialchars($username_val) ?>"
                 autocomplete="username" placeholder="Masukkan username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 autocomplete="current-password" placeholder="••••••••" required />
        </div>
        <button type="submit" class="btn-masuk">Masuk</button>
      </form>

      <div class="divider-or">atau</div>
      <div class="link-row">Belum punya akun? <a href="<?= base_url('daftar.php') ?>">Daftar di sini</a></div>
    </div>
  </div>
</body>
</html>