<?php
declare(strict_types=1);

namespace App\Services;

use PDO;


class AttendanceService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Record attendance for a single user on a specific date
     *
     * @param int $userId
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @param bool $isPresent
     * @param string|null $notes
     * @return bool Success
     */
    public function recordAttendance(int $userId, int $messId, string $attendanceDate, bool $isPresent, ?string $notes = null): bool
    {
        // Check if record exists
        $stmt = $this->pdo->prepare("
            SELECT id FROM attendance
            WHERE user_id = ? AND mess_id = ? AND attendance_date = ?
        ");
        $stmt->execute([$userId, $messId, $attendanceDate]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing record
            $stmt = $this->pdo->prepare("
                UPDATE attendance
                SET is_present = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$isPresent ? 1 : 0, $notes, $existing['id']]);
        } else {
            // Insert new record
            $stmt = $this->pdo->prepare("
                INSERT INTO attendance (user_id, mess_id, attendance_date, is_present, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([$userId, $messId, $attendanceDate, $isPresent ? 1 : 0, $notes]);
        }
    }

    /**
     * Batch mark multiple users as present on a specific date
     *
     * @param array $userIds Array of user IDs
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @param string|null $notes Optional note for all records
     * @return int Count of successfully marked users
     */
    public function batchMarkPresent(array $userIds, int $messId, string $attendanceDate, ?string $notes = null): int
    {
        $successCount = 0;
        $this->pdo->beginTransaction();

        try {
            foreach ($userIds as $userId) {
                if ($this->recordAttendance($userId, $messId, $attendanceDate, true, $notes)) {
                    $successCount++;
                }
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return 0;
        }

        return $successCount;
    }

    /**
     * Batch mark multiple users as absent on a specific date
     *
     * @param array $userIds Array of user IDs
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @param string|null $notes Optional note for all records
     * @return int Count of successfully marked users
     */
    public function batchMarkAbsent(array $userIds, int $messId, string $attendanceDate, ?string $notes = null): int
    {
        $successCount = 0;
        $this->pdo->beginTransaction();

        try {
            foreach ($userIds as $userId) {
                if ($this->recordAttendance($userId, $messId, $attendanceDate, false, $notes)) {
                    $successCount++;
                }
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return 0;
        }

        return $successCount;
    }

    /**
     * Get attendance records for a specific date range
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Attendance records grouped by date
     */
    public function getAttendanceRecords(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id,
                a.user_id,
                u.full_name,
                u.email,
                a.attendance_date,
                a.is_present,
                a.notes,
                a.created_at,
                a.updated_at
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.id
            WHERE a.mess_id = ? 
            AND a.attendance_date BETWEEN ? AND ?
            ORDER BY a.attendance_date DESC, u.full_name ASC
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance for a specific date
     *
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @return array Attendance records for that day
     */
    public function getAttendanceByDate(int $messId, string $attendanceDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id,
                a.user_id,
                u.full_name,
                u.email,
                a.is_present,
                a.notes
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.id
            WHERE a.mess_id = ? 
            AND a.attendance_date = ?
            ORDER BY u.full_name ASC
        ");
        
        $stmt->execute([$messId, $attendanceDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance statistics for a user in a date range
     *
     * @param int $userId
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Statistics including present/absent counts and percentage
     */
    public function getUserAttendanceStats(int $userId, int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN is_present = 1 THEN 1 ELSE 0 END) as days_present,
                SUM(CASE WHEN is_present = 0 THEN 1 ELSE 0 END) as days_absent
            FROM attendance
            WHERE user_id = ? 
            AND mess_id = ?
            AND attendance_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$userId, $messId, $startDate, $endDate]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stats || $stats['total_days'] == 0) {
            return [
                'total_days' => 0,
                'days_present' => 0,
                'days_absent' => 0,
                'attendance_percentage' => 0.0,
            ];
        }

        $percentage = round(($stats['days_present'] / $stats['total_days']) * 100, 2);

        return [
            'total_days' => (int) $stats['total_days'],
            'days_present' => (int) $stats['days_present'],
            'days_absent' => (int) $stats['days_absent'],
            'attendance_percentage' => $percentage,
        ];
    }

    /**
     * Get attendance statistics for all members of a mess
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Array of user statistics sorted by attendance percentage descending
     */
    public function getMessAttendanceStats(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.full_name,
                u.email,
                COUNT(*) as total_days,
                SUM(CASE WHEN is_present = 1 THEN 1 ELSE 0 END) as days_present,
                SUM(CASE WHEN is_present = 0 THEN 1 ELSE 0 END) as days_absent,
                ROUND((SUM(CASE WHEN is_present = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.id
            WHERE a.mess_id = ? 
            AND a.attendance_date BETWEEN ? AND ?
            GROUP BY a.user_id, u.id, u.full_name, u.email
            ORDER BY attendance_percentage DESC, u.full_name ASC
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a user is marked present for a specific date
     *
     * @param int $userId
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @return bool
     */
    public function isUserPresent(int $userId, int $messId, string $attendanceDate): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT is_present FROM attendance
            WHERE user_id = ? AND mess_id = ? AND attendance_date = ?
        ");
        $stmt->execute([$userId, $messId, $attendanceDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['is_present'] == 1;
    }

    /**
     * Get present count for a specific date
     *
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @return int Count of members marked present
     */
    public function getPresentCount(int $messId, string $attendanceDate): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM attendance
            WHERE mess_id = ? 
            AND attendance_date = ?
            AND is_present = 1
        ");
        
        $stmt->execute([$messId, $attendanceDate]);
        return (int) ($stmt->fetchColumn() ?? 0);
    }

    /**
     * Get absent count for a specific date
     *
     * @param int $messId
     * @param string $attendanceDate Format: Y-m-d
     * @return int Count of members marked absent
     */
    public function getAbsentCount(int $messId, string $attendanceDate): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM attendance
            WHERE mess_id = ? 
            AND attendance_date = ?
            AND is_present = 0
        ");
        
        $stmt->execute([$messId, $attendanceDate]);
        return (int) ($stmt->fetchColumn() ?? 0);
    }

    /**
     * Delete an attendance record
     *
     * @param int $attendanceId
     * @return bool Success
     */
    public function deleteAttendance(int $attendanceId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM attendance WHERE id = ?");
        return $stmt->execute([$attendanceId]);
    }
}
