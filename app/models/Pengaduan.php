<?php
/** Model: tabel pengaduan */
class Pengaduan {
    // ----- sisi mahasiswa -----
    static function byMahasiswa(int $idMhs, string $status = '', string $q = ''): array {
        $sql = 'SELECT p.*, (SELECT COUNT(*) FROM tanggapan t WHERE t.id_pengaduan=p.id_pengaduan) jml_tanggapan FROM pengaduan p WHERE p.id_mahasiswa=?';
        $a = [$idMhs];
        if ($status !== '') { $sql .= ' AND p.status_pengaduan=?'; $a[] = $status; }
        if ($q !== '')      { $sql .= ' AND p.deskripsi_pengaduan LIKE ?'; $a[] = '%' . $q . '%'; }
        $s = db()->prepare($sql . ' ORDER BY p.tanggal DESC, p.id_pengaduan DESC'); $s->execute($a); return $s->fetchAll();
    }
    static function hitung(int $idMhs): array {
        $s = db()->prepare('SELECT status_pengaduan s, COUNT(*) n FROM pengaduan WHERE id_mahasiswa=? GROUP BY status_pengaduan');
        $s->execute([$idMhs]);
        $r = ['Semua' => 0, 'Menunggu' => 0, 'Sedang diproses' => 0, 'Selesai' => 0];
        foreach ($s->fetchAll() as $x) { $r[$x['s']] = (int)$x['n']; $r['Semua'] += (int)$x['n']; }
        return $r;
    }
    static function findMilik(int $id, int $idMhs): ?array {
        $s = db()->prepare('SELECT p.*, a.nama_admin FROM pengaduan p LEFT JOIN admin a ON a.id_admin=p.id_admin WHERE p.id_pengaduan=? AND p.id_mahasiswa=?');
        $s->execute([$id, $idMhs]); return $s->fetch() ?: null;
    }
    static function create(int $idMhs, string $deskripsi, ?string $foto, string $tanggal): int {
        $s = db()->prepare("INSERT INTO pengaduan (id_mahasiswa, deskripsi_pengaduan, foto_pengaduan, tanggal, status_pengaduan) VALUES (?,?,?,?, 'Menunggu')");
        $s->execute([$idMhs, $deskripsi, $foto, $tanggal]); return (int) db()->lastInsertId();
    }
    // ----- sisi admin -----
    private static function baris(array $r): array {
        $r['s'] = $r['st'] === 'Sedang diproses' ? 'Diproses' : $r['st']; unset($r['st']);
        $r['t'] = array_map(fn($t) => [$t['admin'], $t['tgl'], $t['isi']], Tanggapan::byPengaduan((int)$r['id']));
        return $r;
    }
    private const SEL = 'SELECT p.id_pengaduan id, m.nama_mahasiswa n, m.nim, p.tanggal tgl, p.deskripsi_pengaduan d, p.foto_pengaduan foto, p.status_pengaduan st, a.nama_admin admin
                         FROM pengaduan p JOIN mahasiswa m ON m.id_mahasiswa=p.id_mahasiswa LEFT JOIN admin a ON a.id_admin=p.id_admin';
    static function semua(): array {
        return array_map([self::class, 'baris'], db()->query(self::SEL . ' ORDER BY p.tanggal DESC, p.id_pengaduan DESC')->fetchAll());
    }
    static function baca(int $id): ?array {
        $s = db()->prepare(self::SEL . ' WHERE p.id_pengaduan=?'); $s->execute([$id]);
        $r = $s->fetch(); return $r ? self::baris($r) : null;
    }
    static function tindak(int $id, int $idAdmin, string $status): void {
        db()->prepare('UPDATE pengaduan SET status_pengaduan=?, id_admin=? WHERE id_pengaduan=?')->execute([$status, $idAdmin, $id]);
    }
    static function stat(): array {
        $r = db()->query("SELECT COUNT(*) total, SUM(status_pengaduan='Menunggu') baru FROM pengaduan")->fetch();
        return ['total' => (int)$r['total'], 'baru' => (int)$r['baru']];
    }
}
