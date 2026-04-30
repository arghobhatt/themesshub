<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\JoinRequest;
use App\Models\Mess;
use Core\Controller;

final class JoinRequestController extends Controller
{
    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/');
        }

        $user = $this->currentUser();
        if ($user === null) {
            $this->redirect('/login');
        }

        if (($user['role'] ?? '') !== 'seeker') {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->flash('error', 'Invalid request token.');
            $this->redirect('/');
        }

        $messId = filter_var($_POST['mess_id'] ?? null, FILTER_VALIDATE_INT);
        $message = trim((string) ($_POST['message'] ?? ''));

        if (!$messId) {
            $this->flash('error', 'Select a valid mess.');
            $this->redirect('/');
        }

        if (strlen($message) > 255) {
            $this->flash('error', 'Message must be 255 characters or less.');
            $this->redirect('/');
        }

        $messModel = new Mess();
        $mess = $messModel->findById((int) $messId);
        if ($mess === null || (int) $mess['is_active'] !== 1) {
            $this->flash('error', 'Mess not available for requests.');
            $this->redirect('/');
        }

        $joinModel = new JoinRequest();
        $userId = (int) $user['id'];

        if ($joinModel->hasActiveMembership((int) $messId, $userId)) {
            $this->flash('error', 'You are already a member of this mess.');
            $this->redirect('/');
        }

        if ($joinModel->existsForUser((int) $messId, $userId)) {
            $this->flash('error', 'You have already requested to join this mess.');
            $this->redirect('/');
        }

        $joinModel->create((int) $messId, $userId, $message === '' ? null : $message);
        $this->flash('success', 'Join request sent.');
        $this->redirect('/');
    }

    public function index(): void
    {
        $this->requireRole('manager');
        $user = $this->currentUser();

        $joinModel = new JoinRequest();
        $requests = $joinModel->listPendingForManager((int) $user['id']);

        $flash = [
            'success' => $this->flash('success'),
            'error' => $this->flash('error'),
        ];

        $this->view('join_requests/index', [
            'requests' => $requests,
            'csrf' => $this->csrfToken(),
            'user' => $user,
            'flash' => $flash,
        ]);
    }

    public function approve(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/join-requests');
        }

        $this->requireRole('manager');
        $user = $this->currentUser();

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->flash('error', 'Invalid request token.');
            $this->redirect('/join-requests');
        }

        $requestId = filter_var($_POST['request_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$requestId) {
            $this->flash('error', 'Invalid request id.');
            $this->redirect('/join-requests');
        }

        $joinModel = new JoinRequest();
        $request = $joinModel->findById((int) $requestId);
        if ($request === null) {
            http_response_code(404);
            echo 'Join request not found.';
            return;
        }

        $messModel = new Mess();
        if (!$messModel->canManage((int) $request['mess_id'], (int) $user['id'])) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        if (!$joinModel->approve((int) $requestId, (int) $user['id'])) {
            $this->flash('error', 'Request could not be approved.');
            $this->redirect('/join-requests');
        }

        $this->flash('success', 'Request approved and membership created.');
        $this->redirect('/join-requests');
    }

    public function reject(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/join-requests');
        }

        $this->requireRole('manager');
        $user = $this->currentUser();

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->flash('error', 'Invalid request token.');
            $this->redirect('/join-requests');
        }

        $requestId = filter_var($_POST['request_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$requestId) {
            $this->flash('error', 'Invalid request id.');
            $this->redirect('/join-requests');
        }

        $joinModel = new JoinRequest();
        $request = $joinModel->findById((int) $requestId);
        if ($request === null) {
            http_response_code(404);
            echo 'Join request not found.';
            return;
        }

        $messModel = new Mess();
        if (!$messModel->canManage((int) $request['mess_id'], (int) $user['id'])) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        if (!$joinModel->reject((int) $requestId, (int) $user['id'])) {
            $this->flash('error', 'Request could not be rejected.');
            $this->redirect('/join-requests');
        }

        $this->flash('success', 'Request rejected.');
        $this->redirect('/join-requests');
    }
}
