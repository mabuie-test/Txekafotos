<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\HomepageService;

class HomeController extends Controller
{
    public function index(): void
    {
        $service = new HomepageService();
        $this->view('home/index', $service->getHomepageData());
    }
}
