<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = Str::lower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        $errorMessage = __('The provided credentials do not match our records.');

        if ($user && !empty($user->id_google)) {
            $errorMessage = 'Password tidak cocok. Bisa juga login lewat tombol Google.';
        }

        throw ValidationException::withMessages([
            'email' => $errorMessage,
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
