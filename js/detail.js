// js/detail.js – Variant selector untuk halaman detail produk

document.querySelectorAll('.variant-btn').forEach((btn, idx) => {
  btn.addEventListener('click', () => {
    const data    = JSON.parse(document.getElementById('variants-data').textContent);
    const variant = data[idx];

    // Aktifkan tombol
    document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Ganti gambar
    const imgEl = document.getElementById('main-img');
    if (imgEl && variant.gambar) {
      imgEl.style.opacity   = '0';
      imgEl.style.transform = 'scale(.97)';
      setTimeout(() => {
        imgEl.src    = '/' + variant.gambar;
        imgEl.onload = () => { imgEl.style.opacity = '1'; imgEl.style.transform = 'scale(1)'; };
        imgEl.onerror= () => { imgEl.style.opacity = '1'; imgEl.style.transform = 'scale(1)'; };
      }, 200);
    }

    // Harga
    const priceEl = document.getElementById('detail-price');
    if (priceEl) priceEl.textContent = 'Rp ' + Number(variant.harga).toLocaleString('id-ID');

    // Label variasi
    const labelEl = document.getElementById('active-variant-name');
    if (labelEl) labelEl.textContent = variant.nama;

    // WhatsApp
    const waEl = document.getElementById('wa-link');
    if (waEl) {
      const name = document.getElementById('product-name').textContent;
      const txt  = `Halo Kios Bunga Talenta, saya tertarik dengan produk *${name}* variasi *${variant.nama}* (Rp ${Number(variant.harga).toLocaleString('id-ID')}). Apakah masih tersedia?`;
      waEl.href  = `https://wa.me/6285233608339?text=${encodeURIComponent(txt)}`;
    }
  });
});