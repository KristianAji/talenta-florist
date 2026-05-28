<?php
// admin/produk/tambah.php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/db.php';

$active_menu = 'produk';
$errors = [];

$kategori_list = db()->query('SELECT * FROM kategori ORDER BY urutan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $badge       = trim($_POST['badge'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $aktif       = isset($_POST['aktif']) ? 1 : 0;

    $var_nama   = $_POST['var_nama']   ?? [];
    $var_harga  = $_POST['var_harga']  ?? [];

    if (!$nama)        $errors[] = 'Nama produk wajib diisi.';
    if (!$kategori_id) $errors[] = 'Kategori wajib dipilih.';
    if (empty($var_nama) || !array_filter($var_nama)) $errors[] = 'Minimal 1 variasi harus diisi.';

    if (empty($errors)) {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare("INSERT INTO produk (kategori_id, nama, deskripsi, badge, aktif) VALUES (?,?,?,?,?)");
            $stmt->execute([$kategori_id, $nama, $deskripsi, $badge ?: null, $aktif]);
            $produk_id = db()->lastInsertId();

            $stmtV = db()->prepare("INSERT INTO variasi (produk_id, nama, harga, gambar, urutan) VALUES (?,?,?,?,?)");

            foreach ($var_nama as $i => $vn) {
                $vn = trim($vn);
                if (!$vn) continue;

                $vh = (int)($var_harga[$i] ?? 0);

                $gambar = null;
                $file_key = "var_gambar_$i";
                if (!empty($_FILES[$file_key]['tmp_name'])) {
                    $ext     = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp'];
                    if (in_array($ext, $allowed)) {
                        $dir  = __DIR__ . '/../../uploads/';
                        $fname = uniqid('img_') . '.' . $ext;
                        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $dir . $fname)) {
                            $gambar = 'uploads/' . $fname;
                        }
                    }
                }

                $stmtV->execute([$produk_id, $vn, $vh, $gambar, $i + 1]);
            }

            db()->commit();
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Produk berhasil ditambahkan.'];
            header('Location: ' . base_url('admin/produk/index.php'));
            exit;
        } catch (\Exception $e) {
            db()->rollBack();
            $errors[] = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Produk – Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('admin/admin.css') ?>" />
</head>
<body>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <button id="sidebar-toggle">☰</button>
    <h1 class="topbar-title">Tambah <em>Produk</em></h1>
    <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline btn-sm">← Kembali</a>
  </div>

  <div class="admin-body">

    <?php if ($errors): ?>
    <div class="flash-msg flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="panel">
        <div class="panel-title">Informasi Produk</div>
        <div class="form-row">
          <div class="form-group">
            <label>Nama Produk *</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label>Kategori *</label>
            <select name="kategori_id" required>
              <option value="">– Pilih Kategori –</option>
              <?php foreach ($kategori_list as $k): ?>
              <option value="<?= $k['id'] ?>" <?= (($_POST['kategori_id']??0)==$k['id'])?'selected':'' ?>>
                <?= htmlspecialchars($k['nama']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Badge (opsional, contoh: Custom, Baru, Promo)</label>
            <input type="text" name="badge" value="<?= htmlspecialchars($_POST['badge'] ?? '') ?>" />
          </div>
          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem;">
            <label class="form-check">
              <input type="checkbox" name="aktif" value="1" <?= ($_POST['aktif']??1)?'checked':'' ?> />
              Produk Aktif (tampil di katalog)
            </label>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-title">Variasi Produk</div>
        <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem;">Tambahkan minimal 1 variasi. Setiap variasi memiliki nama, harga, dan gambar tersendiri.</p>
        <div class="variasi-list" id="variasi-list">
          <div class="variasi-item">
            <span class="remove-variasi" title="Hapus variasi">✕</span>
            <div class="form-row">
              <div class="form-group">
                <label>Nama Variasi</label>
                <input type="text" name="var_nama[]" placeholder="cth: Variasi 1" required />
              </div>
              <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="var_harga[]" min="0" placeholder="150000" required />
              </div>
            </div>
            <div class="form-group">
              <label>Gambar Variasi</label>
              <input type="file" name="var_gambar_0" accept="image/*" data-preview="prev_0" />
              <img id="prev_0" class="img-preview" src="" alt="" />
            </div>
          </div>
        </div>
        <button type="button" id="btn-add-variasi">+ Tambah Variasi</button>
      </div>

      <div style="display:flex;gap:.8rem;">
        <button class="btn btn-primary" type="submit">Simpan Produk</button>
        <a href="<?= base_url('admin/produk/index.php') ?>" class="btn btn-outline">Batal</a>
      </div>
    </form>

  </div>
</div>

<script src="<?= base_url('js/admin.js') ?>"></script>
<script>
let idx = 1;
document.getElementById('btn-add-variasi').addEventListener('click', () => {
  const list = document.getElementById('variasi-list');
  const div  = document.createElement('div');
  div.className = 'variasi-item';
  div.innerHTML = `
    <span class="remove-variasi" title="Hapus variasi">✕</span>
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
      <label>Gambar Variasi</label>
      <input type="file" name="var_gambar_${idx}" accept="image/*" data-preview="prev_${idx}" />
      <img id="prev_${idx}" class="img-preview" src="" alt="" />
    </div>`;
  list.appendChild(div);
  div.querySelector('input[type=file]').addEventListener('change', function() {
    const p = document.getElementById(this.dataset.preview);
    if (!p) return;
    const r = new FileReader();
    r.onload = e => { p.src = e.target.result; p.style.display = 'block'; };
    r.readAsDataURL(this.files[0]);
  });
  div.querySelector('.remove-variasi').addEventListener('click', () => div.remove());
  idx++;
});

document.querySelector('.remove-variasi').addEventListener('click', function() {
  const items = document.querySelectorAll('.variasi-item');
  if (items.length > 1) this.closest('.variasi-item').remove();
});
</script>
</body>
</html>