<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Feedback;
use PDO;

class FeedbackController extends Controller
{
    private PDO $db;
    private Feedback $feedback;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
        $this->requireAuth();
        $this->feedback = new Feedback($this->db);
    }

    
    public function create()
    {
        $messId = $this->getMemberMess();

        $this->view('feedback/create', [
            'messId' => $messId,
            'csrf' => $this->csrfToken(),
            'categories' => ['meal_quality', 'cleanliness', 'management', 'overall', 'other'],
            'emptyState' => $messId === null ? 'Join or create a mess first to send feedback.' : null,
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

        $rating = (int) ($_POST['rating'] ?? 0);
        $category = $_POST['category'] ?? '';
        $comment = $_POST['comment'] ?? '';

        if ($rating < 1 || $rating > 5 || empty($category)) {
            $_SESSION['error'] = 'Invalid feedback data';
            header('Location: /feedback/create');
            exit;
        }

        $user = $this->currentUser();
        if ($this->feedback->create((int) $user['id'], $messId, $rating, $category, $comment)) {
            $_SESSION['success'] = 'Thank you for your feedback!';
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = 'Failed to submit feedback';
            header('Location: /feedback/create');
        }
        exit;
    }

    
    public function report()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $allFeedback = [];
        $statistics = [];
        $categoryAverage = [];
        $overallAverage = 0.0;

        if ($messId !== null) {
            $allFeedback = $this->feedback->getByMess($messId);
            $statistics = $this->feedback->getStatistics($messId);
            $categoryAverage = $this->feedback->getAverageRatingByCategory($messId);
            $overallAverage = $this->feedback->getOverallAverageRating($messId);
        }

        $this->view('feedback/report', [
            'feedback' => $allFeedback,
            'statistics' => $statistics,
            'categoryAverage' => $categoryAverage,
            'overallAverage' => $overallAverage,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Create or manage a mess first to view feedback reports.' : null,
        ]);
    }

    
    public function delete()
    {
        $this->requireRole('manager');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $feedbackId = $_POST['id'] ?? null;
        if (!$feedbackId) {
            http_response_code(400);
            return;
        }

        $mess = $this->getManagedMess();
        $feedback = $this->feedback->getById((int) $feedbackId);

        if (!$feedback || $feedback['mess_id'] != $mess) {
            http_response_code(403);
            return;
        }

        if ($this->feedback->delete((int) $feedbackId)) {
            $_SESSION['success'] = 'Feedback deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete feedback';
        }

        header('Location: /feedback/report');
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
