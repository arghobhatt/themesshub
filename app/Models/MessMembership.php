<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class MessMembership
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function create(int $messId, int $userId, string $role = 'member'): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO mess_memberships (mess_id, user_id, role_in_mess, joined_on)
             VALUES (:mess_id, :user_id, :role, CURDATE())'
        );
        return $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function listMembers(int $messId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.full_name, u.email, mm.role_in_mess
             FROM mess_memberships mm
             JOIN users u ON u.id = mm.user_id
             WHERE mm.mess_id = :mess_id AND mm.status = "active"
             ORDER BY u.full_name'
        );
        $stmt->execute(['mess_id' => $messId]);

        return $stmt->fetchAll();
    }

    public function isActiveMember(int $messId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM mess_memberships
             WHERE mess_id = :mess_id AND user_id = :user_id AND status = "active"
             LIMIT 1'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function listMessesForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.id, m.name, m.location, mm.role_in_mess
             FROM mess_memberships mm
             JOIN messes m ON m.id = mm.mess_id
             WHERE mm.user_id = :user_id AND mm.status = "active"
             ORDER BY m.name'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
