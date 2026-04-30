<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class WeeklyMenu
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

   
    public function create(int $messId, int $dayOfWeek, string $breakfast = '', string $lunch = '', string $dinner = ''): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO weekly_menu (mess_id, day_of_week, breakfast, lunch, dinner, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$messId, $dayOfWeek, $breakfast, $lunch, $dinner]);
    }

  
    public function getByDay(int $messId, int $dayOfWeek): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM weekly_menu
            WHERE mess_id = ? AND day_of_week = ?
        ");
        $stmt->execute([$messId, $dayOfWeek]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

   
    public function getWeeklyMenu(int $messId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM weekly_menu
            WHERE mess_id = ?
            ORDER BY day_of_week ASC
        ");
        $stmt->execute([$messId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function update(int $id, string $breakfast = '', string $lunch = '', string $dinner = ''): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE weekly_menu
            SET breakfast = ?, lunch = ?, dinner = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$breakfast, $lunch, $dinner, $id]);
    }

    
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM weekly_menu WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
