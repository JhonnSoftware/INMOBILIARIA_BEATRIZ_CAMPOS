<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Support\ProyectoDashboardService;
use Illuminate\View\View;

class ProyectoDashboardController extends Controller
{
    public function __construct(
        protected ProyectoDashboardService $dashboardService
    ) {
    }

    public function index(Proyecto $proyecto): View
    {
        return view('admin.proyectos.dashboard.index', [
            'proyecto' => $proyecto,
            'dashboard' => $this->dashboardService->build($proyecto),
        ]);
    }
}
