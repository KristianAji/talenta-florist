<?php
/**
 * views/login.view.php
 *
 * View layer untuk halaman login admin.
 *
 * Variabel yang diharapkan:
 *   $error       – string pesan error (boleh kosong)
 *   $username_val– string nilai input username (untuk sticky form)
 */
if (!isset($error))        $error        = '';
if (!isset($username_val)) $username_val = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Admin – Talenta Florist</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    :root{--cream:#faf6f0;--sand:#ede5d8;--blush:#e8c4b4;--rose:#c97a6a;--deep:#3d2b24;--muted:#8a7060;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--deep);font-family:'DM Sans',sans-serif;font-weight:300;}
    .login-box{background:var(--cream);border-radius:20px;padding:3rem 2.5rem;width:100%;max-width:400px;box-shadow:0 24px 80px rgba(0,0,0,.3);}
    .logo{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:600;color:var(--deep);text-align:center;display:block;margin-bottom:2rem;text-decoration:none;}
    .logo span{color:var(--rose);font-style:italic;}
    label{display:block;font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:.4rem;}
    input[type=text],input[type=password]{width:100%;padding:.8rem 1rem;border:1.5px solid var(--sand);border-radius:8px;font-size:.9rem;font-family:'DM Sans',sans-serif;background:#fff;color:var(--deep);transition:border-color .25s;outline:none;margin-bottom:1.2rem;}
    input:focus{border-color:var(--rose);}
    .btn-login{width:100%;padding:.9rem;border-radius:2rem;background:var(--rose);color:#fff;font-size:.85rem;letter-spacing:.08em;text-transform:uppercase;border:none;cursor:pointer;transition:background .25s,transform .2s;margin-top:.5rem;}
    .btn-login:hover{background:var(--deep);transform:translateY(-2px);}
    .error-box{background:#fce8e8;border:1px solid #f5c6c6;color:#c0392b;border-radius:8px;padding:.8rem 1rem;font-size:.82rem;margin-bottom:1.2rem;}
    .back-link{display:block;text-align:center;margin-top:1.5rem;font-size:.78rem;color:var(--muted);text-decoration:none;}
    .back-link:hover{color:var(--rose);}
  </style>
</head>
<body>
  <div class="login-box">
    <a class="logo" href="/index.php">Talenta <span>Florist</span></a>

    <?php if ($error): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="username">Username</label>
      <input type="text" id="username" name="username"
             value="<?= htmlspecialchars($username_val) ?>"
             autocomplete="username" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             autocomplete="current-password" required />

      <button class="btn-login" type="submit">Masuk ke Dashboard</button>
    </form>

    <a class="back-link" href="/index.php">← Kembali ke website</a>
  </div>
</body>
</html>
