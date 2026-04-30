<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Complaint;
use Core\Controller;
use PDO;

class ComplaintController extends Controller
{
    private PDO $db;
    private Complaint $complaint;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
        $this->requireAuth();
        $this->complaint = new Complaint($this->db);
    }

   
    public function index()
    {
        $user = $this->currentUser();
        $role = $user['role'] ?? '';

        if ($role === 'manager') {
            $messId = $this->getManagedMess();
            $complaints = $messId ? $this->complaint->getByMess($messId) : [];
        } else {
            
            $complaints = []; 
        }

        $this->view('complaints/index', [
            'complaints' => $complaints,
            'user' => $user,
            'csrf' => $this->csrfToken(),
        ]);
    }

    
    public function create()
    {
        $messId = $this->getMemberMess();
        $this->view('complaints/create', [
            'messId' => $messId,
            'csrf' => $this->csrfToken(),
        ]);
    }

    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $messId = $this->getMemberMess();
        if (!$messId) {
            http_response_code(403);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';

        if (empty($title) || empty($description)) {
            $_SESSION['error'] = 'Title and description are required';
            header('Location: /complaints/create');
            exit;
        }

        $user = $this->currentUser();
        if ($this->complaint->create((int) $user['id'], $messId, $title, $description, $priority)) {
            $_SESSION['success'] = 'Complaint submitted successfully';
            header('Location: /complaints');
        } else {
            $_SESSION['error'] = 'Failed to submit complaint';
            header('Location: /complaints/create');
        }
        exit;
    }

   
    public function updateStatus()
    {
        $this->requireRole('manager');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $complaintId = (int) ($_POST['complaint_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$complaintId || !in_array($status, ['open', 'investigating', 'resolved', 'closed'])) {
            http_response_code(400);
            return;
        }

        if ($this->complaint->updateStatus($complaintId, $status)) {
            $_SESSION['success'] = 'Complaint status updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update complaint status';
        }

        header('Location: /complaints');
        exit;
    }

    
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $complaintId = (int) ($_POST['complaint_id'] ?? 0);

        if (!$complaintId) {
            http_response_code(400);
            return;
        }

        $user = $this->currentUser();
        if ($this->complaint->delete($complaintId, (int) $user['id'])) {
            $_SESSION['success'] = 'Complaint deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete complaint';
        }

        header('Location: /complaints');
        exit;
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