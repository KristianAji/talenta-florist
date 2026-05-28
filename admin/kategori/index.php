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
  <title>Kategori – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
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
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada kategori.</td></tr>
            <?php else: foreach ($list as $k): ?>
            <tr>
              <td><?= $k['id'] ?></td>
              <td style="font-weight:500;"><?= htmlspecialchars($k['nama']) ?></td>
              <td><code style="font-size:.78rem;background:var(--sand);padding:.1rem .4rem;border-radius:4px;"><?= htmlspecialchars($k['slug']) ?></code></td>
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

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>