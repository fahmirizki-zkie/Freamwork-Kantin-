<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpMail;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'User Google',
                'email' => $googleUser->getEmail(),
                'id_google' => $googleUser->getId(),
                'password' => Str::random(32),
            ]);
        } elseif (!$user->id_google) {
            $user->update([
                'id_google' => $googleUser->getId(),
            ]);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
        ]);

        Mail::to($user->email)->send(new OtpMail($otp));

        session(['otp_user_id' => $user->id]);

        return redirect('/verify-otp');
    }

    /**
     * Tampilkan halaman input OTP
     */
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $user = User::find(session('otp_user_id'));

        if ($user && $user->otp == $request->otp) {

            // Login user
            Auth::login($user);
            $request->session()->regenerate();

            // Hapus OTP setelah berhasil
            $user->update([
                'otp' => null
            ]);

            session()->forget('otp_user_id');

            $handoffToken = Str::random(64);
            Cache::put('vendor_login_handoff_'.$handoffToken, $user->id, now()->addMinutes(2));

            return redirect()->to('http://vendor.localhost:8000/auth/vendor-handoff?token='.$handoffToken);
        }

        return back()->with('error', 'OTP salah');
    }

    /**
     * Bangun session login di domain vendor menggunakan token one-time.
     */
    public function vendorHandoff(Request $request)
    {
        $token = (string) $request->query('token', '');

        if ($token === '') {
            abort(403, 'Token tidak valid.');
        }

        $userId = Cache::pull('vendor_login_handoff_'.$token);

        if (!$userId) {
            abort(403, 'Token login sudah kedaluwarsa atau tidak valid.');
        }

        $user = User::find($userId);

        if (!$user) {
            abort(403, 'User tidak ditemukan.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to('http://vendor.localhost:8000/')->with('success', 'Login berhasil');
    }
}
