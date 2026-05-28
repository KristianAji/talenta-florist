<?php
// admin/produk_index_view.php  —  dipakai oleh produk/index.php
// Variabel yang harus sudah tersedia:
//   $produk_list   : hasil query produk
//   $kategori_list : semua kategori
//   $flash         : flash message atau null
//   $search        : string pencarian
//   $kat_filter    : int id kategori filter
//   $active_menu   = 'produk'
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manajemen Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/admin/admin.css" />
  <style>
    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      gap: .75rem;
      align-items: center;
      padding: 1rem 1.5rem;
      background: #fff;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 12px;
      margin-bottom: 1.5rem;
    }
    .filter-bar input[type="text"],
    .filter-bar select {
      padding: .55rem .9rem;
      border: 1.5px solid var(--sand, #f0ece4);
      border-radius: 8px;
      font-size: .85rem;
      outline: none;
      transition: border-color .2s;
      background: #fff;
    }
    .filter-bar input[type="text"] { min-width: 200px; }
    .filter-bar input[type="text"]:focus,
    .filter-bar select:focus { border-color: var(--accent, #b8a99a); }

    .produk-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1.1rem;
    }
    .produk-card {
      background: #fff;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 14px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: box-shadow .2s, transform .2s;
    }
    .produk-card:hover {
      box-shadow: 0 6px 24px rgba(0,0,0,.09);
      transform: translateY(-3px);
    }
    .produk-card-img {
      width: 100%;
      aspect-ratio: 1;
      object-fit: cover;
      background: var(--sand, #f0ece4);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.8rem;
    }
    .produk-card-img img {
      width: 100%;
      aspect-ratio: 1;
      object-fit: cover;
    }
    .produk-card-body {
      padding: 1rem 1.1rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .produk-card-kat {
      font-size: .7rem;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--muted, #9a9087);
      margin-bottom: .3rem;
    }
    .produk-card-nama {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--dark, #2a2420);
      line-height: 1.3;
      margin-bottom: .5rem;
    }
    .produk-card-harga {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      font-weight: 600;
      color: var(--dark, #2a2420);
      margin-top: auto;
      margin-bottom: .75rem;
    }
    .produk-card-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: .73rem;
      color: var(--muted, #9a9087);
      margin-bottom: .75rem;
    }
    .produk-card-actions {
      display: flex;
      gap: .5rem;
      border-top: 1px solid var(--sand, #f0ece4);
      padding-top: .75rem;
    }

    .view-toggle {
      display: flex;
      gap: .4rem;
      align-items: center;
    }
    .view-toggle button {
      background: none;
      border: 1px solid var(--sand, #f0ece4);
      border-radius: 6px;
      padding: .35rem .6rem;
      cursor: pointer;
      font-size: .85rem;
      color: var(--muted, #9a9087);
      transition: all .15s;
    }
    .view-toggle button.active,
    .view-toggle button:hover {
      background: var(--sand, #f0ece4);
      color: var(--dark, #2a2420);
    }

    .summary-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    .summary-count {
      font-size: .82rem;
      color: var(--muted, #9a9087);
    }

    #view-table { display: none; }
    body.view-list #view-table  { display: block; }
    body.view-list .produk-cards { display: none; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Manajemen <em>Produk</em></h1>
    <a href="/admin/produk/tambah.php" class="btn btn-primary btn-sm">+ Tambah Produk</a>
  </div>

  <div class="admin-body">

    <?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <form method="get" style="display:contents;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="🔍  Cari nama produk…" />
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
        <a href="/admin/produk/index.php" class="btn btn-outline btn-sm">✕ Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Summary + View Toggle -->
    <div class="summary-bar">
      <span class="summary-count">
        <?= count($produk_list) ?> produk ditemukan
        <?php if ($search): ?> · pencarian "<em><?= htmlspecialchars($search) ?></em>"<?php endif; ?>
      </span>
      <div class="view-toggle">
        <button id="btn-card" class="active" title="Tampilan kartu">⊞</button>
        <button id="btn-list" title="Tampilan tabel">☰</button>
      </div>
    </div>

    <!-- CARD VIEW -->
    <div class="produk-cards" id="view-cards">
      <?php if (empty($produk_list)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted);">
        <div style="font-size:2.8rem;margin-bottom:.8rem;opacity:.5;">🌸</div>
        <p>Tidak ada produk ditemukan.</p>
      </div>
      <?php else: foreach ($produk_list as $p): ?>
      <div class="produk-card">
        <div class="produk-card-img">
          <?php if ($p['gambar']): ?>
            <img src="/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" loading="lazy" />
          <?php else: ?>
            🌸
          <?php endif; ?>
        </div>
        <div class="produk-card-body">
          <div class="produk-card-kat"><?= htmlspecialchars($p['kat']) ?></div>
          <div class="produk-card-nama"><?= htmlspecialchars($p['nama']) ?></div>
          <div class="produk-card-meta">
            <span><?= $p['jml_variasi'] ?> variasi</span>
            <span class="badge <?= $p['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
              <?= $p['aktif'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </div>
          <div class="produk-card-harga">Rp <?= number_format($p['harga_min'] ?? 0, 0, ',', '.') ?></div>
          <div class="produk-card-actions">
            <a href="/admin/produk/edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;text-align:center;">Edit</a>
            <a href="/admin/produk/hapus.php?id=<?= $p['id'] ?>" class="btn btn-hapus btn-sm"
               onclick="return confirm('Hapus produk <?= htmlspecialchars(addslashes($p['nama'])) ?>?')">Hapus</a>
          </div>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div class="panel" id="view-table">
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
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted);">Tidak ada produk.</td></tr>
            <?php else: foreach ($produk_list as $p): ?>
            <tr>
              <td>
                <?php if ($p['gambar']): ?>
                  <img class="tbl-img" src="/<?= htmlspecialchars($p['gambar']) ?>" alt="" />
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
                <a href="/admin/produk/edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                <a href="/admin/produk/hapus.php?id=<?= $p['id'] ?>" class="btn btn-hapus btn-sm"
                   onclick="return confirm('Hapus produk ini?')">Hapus</a>
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
<script>
const btnCard = document.getElementById('btn-card');
const btnList = document.getElementById('btn-list');
const vCards  = document.getElementById('view-cards');
const vTable  = document.getElementById('view-table');

btnCard.addEventListener('click', () => {
  btnCard.classList.add('active');
  btnList.classList.remove('active');
  vCards.style.display = '';
  vTable.style.display = 'none';
  localStorage.setItem('produk_view', 'card');
});
btnList.addEventListener('click', () => {
  btnList.classList.add('active');
  btnCard.classList.remove('active');
  vCards.style.display = 'none';
  vTable.style.display = 'block';
  localStorage.setItem('produk_view', 'list');
});

// Restore preference
if (localStorage.getItem('produk_view') === 'list') btnList.click();
</script>
</body>
</html>