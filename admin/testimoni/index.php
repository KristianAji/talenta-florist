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
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/testimoni.css') ?>" />
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

      <!-- ── DESKTOP: tabel ── -->
      <div class="tbl-desktop">
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
                <td><span class="badge <?= $t['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>"><?= $t['aktif'] ? 'Tampil' : 'Menunggu' ?></span></td>
                <td style="white-space:nowrap;"><?= date('d M Y', strtotime($t['dibuat_pada'])) ?></td>
                <td style="white-space:nowrap;">
                  <?php if (!$t['aktif']): ?>
                  <a href="<?= base_url('admin/testimoni/approve.php?id=' . $t['id']) ?>"
                     class="btn btn-outline btn-sm"
                     style="color:#3b6d11;border-color:#3b6d11;">✓ Approve</a>
                  <?php endif; ?>
                  <a href="<?= base_url('admin/testimoni/edit.php?id=' . $t['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                  <a href="<?= base_url('admin/testimoni/hapus.php?id=' . $t['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /tbl-desktop -->

      <!-- ── MOBILE: kartu testimoni ── -->
      <div class="tbl-mobile">
        <?php if (empty($list)): ?>
        <p style="text-align:center;padding:2rem;color:var(--muted);font-size:.85rem;">Belum ada testimoni.</p>
        <?php else: ?>
        <div class="testi-card-list">
          <?php foreach ($list as $t):
            $status_class = $t['aktif'] ? 'status-tampil' : 'status-menunggu';
            $inisial = mb_strtoupper(mb_substr($t['nama'], 0, 1));
          ?>
          <div class="testi-card <?= $status_class ?>">

            <!-- Baris atas: avatar + nama + tanggal + rating + badge -->
            <div class="testi-card-top">
              <div class="testi-card-avatar"><?= $inisial ?></div>
              <div class="testi-card-info">
                <div class="testi-card-nama"><?= htmlspecialchars($t['nama']) ?></div>
                <div class="testi-card-meta">
                  <span class="testi-card-date"><?= date('d M Y', strtotime($t['dibuat_pada'])) ?></span>
                  <span class="testi-card-sep"></span>
                  <span class="testi-card-stars"><?= str_repeat('★', (int)$t['rating']) ?></span>
                </div>
              </div>
              <span class="badge <?= $t['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                <?= $t['aktif'] ? 'Tampil' : 'Menunggu' ?>
              </span>
            </div>

            <!-- Pesan -->
            <div class="testi-card-pesan"><?= htmlspecialchars($t['pesan']) ?></div>

            <!-- Aksi -->
            <div class="testi-card-actions">
              <?php if (!$t['aktif']): ?>
              <a href="<?= base_url('admin/testimoni/approve.php?id=' . $t['id']) ?>"
                 class="btn btn-outline btn-sm"
                 style="color:#3b6d11;border-color:#3b6d11;">✓ Approve</a>
              <?php endif; ?>
              <a href="<?= base_url('admin/testimoni/edit.php?id=' . $t['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="<?= base_url('admin/testimoni/hapus.php?id=' . $t['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
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