<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientAuthController extends Controller
{
    /**
     * Show the patient login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'patient') {
            return redirect()->route('patient.dashboard');
        }

        return view('patient.login');
    }

    /**
     * Handle patient login (using first name and mobile number)
     */
    public function login(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'phone' => 'required|string',
        ], [
            'first_name.required' => 'Please enter your first name.',
            'phone.required' => 'Please enter your mobile number.',
        ]);

        // Find patient by first name and phone number
        $patient = User::where('role', 'patient')
            ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($request->first_name))])
            ->where('phone', $request->phone)
            ->first();

        if (! $patient) {
            return back()
                ->withInput($request->only('first_name', 'phone'))
                ->withErrors([
                    'credentials' => 'No patient found with this name and mobile number. Please check your details.',
                ]);
        }

        // Check if account is active
        if ($patient->status !== 'active') {
            return back()
                ->withInput($request->only('first_name', 'phone'))
                ->withErrors([
                    'credentials' => 'Your account is not active. Please contact the hospital.',
                ]);
        }

        // Log in the patient
        Auth::login($patient, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('patient.dashboard'))
            ->with('success', 'Welcome back, '.$patient->first_name.'!');
    }

    /**
     * Handle patient logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('patient.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
