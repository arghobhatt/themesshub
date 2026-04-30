<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Expense
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function create(
        int $messId,
        int $purchaserId,
        string $expenseDate,
        string $amount,
        ?string $vendor,
        ?string $notes
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO expenses (mess_id, purchaser_id, expense_date, amount, vendor, notes)
             VALUES (:mess_id, :purchaser_id, :expense_date, :amount, :vendor, :notes)'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'purchaser_id' => $purchaserId,
            'expense_date' => $expenseDate,
            'amount' => $amount,
            'vendor' => $vendor,
            'notes' => $notes,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function listRecentForManager(int $managerId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->prepare(
            'SELECT e.id,
                    e.expense_date,
                    e.amount,
                    e.vendor,
                    e.notes,
                    u.full_name AS purchaser_name,
                    m.name AS mess_name
             FROM expenses e
             JOIN users u ON u.id = e.purchaser_id
             JOIN messes m ON m.id = e.mess_id
             LEFT JOIN mess_memberships mm
               ON mm.mess_id = m.id
              AND mm.user_id = :manager_id
              AND mm.role_in_mess = "manager"
              AND mm.status = "active"
             WHERE m.created_by = :manager_id OR mm.id IS NOT NULL
             ORDER BY e.expense_date DESC, e.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('manager_id', $managerId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
