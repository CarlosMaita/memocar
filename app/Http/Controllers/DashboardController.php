<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceSchedule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(MaintenanceSchedule $schedule): View
    {
        $car = $this->car();
        $records = $car->records();

        return view('dashboard', [
            'car' => $car,
            'items' => $schedule->forCar($car),
            'neverDone' => $schedule->neverDone($car),
            'totalCost' => (float) $records->clone()->sum('cost'),
            'yearCost' => (float) $records->clone()->whereYear('date', now()->year)->sum('cost'),
            'recordsCount' => $records->clone()->count(),
            'recent' => $records->clone()->with('type')->latest('date')->latest('id')->limit(4)->get(),
        ]);
    }
}
