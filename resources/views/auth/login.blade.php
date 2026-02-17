@extends('front.layouts.app')

@section('content')
<div class="auth-card">
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="front-card">
        <h3>{{ __('Login') }}</h3>

        @include('admin.components.messages')

        <form method="POST" action="{{ url()->current() }}">
          @csrf

          @php
            $isAdmin = request()->is('admin*');
            $loginField = $isAdmin ? 'email' : 'mobile_or_email';
          @endphp

          <div class="form-group">
            <label for="{{ $loginField }}" class="front-form-label">
              {{ $isAdmin ? __('E-Mail Address') : __('Mobile or Email') }}
            </label>
            <input
              id="{{ $loginField }}"
              type="{{ $isAdmin ? 'email' : 'text' }}"
              name="{{ $loginField }}"
              value="{{ old($loginField) }}"
              class="front-form-control{{ $errors->has($loginField) ? ' is-invalid' : '' }}"
              placeholder="{{ $isAdmin ? 'Email address' : 'Phone number or email' }}"
              required autofocus
            >
            @if ($errors->has($loginField))
              <span class="front-help-block">
                <strong>{{ $errors->first($loginField) }}</strong>
              </span>
            @endif
          </div>

          <div class="form-group">
            <label for="password" class="front-form-label">{{ __('Password') }}</label>
            <input
              id="password"
              type="password"
              name="password"
              class="front-form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
              placeholder="Enter your password"
              required
            >
            @if ($errors->has('password'))
              <span class="front-help-block">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
            @endif
          </div>

          <div class="form-group">
            <label class="front-checkbox">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              {{ __('Remember Me') }}
            </label>
          </div>

          <button type="submit" class="btn btn-theme btn-block btn-lg">
            {{ __('Login') }}
          </button>

          @if (Route::has('password.request'))
            <p class="text-center">
              <a href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
              </a>
            </p>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
