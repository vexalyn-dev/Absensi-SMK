<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider
     */
    public function redirect(string $provider)
    {
        // Force Google to always show account chooser
        if ($provider === 'google') {
            return Socialite::driver($provider)
                ->scopes(['openid', 'email', 'profile'])
                ->with(['prompt' => 'select_account'])
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Callback from provider
     */
    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if user already exists
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // User already exists — update provider info and auto-login
                $existingUser->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);

                Auth::login($existingUser);

                // Redirect based on role
                if ($existingUser->isTeacher()) {
                    return redirect()->intended(route('teacher.dashboard'))
                        ->with('success', 'Login berhasil melalui ' . ucfirst($provider));
                }

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Login berhasil melalui ' . ucfirst($provider));
            }

            // User is NEW — create account and auto-login
            $newUser = User::create([
                'name'        => $socialUser->getName(),
                'email'       => $socialUser->getEmail(),
                'password'    => bcrypt(Str::random(24)),
                'role'        => 'guru',
                'is_active'   => true,
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);

            Auth::login($newUser);

            // Redirect based on role
            if ($newUser->isTeacher()) {
                return redirect()->intended(route('teacher.dashboard'))
                    ->with('success', 'Akun berhasil dibuat dan Anda telah masuk melalui ' . ucfirst($provider) . '!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Akun berhasil dibuat dan Anda telah masuk melalui ' . ucfirst($provider) . '!');

        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'Terjadi kesalahan saat login dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }
    }
}
