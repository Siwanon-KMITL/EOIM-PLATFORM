<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\DeviceRepository;
use App\Repositories\UserRepository;

class DeviceController extends Controller
{
    private DeviceRepository $devices;
    private UserRepository $users;

    public function __construct()
    {
        $this->devices = new DeviceRepository();
        $this->users = new UserRepository();
    }

    public function index(): void
    {
        $user = auth_user();
        $isAdmin = has_role('admin');

        $items = $isAdmin ? $this->devices->all() : $this->devices->all($user['id']);

        $this->view('devices/index', [
            'title' => 'จัดการอุปกรณ์',
            'user' => $user,
            'devices' => $items
        ]);
    }

    public function create(): void
    {
        $data = [
            'title' => 'เพิ่มอุปกรณ์',
            'user' => auth_user()
        ];

        if (has_role('admin')) {
            $data['users'] = $this->users->all();
        }

        $this->view('devices/create', $data);
    }

    public function store(): void
    {
        $user = auth_user();

        $deviceSecret = trim((string)$this->input('device_secret', ''));
        if ($deviceSecret === '') {
            $deviceSecret = bin2hex(random_bytes(16));
        }

        $userId = $user['id'];
        if (has_role('admin')) {
            $userId = (int)$this->input('user_id', $userId);
            $owner = $this->users->findById($userId);
            if (!$owner) {
                $_SESSION['error'] = 'กรุณาเลือกเจ้าของอุปกรณ์ที่ถูกต้อง';
                $_SESSION['old'] = [
                    'device_name' => trim((string)$this->input('device_name', '')),
                    'device_type' => trim((string)$this->input('device_type', '')),
                    'location' => trim((string)$this->input('location', '')),
                    'status' => trim((string)$this->input('status', 'active')),
                    'ip_address' => trim((string)$this->input('ip_address', '')),
                    'device_secret' => $deviceSecret,
                    'user_id' => $userId,
                ];
                redirect('/devices/create');
            }
        }

        $data = [
            'device_name' => trim((string)$this->input('device_name', '')),
            'device_type' => trim((string)$this->input('device_type', '')),
            'location' => trim((string)$this->input('location', '')),
            'status' => trim((string)$this->input('status', 'active')),
            'user_id' => $userId,
            'ip_address' => trim((string)$this->input('ip_address', '')),
            'device_secret' => $deviceSecret,
        ];

        if ($data['device_name'] === '' || $data['device_type'] === '') {
            $_SESSION['error'] = 'กรุณากรอกชื่ออุปกรณ์และประเภทอุปกรณ์';
            $_SESSION['old'] = $data;
            redirect('/devices/create');
        }

        if (!in_array($data['status'], ['active', 'inactive', 'maintenance'], true)) {
            $_SESSION['error'] = 'สถานะอุปกรณ์ไม่ถูกต้อง';
            $_SESSION['old'] = $data;
            redirect('/devices/create');
        }

        $this->devices->create($data);
        app_log("Device created: {$data['device_name']} (user_id={$user['id']})");

        $_SESSION['success'] = 'เพิ่มอุปกรณ์เรียบร้อยแล้ว';
        redirect('/devices');
    }

    public function edit(): void
    {
        $user = auth_user();
        $id = (int)($_GET['id'] ?? 0);
        $device = has_role('admin') ? $this->devices->find($id) : $this->devices->findForUser($id, $user['id']);

        if (!$device) {
            http_response_code(404);
            exit('Device not found');
        }

        $data = [
            'title' => 'แก้ไขอุปกรณ์',
            'user' => $user,
            'device' => $device
        ];

        if (has_role('admin')) {
            $data['users'] = $this->users->all();
        }

        $this->view('devices/edit', $data);
    }

    public function update(): void
    {
        $user = auth_user();
        $id = (int)($_POST['id'] ?? 0);
        $device = has_role('admin') ? $this->devices->find($id) : $this->devices->findForUser($id, $user['id']);

        if (!$device) {
            http_response_code(404);
            exit('Device not found');
        }

        $secretInput = trim((string)$this->input('device_secret', ''));
        $deviceSecret = $secretInput !== '' ? $secretInput : $device['device_secret'];

        $userId = $device['user_id'];
        if (has_role('admin')) {
            $userId = (int)$this->input('user_id', $userId);
            $owner = $this->users->findById($userId);
            if (!$owner) {
                $_SESSION['error'] = 'กรุณาเลือกเจ้าของอุปกรณ์ที่ถูกต้อง';
                redirect('/devices/edit?id=' . $id);
            }
        }

        $data = [
            'device_name' => trim((string)$this->input('device_name', '')),
            'device_type' => trim((string)$this->input('device_type', '')),
            'location' => trim((string)$this->input('location', '')),
            'status' => trim((string)$this->input('status', 'active')),
            'user_id' => $userId,
            'ip_address' => trim((string)$this->input('ip_address', '')),
            'device_secret' => $deviceSecret,
        ];

        if ($data['device_name'] === '' || $data['device_type'] === '') {
            $_SESSION['error'] = 'กรุณากรอกชื่ออุปกรณ์และประเภทอุปกรณ์';
            redirect('/devices/edit?id=' . $id);
        }

        if (!in_array($data['status'], ['active', 'inactive', 'maintenance'], true)) {
            $_SESSION['error'] = 'สถานะอุปกรณ์ไม่ถูกต้อง';
            redirect('/devices/edit?id=' . $id);
        }

        $this->devices->update($id, $data);
        app_log("Device updated: ID={$id} (user_id={$user['id']})");

        $_SESSION['success'] = 'แก้ไขอุปกรณ์เรียบร้อยแล้ว';
        redirect('/devices');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $device = $this->devices->find($id);

        if (!$device) {
            http_response_code(404);
            exit('Device not found');
        }

        $this->devices->delete($id);
        app_log("Device deleted: ID={$id}");

        $_SESSION['success'] = 'ลบอุปกรณ์เรียบร้อยแล้ว';
        redirect('/devices');
    }
}