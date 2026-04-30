<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Notice
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

  
    public function create(int $messId, int $createdBy, string $title, string $content, string $priority = 'normal'): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO notices (mess_id, created_by, title, content, priority, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$messId, $createdBy, $title, $content, $priority]);
    }

    
    public function getByMess(int $messId, int $limit = 20, int $offset = 0): array
    {
        $limit = max(1, min($limit, 50));
        $offset = max(0, $offset);

        $stmt = $this->pdo->prepare("
            SELECT n.*, u.full_name as created_by_name
            FROM notices n
            INNER JOIN users u ON n.created_by = u.id
            WHERE n.mess_id = ?
            ORDER BY n.created_at DESC
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
            SELECT n.*, u.full_name as created_by_name
            FROM notices n
            INNER JOIN users u ON n.created_by = u.id
            WHERE n.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

  
    public function getRecent(int $messId, int $limit = 5): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->pdo->prepare("
            SELECT n.*, u.full_name as created_by_name
            FROM notices n
            INNER JOIN users u ON n.created_by = u.id
            WHERE n.mess_id = ?
            ORDER BY n.created_at DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, $messId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getHighPriority(int $messId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT n.*, u.full_name as created_by_name
            FROM notices n
            INNER JOIN users u ON n.created_by = u.id
            WHERE n.mess_id = ? AND n.priority = 'high'
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$messId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function update(int $id, string $title, string $content, string $priority = 'normal'): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE notices
            SET title = ?, content = ?, priority = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$title, $content, $priority, $id]);
    }

   
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM notices WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
