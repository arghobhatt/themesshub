<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Mess;
use Core\Controller;

final class MessController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $user = $this->currentUser();
        $role = $user['role'] ?? '';

        if ($role === 'manager') {
            $messModel = new Mess();
            $messes = $messModel->listActive(50); 
        } else {
            
            $messModel = new Mess();
            $messes = $messModel->listActive(12);
        }

        $this->view('messes/index', [
            'messes' => $messes,
            'user' => $user,
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->view('messes/create', [
            'csrf' => $this->csrfToken(),
            'errors' => [],
            'old' => [
                'name' => '',
                'location' => '',
                'rent' => '',
                'description' => '',
            ],
        ]);
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/messes/create');
        }

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        [$data, $inputErrors] = $this->validateInput($_POST);
        $errors = array_merge($errors, $inputErrors);

        if ($errors !== []) {
            $this->view('messes/create', [
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => $data,
            ]);
            return;
        }

        $user = $this->currentUser();
        if ($user === null) {
            $this->redirect('/login');
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['name'] !== '') {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = realpath(__DIR__ . '/../../') . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '/uploads/' . $fileName;
                } else {
                    $errors['image'] = 'Failed to move uploaded file.';
                }
            } elseif ($_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['image']['error'] === UPLOAD_ERR_FORM_SIZE) {
                $errors['image'] = 'The uploaded image is too large (max 2MB).';
            } else {
                $errors['image'] = 'Image upload failed with error code: ' . $_FILES['image']['error'];
            }
        }

        $messModel = new Mess();
        $messId = $messModel->create(
            $data['name'],
            $data['location'],
            $data['rent'],
            $data['description'],
            (int) $user['id'],
            $imagePath
        );

        
        $membershipModel = new \App\Models\MessMembership();
        $membershipModel->create($messId, (int) $user['id'], 'manager');

        $this->redirect('/messes/edit?id=' . $messId);
    }

    public function edit(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo 'Invalid mess id.';
            return;
        }

        $messModel = new Mess();
        $mess = $messModel->findById($id);
        if ($mess === null) {
            http_response_code(404);
            echo 'Mess not found.';
            return;
        }

        $this->view('messes/edit', [
            'csrf' => $this->csrfToken(),
            'errors' => [],
            'old' => [
                'name' => $mess['name'],
                'location' => $mess['location'],
                'rent' => $mess['rent'] ?? '',
                'description' => $mess['description'] ?? '',
            ],
            'mess' => $mess,
            'updated' => (($_GET['updated'] ?? '') === '1'),
        ]);
    }

    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/messes/create');
        }

        $id = filter_var($_POST['mess_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo 'Invalid mess id.';
            return;
        }

        $errors = [];
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors['general'] = 'Invalid request token.';
        }

        [$data, $inputErrors] = $this->validateInput($_POST);
        $errors = array_merge($errors, $inputErrors);

        $messModel = new Mess();
        $mess = $messModel->findById($id);
        if ($mess === null) {
            http_response_code(404);
            echo 'Mess not found.';
            return;
        }

        if ($errors !== []) {
            $this->view('messes/edit', [
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => $data,
                'mess' => $mess,
                'updated' => false,
            ]);
            return;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['name'] !== '') {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = realpath(__DIR__ . '/../../') . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '/uploads/' . $fileName;
                } else {
                    $errors['image'] = 'Failed to move uploaded file.';
                }
            } elseif ($_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['image']['error'] === UPLOAD_ERR_FORM_SIZE) {
                $errors['image'] = 'The uploaded image is too large (max 2MB).';
            } else {
                $errors['image'] = 'Image upload failed with error code: ' . $_FILES['image']['error'];
            }
        }

        $messModel->update(
            $id,
            $data['name'],
            $data['location'],
            $data['rent'],
            $data['description'],
            $imagePath
        );

        $this->redirect('/messes/edit?id=' . $id . '&updated=1');
    }

    private function validateInput(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $location = trim((string) ($input['location'] ?? ''));
        $rentRaw = trim((string) ($input['rent'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));

        $errors = [];
        if ($name === '' || strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }
        if ($location === '') {
            $errors['location'] = 'Location is required.';
        }
        if ($rentRaw !== '' && (!is_numeric($rentRaw) || (float) $rentRaw < 0)) {
            $errors['rent'] = 'Rent must be a positive number.';
        }
        if (strlen($description) > 1000) {
            $errors['description'] = 'Description must be under 1000 characters.';
        }

        $data = [
            'name' => $name,
            'location' => $location,
            'rent' => $rentRaw === '' ? null : $rentRaw,
            'description' => $description,
        ];

        return [$data, $errors];
    }
}
