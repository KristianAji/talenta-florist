<?php
// login.php
// Letakkan di ROOT project (sejajar dengan index.php, katalog.php, dll)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/base_path.php';
require_once __DIR__ . '/config/db.php';

// Sudah login → langsung ke dashboard
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/dashboard.php');
}

$error        = '';
$username_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Mendukung password_hash() untuk produksi dan plain-text untuk development
        $valid = $admin && (
            password_verify($password, $admin['password']) ||
            $password === $admin['password']
        );

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];

            // Redirect ke halaman asal jika ref valid, atau ke dashboard
            $ref = $_GET['ref'] ?? '';
            if ($ref && str_starts_with($ref, BASE_PATH . '/admin/') && !str_contains($ref, '..')) {
                header('Location: ' . $ref);
                exit;
            }
            redirect('admin/dashboard.php');
        } else {
            $error        = 'Username atau password salah.';
            $username_val = $username;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Admin – Talenta Florist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    /* ── TOKENS ── */
    :root {
      --cream: #faf6f0; --sand: #ede5d8; --blush: #e8c4b4;
      --rose:  #c97a6a; --deep: #3d2b24; --muted: #8a7060;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── ANIMASI ── */
    @keyframes pageIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes drift {
      0%   { transform: translateY(-120px) rotate(0deg);   opacity: .10; }
      50%  {                                               opacity: .16; }
      100% { transform: translateY(110vh)  rotate(360deg); opacity: .04; }
    }

    /* ── BODY ── */
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--deep);
      font-family: 'DM Sans', sans-serif;
      font-weight: 300;
      overflow: hidden;
    }

    /* ── PETAL — juga tampil di halaman login ── */
    .petal {
      position: fixed;
      border-radius: 50% 0 50% 0;
      pointer-events: none;
      animation: drift linear infinite;
    }
    .petal:nth-child(1) { width:60px; height:60px; background:var(--rose);  top:-10%; left:10%;  animation-duration:18s;                    opacity:.08; }
    .petal:nth-child(2) { width:40px; height:40px; background:var(--blush); top:5%;   left:80%;  animation-duration:22s; animation-delay:4s; opacity:.07; }
    .petal:nth-child(3) { width:80px; height:80px; background:var(--rose);  top:30%;  left:92%;  animation-duration:26s; animation-delay:8s; opacity:.05; }
    .petal:nth-child(4) { width:50px; height:50px; background:var(--blush); top:70%;  left:3%;   animation-duration:20s; animation-delay:2s; opacity:.07; }

    /* ── LAYOUT ── */
    .login-wrap {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      padding: 1rem;
      animation: pageIn .6s cubic-bezier(.22, 1, .36, 1) both;
    }
    .login-box {
      background: var(--cream);
      border-radius: 24px;
      padding: 3rem 2.5rem 2.5rem;
      box-shadow: 0 32px 80px rgba(0, 0, 0, .4);
    }

    /* ── LOGO ── */
    .login-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--deep);
      text-align: center;
      display: block;
      margin-bottom: .35rem;
      text-decoration: none;
      transition: opacity .25s;
    }
    .login-logo:hover { opacity: .75; }
    .login-logo span  { color: var(--rose); font-style: italic; }

    .login-subtitle {
      text-align: center;
      font-size: .72rem;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 2rem;
    }

    /* ── BADGE ── */
    .admin-badge {
      display: block;
      text-align: center;
      background: rgba(201, 122, 106, .12);
      color: var(--rose);
      font-size: .65rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: .3rem .8rem;
      border-radius: 2rem;
      border: 1px solid rgba(201, 122, 106, .2);
      margin-bottom: 2rem;
    }

    /* ── ERROR ── */
    .error-box {
      background: #fce8e8;
      border: 1px solid #f5c6c6;
      color: #c0392b;
      border-radius: 10px;
      padding: .85rem 1rem;
      font-size: .83rem;
      margin-bottom: 1.4rem;
    }

    /* ── FORM ── */
    .form-group { margin-bottom: 1.2rem; }
    .form-group label {
      display: block;
      font-size: .68rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: .45rem;
    }
    .form-group input {
      width: 100%;
      padding: .85rem 1rem;
      border: 1.5px solid var(--sand);
      border-radius: 10px;
      font-size: .9rem;
      font-family: 'DM Sans', sans-serif;
      background: #fff;
      color: var(--deep);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }
    .form-group input:focus {
      border-color: var(--rose);
      box-shadow: 0 0 0 3px rgba(201, 122, 106, .15);
    }

    /* ── TOMBOL LOGIN ── */
    .btn-login {
      width: 100%;
      padding: .95rem;
      border-radius: 2rem;
      background: var(--rose);
      color: #fff;
      font-size: .85rem;
      letter-spacing: .08em;
      text-transform: uppercase;
      border: none;
      cursor: pointer;
      font-family: 'DM Sans', sans-serif;
      transition: background .25s, transform .2s, box-shadow .25s;
      margin-top: .5rem;
      box-shadow: 0 4px 20px rgba(201, 122, 106, .4);
    }
    .btn-login:hover {
      background: var(--deep);
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(61, 43, 36, .35);
    }

    /* ── FOOTER LINK ── */
    .login-footer {
      text-align: center;
      margin-top: 1.8rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--sand);
    }
    .login-footer a {
      font-size: .78rem;
      color: var(--muted);
      text-decoration: none;
      transition: color .2s;
    }
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
      <p class="login-subtitle">Panel Admin</p>

      <?php if ($error): ?>
      <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="admin-badge">🔒 Area Terbatas – Hanya Admin</div>

      <form method="post" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text"
                 id="username"
                 name="username"
                 value="<?= htmlspecialchars($username_val) ?>"
                 autocomplete="username"
                 required
                 placeholder="Masukkan username" />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password"
                 id="password"
                 name="password"
                 autocomplete="current-password"
                 required
                 placeholder="••••••••" />
        </div>
        <button class="btn-login" type="submit">Masuk ke Dashboard</button>
      </form>

      <div class="login-footer">
        <a href="<?= base_url('index.php') ?>">← Kembali ke website publik</a>
      </div>

    </div>
  </div>

</body>
</html>