<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    private function redirectByRole(User $user, string $provider, bool $isNew = false)
    {
        $message = $isNew
            ? 'Akun berhasil dibuat dan Anda telah masuk melalui ' . ucfirst($provider) . '!'
            : 'Login berhasil melalui ' . ucfirst($provider);

        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard')->with('success', $message);
        }

        if ($user->isAdmin()) {
            return redirect()->route('dashboard')->with('success', $message);
        }

        Auth::logout();

        return redirect()->route('login')
            ->with('error', 'Role akun tidak dikenali. Hubungi admin.');
    }

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

            Log::info('Social login callback started', [
                'provider' => $provider,
                'email' => $socialUser->getEmail(),
                'id' => $socialUser->getId(),
            ]);

            // Check if user already exists
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                Log::info('Social login existing user', [
                    'provider' => $provider,
                    'user_id' => $existingUser->id,
                    'email' => $existingUser->email,
                ]);

                // User already exists — update provider info and auto-login
                $existingUser->update([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);

                Auth::login($existingUser);

                return $this->redirectByRole($existingUser, $provider);
            }

            Log::info('Social login creating new user', [
                'provider' => $provider,
                'email' => $socialUser->getEmail(),
            ]);

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

            // Kirim email sambutan setelah akun dibuat
            Log::info('Attempting to send welcome email for new social user', [
                'user_id' => $newUser->id,
                'email' => $newUser->email,
                'provider' => $provider,
            ]);

            try {
                Mail::mailer('smtp')->to($newUser->email)->send(new WelcomeEmail($newUser));
                Log::info('Welcome email sent successfully', [
                    'user_id' => $newUser->id,
                    'email' => $newUser->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: ' . $e->getMessage(), [
                    'user_id' => $newUser->id,
                    'email' => $newUser->email,
                ]);
            }

            Auth::login($newUser);

            return $this->redirectByRole($newUser, $provider, true);

        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'Terjadi kesalahan saat login dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }
    }
}
