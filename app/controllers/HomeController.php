<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'title' => 'EOIM Platform',
            'message' => 'ระบบบริหารจัดการพลังงานอัจฉริยะพร้อม AI'
        ]);
    }

    public function health(): void
    {
        $this->json([
            'status' => 'ok',
            'system' => 'EOIM Platform',
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}