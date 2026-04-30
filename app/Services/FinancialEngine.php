<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * FinancialEngine V2
 * Handles advanced meal rate calculations including guest meals,
 * member balances, and detailed financial aggregations.
 */
class FinancialEngine
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Calculate total meals for a mess in a date range
     * Includes regular member meals + guest meals
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float Total meals (regular + guest)
     */
    public function calculateTotalMeals(int $messId, string $startDate, string $endDate): float
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(CASE WHEN has_breakfast = 1 THEN 1 END) as breakfast_count,
                COUNT(CASE WHEN has_lunch = 1 THEN 1 END) as lunch_count,
                COUNT(CASE WHEN has_dinner = 1 THEN 1 END) as dinner_count,
                COALESCE(SUM(guest_meals), 0) as total_guest_meals
            FROM meal_entries
            WHERE mess_id = ? 
            AND meal_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return 0.0;
        }

        // Each meal type (breakfast, lunch, dinner) counts as 1 meal
        $memberMeals = (float) ($result['breakfast_count'] + $result['lunch_count'] + $result['dinner_count']);
        $guestMeals = (float) $result['total_guest_meals'];

        return $memberMeals + $guestMeals;
    }

    /**
     * Calculate total expenses for a mess in a date range
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float Total expenses
     */
    public function calculateTotalExpenses(int $messId, string $startDate, string $endDate): float
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total
            FROM expenses
            WHERE mess_id = ? 
            AND expense_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Calculate current meal rate for a mess
     *
     * Formula: Meal Rate = Total Expenses / (Total Regular Meals + Total Guest Meals)
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float Meal rate per meal (e.g., 50.5 = 50.50 per meal)
     * @throws \Exception If total meals is zero (division by zero)
     */
    public function calculateMealRate(int $messId, string $startDate, string $endDate): float
    {
        $totalExpenses = $this->calculateTotalExpenses($messId, $startDate, $endDate);
        $totalMeals = $this->calculateTotalMeals($messId, $startDate, $endDate);

        if ($totalMeals == 0) {
            throw new \Exception("Cannot calculate meal rate: No meals logged for this period.");
        }

        return round($totalExpenses / $totalMeals, 2);
    }

    /**
     * Calculate total meals consumed by a specific user in a mess during a period
     *
     * @param int $userId
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float Total meals (breakfast + lunch + dinner count as 1 each)
     */
    public function calculateUserMeals(int $userId, int $messId, string $startDate, string $endDate): float
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(CASE WHEN has_breakfast = 1 THEN 1 END) as breakfast_count,
                COUNT(CASE WHEN has_lunch = 1 THEN 1 END) as lunch_count,
                COUNT(CASE WHEN has_dinner = 1 THEN 1 END) as dinner_count
            FROM meal_entries
            WHERE user_id = ? 
            AND mess_id = ?
            AND meal_date BETWEEN ? AND ?
        ");
        
        $stmt->execute([$userId, $messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return 0.0;
        }

        return (float) ($result['breakfast_count'] + $result['lunch_count'] + $result['dinner_count']);
    }

    /**
     * Calculate total deposits made by a user for a mess
     *
     * @param int $userId
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return float Total deposits
     */
    public function calculateUserDeposits(int $userId, int $messId, string $startDate, string $endDate): float
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total
            FROM deposits
            WHERE user_id = ? 
            AND mess_id = ?
            AND deposited_on BETWEEN ? AND ?
        ");
        
        $stmt->execute([$userId, $messId, $startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Calculate a member's balance for a specific mess
     *
     * Formula: Balance = (User Meals × Meal Rate) - User Deposits
     *
     * Positive = User owes money
     * Negative = User is owed money
     * Zero = Settled
     *
     * @param int $userId
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array ['balance' => float, 'meals' => float, 'deposits' => float, 'rate' => float]
     */
    public function calculateMemberBalance(int $userId, int $messId, string $startDate, string $endDate): array
    {
        try {
            $rate = $this->calculateMealRate($messId, $startDate, $endDate);
        } catch (\Exception $e) {
            // No meals or expenses recorded yet
            $rate = 0.0;
        }

        $userMeals = $this->calculateUserMeals($userId, $messId, $startDate, $endDate);
        $userDeposits = $this->calculateUserDeposits($userId, $messId, $startDate, $endDate);

        $mealCost = round($userMeals * $rate, 2);
        $balance = round($mealCost - $userDeposits, 2);

        return [
            'balance' => $balance,
            'meals' => $userMeals,
            'meal_cost' => $mealCost,
            'deposits' => $userDeposits,
            'rate' => $rate,
        ];
    }

    /**
     * Get all member balances for a mess (for settlement report)
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Array of member balances sorted by balance descending
     */
    public function getAllMemberBalances(int $messId, string $startDate, string $endDate): array
    {
        // Get all active members of the mess
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT u.id, u.full_name, u.email
            FROM users u
            INNER JOIN mess_memberships mm ON u.id = mm.user_id
            WHERE mm.mess_id = ? 
            AND mm.status = 'active'
            ORDER BY u.full_name ASC
        ");
        
        $stmt->execute([$messId]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $balances = [];
        foreach ($members as $member) {
            $balance = $this->calculateMemberBalance($member['id'], $messId, $startDate, $endDate);
            $balances[] = array_merge($member, $balance);
        }

        // Sort by balance descending (highest debtors first)
        usort($balances, function($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        return $balances;
    }

    /**
     * Get expense breakdown by category for a mess
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Array of categories with totals
     */
    public function getExpenseBreakdown(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COALESCE(category, 'Uncategorized') as category,
                COUNT(*) as count,
                SUM(amount) as total,
                ROUND(AVG(amount), 2) as average
            FROM expenses
            WHERE mess_id = ? 
            AND expense_date BETWEEN ? AND ?
            GROUP BY category
            ORDER BY total DESC
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get deposit breakdown by payment method for a mess
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Array of payment methods with totals
     */
    public function getDepositBreakdown(int $messId, string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COALESCE(payment_method, 'Not specified') as payment_method,
                COUNT(*) as count,
                SUM(amount) as total
            FROM deposits
            WHERE mess_id = ? 
            AND deposited_on BETWEEN ? AND ?
            GROUP BY payment_method
            ORDER BY total DESC
        ");
        
        $stmt->execute([$messId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate summary statistics for a mess
     *
     * @param int $messId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array Comprehensive financial summary
     */
    public function getFinancialSummary(int $messId, string $startDate, string $endDate): array
    {
        $totalExpenses = $this->calculateTotalExpenses($messId, $startDate, $endDate);
        $totalMeals = $this->calculateTotalMeals($messId, $startDate, $endDate);
        
        try {
            $mealRate = $this->calculateMealRate($messId, $startDate, $endDate);
        } catch (\Exception $e) {
            $mealRate = 0.0;
        }

        // Total deposits in period
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total
            FROM deposits
            WHERE mess_id = ? 
            AND deposited_on BETWEEN ? AND ?
        ");
        $stmt->execute([$messId, $startDate, $endDate]);
        $totalDeposits = (float) ($stmt->fetchColumn() ?? 0);

        // Member count
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM mess_memberships
            WHERE mess_id = ? AND status = 'active'
        ");
        $stmt->execute([$messId]);
        $memberCount = (int) $stmt->fetchColumn();

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_expenses' => $totalExpenses,
            'total_deposits' => $totalDeposits,
            'total_meals' => $totalMeals,
            'meal_rate' => $mealRate,
            'member_count' => $memberCount,
            'settlement_difference' => round($totalExpenses - $totalDeposits, 2),
        ];
    }
}
