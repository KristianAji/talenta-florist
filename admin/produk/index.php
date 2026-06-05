<?php
// admin/produk/index.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'produk';
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$kat_filter = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
$search     = trim($_GET['q'] ?? '');
$params     = [];
$where      = ['1=1'];

if ($kat_filter) { $where[] = 'p.kategori_id = ?'; $params[] = $kat_filter; }
if ($search)     { $where[] = 'p.nama LIKE ?';     $params[] = "%$search%"; }

$sql = "
  SELECT p.id, p.nama, p.aktif, p.dibuat_pada, k.nama AS kat,
         (SELECT v.harga  FROM variasi v WHERE v.produk_id=p.id ORDER BY v.urutan LIMIT 1) AS harga_min,
         (SELECT v.gambar FROM variasi v WHERE v.produk_id=p.id ORDER BY v.urutan LIMIT 1) AS gambar,
         (SELECT COUNT(*) FROM variasi v WHERE v.produk_id=p.id) AS jml_variasi
  FROM produk p JOIN kategori k ON k.id=p.kategori_id
  WHERE " . implode(' AND ', $where) . "
  ORDER BY p.dibuat_pada DESC
";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$produk_list   = $stmt->fetchAll();
$kategori_list = db()->query('SELECT * FROM kategori ORDER BY urutan')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Halaman manajemen produk admin Talenta Florist." />
  <title>Manajemen Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/css/admin-shared.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('admin/css/produk.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Produk</em></h1>
    <a href="<?= base_url('admin/produk/tambah.php') ?>" class="btn btn-primary btn-sm">+ Tambah Produk</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- FILTER BAR -->
    <div class="panel" style="padding:1rem 1.5rem;">
      <form method="get" class="filter-form">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama produk…" />
        <select name="kat">
          <option value="0">Semua Kategori</option>
          <?php foreach ($kategori_list as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $kat_filter == $k['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm" type="submit">Cari</button>
        <?php if ($search || $kat_filter): ?>
        <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- TABEL PRODUK -->
    <div class="panel">

      <!-- ── DESKTOP: tabel biasa ── -->
      <div class="tbl-desktop">
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Variasi</th>
                <th>Harga Mulai</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($produk_list)): ?>
              <tr>
                <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted);">Tidak ada produk ditemukan.</td>
              </tr>
              <?php else: ?>
              <?php foreach ($produk_list as $p): ?>
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
                <td style="text-align:center;"><?= $p['jml_variasi'] ?></td>
                <td>Rp <?= number_format($p['harga_min'] ?? 0, 0, ',', '.') ?></td>
                <td>
                  <span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                    <?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                  </span>
                </td>
                <td style="white-space:nowrap;">
                  <a href="<?= base_url('admin/produk/edit.php?id=' . $p['id']) ?>" class="btn btn-outline btn-sm">Edit</a>
                  <a href="<?= base_url('admin/produk/hapus.php?id=' . $p['id']) ?>" class="btn btn-hapus btn-sm">Hapus</a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /tbl-desktop -->

      <!-- ── MOBILE: kartu produk ── -->
      <div class="tbl-mobile">
        <?php if (empty($produk_list)): ?>
        <p style="text-align:center;padding:2rem;color:var(--muted);font-size:.85rem;">Tidak ada produk ditemukan.</p>
        <?php else: ?>
        <div class="produk-card-list">
          <?php foreach ($produk_list as $p): ?>
          <div class="produk-card">

            <!-- Gambar -->
            <?php if ($p['gambar']): ?>
            <img class="produk-card-img" src="<?= base_url($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" />
            <?php else: ?>
            <div class="produk-card-img--empty">🌸</div>
            <?php endif; ?>

            <!-- Info + aksi -->
            <div class="produk-card-body">
              <!-- Badge status pojok kanan atas -->
              <div class="produk-card-status">
                <span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                  <?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </div>

              <!-- Nama -->
              <div class="produk-card-nama"><?= htmlspecialchars($p['nama']) ?></div>

              <!-- Meta: kategori · variasi -->
              <div class="produk-card-meta">
                <span class="produk-card-kat"><?= htmlspecialchars($p['kat']) ?></span>
                <span class="produk-card-sep"></span>
                <span class="produk-card-variasi"><?= $p['jml_variasi'] ?> variasi</span>
              </div>

              <!-- Harga -->
              <div class="produk-card-harga">Rp <?= number_format($p['harga_min'] ?? 0, 0, ',', '.') ?></div>

              <!-- Tombol aksi -->
              <div class="produk-card-actions">
                <a href="<?= base_url('admin/produk/edit.php?id=' . $p['id']) ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                <a href="<?= base_url('admin/produk/hapus.php?id=' . $p['id']) ?>" class="btn btn-hapus btn-sm">🗑 Hapus</a>
              </div>
            </div>

          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <!-- /tbl-mobile -->

    </div><!-- /panel -->

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>