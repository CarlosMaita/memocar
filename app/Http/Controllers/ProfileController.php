<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Sin contraseña (entra por código): se confirma escribiendo ELIMINAR.
        $request->validateWithBag('userDeletion', $request->user()->hasPassword()
            ? ['password' => ['required', 'current_password']]
            : ['password' => ['required', 'in:ELIMINAR,eliminar']],
            ['password.in' => 'Escribe ELIMINAR para confirmar.']);

        $user = $request->user();

        Auth::logout();

        // Primero los carros (arrastran sus registros), luego el usuario y su catálogo.
        $user->cars()->delete();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
