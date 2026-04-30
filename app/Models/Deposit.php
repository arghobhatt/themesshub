<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Deposit
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function create(
        int $messId,
        int $userId,
        string $depositedOn,
        string $amount,
        ?string $method,
        ?string $reference
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO deposits (mess_id, user_id, amount, deposited_on, method, reference)
             VALUES (:mess_id, :user_id, :amount, :deposited_on, :method, :reference)'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
            'amount' => $amount,
            'deposited_on' => $depositedOn,
            'method' => $method,
            'reference' => $reference,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function listRecentForManager(int $managerId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->prepare(
            'SELECT d.id,
                    d.deposited_on,
                    d.amount,
                    d.method,
                    d.reference,
                    u.full_name AS member_name,
                    m.name AS mess_name
             FROM deposits d
             JOIN users u ON u.id = d.user_id
             JOIN messes m ON m.id = d.mess_id
             LEFT JOIN mess_memberships mm
               ON mm.mess_id = m.id
              AND mm.user_id = :manager_id
              AND mm.role_in_mess = "manager"
              AND mm.status = "active"
             WHERE m.created_by = :manager_id OR mm.id IS NOT NULL
             ORDER BY d.deposited_on DESC, d.id DESC
               LIMIT :limit'
        );
           $stmt->bindValue('manager_id', $managerId, PDO::PARAM_INT);
           $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
           $stmt->execute();

        return $stmt->fetchAll();
    }

    public function listForUser(int $userId, int $limit = 20): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->prepare(
            'SELECT d.id,
                    d.deposited_on,
                    d.amount,
                    d.method,
                    d.reference,
                    m.name AS mess_name
             FROM deposits d
             JOIN messes m ON m.id = d.mess_id
             WHERE d.user_id = :user_id
             ORDER BY d.deposited_on DESC, d.id DESC
               LIMIT :limit'
        );
           $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
           $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
           $stmt->execute();

        return $stmt->fetchAll();
    }
}
