<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use RuntimeException;

final class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function findByEmailWithRole(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.full_name, u.email, u.password_hash, u.status, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public function findByIdWithRole(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.full_name, u.email, u.password_hash, u.status, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $fullName, string $email, string $passwordHash, string $roleName): int
    {
        $roleId = $this->getRoleIdByName($roleName);
        if ($roleId === null) {
            throw new RuntimeException('Invalid role.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO users (role_id, full_name, email, password_hash)
             VALUES (:role_id, :full_name, :email, :password_hash)'
        );
        $stmt->execute([
            'role_id' => $roleId,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, string $email): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET full_name = :full_name,
                 email = :email
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'full_name' => $fullName,
            'email' => $email,
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET password_hash = :password_hash
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    public function emailExistsExcept(string $email, int $excludeId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM users
             WHERE email = :email AND id != :exclude_id
             LIMIT 1'
        );
        $stmt->execute([
            'email' => $email,
            'exclude_id' => $excludeId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    private function getRoleIdByName(string $roleName): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM roles WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $roleName]);
        $roleId = $stmt->fetchColumn();

        return $roleId !== false ? (int) $roleId : null;
    }
}
