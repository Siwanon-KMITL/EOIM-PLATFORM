<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function index(): void
    {
        $this->view('users/index', [
            'title' => 'จัดการผู้ใช้',
            'user' => auth_user(),
            'users' => $this->users->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('users/create', [
            'title' => 'เพิ่มผู้ใช้',
            'user' => auth_user(),
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        $role = trim($_POST['role'] ?? 'viewer');

        if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            $_SESSION['old'] = compact('name', 'email', 'role');
            redirect('/users/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            $_SESSION['old'] = compact('name', 'email', 'role');
            redirect('/users/create');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
            $_SESSION['old'] = compact('name', 'email', 'role');
            redirect('/users/create');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            $_SESSION['old'] = compact('name', 'email', 'role');
            redirect('/users/create');
        }

        if ($this->users->findByEmail($email)) {
            $_SESSION['error'] = 'อีเมลนี้ถูกใช้งานแล้ว';
            $_SESSION['old'] = compact('name', 'email', 'role');
            redirect('/users/create');
        }

        $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => in_array($role, ['admin', 'staff', 'viewer'], true) ? $role : 'viewer',
        ]);

        $_SESSION['success'] = 'เพิ่มผู้ใช้เรียบร้อยแล้ว';
        redirect('/users');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $editUser = $this->users->findById($id);

        if (!$editUser) {
            http_response_code(404);
            exit('User not found');
        }

        $this->view('users/edit', [
            'title' => 'แก้ไขผู้ใช้',
            'user' => auth_user(),
            'editUser' => $editUser,
        ]);
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'viewer');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $existing = $this->users->findById($id);
        if (!$existing) {
            http_response_code(404);
            exit('User not found');
        }

        if ($name === '' || $email === '') {
            $_SESSION['error'] = 'กรุณากรอกชื่อและอีเมล';
            redirect('/users/edit?id=' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            redirect('/users/edit?id=' . $id);
        }

        if ($this->users->findByEmailExceptId($email, $id)) {
            $_SESSION['error'] = 'อีเมลนี้ถูกใช้งานแล้ว';
            redirect('/users/edit?id=' . $id);
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => in_array($role, ['admin', 'staff', 'viewer'], true) ? $role : 'viewer',
            'password' => '',
        ];

        if ($password !== '') {
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
                redirect('/users/edit?id=' . $id);
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
                redirect('/users/edit?id=' . $id);
            }

            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->users->update($id, $data);
        $_SESSION['success'] = 'แก้ไขผู้ใช้เรียบร้อยแล้ว';
        redirect('/users');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $currentUser = auth_user();

        if ($id === $currentUser['id']) {
            $_SESSION['error'] = 'ไม่สามารถลบบัญชีตัวเองได้';
            redirect('/users');
        }

        $this->users->delete($id);
        $_SESSION['success'] = 'ลบผู้ใช้เรียบร้อยแล้ว';
        redirect('/users');
    }

    public function profile(): void
    {
        $currentUser = auth_user();
        $profile = $this->users->findById($currentUser['id']);

        $this->view('profile/edit', [
            'title' => 'แก้ไขโปรไฟล์',
            'user' => $currentUser,
            'profile' => $profile,
        ]);
    }

    public function updateProfile(): void
    {
        $currentUser = auth_user();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['error'] = 'กรุณากรอกชื่อและอีเมล';
            redirect('/profile');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            redirect('/profile');
        }

        if ($this->users->findByEmailExceptId($email, $currentUser['id'])) {
            $_SESSION['error'] = 'อีเมลนี้ถูกใช้งานแล้ว';
            redirect('/profile');
        }

        $this->users->update($currentUser['id'], [
            'name' => $name,
            'email' => $email,
            'role' => $currentUser['role'],
            'password' => '',
        ]);

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['success'] = 'อัปเดตโปรไฟล์เรียบร้อยแล้ว';
        redirect('/profile');
    }

    public function passwordForm(): void
    {
        $this->view('profile/password', [
            'title' => 'เปลี่ยนรหัสผ่าน',
            'user' => auth_user(),
        ]);
    }

    public function updatePassword(): void
    {
        $currentUser = auth_user();
        $oldPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($oldPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            redirect('/profile/password');
        }

        $userRecord = $this->users->findById($currentUser['id']);
        if (!$userRecord || !password_verify($oldPassword, $userRecord['password'])) {
            $_SESSION['error'] = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
            redirect('/profile/password');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน';
            redirect('/profile/password');
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            redirect('/profile/password');
        }

        $this->users->update($currentUser['id'], [
            'name' => $userRecord['name'],
            'email' => $userRecord['email'],
            'role' => $userRecord['role'],
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        $_SESSION['success'] = 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว';
        redirect('/profile/password');
    }
}
