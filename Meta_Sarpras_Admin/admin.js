/* Admin Meta Sarpras - kerangka tampilan + helper */

const $ = s => document.querySelector(s);

const esc = t => String(t).replace(/[&<>"']/g, c => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
}[c]));

const MENU = [
    ['Ringkasan', [
        ['dashboard', '', 'Dashboard Utama', 'dashboard.php']
    ]],
    ['Layanan Fasilitas', [
        ['pengaduan', '', 'Pengaduan Fasilitas', 'pengaduan.php']
    ]],
    ['Peminjaman & Aset', [
        ['peminjaman', '', 'Pengajuan Peminjaman', 'peminjaman.php'],
        ['pengembalian', '', 'Pengembalian & Denda', 'pengembalian.php']
    ]],
    ['Komunikasi & Master Data', [
        ['riwayat', '', 'Riwayat Tanggapan', 'riwayat.php'],
        ['akun', '', 'Kelola Data Akun', 'akun.php']
    ]]
];

function shell(o) {
    const app = typeof APP !== 'undefined' ? APP : { badges: o.badges || {}, admin: { nama: 'Admin', username: 'admin' }, date: o.date || '', csrf: '', today: new Date().toISOString().slice(0, 10) };
    const b = app.badges || {};
    const P = app.admin || { nama: 'Admin', username: 'admin' };

    document.body.innerHTML = `
    <div class="scrim" id="scrim"></div>

    <aside class="sidebar" id="sb">

        <div class="brand">
            <img class="brand-logo" src="logo.png" alt="Logo Politeknik Meta">

            <div>
                <div class="brand-text">
                    POLITEKNIK <span>META</span>
                </div>
                <div class="brand-sub">
                    ADMINISTRATOR SARPRAS
                </div>
            </div>
        </div>

        ${MENU.map(([t, l]) => `
            <div class="msec">
                <div class="mtitle">${t}</div>

                ${l.map(([k, i, n, h]) => `
                    <a class="mi ${k == o.active ? 'on' : ''}" href="${h}">
                        <span>${i}</span>
                        ${n}
                        ${b[k] ? `<span class="nb">${b[k]}</span>` : ''}
                    </a>
                `).join('')}

            </div>
        `).join('')}

    </aside>

    <main class="main">

        <header class="header">

            <div style="display:flex;gap:14px;align-items:center">

                <button class="burger" id="bg" aria-label="Menu">
                    ☰
                </button>

                <div>
                    <h1>${o.title}</h1>
                    <p>
                        Politeknik Meta Industri Cikarang •
                        Sistem Pengaduan Fasilitas & Peminjaman Alat
                    </p>
                </div>

            </div>

            <div class="hact">

                <div class="date">
                    📅 ${app.date || o.date || ''}
                </div>

                <div class="user-dropdown">

                    <button
                        class="user-btn"
                        id="userBtn"
                        type="button"
                        aria-expanded="false"
                        aria-haspopup="true"
                    >
                        <div class="user-avatar">🛡️</div>

                        <span class="user-name">
                            ${esc(P.nama)}
                        </span>

                        <span class="user-arrow">▾</span>
                    </button>

                    <div class="user-menu" id="userMenu">

                        <a href="profil.php" class="user-menu-item">
                            <span></span>
                            Profil Saya
                        </a>

                        <a href="logout.php" class="user-menu-item logout-item">
                            <span></span>
                            Logout
                        </a>

                    </div>

                </div>

            </div>

        </header>

        <div id="content"
            style="display:flex;flex-direction:column;gap:24px">
        </div>

    </main>

    <div class="ov" id="ov">
        <div class="modal" id="mo"></div>
    </div>

    <div class="toast" id="toast"></div>
    `;

    // Sidebar mobile
    const sb = $('#sb');
    const sc = $('#scrim');

    $('#bg').onclick = () => {
        sb.classList.toggle('open');
        sc.classList.toggle('show');
    };

    sc.onclick = () => {
        sb.classList.remove('open');
        sc.classList.remove('show');
    };

    // Dropdown profil
    const userBtn = $('#userBtn');
    const userMenu = $('#userMenu');

    userBtn.onclick = e => {
        e.stopPropagation();

        const buka = userMenu.classList.toggle('show');

        userBtn.setAttribute('aria-expanded', buka);
    };

    // Tutup dropdown ketika klik di luar
    document.addEventListener('click', e => {
        if (!e.target.closest('.user-dropdown')) {
            userMenu.classList.remove('show');
            userBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // Tutup dropdown dengan Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            userMenu.classList.remove('show');
            userBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // Popup
    $('#ov').onclick = e => {
        if (e.target.id == 'ov') closeM();
    };
}

function toast(m) {
    const t = $('#toast');

    t.innerHTML = m;
    t.classList.add('show');

    clearTimeout(t._t);

    t._t = setTimeout(() => {
        t.classList.remove('show');
    }, 2600);
}

/* Kirim aksi ke api.php */
async function api(action, data = {}) {
    try {
        const r = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF': APP.csrf
            },
            body: JSON.stringify({
                action,
                ...data
            })
        });

        if (r.status === 401) {
            location.href = 'login.php';
            return null;
        }

        const j = await r.json();

        if (!j.ok) {
            toast(esc(j.msg || 'Terjadi kesalahan'));
            return null;
        }

        return j;

    } catch (e) {
        toast('Tidak dapat terhubung ke server');
        return null;
    }
}

const BLN = [
    'Januari', 'Februari', 'Maret', 'April',
    'Mei', 'Juni', 'Juli', 'Agustus',
    'September', 'Oktober', 'November', 'Desember'
];

const fd = i => {
    if (!i) return '-';

    const [y, m, d] = i.slice(0, 10).split('-');

    return +d + ' ' + BLN[m - 1] + ' ' + y;
};

const dd = (a, b) =>
    Math.round((Date.parse(a) - Date.parse(b)) / 864e5);

const rp = n =>
    'Rp' + Number(n).toLocaleString('id-ID');

const rpd = n =>
    'Rp ' + Number(n).toLocaleString('id-ID');

const fotoUrl = f =>
    f ? '../uploads/' + encodeURIComponent(f) : '';

/* Popup */
function openM(o) {
    const mo = $('#mo');

    mo.style.maxWidth = (o.w || 650) + 'px';

    mo.innerHTML = `
        <div class="mh">
            <h3>${o.icon || ''} ${o.title}</h3>
            <button class="x" id="mx" aria-label="Tutup">×</button>
        </div>

        <div class="mb">${o.body}</div>

        <div class="mf">
            <button class="b" id="mc">
                ${o.cancel || (o.ok ? 'Batal' : 'Tutup')}
            </button>

            ${o.ok ? `
                <button class="b ${o.cls || 'p'}" id="ms">
                    ${o.ok}
                </button>
            ` : ''}
        </div>
    `;

    $('#ov').classList.add('show');

    $('#mx').onclick = $('#mc').onclick = closeM;

    if (o.ok) {
        $('#ms').onclick = async () => {
            const btn = $('#ms');
            const r = o.onOk ? o.onOk() : undefined;

            if (r && r.then) {
                btn.disabled = true;

                const v = await r;

                btn.disabled = false;

                if (v !== false) closeM();

            } else if (r !== false) {
                closeM();
            }
        };
    }
}

function closeM() {
    $('#ov').classList.remove('show');
}

document.addEventListener('keydown', e => {
    if (e.key == 'Escape') closeM();
});

/* Popup cek kondisi / edit denda */
function returnModal(x, o) {

    const calc = d => {
        const n = dd(d, x.t);

        return n >= 3
            ? {
                n,
                den: n * 10000,
                k: `Keterlambatan ${n} hari (≥ 3 hari dikenakan denda)`
            }
            : {
                n,
                den: 0,
                k: n > 0
                    ? `Terlambat ${n} hari (<3 hari, bebas denda)`
                    : 'Tepat Waktu'
            };
    };

    const alertH = c => c.n >= 3
        ? `
            <div class="al red">
                <b>🚨 KETERLAMBATAN ≥ 3 HARI: WAJIB DENDA</b>
                <br>
                Alat terlambat <b>${c.n} hari</b> dari batas waktu.
                Dikenakan denda Rp 10.000 / hari.
                <strong>${rpd(c.den)}</strong>
            </div>
        `
        : `
            <div class="al grn">
                <span>✅</span>
                <span>
                    ${c.n > 0
                        ? `Terlambat ${c.n} hari, belum mencapai batas minimal 3 hari. Bebas denda otomatis`
                        : 'Pengembalian tepat waktu sebelum atau pada tanggal jatuh tempo. Bebas denda otomatis'
                    }
                    (Admin dapat mengisi denda manual di bawah).
                </span>
            </div>
        `;

    const d0 = x.ret || APP.today;
    const c0 = calc(d0);

    openM({
        icon: o.icon,
        title: `${o.title} #${x.id}`,
        w: 650,
        ok: o.ok,

        body: `
            <div class="ib">
                <div class="g2">
                    <div>
                        <small>Peminjam:</small>
                        <b>${x.n} (${x.nim})</b>
                    </div>

                    <div>
                        <small>Alat &amp; Jumlah (Qty):</small>
                        <b>${x.a} (${x.q} unit)</b>
                    </div>

                    <div>
                        <small>Tanggal Pinjam:</small>
                        <b>${fd(x.p)}</b>
                    </div>

                    <div>
                        <small>Batas Tempo Kembali:</small>
                        <b>${fd(x.t)}</b>
                    </div>
                </div>
            </div>

            <label class="fl" for="rd">
                Tanggal Aktual Pengembalian <i>*</i>
            </label>

            <input type="date" id="rd" value="${d0}">

            <label class="fl" for="rk">
                Kondisi Fisik Barang Saat Dikembalikan
            </label>

            <select id="rk">
                ${[
                    'Baik (Utuh, Bersih, dan Berfungsi Normal)',
                    'Rusak Ringan (Masih Berfungsi)',
                    'Rusak Berat',
                    'Hilang'
                ].map(k => `
                    <option ${k == x.kond ? 'selected' : ''}>
                        ${k}
                    </option>
                `).join('')}
            </select>

            <div id="ra">${alertH(c0)}</div>

            <div class="g2" style="margin-top:0">
                <div>
                    <label class="fl" for="rn">Nominal Denda (Rp)</label>
                    <input
                        type="number"
                        min="0"
                        id="rn"
                        value="${x.ret ? x.den : c0.den}"
                    >
                </div>

                <div>
                    <label class="fl" for="rt">Keterangan Denda</label>
                    <input
                        id="rt"
                        value="${x.ret ? (x.ket || c0.k) : c0.k}"
                    >
                </div>
            </div>

            <p class="hint">
                Otomatis dihitung Rp 10.000/hari jika telat ≥ 3 hari.
                <br>
                Admin dapat mengisi denda manual kapan saja.
            </p>

            <label class="fl" for="rc">
                Catatan Pemeriksaan Admin Tambahan
            </label>

            <textarea
                id="rc"
                placeholder="Catatan opsional mengenai kelengkapan komponen, nomor seri, atau kartu kendali..."
            >${x.cat || ''}</textarea>
        `,

        onOk() {
            const d = $('#rd').value;

            $('#rd').classList.toggle('err', !d);

            if (!d) {
                toast('Tanggal pengembalian wajib diisi');
                return false;
            }

            return o.save({
                id: x.id,
                ret: d,
                den: Math.max(0, +$('#rn').value || 0),
                ket: $('#rt').value,
                kond: $('#rk').value,
                cat: $('#rc').value
            });
        }
    });

    $('#rd').onchange = e => {
        if (!e.target.value) return;

        const c = calc(e.target.value);

        $('#ra').innerHTML = alertH(c);
        $('#rn').value = c.den;
        $('#rt').value = c.k;
    };
}
