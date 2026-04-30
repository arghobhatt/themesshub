<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\MealEntry;
use App\Models\MessMembership;
use Core\Controller;
use DateTime;

final class MealController extends Controller
{
    public function create(): void
    {
        $this->requireRole(['member', 'manager']);
        $user = $this->currentUser();

        $selectedMessId = filter_var($_GET['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $old = [
            'mess_id' => $selectedMessId,
            'meal_date' => date('Y-m-d'),
            'meals_count' => '1.00',
        ];

        $this->view('meals/create', $this->buildFormData((int) $user['id'], $selectedMessId, $old, []));
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/meals/create');
        }

        $this->requireRole(['member', 'manager']);
        $user = $this->currentUser();

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        $messId = filter_var($_POST['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $mealDate = trim((string) ($_POST['meal_date'] ?? ''));
        $mealsCountRaw = trim((string) ($_POST['meals_count'] ?? ''));

        if ($messId === null) {
            $errors['mess_id'] = 'Select a mess.';
        }
        if (!$this->isValidDate($mealDate)) {
            $errors['meal_date'] = 'Select a valid date.';
        }
        if ($mealsCountRaw === '' || !is_numeric($mealsCountRaw) || (float) $mealsCountRaw <= 0) {
            $errors['meals_count'] = 'Meals must be greater than 0.';
        }

        $membershipModel = new MessMembership();
        if ($messId !== null && !$membershipModel->isActiveMember($messId, (int) $user['id'])) {
            $errors['mess_id'] = 'You are not an active member of this mess.';
        }

        $mealModel = new MealEntry();
        if ($errors === [] && $mealModel->existsForUserDate((int) $user['id'], $mealDate)) {
            $errors['general'] = 'You already logged meals for this date.';
        }

        $old = [
            'mess_id' => $messId,
            'meal_date' => $mealDate,
            'meals_count' => $mealsCountRaw,
        ];

        if ($errors !== []) {
            $this->view('meals/create', $this->buildFormData((int) $user['id'], $messId, $old, $errors));
            return;
        }

        $mealModel->create(
            $messId,
            (int) $user['id'],
            $mealDate,
            $mealsCountRaw
        );

        $this->flash('success', 'Meal entry saved.');
        $this->redirect('/meals/create?mess_id=' . $messId);
    }

    private function buildFormData(int $userId, ?int $selectedMessId, array $old, array $errors): array
    {
        $membershipModel = new MessMembership();
        $messes = $membershipModel->listMessesForUser($userId);
        $messIds = array_map(static fn (array $mess) => (int) $mess['id'], $messes);

        if ($selectedMessId === null || !in_array($selectedMessId, $messIds, true)) {
            $selectedMessId = $messIds[0] ?? null;
        }

        $mealModel = new MealEntry();
        $recent = $mealModel->listRecentForUser($userId, 10);

        $flash = [
            'success' => $this->flash('success'),
            'error' => $this->flash('error'),
        ];

        $old['mess_id'] = $selectedMessId;

        return [
            'messes' => $messes,
            'selectedMessId' => $selectedMessId,
            'old' => $old,
            'errors' => $errors,
            'recent' => $recent,
            'csrf' => $this->csrfToken(),
            'flash' => $flash,
        ];
    }

    private function isValidDate(string $date): bool
    {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
