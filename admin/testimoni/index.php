<?php
// admin/testimoni/index.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'testimoni';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$list = db()->query('SELECT * FROM testimoni ORDER BY dibuat_pada DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Testimoni – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Testimoni</em></h1>
    <a href="<?= base_url('admin/testimoni/tambah.php') ?>" class="btn btn-primary btn-sm">+ Tambah</a>
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
              <th>Nama</th>
              <th>Pesan</th>
              <th>Rating</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted);">Belum ada testimoni.</td></tr>
            <?php else: foreach ($list as $t): ?>
            <tr>
              <td style="font-weight:500;white-space:nowrap;"><?= htmlspecialchars($t['nama']) ?></td>
              <td style="max-width:320px;">
                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                  <?= htmlspecialchars($t['pesan']) ?>
                </span>
              </td>
              <td><?= str_repeat('★', (int)$t['rating']) ?></td>
              <td><span class="badge <?= $t['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>"><?= $t['aktif'] ? 'Tampil' : 'Disembunyikan' ?></span></td>
              <td style="white-space:nowrap;"><?= date('d M Y', strtotime($t['dibuat_pada'])) ?></td>
              <td style="white-space:nowrap;">
                <a href="<?= base_url('admin/testimoni/edit.php?id=' . $t['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                <a href="<?= base_url('admin/testimoni/hapus.php?id=' . $t['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
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