// js/pesan_produk.js – Ringkasan pesanan & kontrol jumlah

const selVariasi = document.getElementById('sel-variasi');
const qtyInput   = document.getElementById('qty-input');

if (selVariasi && qtyInput) {
  function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
  }

  function update() {
    const opt    = selVariasi.options[selVariasi.selectedIndex];
    const harga  = parseInt(opt.dataset.harga);
    const gambar = opt.dataset.gambar;
    const jumlah = parseInt(qtyInput.value) || 1;

    document.getElementById('sum-variasi').textContent = opt.text.split(' — ')[0];
    document.getElementById('sum-harga').textContent   = fmt(harga);
    document.getElementById('sum-jumlah').textContent  = jumlah;
    document.getElementById('sum-total').textContent   = fmt(harga * jumlah);

    const img = document.getElementById('sum-img');
    if (img && gambar) img.src = gambar;
  }

  selVariasi.addEventListener('change', update);
  qtyInput.addEventListener('input', update);

  document.getElementById('qty-min').addEventListener('click', () => {
    if (parseInt(qtyInput.value) > 1) { qtyInput.value--; update(); }
  });

  document.getElementById('qty-plus').addEventListener('click', () => {
    if (parseInt(qtyInput.value) < 99) { qtyInput.value++; update(); }
  });

  update();
}