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
  <title>Manajemen Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
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
      <form method="get" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Cari nama produk…"
               style="padding:.55rem .9rem;border:1.5px solid var(--sand);border-radius:8px;font-size:.85rem;outline:none;min-width:200px;" />
        <select name="kat" style="padding:.55rem .9rem;border:1.5px solid var(--sand);border-radius:8px;font-size:.85rem;background:#fff;outline:none;">
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
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted);">Tidak ada produk ditemukan.</td></tr>
            <?php else: ?>
            <?php foreach ($produk_list as $p): ?>
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
              <td style="text-align:center;"><?= $p['jml_variasi'] ?></td>
              <td>Rp <?= number_format($p['harga_min'] ?? 0, 0, ',', '.') ?></td>
              <td><span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>"><?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
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

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>