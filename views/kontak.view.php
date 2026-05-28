<?php
/**
 * views/kontak.view.php
 *
 * View layer untuk halaman kontak.
 * Tidak membutuhkan variabel khusus dari controller.
 */
?>

<section id="kontak">
  <div class="contact-grid">

    <!-- Kiri: info kontak -->
    <div class="reveal-left">
      <p class="section-label">Hubungi Kami</p>
      <h2 class="section-title">Temukan <em>Kami</em></h2>
      <p class="section-desc">Kami siap melayani Anda setiap hari. Hubungi kami melalui salah satu channel di bawah ini.</p>

      <div class="contact-cards">
        <a class="contact-card" href="https://wa.me/6285233608339" target="_blank">
          <span class="contact-icon">💬</span>
          <div>
            <div class="contact-label">WhatsApp</div>
            <div class="contact-value">0852-3360-8339</div>
          </div>
        </a>
        <a class="contact-card" href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58" target="_blank">
          <span class="contact-icon">📍</span>
          <div>
            <div class="contact-label">Lokasi</div>
            <div class="contact-value">Kota Tomohon, Sulawesi Utara</div>
          </div>
        </a>
        <a class="contact-card" href="https://www.instagram.com/kbtalenta" target="_blank">
          <span class="contact-icon">📸</span>
          <div>
            <div class="contact-label">Instagram</div>
            <div class="contact-value">@kbtalenta</div>
          </div>
        </a>
        <div class="contact-card" style="cursor:default;">
          <span class="contact-icon">🕐</span>
          <div>
            <div class="contact-label">Jam Operasional</div>
            <div class="contact-value">Setiap Hari, 07.00 – 20.00 WITA</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kanan: peta -->
    <div class="reveal-right">
      <div class="map-placeholder" id="mapBox">
        <div>
          Kios Bunga Talenta<br>
          <small style="opacity:.7">Kota Tomohon, Sulawesi Utara</small><br><br>
          <a class="btn btn-primary"
             href="https://maps.app.goo.gl/4P4Kyo859XVRhHW58"
             target="_blank"
             style="font-size:.75rem">
            Buka di Google Maps
          </a>
        </div>
      </div>
      <!--
        CARA EMBED GOOGLE MAPS:
        Ganti div.map-placeholder di atas dengan:
        <iframe
          src="PASTE_EMBED_URL_DARI_GOOGLE_MAPS_DI_SINI"
          width="100%" height="300"
          style="border:0;border-radius:16px;"
          allowfullscreen loading="lazy">
        </iframe>
      -->
    </div>

  </div>
</section>