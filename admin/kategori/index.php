<?php
// admin/kategori/index.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'kategori';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$list = db()->query("
  SELECT k.*, COUNT(p.id) AS jml_produk
  FROM kategori k LEFT JOIN produk p ON p.kategori_id = k.id
  GROUP BY k.id ORDER BY k.urutan
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Halaman manajemen kategori admin Talenta Florist." />
  <title>Kategori – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/kategori.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Kategori</em></h1>
    <a href="<?= base_url('admin/kategori/tambah.php') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="panel">

      <!-- ── DESKTOP: tabel ── -->
      <div class="tbl-desktop">
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Slug</th>
                <th>Urutan</th>
                <th>Jml Produk</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($list)): ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada kategori.</td>
              </tr>
              <?php else: foreach ($list as $k): ?>
              <tr>
                <td><?= $k['id'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($k['nama']) ?></td>
                <td><code class="slug-code"><?= htmlspecialchars($k['slug']) ?></code></td>
                <td><?= $k['urutan'] ?></td>
                <td><?= $k['jml_produk'] ?></td>
                <td style="white-space:nowrap;">
                  <a href="<?= base_url('admin/kategori/edit.php?id=' . $k['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                  <a href="<?= base_url('admin/kategori/hapus.php?id=' . $k['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /tbl-desktop -->

      <!-- ── MOBILE: kartu ── -->
      <div class="tbl-mobile">
        <?php if (empty($list)): ?>
        <p style="text-align:center;padding:2rem;color:var(--muted);font-size:.85rem;">Belum ada kategori.</p>
        <?php else: ?>
        <div class="kat-card-list">
          <?php foreach ($list as $k): ?>
          <div class="kat-card">

            <!-- Nomor urutan -->
            <div class="kat-card-num"><?= $k['urutan'] ?></div>

            <!-- Info -->
            <div class="kat-card-body">
              <div class="kat-card-nama"><?= htmlspecialchars($k['nama']) ?></div>
              <div class="kat-card-meta">
                <span class="kat-card-slug"><?= htmlspecialchars($k['slug']) ?></span>
                <span class="kat-card-sep"></span>
                <span class="kat-card-count"><?= $k['jml_produk'] ?> produk</span>
              </div>
            </div>

            <!-- Aksi -->
            <div class="kat-card-actions">
              <a href="<?= base_url('admin/kategori/edit.php?id=' . $k['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="<?= base_url('admin/kategori/hapus.php?id=' . $k['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
            </div>

          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <!-- /tbl-mobile -->

    </div>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>