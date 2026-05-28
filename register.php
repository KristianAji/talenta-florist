<?php
// register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/config/db.php';

// Jika sudah login, tendang ke beranda
if (!empty($_SESSION['admin_id']) || !empty($_SESSION['pelanggan_id'])) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telepon  = trim($_POST['telepon'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Field bertanda bintang (*) wajib diisi.';
    } else {
        try {
            $stmtCek = db()->prepare("SELECT id FROM pelanggan WHERE username = ? OR email = ? LIMIT 1");
            $stmtCek->execute([$username, $email]);
            
            if ($stmtCek->fetch()) {
                $error = 'Username atau Email sudah terdaftar! Gunakan yang lain.';
            } else {
                $password_terenkripsi = password_hash($password, PASSWORD_BCRYPT);
                $sqlInsert = "INSERT INTO pelanggan (nama, username, email, password, telepon, alamat) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsert = db()->prepare($sqlInsert);
                $stmtInsert->execute([$nama, $username, $email, $password_terenkripsi, $telepon, $alamat]);

                $success = 'Pendaftaran berhasil! Silakan kembali ke halaman Login untuk masuk.';
            }
        } catch (PDOException $e) {
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
  <title>Daftar Akun – Talenta Florist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    /* ── TOKENS Sama Persis dengan Login ── */
    :root {
      --cream: #faf6f0; --sand: #ede5d8; --blush: #e8c4b4;
      --rose:  #c97a6a; --deep: #3d2b24; --muted: #8a7060;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    @keyframes pageIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes drift {
      0%   { transform: translateY(-120px) rotate(0deg);   opacity: .10; }
      50%  {                                               opacity: .16; }
      100% { transform: translateY(110vh)  rotate(360deg); opacity: .04; }
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--deep); /* Background gelap sama dengan login */
      font-family: 'DM Sans', sans-serif;
      font-weight: 300;
      overflow-x: hidden;
      padding: 2rem 0;
    }

    .petal { position: fixed; border-radius: 50% 0 50% 0; pointer-events: none; animation: drift linear infinite; }
    .petal:nth-child(1) { width:60px; height:60px; background:var(--rose);  top:-10%; left:10%;  animation-duration:18s; opacity:.08; }
    .petal:nth-child(2) { width:40px; height:40px; background:var(--blush); top:5%;   left:80%;  animation-duration:22s; animation-delay:4s; opacity:.07; }
    .petal:nth-child(3) { width:80px; height:80px; background:var(--rose);  top:30%;  left:92%;  animation-duration:26s; animation-delay:8s; opacity:.05; }
    .petal:nth-child(4) { width:50px; height:50px; background:var(--blush); top:70%;  left:3%;   animation-duration:20s; animation-delay:2s; opacity:.07; }

    .login-wrap {
      position: relative; z-index: 10; width: 100%; max-width: 450px; padding: 1rem;
      animation: pageIn .6s cubic-bezier(.22, 1, .36, 1) both;
    }
    .login-box {
      background: var(--cream); border-radius: 24px; padding: 2.5rem 2.5rem;
      box-shadow: 0 32px 80px rgba(0, 0, 0, .4);
    }

    .login-logo {
      font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 600; color: var(--deep);
      text-align: center; display: block; margin-bottom: .35rem; text-decoration: none; transition: opacity .25s;
    }
    .login-logo:hover { opacity: .75; }
    .login-logo span  { color: var(--rose); font-style: italic; }

    .login-subtitle {
      text-align: center; font-size: .72rem; letter-spacing: .14em; text-transform: uppercase;
      color: var(--muted); margin-bottom: 2rem;
    }

    .alert-box {
      border-radius: 10px; padding: .85rem 1rem; font-size: .83rem; margin-bottom: 1.4rem; text-align: center;
    }
    .alert-error { background: #fce8e8; border: 1px solid #f5c6c6; color: #c0392b; }
    .alert-success { background: #e8fce8; border: 1px solid #c6f5c6; color: #2bc039; }

    .form-group { margin-bottom: 1.2rem; }
    .form-group label {
      display: block; font-size: .68rem; letter-spacing: .12em; text-transform: uppercase;
      color: var(--muted); margin-bottom: .45rem;
    }
    .form-group input, .form-group textarea {
      width: 100%; padding: .85rem 1rem; border: 1.5px solid var(--sand); border-radius: 10px;
      font-size: .9rem; font-family: 'DM Sans', sans-serif; background: #fff; color: var(--deep);
      transition: border-color .2s, box-shadow .2s; outline: none;
    }
    .form-group input:focus, .form-group textarea:focus {
      border-color: var(--rose); box-shadow: 0 0 0 3px rgba(201, 122, 106, .15);
    }

    .btn-login {
      width: 100%; padding: .95rem; border-radius: 2rem; background: var(--rose); color: #fff;
      font-size: .85rem; letter-spacing: .08em; text-transform: uppercase; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; transition: background .25s, transform .2s, box-shadow .25s;
      margin-top: .5rem; box-shadow: 0 4px 20px rgba(201, 122, 106, .4);
    }
    .btn-login:hover { background: var(--deep); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(61, 43, 36, .35); }

    .login-footer { text-align: center; margin-top: 1.8rem; padding-top: 1.5rem; border-top: 1px solid var(--sand); }
    .login-footer a { font-size: .78rem; color: var(--muted); text-decoration: none; transition: color .2s; display: block; margin-bottom: .5rem;}
    .login-footer a:hover { color: var(--rose); }
  </style>
</head>
<body>

  <div class="petal"></div>
  <div class="petal"></div>
  <div class="petal"></div>
  <div class="petal"></div>

  <div class="login-wrap">
    <div class="login-box">

      <a class="login-logo" href="<?= base_url('index.php') ?>">Talenta <span>Florist</span></a>
      <p class="login-subtitle">Pendaftaran Pelanggan</p>

      <?php if ($error): ?>
        <div class="alert-box alert-error">⚠ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert-box alert-success">✔ <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" action="" id="formRegister" onsubmit="return validasiForm()">
        <div class="form-group">
          <label for="nama">Nama Lengkap *</label>
          <input type="text" id="nama" name="nama" required placeholder="Masukkan nama Anda" />
        </div>
        <div class="form-group">
          <label for="username">Username *</label>
          <input type="text" id="username" name="username" required placeholder="Buat username" />
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" required placeholder="contoh@email.com" />
        </div>
        <div class="form-group">
          <label for="password">Password * (Min. 6 Karakter)</label>
          <input type="password" id="password" name="password" required placeholder="••••••••" />
        </div>
        <div class="form-group">
          <label for="telepon">No. Telepon / WA</label>
          <input type="text" id="telepon" name="telepon" placeholder="08123456789" />
        </div>
        <div class="form-group">
          <label for="alamat">Alamat Lengkap</label>
          <textarea id="alamat" name="alamat" rows="2" placeholder="Detail alamat pengiriman"></textarea>
        </div>
        
        <button class="btn-login" type="submit">Daftar Sekarang</button>
      </form>

      <div class="login-footer">
        <a href="<?= base_url('login.php') ?>">Sudah punya akun? <b>Masuk di sini</b></a>
        <a href="<?= base_url('index.php') ?>">← Kembali ke halaman utama</a>
      </div>

    </div>
  </div>

  <script>
    function validasiForm() {
        var password = document.getElementById("password").value;
        var username = document.getElementById("username").value;

        if (password.length < 6) {
            alert("Pendaftaran Gagal:\nPassword harus memiliki minimal 6 karakter!");
            return false; 
        }
        
        if (username.indexOf(' ') >= 0) {
            alert("Pendaftaran Gagal:\nUsername tidak boleh menggunakan spasi!");
            return false;
        }
        return true;
    }
  </script>

</body>
</html>
