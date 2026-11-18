<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
admin_open('Kelola Data Akun', ['mhs' => Mahasiswa::all(), 'adm' => Admin::all()]); ?>
<script>
shell({ active: 'akun', title: 'Kelola Data Akun' });
const M = DATA.mhs, A = DATA.adm; let tab = 'm', q = '';
$('#content').innerHTML = `<section class="card"><div class="chead"><div><h2>👥 Kelola Data Akun</h2><p>Buat, lihat, dan hapus akun mahasiswa serta kelola petugas administrator sarpras.</p></div><div class="top-r"><div class="fg" id="fg"></div><button class="b p" id="add" style="padding:10px 16px"></button></div></div>
<input class="search" id="q" placeholder="Cari nama atau NIM / username..." aria-label="Cari"><div class="tr"><table style="min-width:700px"><thead id="th"></thead><tbody id="tb"></tbody></table></div></section>`;
function render() {
  $('#fg').innerHTML = [['m', 'Mahasiswa'], ['a', 'Petugas Admin']].map(([k, l]) => `<button class="bf ${tab == k ? 'on' : ''}" data-t="${k}">${l}</button>`).join('');
  $('#add').textContent = tab == 'm' ? '+ Buat Akun Mahasiswa' : '+ Tambah Petugas Admin'; $('#add').className = 'b ' + (tab == 'm' ? 'p' : 'bl');
  const L = (tab == 'm' ? M : A).map((r, i) => ({ r, i })).filter(x => (x.r.nim || x.r.username).toLowerCase().includes(q) || x.r.nama.toLowerCase().includes(q));
  $('#th').innerHTML = tab == 'm' ? '<tr><th>No</th><th>NIM</th><th>Nama Lengkap</th><th>Aksi</th></tr>' : '<tr><th>No</th><th>Username</th><th>Nama Lengkap</th><th>Peran</th></tr>';
  $('#tb').innerHTML = L.length ? L.map((x, k) => tab == 'm'
    ? `<tr><td>${k + 1}</td><td><b>${x.r.nim}</b></td><td class="tm">${x.r.nama}</td><td><div class="acts"><button class="b" data-r="${x.i}">Reset Password</button><button class="b r" data-d="${x.i}">Hapus</button></div></td></tr>`
    : `<tr><td>${k + 1}</td><td><b>${x.r.username}</b></td><td class="tm">${x.r.nama}</td><td><span class="role">@ADMIN</span></td></tr>`).join('') : '<tr><td colspan="4" class="empty">Tidak ada akun yang cocok.</td></tr>';
}
function mhs() {
  openM({
    icon: '🎓', title: 'Buat Akun Mahasiswa Baru', w: 550, ok: 'Buat Akun Mahasiswa →', body: `<label class="fl first" for="n1">Nomor Induk Mahasiswa (NIM) <i>*</i></label><input id="n1" placeholder="Contoh: 23010303" inputmode="numeric"><label class="fl" for="n2">Nama Lengkap <i>*</i></label><input id="n2" placeholder="Contoh: Fajar Ramadhan"><label class="fl" for="n3">Password Akun (Default: password123)</label><input id="n3" value="password123">`,
    async onOk() {
      const n = $('#n1').value.trim(), m = $('#n2').value.trim(); $('#n1').classList.toggle('err', !n); $('#n2').classList.toggle('err', !m);
      if (!n || !m) { toast('NIM dan nama wajib diisi'); return false }
      const j = await api('akun.mhs.tambah', { nim: n, nama: m, password: $('#n3').value }); if (!j) return false;
      M.push(j.row); M.sort((a, b) => a.nama.localeCompare(b.nama)); tab = 'm'; render(); toast('Akun mahasiswa dibuat');
    }
  });
}
function adm() {
  openM({
    icon: '<img src="logo.png" alt="">', title: 'Tambah Petugas Administrator Baru', w: 550, ok: 'Simpan Akun Admin →', cls: 'bl', body: `<label class="fl first" for="a1">Username Login Admin <i>*</i></label><input id="a1" placeholder="Contoh: sarpras_andi"><label class="fl" for="a2">Nama Lengkap Petugas <i>*</i></label><input id="a2" placeholder="Contoh: Andi Wijaya, S.T."><label class="fl" for="a3">Password Admin <i>*</i></label><input id="a3" type="password" placeholder="Minimal 6 karakter">`,
    async onOk() {
      const u = $('#a1').value.trim(), n = $('#a2').value.trim(), p = $('#a3').value;
      ['a1', 'a2', 'a3'].forEach(i => $('#' + i).classList.toggle('err', !$('#' + i).value.trim()));
      if (!u || !n || !p) { toast('Semua kolom wajib diisi'); return false }
      const j = await api('akun.admin.tambah', { username: u, nama: n, password: p }); if (!j) return false;
      A.push(j.row); tab = 'a'; render(); toast('Akun admin disimpan');
    }
  });
}
document.addEventListener('click', e => {
  const t = e.target.closest('[data-t]'); if (t) { tab = t.dataset.t; render(); return }
  if (e.target.id == 'add') { tab == 'm' ? mhs() : adm(); return }
  const r = e.target.closest('[data-r]'); if (r) { const x = M[+r.dataset.r]; openM({ icon: '🔑', title: 'Reset password', w: 460, ok: 'Reset', body: `<p style="font-size:13px">Reset password <b>${x.nama}</b> (${x.nim}) menjadi <b>password123</b>?</p>`, async onOk() { const j = await api('akun.mhs.reset', { id: x.id }); if (!j) return false; toast('Password direset ke password123') } }); return }
  const d = e.target.closest('[data-d]'); if (d) {
    const i = +d.dataset.d, x = M[i];
    openM({ icon: '🗑️', title: 'Hapus akun', w: 460, ok: 'Hapus', cls: 'r', body: `<p style="font-size:13px">Hapus akun <b>${x.nama}</b> (${x.nim})? Tindakan ini tidak dapat dibatalkan.</p>`, async onOk() { const j = await api('akun.mhs.hapus', { id: x.id }); if (!j) return false; M.splice(i, 1); render(); toast('Akun dihapus') } });
  }
});
$('#q').oninput = e => { q = e.target.value.toLowerCase(); render() }; render();
</script></body></html>
