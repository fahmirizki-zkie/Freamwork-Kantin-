<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OtpMail;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
        $googleUser = Socialite::driver('google')->stateless()->user();
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
        ]);
    }

    // Generate OTP
    $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp
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

            // Hapus OTP setelah berhasil
            $user->update([
                'otp' => null
            ]);

            session()->forget('otp_user_id');

            return redirect('/')->with('success', 'Login berhasil');
        }

        return back()->with('error', 'OTP salah');
    }
}
