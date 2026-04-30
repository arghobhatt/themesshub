<?php
declare(strict_types=1);

namespace Core\Middleware;

final class RequireRole
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function __invoke(): bool
    {
        $user = $_SESSION['user'] ?? null;

        if ($user === null) {
            header('Location: /login');
            return false;
        }

        $role = $user['role'] ?? '';
        if (!in_array($role, $this->roles, true)) {
            http_response_code(403);
            echo '403 Forbidden';
            return false;
        }

        return true;
    }
}
