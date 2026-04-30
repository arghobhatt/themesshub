<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Mess;
use App\Models\MessMembership;
use App\Models\User;
use Core\Controller;

final class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $user = $this->currentUser();
        $this->view('profile/index', $this->buildViewData($user));
    }

    public function updateProfile(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/profile');
        }

        $this->requireAuth();
        $user = $this->currentUser();
        $userId = (int) $user['id'];

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));

        if ($fullName === '' || strlen($fullName) < 2) {
            $errors['full_name'] = 'Name must be at least 2 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email.';
        }

        $userModel = new User();
        if ($errors === [] && $userModel->emailExistsExcept($email, $userId)) {
            $errors['email'] = 'Email already in use.';
        }

        if ($errors !== []) {
            $old = ['full_name' => $fullName, 'email' => $email];
            $this->view('profile/index', $this->buildViewData($user, $errors, [], $old));
            return;
        }

        $userModel->updateProfile($userId, $fullName, $email);
        $_SESSION['user']['name'] = $fullName;
        $_SESSION['user']['email'] = $email;

        $this->flash('success', 'Profile updated.');
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/profile');
        }

        $this->requireAuth();
        $user = $this->currentUser();
        $userId = (int) $user['id'];

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($currentPassword === '') {
            $errors['current_password'] = 'Current password is required.';
        }
        if (strlen($newPassword) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        $userModel = new User();
        $userRow = $userModel->findByIdWithRole($userId);
        if ($errors === [] && ($userRow === null || !password_verify($currentPassword, $userRow['password_hash']))) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        if ($errors !== []) {
            $this->view('profile/index', $this->buildViewData($user, [], $errors));
            return;
        }

        $userModel->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
        session_regenerate_id(true);

        $this->flash('success', 'Password updated.');
        $this->redirect('/profile');
    }

    private function buildViewData(array $user, array $errorsProfile = [], array $errorsPassword = [], array $old = []): array
    {
        $userId = (int) $user['id'];
        $userModel = new User();
        $userRow = $userModel->findByIdWithRole($userId);

        $profile = [
            'full_name' => $old['full_name'] ?? ($userRow['full_name'] ?? ''),
            'email' => $old['email'] ?? ($userRow['email'] ?? ''),
        ];

        $membershipModel = new MessMembership();
        $memberMesses = $membershipModel->listMessesForUser($userId);

        $managedMesses = [];
        if (($user['role'] ?? '') === 'manager') {
            $messModel = new Mess();
            $managedMesses = $messModel->listManagedBy($userId);
        }

        $flash = [
            'success' => $this->flash('success'),
            'error' => $this->flash('error'),
        ];

        return [
            'user' => $user,
            'csrf' => $this->csrfToken(),
            'profile' => $profile,
            'memberMesses' => $memberMesses,
            'managedMesses' => $managedMesses,
            'errorsProfile' => $errorsProfile,
            'errorsPassword' => $errorsPassword,
            'flash' => $flash,
        ];
    }
}
