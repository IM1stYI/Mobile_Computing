<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
$rows = array_values(array_filter(Peminjaman::semua(), fn($r) => in_array($r['s'], ['serah', 'selesai'], true)));
admin_open('Pengembalian & Denda', ['rows' => $rows]); ?>
<script>
shell({ active: 'pengembalian', title: 'Pengembalian & Denda Alat' });
const D = DATA.rows;
const FL = [['Semua', ''], ['Belum Kembali', 'serah'], ['Sudah Kembali', 'selesai'], ['Ada Denda', 'denda']]; let f = '', q = '';
$('#content').innerHTML = `<section class="card"><div class="chead"><div><h2>🔄 Pengembalian &amp; Denda Alat</h2><p>Periksa kondisi barang, konfirmasi pengembalian, dan kelola denda keterlambatan (Rp 10.000/hari jika telat ≥ 3 hari).</p></div><div class="fg" id="fg"></div></div>
<input class="search" id="q" placeholder="Cari peminjam, NIM, nama alat..." aria-label="Cari"><div class="tr"><table style="min-width:1050px"><thead><tr><th>No</th><th>Mahasiswa Peminjam</th><th>Nama Alat</th><th>Qty</th><th>Tgl Pinjam &amp; Tempo</th><th>Status</th><th>Denda</th><th>Aksi</th></tr></thead><tbody id="tb"></tbody></table></div></section>`;
function render() {
  $('#fg').innerHTML = FL.map(([l, k]) => `<button class="bf ${f == k ? 'on' : ''}" data-f="${k}">${l}</button>`).join('');
  const r = D.map((x, i) => ({ ...x, i })).filter(x => (!f || (f == 'denda' ? x.den > 0 : x.s == f)) && (x.n + x.nim + x.a).toLowerCase().includes(q));
  $('#tb').innerHTML = r.length ? r.map((x, k) => `<tr><td>${k + 1}</td><td><div class="stack"><span class="tm">${x.n}</span><span class="ts">NIM: ${x.nim}</span></div></td><td><span class="tm">${x.a}</span></td><td><div class="qty">${x.q}<br>unit</div></td><td><div class="stack"><span class="ts">Pinjam: ${fd(x.p)}</span><b style="color:#112a4f">Tempo: ${fd(x.t)}</b></div></td><td>${x.s == 'selesai' ? '<span class="st gr">Selesai (Dikembalikan)</span>' : '<span class="st bl">Serah Terima (Dipinjam)</span>'}</td><td>${x.den ? `<span class="den">${rp(x.den)}</span>` : '-'}</td><td><div class="acts">${x.s == 'selesai' ? `<button class="b w" data-a="denda" data-i="${x.i}">✏️ Edit Denda</button>` : `<button class="b p w" data-a="cek" data-i="${x.i}">🔍 Cek Kondisi</button>`}</div></td></tr>`).join('') : '<tr><td colspan="8" class="empty">Tidak ada data yang cocok.</td></tr>';
}
document.addEventListener('click', e => {
  const fb = e.target.closest('[data-f]'); if (fb) { f = fb.dataset.f; render(); return }
  const b = e.target.closest('[data-a]'); if (!b) return; const i = +b.dataset.i, x = D[i], cek = b.dataset.a == 'cek';
  returnModal(x, { icon: cek ? '🔍' : '✏️', title: cek ? 'Cek Kondisi & Konfirmasi Pengembalian' : 'Edit Denda & Pengembalian', ok: cek ? '✅ Konfirmasi Pengembalian Selesai →' : '💾 Simpan Perubahan Denda', save: async p => { const j = await api('peminjaman.kembali', p); if (!j) return false; D[i] = j.row; render(); toast(cek ? 'Pengembalian dikonfirmasi' : 'Denda diperbarui') } });
});
$('#q').oninput = e => { q = e.target.value.toLowerCase(); render() }; render();
</script></body></html>
