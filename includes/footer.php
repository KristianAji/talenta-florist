<?php
// includes/footer.php
// Sertakan di akhir semua halaman PUBLIK.
// base_url() sudah tersedia karena header.php di-include lebih dulu.
?>

  <footer class="reveal">
    <div class="footer-inner">
      <span class="footer-logo">Talenta Florist</span>
      <ul class="footer-links">
        <li><a href="<?= base_url('tentang.php') ?>">Tentang</a></li>
        <li><a href="<?= base_url('katalog.php') ?>">Katalog</a></li>
        <li><a href="<?= base_url('pesan.php') ?>">Cara Pesan</a></li>
        <li><a href="<?= base_url('kontak.php') ?>">Kontak</a></li>
      </ul>
      <p>© <?= date('Y') ?> <strong>Kios Bunga Talenta</strong> · Kota Tomohon, Sulawesi Utara</p>
      <p class="footer-copy">
        Website dibuat sebagai bagian dari program digitalisasi UMKM ·
        <a href="https://unsrat.ac.id" target="_blank">Universitas Sam Ratulangi</a>
      </p>
    </div>
  </footer>

  <script src="<?= base_url('js/main.js') ?>"></script>
</body>
</html>