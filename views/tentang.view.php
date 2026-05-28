<?php
/**
 * views/tentang.view.php
 *
 * View layer untuk halaman tentang & anggota kelompok.
 * Dipanggil dari tentang.php setelah data siap.
 *
 * Variabel yang diharapkan:
 *   $anggota – array anggota kelompok dari DB
 */
?>

<!-- TENTANG -->
<section id="tentang">
  <div class="about-grid">
    <div class="about-img-wrap reveal-left">
      <img src="image/Talenta.jpeg" alt="Kios Bunga Talenta" />
      <div class="about-badge">
        <span class="about-badge-num">★</span>
        <span class="about-badge-txt">Tomohon<br>Florist</span>
      </div>
    </div>
    <div class="reveal-right">
      <p class="section-label">Tentang Kami</p>
      <h2 class="section-title">Kios Bunga <em>Talenta</em></h2>
      <p class="section-desc">Kios Bunga Talenta adalah usaha florist lokal yang berlokasi di Kota Tomohon, kota bunga terbaik di Sulawesi Utara. Kami menyediakan berbagai rangkaian bunga segar untuk kebutuhan personal maupun acara seremonial.</p>
      <div class="about-features">
        <div class="feature-card reveal">
          <div class="feature-icon">🌷</div>
          <div class="feature-title">Bunga Segar</div>
          <div class="feature-desc">Langsung dari sumber terpercaya di Tomohon</div>
        </div>
        <div class="feature-card reveal">
          <div class="feature-icon">🚚</div>
          <div class="feature-title">Antar ke Lokasi</div>
          <div class="feature-desc">Pengiriman ke seluruh area Tomohon</div>
        </div>
        <div class="feature-card reveal">
          <div class="feature-icon">💌</div>
          <div class="feature-title">Custom Order</div>
          <div class="feature-desc">Desain sesuai keinginan dan budget Anda</div>
        </div>
        <div class="feature-card reveal">
          <div class="feature-icon">⚡</div>
          <div class="feature-title">Respons Cepat</div>
          <div class="feature-desc">Balas WhatsApp dalam hitungan menit</div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ANGGOTA KELOMPOK -->
<section class="anggota-section">
  <p class="section-label reveal">Tim Kami</p>
  <h2 class="section-title reveal">Anggota <em>Kelompok</em></h2>
  <p class="section-desc reveal">Website ini merupakan proyek kelompok dalam mata kuliah Pemrograman Web, Universitas Sam Ratulangi.</p>
  <div class="anggota-grid">
    <?php foreach ($anggota as $a): ?>
    <div class="anggota-card reveal">
      <?php if ($a['foto']): ?>
      <img class="anggota-foto" src="uploads/<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['nama']) ?>" />
      <?php else: ?>
      <div class="anggota-foto-placeholder"><?= mb_substr($a['nama'], 0, 1) ?></div>
      <?php endif; ?>
      <div class="anggota-nama"><?= htmlspecialchars($a['nama']) ?></div>
      <div class="anggota-nim"><?= htmlspecialchars($a['nim']) ?></div>
      <span class="anggota-peran"><?= htmlspecialchars($a['peran']) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>