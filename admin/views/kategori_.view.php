<?php
// admin/kategori__view.php  —  dipakai oleh kategori/index.php
// Variabel yang harus sudah tersedia sebelum include:
//   $list   : array hasil query (id, nama, slug, urutan, jml_produk)
//   $flash  : ['type'=>'success|error', 'msg'=>'...'] atau null
//   $active_menu = 'kategori'
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kategori – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/admin/admin.css" />
  <style>
    .kat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 1.1rem;
      margin-top: 1rem;
    }
    .kat-card {
      background: #fff;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 12px;
      padding: 1.25rem 1.4rem;
      display: flex;
      flex-direction: column;
      gap: .6rem;
      transition: box-shadow .2s, transform .2s;
    }
    .kat-card:hover {
      box-shadow: 0 4px 20px rgba(0,0,0,.07);
      transform: translateY(-2px);
    }
    .kat-card-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: .5rem;
    }
    .kat-nama {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.15rem;
      font-weight: 600;
      color: var(--dark, #2a2420);
      line-height: 1.2;
    }
    .kat-urutan {
      font-size: .7rem;
      background: var(--sand, #f0ece4);
      color: var(--muted, #9a9087);
      border-radius: 20px;
      padding: .15rem .65rem;
      white-space: nowrap;
      margin-top: .15rem;
    }
    .kat-slug {
      font-family: 'DM Mono', monospace;
      font-size: .75rem;
      background: var(--sand, #f0ece4);
      color: var(--muted, #9a9087);
      border-radius: 5px;
      padding: .2rem .5rem;
      display: inline-block;
      letter-spacing: .02em;
    }
    .kat-produk {
      font-size: .78rem;
      color: var(--muted, #9a9087);
    }
    .kat-produk strong {
      color: var(--dark, #2a2420);
      font-weight: 500;
    }
    .kat-actions {
      display: flex;
      gap: .5rem;
      margin-top: auto;
      padding-top: .75rem;
      border-top: 1px solid var(--sand, #f0ece4);
    }
    .kat-empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 3rem;
      color: var(--muted, #9a9087);
    }
    .kat-empty-icon {
      font-size: 2.8rem;
      margin-bottom: .8rem;
      opacity: .5;
    }
    .page-header-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }
    .page-header-bar h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      font-weight: 400;
      color: var(--muted, #9a9087);
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Kategori</em></h1>
    <a href="/admin/kategori/tambah.php" class="btn btn-primary btn-sm">+ Tambah Kategori</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="page-header-bar">
      <h2><?= count($list) ?> kategori terdaftar</h2>
    </div>

    <!-- Kartu Kategori -->
    <div class="kat-grid">
      <?php if (empty($list)): ?>
      <div class="kat-empty">
        <div class="kat-empty-icon">🗂️</div>
        <p>Belum ada kategori.<br>Mulai dengan menambahkan kategori baru.</p>
      </div>
      <?php else: foreach ($list as $k): ?>
      <div class="kat-card">
        <div class="kat-card-head">
          <div class="kat-nama"><?= htmlspecialchars($k['nama']) ?></div>
          <span class="kat-urutan">Urutan <?= $k['urutan'] ?></span>
        </div>
        <code class="kat-slug"><?= htmlspecialchars($k['slug']) ?></code>
        <div class="kat-produk">
          <strong><?= $k['jml_produk'] ?></strong> produk dalam kategori ini
        </div>
        <div class="kat-actions">
          <a href="/admin/kategori/edit.php?id=<?= $k['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;text-align:center;">Edit</a>
          <?php if ($k['jml_produk'] == 0): ?>
          <a href="/admin/kategori/hapus.php?id=<?= $k['id'] ?>" class="btn btn-hapus btn-sm"
             onclick="return confirm('Hapus kategori <?= htmlspecialchars(addslashes($k['nama'])) ?>?')">Hapus</a>
          <?php else: ?>
          <button class="btn btn-outline btn-sm" disabled title="Tidak bisa dihapus karena masih ada produk" style="cursor:not-allowed;opacity:.5;">Hapus</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Tabel fallback (tersembunyi secara default, bisa diaktifkan via toggle) -->
    <div class="panel" style="margin-top:2rem;">
      <div class="panel-title">Semua Kategori (Tabel)</div>
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
                <a href="/admin/kategori/edit.php?id=<?= $k['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <a href="/admin/kategori/hapus.php?id=<?= $k['id'] ?>" class="btn btn-hapus btn-sm"
                   onclick="return confirm('Hapus kategori ini?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="/js/admin.js"></script>
</body>
</html>