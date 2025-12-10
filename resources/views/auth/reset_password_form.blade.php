@extends('layouts.public')

@section('title', 'Login')
@section('body-class',
    'bg-gradient-to-br from-sky-50 via-white to-sky-50 min-h-screen flex items-center justify-center
    p-4')

@section('content')
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-6 sm:mb-8">
            <div
                class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-sky-600 rounded-2xl mb-3 sm:mb-4">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">MediCare HMS</h1>
            <p class="text-sm sm:text-base text-gray-600">Hospital Management System</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Reset Your Password</h2>

            <!-- Instructions -->
            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                Enter your new password below to reset your account password.
            </p>

            <!-- Alert Message -->
            <div id="alert-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

            <form id="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                            readonly>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">This email is verified and cannot be changed</p>
                </div>

                <!-- Password -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Enter your password"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors duration-200 pr-10">
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
                            <!-- Eye Icon (Visible) -->
                            <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon (Hidden) -->
                            <svg id="password-eye-slash" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <span class="text-red-500 text-xs mt-1 hidden" id="password-error"></span>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Confirm your password"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors duration-200 pr-10">
                        <button type="button" id="toggle-password-confirmation"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
                            <!-- Eye Icon (Visible) -->
                            <svg id="password-confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon (Hidden) -->
                            <svg id="password-confirmation-eye-slash" class="w-5 h-5 hidden" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <span class="text-red-500 text-xs mt-1 hidden" id="password_confirmation-error"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn"
                    class="w-full py-2.5 sm:py-3 px-4 text-sm sm:text-base bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="btn-text">Reset Password</span>
                    <svg id="btn-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 sm:mt-6">
            <a href="{{ route('login') }}" class="text-xs sm:text-sm text-sky-600 hover:text-sky-700 font-medium">
                ← Back to Login
            </a>
        </div>
        <p class="text-center text-xs sm:text-sm text-gray-600 mt-3 sm:mt-4">
            © 2025 MediCare HMS. All rights reserved.
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Password visibility toggle
                const togglePassword = document.getElementById('toggle-password');
                const passwordInput = document.getElementById('password');
                const passwordEye = document.getElementById('password-eye');
                const passwordEyeSlash = document.getElementById('password-eye-slash');

                const togglePasswordConfirmation = document.getElementById('toggle-password-confirmation');
                const passwordConfirmationInput = document.getElementById('password_confirmation');
                const passwordConfirmationEye = document.getElementById('password-confirmation-eye');
                const passwordConfirmationEyeSlash = document.getElementById('password-confirmation-eye-slash');

                const emailInput = document.getElementById('email');

                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle eye icons
                    passwordEye.classList.toggle('hidden');
                    passwordEyeSlash.classList.toggle('hidden');
                });

                togglePasswordConfirmation.addEventListener('click', function() {
                    const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' :
                        'password';
                    passwordConfirmationInput.setAttribute('type', type);

                    // Toggle eye icons
                    passwordConfirmationEye.classList.toggle('hidden');
                    passwordConfirmationEyeSlash.classList.toggle('hidden');
                });

                // Form validation
                const form = document.getElementById('reset-password-form');
                const passwordError = document.getElementById('password-error');
                const passwordConfirmationError = document.getElementById('password_confirmation-error');
                const alertMessage = document.getElementById('alert-message');
                const submitBtn = document.getElementById('submit-btn');
                const btnText = document.getElementById('btn-text');
                const btnSpinner = document.getElementById('btn-spinner');

                // Remove error on input
                passwordInput.addEventListener('input', function() {
                    clearError(passwordInput, passwordError);
                    clearAlert();
                });

                passwordConfirmationInput.addEventListener('input', function() {
                    clearError(passwordConfirmationInput, passwordConfirmationError);
                    clearAlert();
                });

                // Form submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Reset errors
                    clearError(passwordInput, passwordError);
                    clearError(passwordConfirmationInput, passwordConfirmationError);
                    clearAlert();

                    // Get form values
                    const email = emailInput.value.trim();
                    const password = passwordInput.value.trim();
                    const passwordConfirmation = passwordConfirmationInput.value.trim();
                    const token = document.querySelector('input[name="token"]').value;

                    let isValid = true;

                    // Validate password
                    if (!password) {
                        showError(passwordInput, passwordError, 'Password is required');
                        isValid = false;
                    } else if (password.length < 6) {
                        showError(passwordInput, passwordError, 'Password must be at least 6 characters');
                        isValid = false;
                    }

                    // Validate password confirmation
                    if (!passwordConfirmation) {
                        showError(passwordConfirmationInput, passwordConfirmationError,
                            'Please confirm your password');
                        isValid = false;
                    } else if (password !== passwordConfirmation) {
                        showError(passwordConfirmationInput, passwordConfirmationError,
                            'Passwords do not match');
                        isValid = false;
                    }

                    if (isValid) {
                        // Show loading state
                        submitBtn.disabled = true;
                        btnText.textContent = 'Resetting Password...';
                        btnSpinner.classList.remove('hidden');

                        // Make AJAX request
                        fetch('{{ route('password.update') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    token: token,
                                    email: email,
                                    password: password,
                                    password_confirmation: passwordConfirmation
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    // Show success message
                                    showAlert(data.message, 'success');

                                    // Redirect to login after 2 seconds
                                    setTimeout(() => {
                                        window.location.href = '{{ route('login') }}';
                                    }, 2000);
                                } else {
                                    // Reset loading state
                                    submitBtn.disabled = false;
                                    btnText.textContent = 'Reset Password';
                                    btnSpinner.classList.add('hidden');

                                    // Show error message
                                    showAlert(data.message, 'error');
                                }
                            })
                            .catch(error => {
                                // Reset loading state
                                submitBtn.disabled = false;
                                btnText.textContent = 'Reset Password';
                                btnSpinner.classList.add('hidden');

                                // Show error message
                                showAlert('Something went wrong. Please try again.', 'error');
                            });
                    }
                });

                function showError(inputElement, errorElement, message) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');

                    // Apply Tailwind error styling
                    inputElement.classList.remove('border-gray-300', 'border-green-500', 'ring-2', 'ring-green-200');
                    inputElement.classList.add('border-red-500', 'ring-2', 'ring-red-200');

                    inputElement.focus();
                }

                function clearError(inputElement, errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.add('hidden');

                    // Remove Tailwind error styling and restore default
                    inputElement.classList.remove('border-red-500', 'ring-2', 'ring-red-200', 'border-green-500',
                        'ring-2', 'ring-green-200');
                    inputElement.classList.add('border-gray-300');
                }

                function showAlert(message, type) {
                    alertMessage.textContent = message;
                    alertMessage.classList.remove('hidden');

                    if (type === 'success') {
                        alertMessage.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-200');
                    } else {
                        alertMessage.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
                    }
                }

                function clearAlert() {
                    alertMessage.textContent = '';
                    alertMessage.classList.add('hidden');
                    alertMessage.className = 'hidden mb-4 p-3 rounded-lg text-sm';
                }
            });
        </script>
    @endpush
@endsection
