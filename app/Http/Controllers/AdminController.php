<?php

namespace App\Http\Controllers;

use App\Services\AdminStats;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __invoke(Request $request): View
    {
        $includeDemo = $request->boolean('demo');
        $stats = new AdminStats($includeDemo);

        return view('admin.dashboard', [
            'includeDemo' => $includeDemo,
            'kpis' => $stats->kpis(),
            'spectrum' => $stats->spectrum(),
            'byCategory' => $stats->byCategory(),
            'spectrogram' => $stats->spectrogram(),
            'perMonth' => $stats->recordsPerMonth(),
            'latestUsers' => $stats->latestUsers(),
        ]);
    }
}
