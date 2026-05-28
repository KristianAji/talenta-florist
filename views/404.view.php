<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>404 – Halaman Tidak Ditemukan</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <style>
    :root{--cream:#faf6f0;--rose:#c97a6a;--deep:#3d2b24;--muted:#8a7060;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--cream);font-family:'DM Sans',sans-serif;text-align:center;padding:2rem;}
    .num{font-family:'Cormorant Garamond',serif;font-size:clamp(6rem,20vw,12rem);font-weight:300;color:var(--rose);line-height:1;opacity:.35;}
    h1{font-family:'Cormorant Garamond',serif;font-size:clamp(1.5rem,4vw,2.2rem);font-weight:300;color:var(--deep);margin:.5rem 0 1rem;}
    p{color:var(--muted);font-size:.9rem;line-height:1.8;max-width:380px;margin-bottom:2rem;}
    .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.8rem 1.8rem;border-radius:2rem;background:var(--rose);color:#fff;text-decoration:none;font-size:.82rem;letter-spacing:.06em;text-transform:uppercase;transition:background .25s,transform .2s;}
    .btn:hover{background:var(--deep);transform:translateY(-2px);}
  </style>
</head>
<body>
  <div class="num">404</div>
  <h1>Halaman Tidak Ditemukan</h1>
  <p>Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <a class="btn" href="/index.php">← Kembali ke Beranda</a>
</body>
</html>