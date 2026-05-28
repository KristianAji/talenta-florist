<?php
/**
 * views/pesan.view.php
 *
 * View layer untuk halaman cara pemesanan.
 * Tidak membutuhkan variabel khusus dari controller.
 */

$langkah = [
  ['num'=>'01','title'=>'Pilih Produk',      'desc'=>'Jelajahi katalog dan pilih rangkaian bunga yang Anda inginkan.'],
  ['num'=>'02','title'=>'Hubungi Kami',       'desc'=>'Chat via WhatsApp, sebutkan produk dan tanggal kebutuhan.'],
  ['num'=>'03','title'=>'Konfirmasi & Bayar','desc'=>'Setujui harga dan lakukan pembayaran via transfer bank.'],
  ['num'=>'04','title'=>'Terima Bunga',       'desc'=>'Bunga diantar ke lokasi Anda atau dapat diambil di kios.'],
];

$faqs = [
  ['q'=>'Apakah bisa custom desain?',             'a'=>'Tentu! Kami menerima custom order. Hubungi kami via WhatsApp dan ceritakan konsep yang Anda inginkan.'],
  ['q'=>'Berapa lama proses pemesanan?',           'a'=>'Untuk pesanan reguler, kami membutuhkan minimal 1 hari. Untuk dekorasi acara besar, disarankan 3–7 hari sebelum acara.'],
  ['q'=>'Apakah ada pengiriman ke luar Tomohon?', 'a'=>'Saat ini kami melayani pengiriman di dalam kota Tomohon. Untuk area lain, silakan hubungi kami untuk konfirmasi.'],
  ['q'=>'Metode pembayaran apa yang tersedia?',   'a'=>'Kami menerima transfer bank (BRI, BNI, Mandiri) dan pembayaran tunai di kios.'],
  ['q'=>'Apakah bunga yang dijual selalu segar?', 'a'=>'Ya, kami mengutamakan kualitas. Bunga langsung dari sumber terpercaya di Tomohon dan dikerjakan fresh sesuai tanggal pesanan.'],
];
?>

<!-- LANGKAH PEMESANAN -->
<section id="pesan">
  <div class="steps-bg">
    <p class="section-label" style="color:var(--blush)">Mudah & Cepat</p>
    <h2 class="section-title">Cara <em>Pemesanan</em></h2>
    <p class="section-desc">Pesan bunga favorit Anda hanya dalam 4 langkah mudah melalui WhatsApp.</p>
    <div class="steps-grid">
      <?php foreach ($langkah as $l): ?>
      <div class="step">
        <div class="step-num-wrap">
          <div class="step-num"><?= $l['num'] ?></div>
        </div>
        <div class="step-title"><?= $l['title'] ?></div>
        <div class="step-desc"><?= $l['desc'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq-section">
  <p class="section-label reveal">Pertanyaan Umum</p>
  <h2 class="section-title reveal">FAQ</h2>
  <div class="faq-list">
    <?php foreach ($faqs as $f): ?>
    <div class="faq-item reveal">
      <button class="faq-q"><?= htmlspecialchars($f['q']) ?></button>
      <div class="faq-a"><?= htmlspecialchars($f['a']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>