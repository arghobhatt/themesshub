<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Complaint
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function create(int $userId, int $messId, string $title, string $description, string $priority = 'medium'): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO complaints (user_id, mess_id, title, description, priority, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 'open', NOW(), NOW())
        ");
        return $stmt->execute([$userId, $messId, $title, $description, $priority]);
    }

    
    public function getByMess(int $messId, int $limit = 20, int $offset = 0): array
    {
        $limit = max(1, min($limit, 50));
        $offset = max(0, $offset);

        $stmt = $this->pdo->prepare("
            SELECT c.*, u.full_name as user_name
            FROM complaints c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.mess_id = ?
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $messId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.full_name as user_name
            FROM complaints c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

   
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE complaints
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }

    
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM complaints
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }
}