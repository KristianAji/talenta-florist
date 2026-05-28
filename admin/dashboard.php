<?php
// admin/dashboard.php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$active_menu = 'dashboard';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── DATA STATISTIK ───────────────────────────────────────────
$jml_produk    = db()->query('SELECT COUNT(*) FROM produk WHERE aktif = 1')->fetchColumn();
$jml_kategori  = db()->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$jml_testimoni = db()->query('SELECT COUNT(*) FROM testimoni WHERE aktif = 1')->fetchColumn();
$jml_anggota   = db()->query('SELECT COUNT(*) FROM anggota')->fetchColumn();

// ── PRODUK TERBARU ────────────────────────────────────────────
$produk_baru = db()->query("
    SELECT p.id, p.nama, p.aktif, p.dibuat_pada, k.nama AS kat,
           (SELECT v.harga  FROM variasi v WHERE v.produk_id = p.id ORDER BY v.urutan LIMIT 1) AS harga,
           (SELECT v.gambar FROM variasi v WHERE v.produk_id = p.id ORDER BY v.urutan LIMIT 1) AS gambar
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    ORDER BY p.dibuat_pada DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Admin Talenta Florist</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <!-- admin.css menggunakan base_url agar benar di localhost maupun hosting -->
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="admin-main">

  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Dashboard <em>Admin</em></h1>
    <a href="<?= base_url('index.php') ?>" class="btn btn-outline btn-sm" target="_blank">↗ Lihat Website</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- ── STAT CARDS ── -->
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

    <!-- ── PRODUK TERBARU ── -->
    <div class="panel">
      <div class="panel-title" style="display:flex;justify-content:space-between;align-items:center;">
        Produk Terbaru
        <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
      </div>
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>Gambar</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($produk_baru)): ?>
            <tr>
              <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada produk.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($produk_baru as $p): ?>
            <tr>
              <td>
                <?php if ($p['gambar']): ?>
                <img class="tbl-img" src="<?= base_url($p['gambar']) ?>" alt="" />
                <?php else: ?>
                <div class="tbl-img" style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;background:var(--sand);">🌸</div>
                <?php endif; ?>
              </td>
              <td style="font-weight:500;"><?= htmlspecialchars($p['nama']) ?></td>
              <td><?= htmlspecialchars($p['kat']) ?></td>
              <td>
                <span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                  <?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td><?= date('d M Y', strtotime($p['dibuat_pada'])) ?></td>
              <td>
                <a href="<?= base_url('admin/produk/edit.php?id=' . $p['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── MENU CEPAT ── -->
    <div class="panel">
      <div class="panel-title">Menu Cepat</div>
      <div style="display:flex;flex-wrap:wrap;gap:.75rem;">
        <a href="<?= base_url('admin/produk/tambah.php') ?>"    class="btn btn-primary">+ Tambah Produk</a>
        <a href="<?= base_url('admin/kategori/tambah.php') ?>"  class="btn btn-outline">+ Tambah Kategori</a>
        <a href="<?= base_url('admin/testimoni/tambah.php') ?>" class="btn btn-outline">+ Tambah Testimoni</a>
        <a href="<?= base_url('admin/pengaturan.php') ?>"       class="btn btn-outline">⚙ Pengaturan</a>
      </div>
    </div>

  </div><!-- end admin-body -->
</div><!-- end admin-main -->

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>