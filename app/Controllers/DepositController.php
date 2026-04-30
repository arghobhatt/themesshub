<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Deposit;
use App\Models\Mess;
use App\Models\MessMembership;
use Core\Controller;
use DateTime;

final class DepositController extends Controller
{
    public function create(): void
    {
        $this->requireRole('manager');
        $user = $this->currentUser();

        $selectedMessId = filter_var($_GET['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $old = [
            'mess_id' => $selectedMessId,
            'member_id' => '',
            'deposited_on' => date('Y-m-d'),
            'amount' => '',
            'method' => '',
            'reference' => '',
        ];

        $this->view('deposits/create', $this->buildFormData((int) $user['id'], $selectedMessId, $old, []));
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/deposits/create');
        }

        $this->requireRole('manager');
        $user = $this->currentUser();

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        $messId = filter_var($_POST['mess_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $memberId = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $depositedOn = trim((string) ($_POST['deposited_on'] ?? ''));
        $amountRaw = trim((string) ($_POST['amount'] ?? ''));
        $method = trim((string) ($_POST['method'] ?? ''));
        $reference = trim((string) ($_POST['reference'] ?? ''));

        if ($messId === null) {
            $errors['mess_id'] = 'Select a mess.';
        }
        if ($memberId === null) {
            $errors['member_id'] = 'Select a member.';
        }
        if (!$this->isValidDate($depositedOn)) {
            $errors['deposited_on'] = 'Select a valid date.';
        }
        if ($amountRaw === '' || !is_numeric($amountRaw) || (float) $amountRaw <= 0) {
            $errors['amount'] = 'Amount must be greater than 0.';
        }
        if (strlen($method) > 50) {
            $errors['method'] = 'Method must be 50 characters or less.';
        }
        if (strlen($reference) > 100) {
            $errors['reference'] = 'Reference must be 100 characters or less.';
        }

        $messModel = new Mess();
        if ($messId !== null && !$messModel->canManage($messId, (int) $user['id'])) {
            $errors['mess_id'] = 'You do not manage this mess.';
        }

        $membershipModel = new MessMembership();
        if ($messId !== null && $memberId !== null
            && !$membershipModel->isActiveMember($messId, $memberId)) {
            $errors['member_id'] = 'Member must be active in this mess.';
        }

        $old = [
            'mess_id' => $messId,
            'member_id' => $memberId,
            'deposited_on' => $depositedOn,
            'amount' => $amountRaw,
            'method' => $method,
            'reference' => $reference,
        ];

        if ($errors !== []) {
            $this->view('deposits/create', $this->buildFormData((int) $user['id'], $messId, $old, $errors));
            return;
        }

        $depositModel = new Deposit();
        $depositModel->create(
            $messId,
            $memberId,
            $depositedOn,
            $amountRaw,
            $method === '' ? null : $method,
            $reference === '' ? null : $reference
        );

        $this->flash('success', 'Deposit recorded.');
        $this->redirect('/deposits/create?mess_id=' . $messId);
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

        $depositModel = new Deposit();
        $recent = $depositModel->listRecentForManager($managerId, 10);

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
