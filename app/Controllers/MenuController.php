<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\WeeklyMenu;
use PDO;

class MenuController extends Controller
{
    private PDO $db;
    private WeeklyMenu $menu;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
        $this->requireAuth();
        $this->menu = new WeeklyMenu($this->db);
    }

    
    public function index()
    {
        $messId = $this->getMemberMess();
        $menu = [];
        if ($messId !== null) {
            $menu = $this->menu->getWeeklyMenu($messId);
        }

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $this->view('menu/index', [
            'menu' => $menu,
            'daysOfWeek' => $daysOfWeek,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Join or create a mess to see the weekly menu.' : null,
        ]);
    }

   
    public function edit()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $menu = [];
        if ($messId !== null) {
            $menu = $this->menu->getWeeklyMenu($messId);
        }

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $this->view('menu/edit', [
            'menu' => $menu,
            'daysOfWeek' => $daysOfWeek,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Create or manage a mess first to edit the weekly menu.' : null,
        ]);
    }

    
    public function updateDay()
    {
        $this->requireRole('manager');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $dayOfWeek = (int) ($_POST['day_of_week'] ?? -1);
        $breakfast = $_POST['breakfast'] ?? '';
        $lunch = $_POST['lunch'] ?? '';
        $dinner = $_POST['dinner'] ?? '';

        if ($dayOfWeek < 0 || $dayOfWeek > 6) {
            http_response_code(400);
            return;
        }

        $messId = $this->getManagedMess();
        $existingMenu = $this->menu->getByDay($messId, $dayOfWeek);

        if ($existingMenu) {
            $this->menu->update($existingMenu['id'], $breakfast, $lunch, $dinner);
        } else {
            $this->menu->create($messId, $dayOfWeek, $breakfast, $lunch, $dinner);
        }

        $_SESSION['success'] = 'Menu updated successfully';
        header('Location: /menu/edit');
        exit;
    }

    
    public function today()
    {
        $messId = $this->getMemberMess();
        if (!$messId) {
            http_response_code(403);
            return;
        }

        $today = date('w') - 1; 
        if ($today == -1) $today = 6; 
        
        $todayMenu = $this->menu->getByDay($messId, $today);

        header('Content-Type: application/json');
        echo json_encode($todayMenu ?? [
            'breakfast' => 'Not set',
            'lunch' => 'Not set',
            'dinner' => 'Not set'
        ]);
    }

 
    public function tomorrow()
    {
        $messId = $this->getMemberMess();
        if (!$messId) {
            http_response_code(403);
            return;
        }

        $tomorrow = (date('w') % 7);
        $tomorrowMenu = $this->menu->getByDay($messId, $tomorrow);

        header('Content-Type: application/json');
        echo json_encode($tomorrowMenu ?? [
            'breakfast' => 'Not set',
            'lunch' => 'Not set',
            'dinner' => 'Not set'
        ]);
    }

   
    private function getMemberMess(): ?int
    {
        $user = $this->currentUser();
        if ($user === null) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT mess_id FROM mess_memberships
            WHERE user_id = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([(int) $user['id']]);
        return (int) ($stmt->fetchColumn() ?? 0) ?: null;
    }

    
    private function getManagedMess(): ?int
    {
        $user = $this->currentUser();
        if ($user === null) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT mess_id FROM mess_memberships
            WHERE user_id = ? AND role_in_mess = 'manager' AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([(int) $user['id']]);
        return (int) ($stmt->fetchColumn() ?? 0) ?: null;
    }
}
