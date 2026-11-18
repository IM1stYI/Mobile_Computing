<?php
/** Model: tabel peminjaman */
class Peminjaman {
    // ----- sisi mahasiswa -----
    static function byMahasiswa(int $idMhs, string $filter = '', string $q = ''): array {
        $sql = 'SELECT * FROM peminjaman WHERE id_mahasiswa=?'; $a = [$idMhs];
        if ($filter === 'aktif')   $sql .= " AND status_peminjaman IN ('Menunggu Konfirmasi','Disetujui','Dipinjam')";
        if ($filter === 'selesai') $sql .= " AND status_peminjaman='Telah Dikembalikan'";
        if ($q !== '') { $sql .= ' AND nama_peminjaman LIKE ?'; $a[] = '%' . $q . '%'; }
        $s = db()->prepare($sql . ' ORDER BY tgl_peminjaman DESC, id_peminjaman DESC'); $s->execute($a); return $s->fetchAll();
    }
    static function hitung(int $idMhs): array {
        $s = db()->prepare("SELECT COUNT(*) semua, COALESCE(SUM(status_peminjaman IN ('Menunggu Konfirmasi','Disetujui','Dipinjam')),0) aktif,
                            COALESCE(SUM(status_peminjaman='Telah Dikembalikan'),0) selesai, COALESCE(SUM(denda),0) denda FROM peminjaman WHERE id_mahasiswa=?");
        $s->execute([$idMhs]); return array_map('intval', $s->fetch());
    }
    static function findMilik(int $id, int $idMhs): ?array {
        $s = db()->prepare('SELECT p.*, a.nama_admin FROM peminjaman p LEFT JOIN admin a ON a.id_admin=p.id_admin WHERE p.id_peminjaman=? AND p.id_mahasiswa=?');
        $s->execute([$id, $idMhs]); return $s->fetch() ?: null;
    }
    static function create(int $idMhs, string $nama, int $jumlah, string $pinjam, string $batas, string $keperluan, ?string $foto): int {
        $s = db()->prepare("INSERT INTO peminjaman (id_mahasiswa, nama_peminjaman, foto_fisik_barang, tgl_peminjaman, batas_kembali, status_peminjaman, keterangan, jumlah)
                            VALUES (?,?,?,?,?, 'Menunggu Konfirmasi', ?, ?)");
        $s->execute([$idMhs, $nama, $foto, $pinjam, $batas, $keperluan, $jumlah]); return (int) db()->lastInsertId();
    }
    // ----- sisi admin -----
    private const SEL = 'SELECT p.id_peminjaman id, m.nama_mahasiswa n, m.nim, p.nama_peminjaman a, p.keterangan k, p.jumlah q, p.tgl_peminjaman p, p.batas_kembali t,
                         p.tgl_pengembalian_aktual ret, p.status_peminjaman st, p.kondisi_peminjaman kond, p.denda den, p.keterangan_denda ket, p.catatan_admin cat,
                         p.foto_fisik_barang foto, a.nama_admin admin
                         FROM peminjaman p JOIN mahasiswa m ON m.id_mahasiswa=p.id_mahasiswa LEFT JOIN admin a ON a.id_admin=p.id_admin';
    private static function baris(array $r): array {
        $r['s'] = array_search($r['st'], STATUS_PINJAM, true) ?: 'menunggu'; unset($r['st']);
        $r['q'] = (int)$r['q']; $r['den'] = (int)$r['den']; return $r;
    }
    static function semua(): array {
        return array_map([self::class, 'baris'], db()->query(self::SEL . ' ORDER BY p.tgl_peminjaman DESC, p.id_peminjaman DESC')->fetchAll());
    }
    static function baca(int $id): ?array {
        $s = db()->prepare(self::SEL . ' WHERE p.id_peminjaman=?'); $s->execute([$id]);
        $r = $s->fetch(); return $r ? self::baris($r) : null;
    }
    static function setStatus(int $id, int $idAdmin, string $statusDb, ?string $catatan = null): void {
        db()->prepare('UPDATE peminjaman SET status_peminjaman=?, id_admin=?, catatan_admin=COALESCE(?, catatan_admin) WHERE id_peminjaman=?')
            ->execute([$statusDb, $idAdmin, $catatan, $id]);
    }
    static function kembalikan(int $id, int $idAdmin, string $tglAktual, string $kondisi, int $denda, string $ketDenda, string $catatan): void {
        db()->prepare("UPDATE peminjaman SET status_peminjaman='Telah Dikembalikan', id_admin=?, tgl_pengembalian_aktual=?, kondisi_peminjaman=?, denda=?, keterangan_denda=?, catatan_admin=? WHERE id_peminjaman=?")
            ->execute([$idAdmin, $tglAktual, $kondisi, $denda, $ketDenda, $catatan, $id]);
    }
    static function stat(): array {
        $r = db()->query("SELECT COUNT(*) total, COALESCE(SUM(status_peminjaman IN ('Disetujui','Dipinjam')),0) jalan, COALESCE(SUM(denda),0) denda FROM peminjaman")->fetch();
        return ['total' => (int)$r['total'], 'jalan' => (int)$r['jalan'], 'denda' => (int)$r['denda']];
    }
    static function belumKembali(): int {
        return (int) db()->query("SELECT COUNT(*) FROM peminjaman WHERE status_peminjaman='Dipinjam'")->fetchColumn();
    }
}
