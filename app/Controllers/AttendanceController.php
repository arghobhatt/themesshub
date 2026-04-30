<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Services\AttendanceService;
use App\Models\Attendance;
use PDO;

class AttendanceController extends Controller
{
    private PDO $db;
    private AttendanceService $attendanceService;
    private Attendance $attendanceModel;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
        $this->requireAuth();
        $this->attendanceService = new AttendanceService($this->db);
        $this->attendanceModel = new Attendance($this->db);
    }

    
    public function create()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $members = [];

        if ($messId !== null) {
            $stmt = $this->db->prepare("
                SELECT u.id, u.full_name, u.email
                FROM users u
                INNER JOIN mess_memberships mm ON u.id = mm.user_id
                WHERE mm.mess_id = ? AND mm.status = 'active'
                ORDER BY u.full_name ASC
            ");
            $stmt->execute([$messId]);
            $members = $stmt->fetchAll();
        }

        $this->view('attendance/create', [
            'members' => $members,
            'messId' => $messId,
            'date' => date('Y-m-d'),
            'csrf' => $this->csrfToken(),
            'emptyState' => $messId === null ? 'Create or manage a mess first to mark attendance.' : null,
        ]);
    }

    
    public function batchMark()
    {
        $this->requireRole('manager');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $messId = $this->getManagedMess();
        if (!$messId) {
            http_response_code(403);
            return;
        }

        $date = $_POST['date'] ?? date('Y-m-d');
        $presentIds = array_map('intval', $_POST['present_ids'] ?? []);
        $notes = $_POST['notes'] ?? '';

        if (!empty($presentIds)) {
            $this->attendanceService->batchMarkPresent($presentIds, $messId, $date, $notes);
        }

        
        $stmt = $this->db->prepare("
            SELECT users.id FROM users
            INNER JOIN mess_memberships mm ON users.id = mm.user_id
            WHERE mm.mess_id = ? AND mm.status = 'active'
        ");
        $stmt->execute([$messId]);
        $allMembers = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));

        $absentIds = array_values(array_diff($allMembers, $presentIds));
        if (!empty($absentIds)) {
            $this->attendanceService->batchMarkAbsent($absentIds, $messId, $date);
        }

        $_SESSION['success'] = "Attendance marked successfully for " . count($presentIds) . " members.";
        header('Location: /attendance?' . http_build_query(['mess_id' => $messId]));
        exit;
    }

  
    public function index()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $records = [];
        $stats = [];
        if ($messId !== null) {
            $records = $this->attendanceService->getAttendanceRecords($messId, $startDate, $endDate);
            $stats = $this->attendanceService->getMessAttendanceStats($messId, $startDate, $endDate);
        }

        $this->view('attendance/index', [
            'records' => $records,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Create or manage a mess first to view attendance reports.' : null,
        ]);
    }

    
    public function byDate()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $date = $_GET['date'] ?? date('Y-m-d');

        $todayRecords = [];
        $presentCount = 0;
        $absentCount = 0;
        if ($messId !== null) {
            $todayRecords = $this->attendanceService->getAttendanceByDate($messId, $date);
            $presentCount = $this->attendanceService->getPresentCount($messId, $date);
            $absentCount = $this->attendanceService->getAbsentCount($messId, $date);
        }

        $this->view('attendance/by-date', [
            'date' => $date,
            'records' => $todayRecords,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Create or manage a mess first to view attendance by date.' : null,
        ]);
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
