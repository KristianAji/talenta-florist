<?php
// admin/dashboard.php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$active_menu = 'dashboard';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif = 1')->fetchColumn();
$jml_kategori  = db()->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni WHERE aktif = 1')->fetchColumn();
$jml_anggota   = db()->query('SELECT COUNT(*) FROM anggota')->fetchColumn();

$produk_baru = db()->query("
    SELECT p.id, p.nama, p.aktif, p.dibuat_pada, k.nama AS kat,
           (SELECT v.harga  FROM variasi v WHERE v.produk_id = p.id ORDER BY v.urutan LIMIT 1) AS harga,
           (SELECT v.gambar FROM variasi v WHERE v.produk_id = p.id ORDER BY v.urutan LIMIT 1) AS gambar
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    ORDER BY p.dibuat_pada DESC
    LIMIT 5
")->fetchAll();

$nama_admin = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Admin';
$sapa       = (int)date('H') < 11 ? 'Selamat pagi' : ((int)date('H') < 15 ? 'Selamat siang' : ((int)date('H') < 18 ? 'Selamat sore' : 'Selamat malam'));
$hari_id    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan_id   = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$hari_ini   = $hari_id[date('w')] . ', ' . date('d') . ' ' . $bulan_id[(int)date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Halaman dashboard admin Talenta Florist." />
  <title>Dashboard – Admin Talenta Florist</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/dashboard.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/notifikasi.css') ?>" />
  <script>window.BASE_URL = '<?= base_url('') ?>';</script>
</head>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">

  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Dashboard <em>Admin</em></h1>
    <div style="display:flex;align-items:center;gap:.75rem;">
      <div class="notif-bell-wrap">
        <button class="notif-bell-btn" id="notif-bell" title="Notifikasi">
          🔔
          <span id="notif-badge" class="notif-badge-nav" style="display:none">0</span>
        </button>
        <div class="notif-dropdown" id="notif-dropdown">
          <div class="notif-dd-header">
            <span class="notif-dd-title">Notifikasi Pesanan</span>
            <span class="notif-dd-count" id="notif-dd-count">0 baru</span>
          </div>
          <div class="notif-dd-list" id="notif-dd-list">
            <div class="notif-dd-empty"><span>🌸</span>Memuat…</div>
          </div>
          <div class="notif-dd-footer">
            <a href="<?= base_url('admin/notifikasi.php') ?>">Lihat semua notifikasi →</a>
          </div>
        </div>
      </div>
      <a class="btn btn-outline btn-sm btn-topbar-website" href="<?= base_url('index.php') ?>" title="Lihat Website">
        🌐 <span class="btn-website-text">Lihat Website</span>
      </a>
    </div>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="dashboard-greeting">
      <div class="dashboard-greeting-sub"><?= htmlspecialchars($sapa) ?></div>
      <div class="dashboard-greeting-name">Halo, <em><?= htmlspecialchars($nama_admin) ?></em> 👋</div>
      <div class="dashboard-greeting-date"><?= $hari_ini ?></div>
    </div>

    <div class="section-divider"><span>Ringkasan</span></div>
    <div class="stat-cards">
      <div class="stat-card">
        <div class="stat-card-icon">🌸</div>
        <div class="stat-card-num"><?= $jml_produk ?></div>
        <div class="stat-card-label">Produk Aktif</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">🏷</div>
        <div class="stat-card-num"><?= $jml_kategori ?></div>
        <div class="stat-card-label">Kategori</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">⭐</div>
        <div class="stat-card-num"><?= $jml_testimoni ?></div>
        <div class="stat-card-label">Testimoni Aktif</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">👥</div>
        <div class="stat-card-num"><?= $jml_anggota ?></div>
        <div class="stat-card-label">Anggota Tim</div>
      </div>
    </div>

    <div class="section-divider"><span>Aksi Cepat</span></div>
    <div class="quick-actions">
      <a href="<?= base_url('admin/produk/tambah.php') ?>" class="qa-btn">
        <span class="qa-icon">➕</span><span class="qa-label">Produk</span>
      </a>
      <a href="<?= base_url('admin/kategori/tambah.php') ?>" class="qa-btn">
        <span class="qa-icon">🏷</span><span class="qa-label">Kategori</span>
      </a>
      <a href="<?= base_url('admin/testimoni/tambah.php') ?>" class="qa-btn">
        <span class="qa-icon">⭐</span><span class="qa-label">Testimoni</span>
      </a>
      <a href="<?= base_url('admin/pengaturan.php') ?>" class="qa-btn">
        <span class="qa-icon">⚙️</span><span class="qa-label">Pengaturan</span>
      </a>
    </div>

    <div class="section-divider"><span>Produk Terbaru</span></div>
    <div class="panel">
      <div class="panel-title panel-title--flex">
        Produk Terbaru
        <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
      </div>

      <div class="tbl-wrap tbl-desktop">
        <table>
          <thead>
            <tr>
              <th>Gambar</th><th>Nama Produk</th><th>Kategori</th>
              <th>Status</th><th>Tanggal</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($produk_baru)): ?>
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada produk.</td></tr>
            <?php else: foreach ($produk_baru as $p): ?>
            <tr>
              <td>
                <?php if ($p['gambar']): ?>
                <img class="tbl-img" src="<?= base_url($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" />
                <?php else: ?>
                <div class="tbl-img tbl-img--empty">🌸</div>
                <?php endif; ?>
              </td>
              <td style="font-weight:500;"><?= htmlspecialchars($p['nama']) ?></td>
              <td><?= htmlspecialchars($p['kat']) ?></td>
              <td><span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>"><?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
              <td><?= date('d M Y', strtotime($p['dibuat_pada'])) ?></td>
              <td><a href="<?= base_url('admin/produk/edit.php?id=' . $p['id']) ?>" class="btn btn-outline btn-sm">Edit</a></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <div class="product-rank-list tbl-mobile">
        <?php if (empty($produk_baru)): ?>
        <p style="text-align:center;padding:1.5rem;color:var(--muted);font-size:.85rem;">Belum ada produk.</p>
        <?php else: foreach ($produk_baru as $i => $p): ?>
        <div class="product-rank-row">
          <div class="product-rank-num"><?= $i + 1 ?></div>
          <?php if ($p['gambar']): ?>
          <img class="product-rank-thumb" src="<?= base_url($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" />
          <?php else: ?>
          <div class="product-rank-thumb">🌸</div>
          <?php endif; ?>
          <div class="product-rank-info">
            <div class="product-rank-name"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="product-rank-sold"><?= htmlspecialchars($p['kat']) ?> · <?= date('d M Y', strtotime($p['dibuat_pada'])) ?></div>
          </div>
          <span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>" style="font-size:.58rem;"><?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?></span>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <div class="panel tbl-desktop">
      <div class="panel-title">Menu Cepat</div>
      <div style="display:flex;flex-wrap:wrap;gap:.75rem;">
        <a href="<?= base_url('admin/produk/tambah.php') ?>"    class="btn btn-primary">+ Tambah Produk</a>
        <a href="<?= base_url('admin/kategori/tambah.php') ?>"  class="btn btn-outline">+ Tambah Kategori</a>
        <a href="<?= base_url('admin/testimoni/tambah.php') ?>" class="btn btn-outline">+ Tambah Testimoni</a>
        <a href="<?= base_url('admin/pengaturan.php') ?>"       class="btn btn-outline">⚙ Pengaturan</a>
      </div>
    </div>

  </div>
</div>

<nav class="bottom-nav" aria-label="Navigasi utama">
  <a href="<?= base_url('admin/dashboard.php') ?>" class="bnav-item <?= $active_menu === 'dashboard'  ? 'active' : '' ?>">
    <span class="bnav-icon">🏠</span><span class="bnav-label">Dashboard</span>
  </a>
  <a href="<?= base_url('admin/produk/index.php') ?>" class="bnav-item <?= $active_menu === 'produk' ? 'active' : '' ?>">
    <span class="bnav-icon">🌸</span><span class="bnav-label">Produk</span>
  </a>
  <a href="<?= base_url('admin/testimoni/index.php') ?>" class="bnav-item <?= $active_menu === 'testimoni' ? 'active' : '' ?>">
    <span class="bnav-icon">⭐</span><span class="bnav-label">Testimoni</span>
  </a>
  <a href="<?= base_url('admin/notifikasi.php') ?>" class="bnav-item <?= $active_menu === 'notifikasi' ? 'active' : '' ?>" id="bnav-notif">
    <span class="bnav-icon">🔔<span id="bnav-notif-dot" class="bnav-dot" style="display:none"></span></span>
    <span class="bnav-label">Notif</span>
  </a>
  <a href="<?= base_url('index.php') ?>" class="bnav-item" target="_blank">
    <span class="bnav-icon">🌐</span><span class="bnav-label">Website</span>
  </a>
</nav>
<div class="bottom-nav-spacer"></div>

<script src="<?= base_url('js/admin.js') ?>"></script>
<script src="<?= base_url('js/notif-dropdown.js') ?>"></script>
</body>
</html>