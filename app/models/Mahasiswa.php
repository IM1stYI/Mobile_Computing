<?php
/** Model: tabel mahasiswa */
class Mahasiswa {
    static function find(int $id): ?array {
        $s = db()->prepare('SELECT * FROM mahasiswa WHERE id_mahasiswa=?'); $s->execute([$id]); return $s->fetch() ?: null;
    }
    static function login(string $nim, string $pw): ?array {
        $s = db()->prepare('SELECT * FROM mahasiswa WHERE nim=?'); $s->execute([$nim]);
        $u = $s->fetch();
        return ($u && password_verify($pw, $u['password'])) ? $u : null;
    }
    static function all(): array {
        return db()->query('SELECT id_mahasiswa id, nim, nama_mahasiswa nama FROM mahasiswa ORDER BY nama_mahasiswa')->fetchAll();
    }
    static function count(): int { return (int) db()->query('SELECT COUNT(*) FROM mahasiswa')->fetchColumn(); }
    static function create(string $nim, string $nama, string $pw): int {
        $s = db()->prepare('INSERT INTO mahasiswa (nama_mahasiswa, nim, password) VALUES (?,?,?)');
        $s->execute([$nama, $nim, password_hash($pw, PASSWORD_DEFAULT)]);
        return (int) db()->lastInsertId();
    }
    static function nimAda(string $nim): bool {
        $s = db()->prepare('SELECT 1 FROM mahasiswa WHERE nim=?'); $s->execute([$nim]); return (bool) $s->fetchColumn();
    }
    static function delete(int $id): void { db()->prepare('DELETE FROM mahasiswa WHERE id_mahasiswa=?')->execute([$id]); }
    static function resetPassword(int $id, string $pw): void {
        db()->prepare('UPDATE mahasiswa SET password=? WHERE id_mahasiswa=?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    }
}
