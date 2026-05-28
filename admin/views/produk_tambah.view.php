<?php
// admin/produk_tambah_view.php  —  dipakai oleh produk/tambah.php
// Variabel yang harus tersedia:
//   $errors        : array pesan error
//   $kategori_list : array semua kategori
//   $active_menu   = 'produk'
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/admin/admin.css" />
  <style>
    .form-layout {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 1.5rem;
      align-items: start;
    }
    @media (max-width: 900px) {
      .form-layout { grid-template-columns: 1fr; }
    }

    .variasi-item {
      background: var(--sand, #f0ece4);
      border-radius: 12px;
      padding: 1.1rem 1.2rem;
      position: relative;
      margin-bottom: .85rem;
    }
    .variasi-item:last-child { margin-bottom: 0; }
    .variasi-num {
      position: absolute;
      top: .9rem; left: 1.1rem;
      font-family: 'Cormorant Garamond', serif;
      font-size: .8rem;
      font-weight: 600;
      color: var(--muted, #9a9087);
      letter-spacing: .06em;
    }
    .variasi-item .form-row { margin-top: .25rem; }
    .remove-variasi {
      position: absolute;
      top: .85rem; right: .9rem;
      cursor: pointer;
      color: var(--muted, #9a9087);
      font-size: .9rem;
      line-height: 1;
      padding: .25rem .4rem;
      border-radius: 50%;
      transition: background .15s, color .15s;
    }
    .remove-variasi:hover {
      background: #e0c8c8;
      color: #a33;
    }

    .img-preview {
      display: none;
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      margin-top: .5rem;
      border: 1px solid var(--sand, #f0ece4);
    }

    .step-indicator {
      display: flex;
      gap: .5rem;
      margin-bottom: 1.5rem;
    }
    .step {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-size: .78rem;
      color: var(--muted, #9a9087);
    }
    .step-num {
      width: 22px; height: 22px;
      border-radius: 50%;
      background: var(--sand, #f0ece4);
      color: var(--muted, #9a9087);
      display: flex; align-items: center; justify-content: center;
      font-size: .72rem;
      font-weight: 500;
    }
    .step.active .step-num {
      background: var(--dark, #2a2420);
      color: #fff;
    }
    .step.active { color: var(--dark, #2a2420); font-weight: 500; }
    .step-sep { color: var(--sand, #d0cac2); font-size: .8rem; }

    .panel-tip {
      font-size: .78rem;
      color: var(--muted, #9a9087);
      margin-bottom: 1rem;
      padding: .75rem 1rem;
      background: #fdf8f3;
      border-left: 3px solid var(--sand, #d0cac2);
      border-radius: 0 6px 6px 0;
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Tambah <em>Produk</em></h1>
    <a href="/admin/produk/index.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>

  <div class="admin-body">

    <?php if ($errors): ?>
    <div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <!-- Step Indicator -->
    <div class="step-indicator">
      <div class="step active"><span class="step-num">1</span> Informasi Produk</div>
      <span class="step-sep">›</span>
      <div class="step active"><span class="step-num">2</span> Variasi & Harga</div>
      <span class="step-sep">›</span>
      <div class="step active"><span class="step-num">3</span> Simpan</div>
    </div>

    <form method="post" enctype="multipart/form-data">
      <div class="form-layout">

        <!-- Kolom Kiri: Info Produk -->
        <div>
          <div class="panel">
            <div class="panel-title">Informasi Produk</div>

            <div class="form-row">
              <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                       required placeholder="cth: Rangkaian Mawar Premium" />
              </div>
              <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori_id" required>
                  <option value="">– Pilih Kategori –</option>
                  <?php foreach ($kategori_list as $k): ?>
                  <option value="<?= $k['id'] ?>" <?= (($_POST['kategori_id'] ?? 0) == $k['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="deskripsi" rows="4"
                        placeholder="Deskripsikan produk secara singkat…"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Badge <span style="color:var(--muted);font-weight:300;">(opsional)</span></label>
                <input type="text" name="badge" value="<?= htmlspecialchars($_POST['badge'] ?? '') ?>"
                       placeholder="cth: Baru, Promo, Custom" />
              </div>
              <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem;">
                <label class="form-check">
                  <input type="checkbox" name="aktif" value="1" <?= ($_POST['aktif'] ?? 1) ? 'checked' : '' ?> />
                  Tampilkan di katalog
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Variasi -->
        <div>
          <div class="panel">
            <div class="panel-title">Variasi Produk</div>
            <div class="panel-tip">
              Tambahkan minimal 1 variasi. Setiap variasi bisa memiliki nama, harga, dan gambar berbeda.
            </div>

            <div class="variasi-list" id="variasi-list">
              <div class="variasi-item">
                <span class="variasi-num">Variasi 1</span>
                <span class="remove-variasi" title="Hapus variasi">✕</span>
                <div style="height:.5rem;"></div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Nama Variasi</label>
                    <input type="text" name="var_nama[]" placeholder="cth: Standar" required />
                  </div>
                  <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="var_harga[]" min="0" placeholder="150000" required />
                  </div>
                </div>
                <div class="form-group">
                  <label>Gambar</label>
                  <input type="file" name="var_gambar_0" accept="image/*" data-preview="prev_0" />
                  <img id="prev_0" class="img-preview" src="" alt="" />
                </div>
              </div>
            </div>

            <button type="button" id="btn-add-variasi" style="margin-top:.5rem;width:100%;padding:.65rem;border:1.5px dashed var(--sand,#d0cac2);background:none;border-radius:8px;cursor:pointer;color:var(--muted,#9a9087);font-size:.82rem;transition:all .15s;">
              + Tambah Variasi Lain
            </button>
          </div>

          <div style="display:flex;gap:.8rem;margin-top:1rem;">
            <button class="btn btn-primary" type="submit" style="flex:1;">Simpan Produk</button>
            <a href="/admin/produk/index.php" class="btn btn-outline">Batal</a>
          </div>
        </div>

      </div><!-- end form-layout -->
    </form>

  </div>
</div>

<script src="/js/admin.js"></script>
<script>
let idx = 1;

function attachVariasiEvents(div, i) {
  const fileInput = div.querySelector('input[type=file]');
  const removeBtn = div.querySelector('.remove-variasi');

  if (fileInput) {
    fileInput.addEventListener('change', function() {
      const p = document.getElementById(this.dataset.preview);
      if (!p) return;
      const r = new FileReader();
      r.onload = e => { p.src = e.target.result; p.style.display = 'block'; };
      r.readAsDataURL(this.files[0]);
    });
  }
  if (removeBtn) {
    removeBtn.addEventListener('click', () => {
      const items = document.querySelectorAll('.variasi-item');
      if (items.length > 1) div.remove();
      renumberVariasi();
    });
  }
}

function renumberVariasi() {
  document.querySelectorAll('.variasi-item').forEach((item, i) => {
    const num = item.querySelector('.variasi-num');
    if (num) num.textContent = 'Variasi ' + (i + 1);
  });
}

// Init first item
attachVariasiEvents(document.querySelector('.variasi-item'), 0);

document.getElementById('btn-add-variasi').addEventListener('click', () => {
  const list = document.getElementById('variasi-list');
  const div  = document.createElement('div');
  div.className = 'variasi-item';
  div.innerHTML = `
    <span class="variasi-num">Variasi ${idx + 1}</span>
    <span class="remove-variasi" title="Hapus">✕</span>
    <div style="height:.5rem;"></div>
    <div class="form-row">
      <div class="form-group">
        <label>Nama Variasi</label>
        <input type="text" name="var_nama[]" placeholder="cth: Variasi ${idx + 1}" />
      </div>
      <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="var_harga[]" min="0" placeholder="150000" />
      </div>
    </div>
    <div class="form-group">
      <label>Gambar</label>
      <input type="file" name="var_gambar_${idx}" accept="image/*" data-preview="prev_${idx}" />
      <img id="prev_${idx}" class="img-preview" />
    </div>`;
  list.appendChild(div);
  attachVariasiEvents(div, idx);
  idx++;
});
</script>
</body>
</html>