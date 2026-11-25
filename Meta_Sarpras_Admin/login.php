<?php
require __DIR__ . '/../app/bootstrap.php';
if (admin_user()) redirect('dashboard.php');
$err = ''; $userLama = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userLama = trim($_POST['username'] ?? ''); $pw = $_POST['password'] ?? '';
    if (!csrf_ok($_POST['csrf'] ?? null)) { $err = 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.'; }
    elseif ($userLama === '' || $pw === '') { $err = 'Username dan password wajib diisi.'; }
    elseif ($a = User::loginAdmin($userLama, $pw)) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$a['id_user'];
        redirect('dashboard.php');
    } else { $err = 'Username atau password salah.'; }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="admin.css">
    <title>Login Admin - Panel Kelola Sarpras</title>
    <style>
        body {
            background: linear-gradient(135deg, #0d2a4f, #173d70);
            justify-content: center;
            align-items: center;
            padding: 20px
        }

        .lc {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px #0003;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center
        }

        .lg {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #0d2a4f;
            text-align: left
        }

        .lg img {
            width: 34px;
            height: 38px
        }

        .lg b {
            display: block;
            font-size: 16px;
            line-height: 1.1
        }

        .lg span {
            font-size: 12px;
            font-weight: 500
        }

        .rb {
            background: #f8fafc;
            color: #1e3a8a;
            border: 1px solid #e2e8f0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            margin-bottom: 20px
        }

        .lc h2 {
            font-size: 20px;
            color: #112a4f;
            margin-bottom: 6px
        }

        .sub {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 28px
        }

        form {
            width: 100%;
            text-align: left
        }

        .fgp {
            margin-bottom: 20px
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px
        }

        label i {
            color: #ef4444;
            font-style: normal
        }

        .in {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            outline: 0;
            transition: .2s
        }

        .in:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px #1e3a8a1a
        }

        .in.err {
            border-color: #ef4444
        }

        .pw {
            position: relative
        }

        .pw button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 0;
            font-size: 15px
        }

        .em {
            color: #dc2626;
            font-size: 12px;
            margin: -8px 0 12px;
            display: none
        }

        .em.show {
            display: block
        }

        .sb {
            width: 100%;
            padding: 14px;
            background: #112a4f;
            color: #fff;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px #112a4f33;
            transition: .2s
        }

        .sb:hover {
            opacity: .9
        }

        hr {
            width: 100%;
            border: 0;
            border-top: 1px solid #f3f4f6;
            margin-bottom: 20px
        }

        .bk {
            font-size: 11px;
            color: #6b7280;
            font-weight: 500
        }

        .bk:hover {
            color: #112a4f
        }
    </style>
</head>

<body>
    <div class="lc">
        <div class="lg"><img
                src="logo.png"
                alt="Logo Politeknik Meta">
            <div><b>Politeknik</b><span>META Industri</span></div>
        </div>
        <div class="rb"><span style="color:#dc2626">🛡️</span> Akses Terbatas • Staf Administrator Sarpras</div>
        <h2>Panel Kelola Sarpras</h2>
        <p class="sub">Silakan autentikasi untuk mengelola pengaduan &amp; aset</p>
        <form id="f" method="post" action="login.php" novalidate>
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <div class="fgp"><label for="u">Username Administrator <i>*</i></label><input id="u" name="username" class="in" placeholder="Masukkan username admin" autocomplete="username" value="<?= h($userLama) ?>" autofocus required></div>
            <div class="fgp"><label for="p">Password <i>*</i></label>
                <div class="pw"><input id="p" name="password" type="password" class="in" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" id="eye"
                        aria-label="Tampilkan password">👁️</button></div>
            </div>
            <p class="em<?= $err ? ' show' : '' ?>" id="em" role="alert"><?= h($err) ?></p>
            <button class="sb" type="submit">🔓 Masuk sebagai Administrator</button>
        </form>
        <p class="hint" style="text-align:center;margin:-8px 0 16px;font-size:11px;color:#6b7280">Akun demo: <b>zakiajja</b> / <b>zaki123</b></p>
        <hr><a class="bk" href="../Meta_Sarpras_PWA/login.php" id="bk">← Kembali ke Portal Mahasiswa</a>
    </div>
    <div class="toast" id="toast"></div>
    <script>
        const $ = s => document.querySelector(s);
        $('#eye').onclick = () => { const p = $('#p'); p.type = p.type == 'password' ? 'text' : 'password' };
    </script>
</body>

</html>