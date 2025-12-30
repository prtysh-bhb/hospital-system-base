<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt to authenticate a user.
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $remember = $credentials['remember'] ?? false;

        // Attempt to authenticate with username instead of email
        if (
            ! Auth::attempt([
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ], $remember)
        ) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        // Prevent patients from logging in through staff login
        if ($user->role === 'patient') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => ['Patients cannot login here. Please use the Patient Portal.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => ['Your account is not active. Please contact support.'],
            ]);
        }

        // Regenerate session
        request()->session()->regenerate();

        return [
            'user' => $user,
            'redirect_url' => $this->getRedirectUrl($user->role),
        ];
    }

    /**
     * Log the user out.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Get the redirect URL based on user role.
     */
    protected function getRedirectUrl(string $role): string
    {
        return match ($role) {
            'admin' => route('admin.dashboard'),
            'doctor' => route('doctor.dashboard'),
            'frontdesk' => route('frontdesk.dashboard'),
            'patient' => route('patient.dashboard'),
            default => route('home'),
        };
    }

    /**
     * Validate login credentials.
     */
    public function validateLoginData(array $data): array
    {
        return validator($data, [
            'username' => 'required|string',
            // 'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['boolean'],
        ])->validate();
    }

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Check if user exists and is active.
     */
    public function isUserActive(string $email): bool
    {
        $user = $this->getUserByEmail($email);

        return $user && $user->status === 'active';
    }
}
