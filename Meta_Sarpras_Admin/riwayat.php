<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
admin_open('Riwayat Tanggapan', ['rows' => Tanggapan::semua()]); ?>
<script>
shell({ active: 'riwayat', title: 'Riwayat Tanggapan' });
const R = DATA.rows; let q = '';
$('#content').innerHTML = `<section class="card"><div class="chead"><div><h2>💬 Riwayat Tanggapan</h2><p>Seluruh tanggapan resmi petugas untuk pengaduan mahasiswa.</p></div></div><input class="search" id="q" placeholder="Cari mahasiswa atau tanggapan..." aria-label="Cari"><div class="tr"><table style="min-width:900px"><thead><tr><th>Tanggal</th><th>Mahasiswa</th><th>Pengaduan</th><th>Tanggapan</th><th>Petugas</th></tr></thead><tbody id="tb"></tbody></table></div></section>`;
const short = t => t.length > 60 ? t.slice(0, 58) + '...' : t;
function render() {
  const L = R.filter(r => (r.n + r.d + r.isi + r.admin).toLowerCase().includes(q));
  $('#tb').innerHTML = L.length ? L.map(r => `<tr><td>${fd(r.tgl)}</td><td class="tm">${r.n}</td><td><div class="td">${short(r.d)}</div></td><td><div class="td">${r.isi}</div></td><td>${r.admin}</td></tr>`).join('') : '<tr><td colspan="5" class="empty">Belum ada tanggapan.</td></tr>';
}
$('#q').oninput = e => { q = e.target.value.toLowerCase(); render() }; render();
</script></body></html>
