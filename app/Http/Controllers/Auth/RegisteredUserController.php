<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [config('auth.login_codes') ? 'nullable' : 'required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : null,
        ]);

        event(new Registered($user));

        // Sin contraseña: se confirma el correo con un código y entra con él.
        if (! $user->hasPassword()) {
            app(LoginCodeController::class)->sendTo($request, $user->email);

            return redirect()->route('login.code')->with('status', 'Cuenta creada. Te enviamos un código para confirmar tu correo y entrar.');
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
