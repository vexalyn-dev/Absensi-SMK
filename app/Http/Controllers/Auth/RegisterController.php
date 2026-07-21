<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'is_active' => true,
        ]);

        // Generate QR Code for new teacher
        if ($user->role === 'guru') {
            $user->generateQrCode();
        }

        try {
            Mail::mailer('smtp')->to($user->email)->send(new WelcomeEmail($user));
            Log::info('Welcome email sent successfully for registered user', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email for registered user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        Auth::login($user);

        // Redirect based on role
        if ($user->isTeacher()) {
            return redirect('/teacher/dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
        }

        return redirect('/dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
    }
}
