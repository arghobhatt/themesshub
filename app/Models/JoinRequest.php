<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use Throwable;

final class JoinRequest
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function existsForUser(int $messId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM join_requests
             WHERE mess_id = :mess_id AND user_id = :user_id
             LIMIT 1'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function hasActiveMembership(int $messId, int $userId): bool
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

    public function create(int $messId, int $userId, ?string $message): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO join_requests (mess_id, user_id, message)
             VALUES (:mess_id, :user_id, :message)'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
            'message' => $message,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function listPendingForManager(int $managerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT jr.id,
                    jr.mess_id,
                    jr.user_id,
                    jr.message,
                    jr.created_at,
                    u.full_name AS seeker_name,
                    u.email AS seeker_email,
                    m.name AS mess_name,
                    m.location AS mess_location
             FROM join_requests jr
             JOIN users u ON u.id = jr.user_id
             JOIN messes m ON m.id = jr.mess_id
             LEFT JOIN mess_memberships mm
               ON mm.mess_id = m.id
              AND mm.user_id = :manager_id
              AND mm.role_in_mess = "manager"
              AND mm.status = "active"
             WHERE jr.status = "pending"
               AND (m.created_by = :manager_id OR mm.id IS NOT NULL)
             ORDER BY jr.created_at DESC'
        );
        $stmt->execute(['manager_id' => $managerId]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, mess_id, user_id, status
             FROM join_requests
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function approve(int $requestId, int $decidedBy): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT id, mess_id, user_id, status
                 FROM join_requests
                 WHERE id = :id
                 FOR UPDATE'
            );
            $stmt->execute(['id' => $requestId]);
            $request = $stmt->fetch();

            if ($request === false || $request['status'] !== 'pending') {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare(
                'UPDATE join_requests
                 SET status = "approved",
                     decided_by = :decided_by,
                     decided_at = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $requestId,
                'decided_by' => $decidedBy,
            ]);

            $stmt = $this->db->prepare(
                'INSERT INTO mess_memberships (mess_id, user_id, role_in_mess, joined_on, status)
                 VALUES (:mess_id, :user_id, :role_in_mess, CURDATE(), "active")
                 ON DUPLICATE KEY UPDATE
                   status = "active",
                   left_on = NULL,
                   role_in_mess = VALUES(role_in_mess)'
            );
            $stmt->execute([
                'mess_id' => $request['mess_id'],
                'user_id' => $request['user_id'],
                'role_in_mess' => 'member',
            ]);

            
            $stmt = $this->db->prepare(
                'UPDATE users
                 SET role_id = (SELECT id FROM roles WHERE name = "member")
                 WHERE id = :user_id
                 AND role_id = (SELECT id FROM roles WHERE name = "seeker")'
            );
            $stmt->execute(['user_id' => $request['user_id']]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function reject(int $requestId, int $decidedBy): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE join_requests
             SET status = "rejected",
                 decided_by = :decided_by,
                 decided_at = NOW()
             WHERE id = :id AND status = "pending"'
        );
        $stmt->execute([
            'id' => $requestId,
            'decided_by' => $decidedBy,
        ]);

        return $stmt->rowCount() > 0;
    }
}
