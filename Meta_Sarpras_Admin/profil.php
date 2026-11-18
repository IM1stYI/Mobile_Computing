<?php
require __DIR__ . '/../app/bootstrap.php'; require __DIR__ . '/partials.php'; admin_require();
ob_start(); ?>
<style>
.pgrid{display:grid;grid-template-columns:320px 1fr;gap:24px;align-items:start}
.big{width:96px;height:96px;border-radius:50%;background:#1e3a8a;border:3px solid #ef4444;display:grid;place-items:center;font-size:42px;margin:0 auto 14px}
.pc{text-align:center}.pc h2{font-size:18px;color:#112a4f;margin-bottom:8px}
.il{text-align:left;display:flex;flex-direction:column;gap:14px;margin-top:6px}.il small{display:block;font-size:11px;color:#6b7280;margin-bottom:2px}.il b{font-size:13px;color:#112a4f;word-break:break-word}
.pf label{display:block;font-size:13px;font-weight:700;color:#112a4f;margin:16px 0 8px}.pf label i{color:#ef4444;font-style:normal}
.pf input{width:100%;padding:12px 16px;border:1px solid #d1d5db;border-radius:8px;font:inherit;font-size:13px;outline:0;background:#fff}
.pf input:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px #1e3a8a1a}.pf input.err{border-color:#ef4444}.pf input[readonly]{background:#f3f4f6;color:#6b7280}
.pf h2{font-size:16px;color:#112a4f}.pf>div>p{font-size:12px;color:#6b7280;margin-top:4px}
.pf .row2{display:grid;grid-template-columns:1fr 1fr;gap:0 20px}.pf .ft{display:flex;gap:10px;justify-content:flex-end;margin-top:22px;flex-wrap:wrap}
.pf .ft .b{padding:11px 22px;font-size:13px;border-radius:8px}.chk{display:flex;align-items:center;gap:8px;font-size:12px;color:#4b5563;margin-top:14px}.chk input{width:auto}
@media(max-width:1000px){.pgrid{grid-template-columns:1fr}}@media(max-width:560px){.pf .row2{grid-template-columns:1fr}}
</style>
<?php $css = ob_get_clean(); admin_open('Profil Akun', [], $css); ?>

<script>
shell({ active: 'profil', title: 'Profil Akun' });
const P = APP.admin;
$('#content').innerHTML = `<div class="pgrid">
<section class="card pc"><div class="big">🛡️</div><h2 id="pn">${P.nama}</h2><span class="role" style="margin-bottom:20px">@ADMIN</span>
<div class="il"><div><small>Username</small><b>${P.username}</b></div><div><small>Hak akses</small><b>Administrator Sarpras (penuh)</b></div></div></section>
<div style="display:flex;flex-direction:column;gap:24px">
<form class="card pf" id="fp" novalidate><div><h2>👤 Informasi Profil</h2><p>Perbarui nama petugas administrator. Username tidak dapat diubah.</p></div>
<div class="row2"><div><label for="n">Nama Lengkap <i>*</i></label><input id="n" value="${P.nama}" maxlength="100"></div><div><label for="u">Username</label><input id="u" value="${P.username}" readonly></div></div>
<div class="ft"><button class="b p" type="submit">💾 Simpan Perubahan</button></div></form>
<form class="card pf" id="fw" novalidate><div><h2> Ubah Password</h2><p>Gunakan minimal 6 karakter dan jangan bagikan password kepada siapa pun.</p></div>
<label for="w0">Password Saat Ini <i>*</i></label><input id="w0" type="password" autocomplete="current-password">
<div class="row2"><div><label for="w1">Password Baru <i>*</i></label><input id="w1" type="password" autocomplete="new-password"></div><div><label for="w2">Konfirmasi Password Baru <i>*</i></label><input id="w2" type="password" autocomplete="new-password"></div></div>
<label class="chk"><input type="checkbox" id="sh"> Tampilkan password</label><div class="ft"><button class="b bl" type="submit">🔑 Ubah Password</button></div></form></div></div>`;
$('#sh').onchange = e => ['w0', 'w1', 'w2'].forEach(i => $('#' + i).type = e.target.checked ? 'text' : 'password');
$('#fp').onsubmit = async e => {
  e.preventDefault(); const n = $('#n').value.trim(); $('#n').classList.toggle('err', !n); if (!n) { toast('Nama lengkap wajib diisi'); return }
  const j = await api('profil.nama', { nama: n }); if (!j) return;
  $('#pn').innerHTML = j.nama; document.querySelector('.profile h4').innerHTML = j.nama; toast('Profil berhasil diperbarui');
};
$('#fw').onsubmit = async e => {
  e.preventDefault(); const a = $('#w0'), b = $('#w1'), c = $('#w2');[a, b, c].forEach(x => x.classList.remove('err'));
  const er = (x, m) => { x.classList.add('err'); toast(m) };
  if (!a.value) return er(a, 'Password saat ini wajib diisi');
  if (b.value.length < 6) return er(b, 'Password baru minimal 6 karakter');
  if (c.value !== b.value) return er(c, 'Konfirmasi password tidak cocok');
  const j = await api('profil.password', { lama: a.value, baru: b.value }); if (!j) return er(a, 'Periksa kembali password Anda');
  a.value = b.value = c.value = ''; toast('Password berhasil diubah');
};
</script></body></html>
