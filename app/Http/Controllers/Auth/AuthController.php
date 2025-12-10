<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request via AJAX.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $credentials = $this->authService->validateLoginData($request->all());

            // Attempt to login
            $result = $this->authService->login($credentials);

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect_url' => $result['redirect_url'],
                'user' => [
                    'name' => $result['user']->full_name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Handle logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        try {
            // Logout user
            Auth::logout();

            // Clear session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to login page
            return redirect()->route('login')->with('success', 'Logged out successfully');

        } catch (\Exception $e) {

            // Redirect back with normal error message
            return redirect()->back()->with('error', 'Something went wrong during logout.');
        }
    }

    /**
     * Check authentication status.
     */
    public function checkAuth(): JsonResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            return response()->json([
                'authenticated' => true,
                'user' => [
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        }

        return response()->json([
            'authenticated' => false,
        ], 200);
    }

    public function forgot_password(): View
    {
        return view('auth.reset_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'We couldn’t find an account with that email. Please check and try again.',
            ], 404);
        }

        // Create Token
        $token = Str::random(64);

        // Store Token
        DB::table('password_reset_tokens')->updateOrInsert([
            'email' => $request->email,
        ], [
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        // Send Mail
        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return response()->json([
            'status' => true,
            'message' => 'Password reset link sent to your email.',
        ], 200);
    }

    public function reset_password_form($token)
    {
        return view('auth.reset_password_form', compact('token'));
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check Token
        $record = DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if (! $record) {
            return response()->json([
                'status' => false,
                'message' => 'This reset link is invalid or has expired. Please request a new one.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $tokenCreatedAt = Carbon::parse($record->created_at);
        if ($tokenCreatedAt->addMinutes(60)->isPast()) {
            // Remove expired token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'status' => false,
                'message' => 'Your reset link has expired. Please request a new one.',
            ], 400);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'We couldn’t find an account with this email.',
            ], 404);
        }

        // Update Password
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        // Remove used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password has been reset successfully!',
        ], 200);
    }
}
