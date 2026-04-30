<?php
declare(strict_types=1);

namespace App\Services;

use PDO;


class MealCompositionCalculator
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Calculate total meal units for a single meal entry
     * A "meal unit" = 1 breakfast + 1 lunch + 1 dinner (each is 1 unit if taken)
     *
     * @param bool $hasBreakfast
     * @param bool $hasLunch
     * @param bool $hasDinner
     * @param float $guestMeals
     * @return float Total meal units for this entry
     */
    public static function calculateMealUnits(bool $hasBreakfast, bool $hasLunch, bool $hasDinner, float $guestMeals = 0): float
    {
        $units = 0;
        if ($hasBreakfast) $units += 1;
        if ($hasLunch) $units += 1;
        if ($hasDinner) $units += 1;
        return $units + $guestMeals;
    }

    /**
     * Get detailed meal composition for a user on a specific date
     *
     * @param int $userId
     * @param int $messId
     * @param string $mealDate Format: Y-m-d
     * @return array Meal composition with breakdown
     */
    public function getUserMealComposition(int $userId, int $messId, string $mealDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                id,
                has_breakfast,
                has_lunch,
                has_dinner,
                guest_meals,
                notes
            FROM meal_entries
            WHERE user_id = ? 
            AND mess_id = ?
            AND meal_date = ?
            LIMIT 1
        ");
        
        $stmt->execute([$userId, $messId, $mealDate]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            return [
                'found' => false,
                'breakfast' => false,
                'lunch' => false,
                'dinner' => false,
                'guest_meals' => 0,
                'total_units' => 0,
                'notes' => '',
            ];
        }

        $totalUnits = self::calculateMealUnits(
            (bool) $entry['has_breakfast'],
            (bool) $entry['has_lunch'],
            (bool) $entry['has_dinner'],
            (float) $entry['guest_meals']
        );

        return [
            'found' => true,
            'id' => $entry['id'],
            'breakfast' => (bool) $entry['has_breakfast'],
            'lunch' => (bool) $entry['has_lunch'],
            'dinner' => (bool) $entry['has_dinner'],
            'guest_meals' => (float) $entry['guest_meals'],
            'total_units' => $totalUnits,
            'notes' => $entry['notes'] ?? '',
        ];
    }

    /**
     * Get meal composition summary for a mess on a specific date
     *
     * @param int $messId
     * @param string $mealDate Format: Y-m-d
     * @return array Summary including totals and per-meal counts
     */
    public function getMessMealCompositionByDate(int $messId, string $mealDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as entries_count,
                SUM(CASE WHEN has_breakfast = 1 THEN 1 ELSE 0 END) as breakfast_count,
                SUM(CASE WHEN has_lunch = 1 THEN 1 ELSE 0 END) as lunch_count,
                SUM(CASE WHEN has_dinner = 1 THEN 1 ELSE 0 END) as dinner_count,
                COALESCE(SUM(guest_meals), 0) as total_guest_meals,
                ROUND(AVG(guest_meals), 2) as avg_guest_meals
            FROM meal_entries
            WHERE mess_id = ? 
            AND meal_date = ?
        ");
        
        $stmt->execute([$messId, $mealDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || $result['entries_count'] == 0) {
            return [
                'date' => $mealDate,
                'entries_count' => 0,
                'breakfast_count' => 0,
                'lunch_count' => 0,
                'dinner_count' => 0,
                'total_guest_meals' => 0.0,
                'avg_guest_meals' => 0.0,
                'total_meal_units' => 0.0,
            ];
        }

        $breakfastCount = (int) $result['breakfast_count'];
        $lunchCount = (int) $result['lunch_count'];
        $dinnerCount = (int) $result['dinner_count'];
        $guestMeals = (float) $result['total_guest_meals'];

        $totalMealUnits = $breakfastCount + $lunchCount + $dinnerCount + $guestMeals;

        return [
            'date' => $mealDate,
            'entries_count' => (int) $result['entries_count'],
            'breakfast_count' => $breakfastCount,
            'lunch_count' => $lunchCount,
            'dinner_count' => $dinnerCount,
            'total_guest_meals' => $guestMeals,
            'avg_guest_meals' => (float) $result['avg_guest_meals'],
            'total_meal_units' => $totalMealUnits,
        ];
    }

    /**
     * Get meal composition trends for a date range
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Daily breakdown of meal composition
     */
    public function getMealCompositionTrend(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                meal_date,
                COUNT(*) as entries_count,
                SUM(CASE WHEN has_breakfast = 1 THEN 1 ELSE 0 END) as breakfast_count,
                SUM(CASE WHEN has_lunch = 1 THEN 1 ELSE 0 END) as lunch_count,
                SUM(CASE WHEN has_dinner = 1 THEN 1 ELSE 0 END) as dinner_count,
                COALESCE(SUM(guest_meals), 0) as total_guest_meals
            FROM meal_entries
            WHERE mess_id = ? 
            AND meal_date BETWEEN ? AND ?
            GROUP BY meal_date
            ORDER BY meal_date DESC
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $trends = [];
        foreach ($records as $record) {
            $breakfastCount = (int) $record['breakfast_count'];
            $lunchCount = (int) $record['lunch_count'];
            $dinnerCount = (int) $record['dinner_count'];
            $guestMeals = (float) $record['total_guest_meals'];
            $totalUnits = $breakfastCount + $lunchCount + $dinnerCount + $guestMeals;

            $trends[] = [
                'date' => $record['meal_date'],
                'entries_count' => (int) $record['entries_count'],
                'breakfast_count' => $breakfastCount,
                'lunch_count' => $lunchCount,
                'dinner_count' => $dinnerCount,
                'guest_meals' => $guestMeals,
                'total_units' => $totalUnits,
            ];
        }

        return $trends;
    }

    /**
     * Calculate meal type preferences for a user (which meals they typically take)
     *
     * @param int $userId
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Preferences with percentages
     */
    public function getUserMealPreferences(int $userId, int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_entries,
                SUM(CASE WHEN has_breakfast = 1 THEN 1 ELSE 0 END) as breakfast_entries,
                SUM(CASE WHEN has_lunch = 1 THEN 1 ELSE 0 END) as lunch_entries,
                SUM(CASE WHEN has_dinner = 1 THEN 1 ELSE 0 END) as dinner_entries,
                COALESCE(SUM(guest_meals), 0) as total_guest_meals,
                COALESCE(AVG(guest_meals), 0) as avg_guest_meals
            FROM meal_entries
            WHERE user_id = ? 
            AND mess_id = ?
            AND meal_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$userId, $messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || $result['total_entries'] == 0) {
            return [
                'total_entries' => 0,
                'breakfast_percentage' => 0.0,
                'lunch_percentage' => 0.0,
                'dinner_percentage' => 0.0,
                'avg_guest_meals' => 0.0,
            ];
        }

        $total = (int) $result['total_entries'];
        $breakfastPct = round(($result['breakfast_entries'] / $total) * 100, 2);
        $lunchPct = round(($result['lunch_entries'] / $total) * 100, 2);
        $dinnerPct = round(($result['dinner_entries'] / $total) * 100, 2);

        return [
            'total_entries' => $total,
            'breakfast_percentage' => $breakfastPct,
            'lunch_percentage' => $lunchPct,
            'dinner_percentage' => $dinnerPct,
            'avg_guest_meals' => round($result['avg_guest_meals'], 2),
        ];
    }

    /**
     * Get top meal composition stats for a mess
     * Shows which meals are most commonly taken
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Statistics for each meal type
     */
    public function getMessMealStats(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT user_id) as active_users,
                COUNT(*) as total_entries,
                SUM(CASE WHEN has_breakfast = 1 THEN 1 ELSE 0 END) as breakfast_total,
                SUM(CASE WHEN has_lunch = 1 THEN 1 ELSE 0 END) as lunch_total,
                SUM(CASE WHEN has_dinner = 1 THEN 1 ELSE 0 END) as dinner_total,
                COALESCE(SUM(guest_meals), 0) as total_guest_meals,
                ROUND(AVG(CASE WHEN has_breakfast = 1 THEN 1 ELSE 0 END), 3) as breakfast_participation_rate,
                ROUND(AVG(CASE WHEN has_lunch = 1 THEN 1 ELSE 0 END), 3) as lunch_participation_rate,
                ROUND(AVG(CASE WHEN has_dinner = 1 THEN 1 ELSE 0 END), 3) as dinner_participation_rate
            FROM meal_entries
            WHERE mess_id = ? 
            AND meal_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || $result['total_entries'] == 0) {
            return [
                'active_users' => 0,
                'total_entries' => 0,
                'breakfast_total' => 0,
                'lunch_total' => 0,
                'dinner_total' => 0,
                'guest_meals_total' => 0.0,
                'total_units' => 0.0,
            ];
        }

        $totalUnits = $result['breakfast_total'] + $result['lunch_total'] + $result['dinner_total'] + $result['total_guest_meals'];

        return [
            'active_users' => (int) $result['active_users'],
            'total_entries' => (int) $result['total_entries'],
            'breakfast_total' => (int) $result['breakfast_total'],
            'lunch_total' => (int) $result['lunch_total'],
            'dinner_total' => (int) $result['dinner_total'],
            'guest_meals_total' => (float) $result['total_guest_meals'],
            'breakfast_participation_rate' => round((float) $result['breakfast_participation_rate'] * 100, 2),
            'lunch_participation_rate' => round((float) $result['lunch_participation_rate'] * 100, 2),
            'dinner_participation_rate' => round((float) $result['dinner_participation_rate'] * 100, 2),
            'total_units' => $totalUnits,
        ];
    }

    /**
     * Update meal composition for a user on a specific date
     *
     * @param int $userId
     * @param int $messId
     * @param string $mealDate Format: Y-m-d
     * @param bool $hasBreakfast
     * @param bool $hasLunch
     * @param bool $hasDinner
     * @param float $guestMeals
     * @param string|null $notes
     * @return bool Success
     */
    public function updateMealComposition(
        int $userId,
        int $messId,
        string $mealDate,
        bool $hasBreakfast,
        bool $hasLunch,
        bool $hasDinner,
        float $guestMeals = 0,
        ?string $notes = null
    ): bool
    {
        // Check if entry exists
        $stmt = $this->pdo->prepare("
            SELECT id FROM meal_entries
            WHERE user_id = ? AND mess_id = ? AND meal_date = ?
        ");
        $stmt->execute([$userId, $messId, $mealDate]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update
            $stmt = $this->pdo->prepare("
                UPDATE meal_entries
                SET has_breakfast = ?, has_lunch = ?, has_dinner = ?, guest_meals = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([
                $hasBreakfast ? 1 : 0,
                $hasLunch ? 1 : 0,
                $hasDinner ? 1 : 0,
                $guestMeals,
                $notes,
                $existing['id']
            ]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare("
                INSERT INTO meal_entries (user_id, mess_id, meal_date, has_breakfast, has_lunch, has_dinner, guest_meals, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([
                $userId,
                $messId,
                $mealDate,
                $hasBreakfast ? 1 : 0,
                $hasLunch ? 1 : 0,
                $hasDinner ? 1 : 0,
                $guestMeals,
                $notes
            ]);
        }
    }
}
