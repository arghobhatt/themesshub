<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
        'Core\\' => __DIR__ . '/core/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
});

use App\Controllers\AuthController;
use App\Controllers\AttendanceController;
use App\Controllers\DepositController;
use App\Controllers\ExpenseController;
use App\Controllers\FeedbackController;
use App\Controllers\HomeController;
use App\Controllers\JoinRequestController;
use App\Controllers\MealController;
use App\Controllers\MenuController;
use App\Controllers\MessController;
use App\Controllers\NoticeController;
use App\Controllers\ProfileController;
use Core\Middleware\RequireAuth;
use Core\Middleware\RequireRole;
use Core\Router;

$router = new Router();

$authOnly = [new RequireAuth()];
$managerOnly = [new RequireRole(['manager'])];
$seekerOnly = [new RequireRole(['seeker'])];
$memberOnly = [new RequireRole(['member', 'manager'])];

$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [AuthController::class, 'dashboard'], $authOnly);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout'], $authOnly);
$router->get('/manager', [AuthController::class, 'managerArea'], $managerOnly);

$router->get('/messes/create', [MessController::class, 'create'], $managerOnly);
$router->get('/messes', [MessController::class, 'index'], $authOnly);
$router->post('/messes', [MessController::class, 'store'], $managerOnly);
$router->get('/messes/edit', [MessController::class, 'edit'], $managerOnly);
$router->post('/messes/update', [MessController::class, 'update'], $managerOnly);

$router->get('/expenses/create', [ExpenseController::class, 'create'], $managerOnly);
$router->post('/expenses', [ExpenseController::class, 'store'], $managerOnly);
$router->get('/deposits/create', [DepositController::class, 'create'], $managerOnly);
$router->post('/deposits', [DepositController::class, 'store'], $managerOnly);

$router->post('/join-requests', [JoinRequestController::class, 'store'], $seekerOnly);
$router->get('/join-requests', [JoinRequestController::class, 'index'], $managerOnly);
$router->post('/join-requests/approve', [JoinRequestController::class, 'approve'], $managerOnly);
$router->post('/join-requests/reject', [JoinRequestController::class, 'reject'], $managerOnly);

$router->get('/meals/create', [MealController::class, 'create'], $memberOnly);
$router->post('/meals', [MealController::class, 'store'], $memberOnly);

$router->get('/menu', [MenuController::class, 'index'], $authOnly);
$router->get('/menu/edit', [MenuController::class, 'edit'], $managerOnly);
$router->post('/menu/update-day', [MenuController::class, 'updateDay'], $managerOnly);
$router->get('/menu/today', [MenuController::class, 'today'], $authOnly);
$router->get('/menu/tomorrow', [MenuController::class, 'tomorrow'], $authOnly);

$router->get('/attendance', [AttendanceController::class, 'index'], $managerOnly);
$router->get('/attendance/create', [AttendanceController::class, 'create'], $managerOnly);
$router->post('/attendance/batch-mark', [AttendanceController::class, 'batchMark'], $managerOnly);
$router->get('/attendance/by-date', [AttendanceController::class, 'byDate'], $managerOnly);

$router->get('/notice', [NoticeController::class, 'index'], $authOnly);
$router->get('/notice/view', [NoticeController::class, 'show'], $authOnly);
$router->get('/notice/create', [NoticeController::class, 'create'], $managerOnly);
$router->post('/notice/store', [NoticeController::class, 'store'], $managerOnly);
$router->get('/notice/edit', [NoticeController::class, 'edit'], $managerOnly);
$router->post('/notice/update', [NoticeController::class, 'update'], $managerOnly);
$router->post('/notice/delete', [NoticeController::class, 'delete'], $managerOnly);
$router->post('/notice/comment', [NoticeController::class, 'addComment'], $authOnly);
$router->post('/notice/comment/delete', [NoticeController::class, 'deleteComment'], $authOnly);

$router->get('/feedback/create', [FeedbackController::class, 'create'], $memberOnly);
$router->post('/feedback/store', [FeedbackController::class, 'store'], $memberOnly);
$router->get('/feedback/report', [FeedbackController::class, 'report'], $managerOnly);
$router->post('/feedback/delete', [FeedbackController::class, 'delete'], $managerOnly);

$router->get('/profile', [ProfileController::class, 'index'], $authOnly);
$router->post('/profile', [ProfileController::class, 'updateProfile'], $authOnly);
$router->post('/profile/password', [ProfileController::class, 'updatePassword'], $authOnly);

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
