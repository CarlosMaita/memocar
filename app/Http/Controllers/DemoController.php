<?php

namespace App\Http\Controllers;

use App\Actions\CreateDemoUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    /** "Ver como demo": crea una cuenta desechable con datos de ejemplo e inicia sesión. */
    public function store(Request $request, CreateDemoUser $createDemoUser): RedirectResponse
    {
        Auth::login($createDemoUser());
        $request->session()->regenerate();

        return redirect()->route('cars.select');
    }

    /** Salir del demo para crear una cuenta propia. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register');
    }
}
