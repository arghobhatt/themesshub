<?php
declare(strict_types=1);

namespace Core;

use RuntimeException;

class Controller
{
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function view(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../app/Views/' . $view . '.php';
        if (!is_file($viewPath)) {
            throw new RuntimeException("View not found: {$view}");
        }

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    protected function model(string $model): object
    {
        $class = "App\\Models\\{$model}";

        if (!class_exists($class)) {
            throw new RuntimeException("Model not found: {$model}");
        }

        return new $class();
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function requireAuth(): void
    {
        if ($this->currentUser() === null) {
            $this->redirect('/login');
        }
    }

    protected function requireRole(string|array $roles): void
    {
        $this->requireAuth();

        $roles = (array) $roles;
        $user = $this->currentUser();

        if ($user === null || !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['csrf_token'];
    }

    protected function verifyCsrfToken(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || $token === null) {
            return false;
        }

        return hash_equals((string) $_SESSION['csrf_token'], $token);
    }

    protected function flash(string $key, ?string $message = null): ?string
    {
        if ($message === null) {
            $value = $_SESSION['flash'][$key] ?? null;
            if ($value !== null) {
                unset($_SESSION['flash'][$key]);
                if (empty($_SESSION['flash'])) {
                    unset($_SESSION['flash']);
                }
            }

            return $value;
        }

        $_SESSION['flash'][$key] = $message;
        return null;
    }
}
