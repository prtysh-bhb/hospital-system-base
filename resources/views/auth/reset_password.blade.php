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
                Enter your email address and we'll send you a link to reset your password.
            </p>

            <!-- Alert Message -->
            <div id="alert-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

            <form id="forgot-password-form" method="POST">
                @csrf
                <!-- Email -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your registered email"
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors duration-200">
                    <span class="text-red-500 text-xs mt-1 hidden" id="email-error"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn"
                    class="w-full py-2.5 sm:py-3 px-4 text-sm sm:text-base bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg id="btn-spinner" class="hidden animate-spin mr-2 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span id="btn-text">Send Reset Link</span>
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
            $(document).ready(function() {
                const emailInput = $('#email');
                const emailError = $('#email-error');
                const submitBtn = $('#submit-btn');
                const alertMessage = $('#alert-message');

                // Email validation function
                function validateEmail(email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(email);
                }

                // Real-time email validation
                emailInput.on('input', function() {
                    const email = $(this).val().trim();

                    // Clear previous states
                    emailInput.removeClass('error-border success-border');
                    emailError.addClass('hidden');

                    if (email === '') {
                        // If empty, just remove styling
                        return;
                    }

                    if (!validateEmail(email)) {
                        // Invalid email
                        emailInput.addClass('error-border');
                        emailError.removeClass('hidden').text('Please enter a valid email address');
                    } else {
                        // Valid email
                        emailInput.addClass('success-border');
                    }
                });

                // Form submission
                $('#forgot-password-form').on('submit', function(e) {
                    e.preventDefault();

                    let email = $('#email').val().trim();
                    const submitBtn = $('#submit-btn');
                    const btnText = $('#btn-text');
                    const btnSpinner = $('#btn-spinner');

                    // Clear previous alerts
                    alertMessage.addClass('hidden').removeClass(
                        'bg-green-100 text-green-800 bg-red-100 text-red-800');

                    // Validate email
                    if (!email) {
                        emailInput.addClass('error-border');
                        emailError.removeClass('hidden').text('Please enter your email address');
                        return;
                    }

                    if (!validateEmail(email)) {
                        emailInput.addClass('error-border');
                        emailError.removeClass('hidden').text('Please enter a valid email address');
                        return;
                    }

                    // Show loading state
                    submitBtn.prop('disabled', true);
                    btnText.text('Sending...');
                    btnSpinner.removeClass('hidden');

                    $.ajax({
                        url: "{{ route('forgot-password.send') }}",
                        method: "POST",
                        data: {
                            email: email,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            // Reset loading state
                            submitBtn.prop('disabled', false);
                            btnText.text('Send Reset Link');
                            btnSpinner.addClass('hidden');

                            alertMessage
                                .removeClass('hidden bg-red-100 text-red-800')
                                .addClass('bg-green-100 text-green-800 border border-green-200')
                                .text(res.message);

                            // Clear email field
                            emailInput.val('').removeClass('success-border');
                        },
                        error: function(err) {
                            // Reset loading state
                            submitBtn.prop('disabled', false);
                            btnText.text('Send Reset Link');
                            btnSpinner.addClass('hidden');

                            alertMessage
                                .removeClass('hidden bg-green-100 text-green-800')
                                .addClass('bg-red-100 text-red-800 border border-red-200')
                                .text(err.responseJSON?.message ||
                                    'Something went wrong. Please try again.');
                        }
                    });
                });

                // Clear validation on focus
                emailInput.on('focus', function() {
                    $(this).removeClass('error-border');
                    emailError.addClass('hidden');
                });
            });
        </script>
    @endpush
@endsection
