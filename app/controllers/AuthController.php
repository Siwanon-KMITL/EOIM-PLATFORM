<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (is_logged_in()) {
            redirect('/dashboard');
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function registerForm(): void
    {
        if (is_logged_in()) {
            redirect('/dashboard');
        }

        $this->view('auth/register', ['title' => 'สมัครสมาชิก']);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
            ];
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
            ];
            redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
            ];
            redirect('/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
            ];
            redirect('/register');
        }

        $userRepository = new UserRepository();

        if ($userRepository->findByEmail($email)) {
            $_SESSION['error'] = 'อีเมลนี้ถูกใช้งานแล้ว';
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email,
            ];
            redirect('/register');
        }

        $userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'viewer',
        ]);

        $_SESSION['success'] = 'สมัครสมาชิกสำเร็จแล้ว คุณสามารถเข้าสู่ระบบได้ทันที';
        redirect('/login');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('auth/forgot_password', ['title' => 'ลืมรหัสผ่าน']);
    }

    public function sendForgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'กรุณากรอกอีเมลให้ถูกต้อง';
            redirect('/forgot-password');
        }

        $userRepository = new UserRepository();
        $user = $userRepository->findByEmail($email);

        if (!$user) {
            $_SESSION['success'] = 'หากอีเมลอยู่ในระบบ เราได้ส่งลิงก์รีเซ็ตรหัสผ่านให้แล้ว';
            redirect('/forgot-password');
        }

        $token = bin2hex(random_bytes(24));
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');

        $resetRepository = new PasswordResetRepository();
        $resetRepository->createToken($email, $token, $expiresAt);

        $appUrl = rtrim(getenv('APP_URL') ?: '', '/');
        if ($appUrl === '') {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $appUrl = $scheme . '://' . $host . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        }

        $resetUrl = rtrim($appUrl, '/') . '/reset-password?token=' . urlencode($token);
        $body = "สวัสดี {$user['name']},\n\n" .
                "เราได้รับคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณแล้ว\n" .
                "คลิกที่ลิงก์ด้านล่างเพื่อตั้งรหัสผ่านใหม่:\n\n" .
                "{$resetUrl}\n\n" .
                "ลิงก์นี้จะหมดอายุใน 1 ชั่วโมง\n\n" .
                "หากคุณไม่ได้ขอรีเซ็ต กรุณาเพิกเฉยอีเมลฉบับนี้";

        $sent = send_mail(
            $email,
            'ลืมรหัสผ่าน - EOIM Platform',
            $body
        );

        if (!$sent) {
            app_log("Password reset email failed for {$email}. Reset URL: {$resetUrl}");
        }

        $_SESSION['success'] = 'หากอีเมลอยู่ในระบบ เราได้ส่งลิงก์รีเซ็ตรหัสผ่านให้แล้ว';
        redirect('/forgot-password');
    }

    public function resetPasswordForm(): void
    {
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            http_response_code(404);
            exit('ลิงก์ไม่ถูกต้อง');
        }

        $resetRepository = new PasswordResetRepository();
        $reset = $resetRepository->findByToken($token);

        if (!$reset || new \DateTimeImmutable() > new \DateTimeImmutable($reset['expires_at'])) {
            http_response_code(404);
            exit('ลิงก์หมดอายุหรือไม่ถูกต้อง');
        }

        $this->view('auth/reset_password', [
            'title' => 'รีเซ็ตรหัสผ่าน',
            'token' => $token,
        ]);
    }

    public function resetPassword(): void
    {
        $token = trim($_POST['token'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($token === '' || $password === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            redirect('/reset-password?token=' . urlencode($token));
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
            redirect('/reset-password?token=' . urlencode($token));
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            redirect('/reset-password?token=' . urlencode($token));
        }

        $resetRepository = new PasswordResetRepository();
        $reset = $resetRepository->findByToken($token);

        if (!$reset || new \DateTimeImmutable() > new \DateTimeImmutable($reset['expires_at'])) {
            http_response_code(404);
            exit('ลิงก์หมดอายุหรือไม่ถูกต้อง');
        }

        $userRepository = new UserRepository();
        $user = $userRepository->findByEmail($reset['email']);

        if (!$user) {
            http_response_code(404);
            exit('ไม่พบผู้ใช้');
        }

        $userRepository->update($user['id'], [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $resetRepository->deleteByToken($token);

        $_SESSION['success'] = 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว คุณสามารถเข้าสู่ระบบได้ทันที';
        redirect('/login');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'กรุณากรอกอีเมลและรหัสผ่าน';
            redirect('/login');
        }

        $service = new AuthService();

        if (!$service->attemptLogin($email, $password)) {
            $_SESSION['error'] = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
            redirect('/login');
        }

        redirect('/dashboard');
    }

    public function logout(): void
    {
        $service = new AuthService();
        $service->logout();
        redirect('/login');
    }
}