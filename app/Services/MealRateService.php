<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

final class MealRateService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
    }

    public function calculateForMess(int $messId, ?string $startDate = null, ?string $endDate = null): array
    {
        $totalExpense = $this->sumExpenses($messId, $startDate, $endDate);
        $totalMeals = $this->sumMeals($messId, $startDate, $endDate);

        $rate = $totalMeals > 0.0 ? ($totalExpense / $totalMeals) : 0.0;

        return [
            'mess_id' => $messId,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_expense' => $totalExpense,
            'total_meals' => $totalMeals,
            'rate_per_meal' => $rate,
        ];
    }

    public function calculateForUserBalance(
        int $messId,
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $summary = $this->calculateForMess($messId, $startDate, $endDate);
        $userMeals = $this->sumUserMeals($messId, $userId, $startDate, $endDate);
        $userDeposits = $this->sumUserDeposits($messId, $userId, $startDate, $endDate);
        $balance = ($userMeals * $summary['rate_per_meal']) - $userDeposits;

        return array_merge($summary, [
            'user_meals' => $userMeals,
            'user_deposits' => $userDeposits,
            'balance' => $balance,
        ]);
    }

    private function sumExpenses(int $messId, ?string $startDate, ?string $endDate): float
    {
        $params = ['mess_id' => $messId];
        $range = $this->dateRangeClause('expense_date', $startDate, $endDate, $params);

        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS total
             FROM expenses
             WHERE mess_id = :mess_id' . $range
        );
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    private function sumMeals(int $messId, ?string $startDate, ?string $endDate): float
    {
        $params = ['mess_id' => $messId];
        $range = $this->dateRangeClause('meal_date', $startDate, $endDate, $params);

        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(meals_count), 0) AS total
             FROM meal_entries
             WHERE mess_id = :mess_id' . $range
        );
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    private function sumUserMeals(int $messId, int $userId, ?string $startDate, ?string $endDate): float
    {
        $params = [
            'mess_id' => $messId,
            'user_id' => $userId,
        ];
        $range = $this->dateRangeClause('meal_date', $startDate, $endDate, $params);

        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(meals_count), 0) AS total
             FROM meal_entries
             WHERE mess_id = :mess_id AND user_id = :user_id' . $range
        );
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    private function sumUserDeposits(int $messId, int $userId, ?string $startDate, ?string $endDate): float
    {
        $params = [
            'mess_id' => $messId,
            'user_id' => $userId,
        ];
        $range = $this->dateRangeClause('deposited_on', $startDate, $endDate, $params);

        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS total
             FROM deposits
             WHERE mess_id = :mess_id AND user_id = :user_id' . $range
        );
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    private function dateRangeClause(
        string $column,
        ?string $startDate,
        ?string $endDate,
        array &$params
    ): string {
        $range = '';

        if ($startDate !== null) {
            $range .= " AND {$column} >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate !== null) {
            $range .= " AND {$column} <= :end_date";
            $params['end_date'] = $endDate;
        }

        return $range;
    }
}
