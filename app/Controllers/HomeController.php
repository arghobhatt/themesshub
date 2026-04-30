<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Mess;
use Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $messModel = new Mess();
        $messes = $messModel->listActive(12);

        $flash = [
            'success' => $this->flash('success'),
            'error' => $this->flash('error'),
        ];

        $this->view('home/index', [
            'messes' => $messes,
            'user' => $this->currentUser(),
            'csrf' => $this->csrfToken(),
            'flash' => $flash,
        ]);
    }
}
