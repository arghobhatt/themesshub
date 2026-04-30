<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Attendance
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    public function create(int $userId, int $messId, string $attendanceDate, bool $isPresent, ?string $notes = null): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO attendance (user_id, mess_id, attendance_date, is_present, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$userId, $messId, $attendanceDate, $isPresent ? 1 : 0, $notes]);
    }

   
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT a.*, u.full_name, m.name as mess_name
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN messes m ON a.mess_id = m.id
            ORDER BY a.attendance_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.full_name, m.name as mess_name
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN messes m ON a.mess_id = m.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

   
    public function update(int $id, bool $isPresent, ?string $notes = null): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE attendance
            SET is_present = ?, notes = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$isPresent ? 1 : 0, $notes, $id]);
    }

    
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM attendance WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
