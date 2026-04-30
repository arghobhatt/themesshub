<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Feedback
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function create(int $userId, int $messId, int $rating, string $category, string $comment = ''): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO feedback (user_id, mess_id, rating, category, comment, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$userId, $messId, $rating, $category, $comment]);
    }

    public function getByMess(int $messId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.full_name, u.email
            FROM feedback f
            INNER JOIN users u ON f.user_id = u.id
            WHERE f.mess_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$messId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatistics(int $messId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                category,
                rating,
                COUNT(*) as count,
                ROUND(AVG(rating), 2) as average_rating
            FROM feedback
            WHERE mess_id = ?
            GROUP BY category, rating
            ORDER BY category, rating DESC
        ");
        $stmt->execute([$messId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getAverageRatingByCategory(int $messId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                category,
                COUNT(*) as feedback_count,
                ROUND(AVG(rating), 2) as average_rating
            FROM feedback
            WHERE mess_id = ?
            GROUP BY category
            ORDER BY average_rating DESC
        ");
        $stmt->execute([$messId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getOverallAverageRating(int $messId): ?float
    {
        $stmt = $this->pdo->prepare("
            SELECT ROUND(AVG(rating), 2) as average
            FROM feedback
            WHERE mess_id = ?
        ");
        $stmt->execute([$messId]);
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.full_name
            FROM feedback f
            INNER JOIN users u ON f.user_id = u.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

   
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM feedback WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
