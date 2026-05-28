<?php
// includes/header.php
// ─────────────────────────────────────────────────────────────
// Sertakan di awal semua halaman PUBLIK.
// Variabel yang bisa di-set sebelum include:
//   $page_title – judul tab browser (default: 'Talenta Florist')
//   $active_nav – slug menu aktif: 'tentang','katalog','pesan','kontak'
// ─────────────────────────────────────────────────────────────

if (!isset($page_title)) $page_title = 'Talenta Florist';
if (!isset($active_nav)) $active_nav = '';

// Load base_path jika belum di-load
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/base_path.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?> – Talenta Florist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <!-- Semua CSS publik terpusat di satu file — termasuk animasi petal -->
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>

  <!-- Tiga elemen petal — tampil di SEMUA halaman publik karena ada di header -->
  <div class="petal"></div>
  <div class="petal"></div>
  <div class="petal"></div>

  <nav>
    <a class="logo" href="<?= base_url('index.php') ?>">Talenta <span>Florist</span></a>
    <ul class="nav-links">
      <li><a href="<?= base_url('tentang.php') ?>" <?= $active_nav === 'tentang' ? 'class="active"' : '' ?>>Tentang</a></li>
      <li><a href="<?= base_url('katalog.php') ?>" <?= $active_nav === 'katalog' ? 'class="active"' : '' ?>>Katalog</a></li>
      <li><a href="<?= base_url('pesan.php') ?>"   <?= $active_nav === 'pesan'   ? 'class="active"' : '' ?>>Cara Pesan</a></li>
      <li><a href="<?= base_url('kontak.php') ?>"  <?= $active_nav === 'kontak'  ? 'class="active"' : '' ?>>Kontak</a></li>
      <li><a href="<?= base_url('register.php') ?>" <?= $active_nav === 'register' ? 'class="active"' : '' ?>>Daftar</a></li>
      <li><a href="<?= base_url('login.php') ?>"    <?= $active_nav === 'login'    ? 'class="active"' : '' ?>>Login</a></li>
    </ul>
    <a class="btn btn-primary" href="https://wa.me/6285233608339" target="_blank">💬 WhatsApp</a>
  </nav>
