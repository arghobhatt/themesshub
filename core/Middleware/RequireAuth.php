<?php
declare(strict_types=1);

namespace Core\Middleware;

final class RequireAuth
{
    public function __invoke(): bool
    {
        $user = $_SESSION['user'] ?? null;

        if ($user === null) {
            header('Location: /login');
            return false;
        }

        return true;
    }
}
