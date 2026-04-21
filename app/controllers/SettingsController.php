<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class SettingsController extends Controller
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function index(): void
    {
        $currentUser = auth_user();
        $profile = $this->users->findById((int)$currentUser['id']);

        $this->view('settings/index', [
            'title' => 'Settings & User Management',
            'user' => $currentUser,
            'profile' => $profile,
            'users' => has_role('admin') ? $this->users->all() : [],
            'isAdmin' => has_role('admin'),
        ]);
    }
}
