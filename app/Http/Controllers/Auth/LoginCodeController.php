<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoginCodes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Acceso sin contraseña. La respuesta es la misma exista o no la cuenta,
 * para no revelar qué correos están registrados.
 */
class LoginCodeController extends Controller
{
    public const SESSION_EMAIL = 'login_code.email';

    public const SESSION_SENT_AT = 'login_code.sent_at';

    public function __construct(private LoginCodes $codes) {}

    /** Pedir un código. */
    public function store(Request $request): RedirectResponse
    {
        $email = Str::lower($request->validate(['email' => ['required', 'string', 'email', 'max:255']])['email']);

        $this->sendTo($request, $email);

        return redirect()->route('login.code');
    }

    /** Pantalla para escribir el código. */
    public function show(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_EMAIL);
        if (! $email) {
            return redirect()->route('login');
        }

        $wait = LoginCodes::RESEND_SECONDS - (now()->timestamp - (int) $request->session()->get(self::SESSION_SENT_AT, 0));

        return view('auth.login-code', [
            'maskedEmail' => self::mask($email),
            'resendIn' => max(0, $wait),
            'minutes' => LoginCodes::TTL_MINUTES,
        ]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_EMAIL);
        if (! $email) {
            return redirect()->route('login');
        }

        if (now()->timestamp - (int) $request->session()->get(self::SESSION_SENT_AT, 0) < LoginCodes::RESEND_SECONDS) {
            return back()->withErrors(['code' => 'Espera un momento antes de pedir otro código.']);
        }

        $this->sendTo($request, $email);

        return redirect()->route('login.code')->with('status', 'Te enviamos un código nuevo.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_EMAIL);
        if (! $email) {
            return redirect()->route('login');
        }

        $code = preg_replace('/\D/', '', (string) $request->input('code'));
        $user = strlen($code) === 6 ? User::where('email', $email)->first() : null;
        $user = $this->codes->allowedFor($user) ? $this->codes->verify($user, $code) : null;

        if (! $user) {
            throw ValidationException::withMessages(['code' => 'El código es incorrecto o ya venció.']);
        }

        return $this->login($request, $user);
    }

    /** El enlace del correo muestra una confirmación (los antivirus abren enlaces solos). */
    public function confirmLink(string $token): View
    {
        return view('auth.login-link', ['token' => $token]);
    }

    public function link(Request $request, string $token): RedirectResponse
    {
        $user = $this->codes->verifyLink($token);

        if (! $user || ! $this->codes->allowedFor($user)) {
            return redirect()->route('login')->withErrors(['email' => 'El enlace ya se usó o venció. Pide un código nuevo.']);
        }

        return $this->login($request, $user);
    }

    public function sendTo(Request $request, string $email): void
    {
        $limits = ['login-code:email:'.$email => 3, 'login-code:ip:'.$request->ip() => 10];
        foreach ($limits as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                throw ValidationException::withMessages([
                    'email' => 'Pediste demasiados códigos. Intenta de nuevo en '.ceil(RateLimiter::availableIn($key) / 60).' minutos.',
                ]);
            }
        }
        foreach ($limits as $key => $max) {
            RateLimiter::hit($key, 15 * 60);
        }

        $user = User::where('email', $email)->first();
        if ($this->codes->allowedFor($user)) {
            $this->codes->send($user);
        }

        $request->session()->put(self::SESSION_EMAIL, $email);
        $request->session()->put(self::SESSION_SENT_AT, now()->timestamp);
    }

    private function login(Request $request, User $user): RedirectResponse
    {
        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $request->session()->forget([self::SESSION_EMAIL, self::SESSION_SENT_AT]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public static function mask(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1).str_repeat('•', max(2, min(6, mb_strlen($local) - 1))).'@'.$domain;
    }
}
