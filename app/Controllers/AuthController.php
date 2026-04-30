<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Deposit;
use App\Models\JoinRequest;
use App\Models\Attendance;
use App\Models\Feedback;
use App\Models\Notice;
use App\Models\Mess;
use App\Models\MessMembership;
use App\Models\WeeklyMenu;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\MealRateService;
use PDO;
use Core\Controller;
use RuntimeException;
use Throwable;

final class AuthController extends Controller
{
    private const ALLOWED_ROLES = ['seeker', 'member', 'manager'];

    public function showLogin(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'csrf' => $this->csrfToken(),
            'errors' => [],
            'old' => ['email' => ''],
        ]);
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/login');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email.';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors !== []) {
            $this->view('auth/login', [
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmailWithRole($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $errors['general'] = 'Invalid email or password.';
        } elseif ($user['status'] !== 'active') {
            $errors['general'] = 'Account is inactive.';
        }

        if ($errors !== []) {
            $this->view('auth/login', [
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role_name'],
        ];

        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/register', [
            'csrf' => $this->csrfToken(),
            'errors' => [],
            'roles' => self::ALLOWED_ROLES,
            'old' => [
                'full_name' => '',
                'email' => '',
                'role' => 'seeker',
            ],
        ]);
    }

    public function register(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/register');
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $role = (string) ($_POST['role'] ?? 'seeker');

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }
        if ($fullName === '' || strlen($fullName) < 2) {
            $errors['full_name'] = 'Name must be at least 2 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            $errors['role'] = 'Invalid role selected.';
        }

        $userModel = new User();
        if ($errors === [] && $userModel->emailExists($email)) {
            $errors['email'] = 'Email already in use.';
        }

        if ($errors !== []) {
            $this->view('auth/register', [
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'roles' => self::ALLOWED_ROLES,
                'old' => [
                    'full_name' => $fullName,
                    'email' => $email,
                    'role' => $role,
                ],
            ]);
            return;
        }

        try {
            $userId = $userModel->create(
                $fullName,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $role
            );
        } catch (RuntimeException | Throwable $e) {
            $this->view('auth/register', [
                'csrf' => $this->csrfToken(),
                'errors' => ['general' => 'Registration failed. Please try again.'],
                'roles' => self::ALLOWED_ROLES,
                'old' => [
                    'full_name' => $fullName,
                    'email' => $email,
                    'role' => $role,
                ],
            ]);
            return;
        }

        $user = $userModel->findByIdWithRole($userId);
        if ($user === null) {
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role_name'],
        ];

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/dashboard');
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            echo 'Invalid request token.';
            return;
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        $this->redirect('/login');
    }

    public function dashboard(): void
    {
        $this->requireAuth();

        $user = $this->currentUser();
        $userId = (int) $user['id'];

    
        $userModel = new User();
        $freshUser = $userModel->findByIdWithRole($userId);
        if ($freshUser !== null && $freshUser['role_name'] !== $user['role']) {
            $_SESSION['user']['role'] = $freshUser['role_name'];
            $user['role'] = $freshUser['role_name'];
        }

        $role = $user['role'] ?? '';

        if ($role === 'manager') {
            $this->showManagerDashboard($user);
            return;
        }

        if ($role === 'member') {
            $this->showMemberDashboard($user);
            return;
        }

        
        $membershipModel = new MessMembership();
        $messes = $membershipModel->listMessesForUser($userId);
        if (!empty($messes)) {
            $_SESSION['user']['role'] = 'member';
            $user['role'] = 'member';
            $this->showMemberDashboard($user);
            return;
        }

        $this->view('dashboards/seeker', [
            'user' => $user,
            'csrf' => $this->csrfToken(),
        ]);
    }

    private function showManagerDashboard(array $user): void
    {
        $userId = (int) $user['id'];
        $messModel = new Mess();
        $messes = $messModel->listManagedBy($userId);
        $db = $this->database();
        $attendanceService = new AttendanceService($db);
        $noticeModel = new Notice($db);
        $feedbackModel = new Feedback($db);

        $mealService = new MealRateService();
        $messSummaries = [];
        $totalExpense = 0.0;
        $totalMeals = 0.0;
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');

        foreach ($messes as $mess) {
            $summary = $mealService->calculateForMess((int) $mess['id'], $startDate, $endDate);
            $summary['attendance'] = $attendanceService->getMessAttendanceStats((int) $mess['id'], $startDate, $endDate);
            $summary['latest_notices'] = $noticeModel->getRecent((int) $mess['id'], 2);
            $summary['feedback_average'] = $feedbackModel->getOverallAverageRating((int) $mess['id']);
            $summary['mess'] = $mess;
            $messSummaries[] = $summary;
            $totalExpense += (float) $summary['total_expense'];
            $totalMeals += (float) $summary['total_meals'];
        }

        $mealRate = $totalMeals > 0.0 ? ($totalExpense / $totalMeals) : 0.0;

        $joinModel = new JoinRequest();
        $pendingRequests = $joinModel->listPendingForManager($userId);
        $pendingCount = count($pendingRequests);

        $this->view('dashboards/manager', [
            'user' => $user,
            'csrf' => $this->csrfToken(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => [
                'total_expense' => $totalExpense,
                'total_meals' => $totalMeals,
                'meal_rate' => $mealRate,
            ],
            'pendingRequests' => array_slice($pendingRequests, 0, 10),
            'pendingCount' => $pendingCount,
            'messSummaries' => $messSummaries,
        ]);
    }

    private function showMemberDashboard(array $user): void
    {
        $userId = (int) $user['id'];
        $membershipModel = new MessMembership();
        $messes = $membershipModel->listMessesForUser($userId);
        $db = $this->database();
        $attendanceService = new AttendanceService($db);
        $noticeModel = new Notice($db);
        $menuModel = new WeeklyMenu($db);
        $feedbackModel = new Feedback($db);

        $mealService = new MealRateService();
        $messSummaries = [];
        $totalMeals = 0.0;
        $totalDeposits = 0.0;
        $totalBalance = 0.0;
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        $todayIndex = (int) date('w') - 1;
        if ($todayIndex < 0) {
            $todayIndex = 6;
        }
        $tomorrowIndex = ($todayIndex + 1) % 7;

        foreach ($messes as $mess) {
            $summary = $mealService->calculateForUserBalance((int) $mess['id'], $userId);
            $summary['attendance'] = $attendanceService->getUserAttendanceStats($userId, (int) $mess['id'], $startDate, $endDate);
            $summary['latest_notices'] = $noticeModel->getRecent((int) $mess['id'], 2);
            $summary['today_menu'] = $menuModel->getByDay((int) $mess['id'], $todayIndex);
            $summary['tomorrow_menu'] = $menuModel->getByDay((int) $mess['id'], $tomorrowIndex);
            $summary['feedback_average'] = $feedbackModel->getOverallAverageRating((int) $mess['id']);
            $summary['mess'] = $mess;
            $messSummaries[] = $summary;
            $totalMeals += (float) $summary['user_meals'];
            $totalDeposits += (float) $summary['user_deposits'];
            $totalBalance += (float) $summary['balance'];
        }

        $depositModel = new Deposit();
        $depositHistory = $depositModel->listForUser($userId, 15);

        $this->view('dashboards/member', [
            'user' => $user,
            'csrf' => $this->csrfToken(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => [
                'total_meals' => $totalMeals,
                'total_deposits' => $totalDeposits,
                'balance' => $totalBalance,
            ],
            'messSummaries' => $messSummaries,
            'depositHistory' => $depositHistory,
        ]);
    }

    private function database(): PDO
    {
        return require __DIR__ . '/../../config/database.php';
    }

    public function managerArea(): void
    {
        $this->requireRole('manager');
        $this->redirect('/dashboard');
    }
}
