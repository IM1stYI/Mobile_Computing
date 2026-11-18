<?php
/** Model: tabel admin */
class Admin {
    static function find(int $id): ?array {
        $s = db()->prepare('SELECT * FROM admin WHERE id_admin=?'); $s->execute([$id]); return $s->fetch() ?: null;
    }
    static function login(string $username, string $pw): ?array {
        $s = db()->prepare('SELECT * FROM admin WHERE username=?'); $s->execute([$username]);
        $u = $s->fetch();
        return ($u && password_verify($pw, $u['password'])) ? $u : null;
    }
    static function all(): array {
        return db()->query('SELECT id_admin id, username, nama_admin nama FROM admin ORDER BY id_admin')->fetchAll();
    }
    static function usernameAda(string $u): bool {
        $s = db()->prepare('SELECT 1 FROM admin WHERE username=?'); $s->execute([$u]); return (bool) $s->fetchColumn();
    }
    static function create(string $username, string $nama, string $pw): int {
        $s = db()->prepare('INSERT INTO admin (username, password, nama_admin) VALUES (?,?,?)');
        $s->execute([$username, password_hash($pw, PASSWORD_DEFAULT), $nama]);
        return (int) db()->lastInsertId();
    }
    static function updateNama(int $id, string $nama): void {
        db()->prepare('UPDATE admin SET nama_admin=? WHERE id_admin=?')->execute([$nama, $id]);
    }
    static function updatePassword(int $id, string $pw): void {
        db()->prepare('UPDATE admin SET password=? WHERE id_admin=?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    }
}
