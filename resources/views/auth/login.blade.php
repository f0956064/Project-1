@extends('front.layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/login-redesign.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="login-page-wrapper">
    <div class="login-container">

        <!-- Welcome Text -->
        <div class="welcome-text">
            <h2>Hi! Welcome Back</h2>
            <h1>Please, Log In</h1>
        </div>

        @include('admin.components.messages')

        <!-- Login Form -->
        <form method="POST" action="{{ url()->current() }}" class="auth-form">
            @csrf

            @php
                $isAdmin = request()->is('admin*');
                $loginField = $isAdmin ? 'email' : 'mobile_or_email';
            @endphp

            <div class="form-group">
                <input
                    id="{{ $loginField }}"
                    type="{{ $isAdmin ? 'email' : 'text' }}"
                    name="{{ $loginField }}"
                    value="{{ old($loginField) }}"
                    class="pill-input {{ $errors->has($loginField) ? 'is-invalid' : '' }}"
                    placeholder="Mobile"
                    required autofocus
                >
                @if ($errors->has($loginField))
                    <span class="front-help-block" style="color: #ffb7b7; padding-left: 20px;">
                        <strong>{{ $errors->first($loginField) }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="pill-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Password"
                    required
                >
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                @if ($errors->has('password'))
                    <span class="front-help-block" style="color: #ffb7b7; padding-left: 20px;">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" class="btn-pill btn-login">
                Login
            </button>
        </form>

        <!-- Register Section -->
        <div class="register-section">
            <p>Create account</p>
            <a href="{{ route('customer.register') }}" class="btn-pill btn-register">
                Register
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
@endsection
