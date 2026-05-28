<?php
/**
 * views/detail.view.php
 *
 * View layer untuk halaman detail produk.
 * Dipanggil dari detail.php setelah data siap.
 *
 * Variabel yang diharapkan:
 *   $produk  – row produk
 *   $variasi – array variasi
 *   $v0      – variasi pertama (default)
 */
?>
<!-- Variants JSON untuk JS -->
<script id="variants-data" type="application/json">
  <?= json_encode(array_map(fn($v) => [
    'nama'   => $v['nama'],
    'harga'  => $v['harga'],
    'gambar' => $v['gambar'],
  ], $variasi)) ?>
</script>

<div class="detail-wrap">

  <!-- Gambar -->
  <div class="img-box">
    <img id="main-img"
         src="<?= htmlspecialchars($v0['gambar'] ?? '') ?>"
         alt="<?= htmlspecialchars($produk['nama']) ?>"
         style="transition:opacity .3s,transform .3s;" />
    <?php if ($produk['badge']): ?>
    <span class="badge-box"><?= htmlspecialchars($produk['badge']) ?></span>
    <?php endif; ?>
  </div>

  <!-- Info -->
  <div>
    <p class="breadcrumb">
      <a href="/katalog.php">Katalog</a> ›
      <?= htmlspecialchars($produk['kat_nama']) ?> ›
      <?= htmlspecialchars($produk['nama']) ?>
    </p>
    <p class="kat-tag"><?= htmlspecialchars($produk['kat_nama']) ?></p>
    <h1 id="product-name"><?= htmlspecialchars($produk['nama']) ?></h1>
    <p class="desc"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>

    <?php if (count($variasi) > 1): ?>
    <p class="variant-heading">
      Pilih Variasi
      <span id="active-variant-name"><?= htmlspecialchars($v0['nama']) ?></span>
    </p>
    <div class="variant-row">
      <?php foreach ($variasi as $idx => $v): ?>
      <button class="variant-btn <?= $idx===0?'active':'' ?>">
        <?= htmlspecialchars($v['nama']) ?>
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <span id="detail-price">Rp <?= number_format($v0['harga'] ?? 0, 0, ',', '.') ?></span>

    <div class="cta-row">
      <?php
        $waInit = urlencode("Halo Kios Bunga Talenta, saya tertarik dengan produk *{$produk['nama']}* variasi *{$v0['nama']}* (Rp " . number_format($v0['harga'],0,',','.') . "). Apakah masih tersedia?");
      ?>
      <a class="wa-big" id="wa-link"
         href="https://wa.me/6285233608339?text=<?= $waInit ?>"
         target="_blank">
        💬 Pesan via WhatsApp
      </a>
      <a class="back-btn" href="/katalog.php">← Kembali ke Katalog</a>
    </div>
  </div>

</div>