<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Expense;
use App\Models\Mess;
use App\Models\MessMembership;
use Core\Controller;
use DateTime;

final class ExpenseController extends Controller
{
    public function create(): void
    {
        $this->requireRole('manager');
        $user = $this->currentUser();

        $selectedMessId = filter_var($_GET['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $old = [
            'mess_id' => $selectedMessId,
            'purchaser_id' => '',
            'expense_date' => date('Y-m-d'),
            'amount' => '',
            'vendor' => '',
            'notes' => '',
        ];

        $this->view('expenses/create', $this->buildFormData((int) $user['id'], $selectedMessId, $old, []));
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/expenses/create');
        }

        $this->requireRole('manager');
        $user = $this->currentUser();

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        $messId = filter_var($_POST['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $purchaserId = filter_var($_POST['purchaser_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $expenseDate = trim((string) ($_POST['expense_date'] ?? ''));
        $amountRaw = trim((string) ($_POST['amount'] ?? ''));
        $vendor = trim((string) ($_POST['vendor'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if ($messId === null) {
            $errors['mess_id'] = 'Select a mess.';
        }
        if ($purchaserId === null) {
            $errors['purchaser_id'] = 'Select a purchaser.';
        }
        if (!$this->isValidDate($expenseDate)) {
            $errors['expense_date'] = 'Select a valid date.';
        }
        if ($amountRaw === '' || !is_numeric($amountRaw) || (float) $amountRaw <= 0) {
            $errors['amount'] = 'Amount must be greater than 0.';
        }
        if (strlen($vendor) > 120) {
            $errors['vendor'] = 'Vendor must be 120 characters or less.';
        }
        if (strlen($notes) > 255) {
            $errors['notes'] = 'Notes must be 255 characters or less.';
        }

        $messModel = new Mess();
        if ($messId !== null && !$messModel->canManage($messId, (int) $user['id'])) {
            $errors['mess_id'] = 'You do not manage this mess.';
        }

        $membershipModel = new MessMembership();
        if ($messId !== null && $purchaserId !== null
            && !$membershipModel->isActiveMember($messId, $purchaserId)) {
            $errors['purchaser_id'] = 'Purchaser must be an active member.';
        }

        $old = [
            'mess_id' => $messId,
            'purchaser_id' => $purchaserId,
            'expense_date' => $expenseDate,
            'amount' => $amountRaw,
            'vendor' => $vendor,
            'notes' => $notes,
        ];

        if ($errors !== []) {
            $this->view('expenses/create', $this->buildFormData((int) $user['id'], $messId, $old, $errors));
            return;
        }

        $expenseModel = new Expense();
        $expenseModel->create(
            $messId,
            $purchaserId,
            $expenseDate,
            $amountRaw,
            $vendor === '' ? null : $vendor,
            $notes === '' ? null : $notes
        );

        $this->flash('success', 'Expense recorded.');
        $this->redirect('/expenses/create?mess_id=' . $messId);
    }

    private function buildFormData(int $managerId, ?int $selectedMessId, array $old, array $errors): array
    {
        $messModel = new Mess();
        $messes = $messModel->listManagedBy($managerId);
        $messIds = array_map(static fn (array $mess) => (int) $mess['id'], $messes);

        if ($selectedMessId === null || !in_array($selectedMessId, $messIds, true)) {
            $selectedMessId = $messIds[0] ?? null;
        }

        $members = [];
        if ($selectedMessId !== null) {
            $membershipModel = new MessMembership();
            $members = $membershipModel->listMembers($selectedMessId);
        }

        $expenseModel = new Expense();
        $recent = $expenseModel->listRecentForManager($managerId, 10);

        $flash = [
            'success' => $this->flash('success'),
            'error' => $this->flash('error'),
        ];

        $old['mess_id'] = $selectedMessId;

        return [
            'messes' => $messes,
            'members' => $members,
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
