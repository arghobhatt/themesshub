<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Notice;
use App\Models\NoticeComment;
use PDO;

class NoticeController extends Controller
{
    private PDO $db;
    private Notice $notice;
    private NoticeComment $comment;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../config/database.php';
        $this->requireAuth();
        $this->notice = new Notice($this->db);
        $this->comment = new NoticeComment($this->db);
    }


    public function index()
    {
        $messId = $this->getMemberMess();
        $page = $_GET['page'] ?? 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $notices = [];
        if ($messId !== null) {
            $notices = $this->notice->getByMess($messId, $limit, $offset);
        }

        $this->view('notice/index', [
            'notices' => $notices,
            'page' => $page,
            'messId' => $messId,
            'emptyState' => $messId === null ? 'Join or create a mess to see notices.' : null,
        ]);
    }

   
    public function show()
    {
        $messId = $this->getMemberMess();
        $noticeId = $_GET['id'] ?? null;

        if (!$noticeId) {
            http_response_code(404);
            return;
        }

        $notice = $this->notice->getById((int) $noticeId);
        if (!$notice || $notice['mess_id'] != $messId) {
            http_response_code(404);
            return;
        }

        $comments = $this->comment->getByNotice((int) $noticeId);

        $this->view('notice/view', [
            'notice' => $notice,
            'comments' => $comments,
            'csrf' => $this->csrfToken(),
        ]);
    }

    
    public function create()
    {
        $this->requireRole('manager');

        $messId = $this->getManagedMess();
        $this->view('notice/create', [
            'messId' => $messId,
            'csrf' => $this->csrfToken(),
            'emptyState' => $messId === null ? 'Create or manage a mess first to publish notices.' : null,
        ]);
    }

    
    public function store()
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

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $priority = $_POST['priority'] ?? 'normal';

        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Title and content are required';
            header('Location: /notice/create');
            exit;
        }

        $user = $this->currentUser();
        if ($this->notice->create($messId, (int) $user['id'], $title, $content, $priority)) {
            $_SESSION['success'] = 'Notice posted successfully';
            header('Location: /notice');
        } else {
            $_SESSION['error'] = 'Failed to post notice';
            header('Location: /notice/create');
        }
        exit;
    }

    
    public function edit()
    {
        $this->requireRole('manager');

        $noticeId = $_GET['id'] ?? null;
        if (!$noticeId) {
            $this->view('notice/edit', ['notice' => null, 'emptyState' => 'Select a notice to edit.']);
            return;
        }

        $notice = $this->notice->getById((int) $noticeId);
        if (!$notice) {
            $this->view('notice/edit', ['notice' => null, 'emptyState' => 'Notice not found.']);
            return;
        }

        $this->view('notice/edit', ['notice' => $notice, 'emptyState' => null]);
    }

  
    public function update()
    {
        $this->requireRole('manager');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $noticeId = $_POST['id'] ?? null;
        $notice = $this->notice->getById((int) $noticeId);

        $user = $this->currentUser();
        if (!$notice || $notice['created_by'] != $user['id']) {
            http_response_code(403);
            return;
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $priority = $_POST['priority'] ?? 'normal';

        if ($this->notice->update((int) $noticeId, $title, $content, $priority)) {
            $_SESSION['success'] = 'Notice updated successfully';
            header('Location: /notice');
        } else {
            $_SESSION['error'] = 'Failed to update notice';
        }
        exit;
    }

 
    public function delete()
    {
        $this->requireRole('manager');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $noticeId = $_POST['id'] ?? null;
        $notice = $this->notice->getById((int) $noticeId);

        $user = $this->currentUser();
        if (!$notice || $notice['created_by'] != $user['id']) {
            http_response_code(403);
            return;
        }

        if ($this->notice->delete((int) $noticeId)) {
            $_SESSION['success'] = 'Notice deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete notice';
        }

        header('Location: /notice');
        exit;
    }

    public function addComment()
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

        $noticeId = (int) ($_POST['notice_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if (!$noticeId || empty($content)) {
            $_SESSION['error'] = 'Invalid comment data';
            header('Location: /notice/view?id=' . $noticeId);
            exit;
        }

        
        $notice = $this->notice->getById($noticeId);
        if (!$notice || $notice['mess_id'] != $messId) {
            http_response_code(403);
            return;
        }

        $user = $this->currentUser();
        if ($this->comment->create($noticeId, (int) $user['id'], $content)) {
            $_SESSION['success'] = 'Comment added successfully';
        } else {
            $_SESSION['error'] = 'Failed to add comment';
        }

        header('Location: /notice/view?id=' . $noticeId);
        exit;
    }

  
    public function deleteComment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $commentId = (int) ($_POST['comment_id'] ?? 0);
        $noticeId = (int) ($_POST['notice_id'] ?? 0);

        if (!$commentId || !$noticeId) {
            http_response_code(400);
            return;
        }

        $user = $this->currentUser();
        if ($this->comment->delete($commentId, (int) $user['id'])) {
            $_SESSION['success'] = 'Comment deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete comment';
        }

        header('Location: /notice/view?id=' . $noticeId);
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
