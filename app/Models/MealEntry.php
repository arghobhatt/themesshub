<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class MealEntry
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function existsForUserDate(int $userId, string $mealDate): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM meal_entries
             WHERE user_id = :user_id AND meal_date = :meal_date
             LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'meal_date' => $mealDate,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(int $messId, int $userId, string $mealDate, string $mealsCount): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO meal_entries (mess_id, user_id, meal_date, meals_count)
             VALUES (:mess_id, :user_id, :meal_date, :meals_count)'
        );
        $stmt->execute([
            'mess_id' => $messId,
            'user_id' => $userId,
            'meal_date' => $mealDate,
            'meals_count' => $mealsCount,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function listRecentForUser(int $userId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        $stmt = $this->db->prepare(
            'SELECT me.id,
                    me.meal_date,
                    me.meals_count,
                    m.name AS mess_name,
                    m.location AS mess_location
             FROM meal_entries me
             JOIN messes m ON m.id = me.mess_id
             WHERE me.user_id = :user_id
             ORDER BY me.meal_date DESC, me.id DESC
               LIMIT :limit'
        );
           $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
           $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
           $stmt->execute();

        return $stmt->fetchAll();
    }
}
