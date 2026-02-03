@extends('front.layouts.app')

@section('content')
<div class="auth-card">
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="front-card">
        <h3>Verify OTP</h3>

        <p class="text-center" style="color: var(--text-secondary); margin-bottom: 20px;">
          Enter the 6-digit OTP sent to your email or phone
        </p>

        @include('admin.components.messages')

        <form method="POST" action="{{ route('otp.verify') }}">
          @csrf

          <div class="form-group">
            <label class="front-form-label">OTP</label>
            <input
              type="text"
              name="otp"
              value="{{ old('otp') }}"
              maxlength="6"
              pattern="[0-9]*"
              inputmode="numeric"
              class="front-form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}"
              placeholder="000000"
              required autofocus
              style="text-align:center; letter-spacing:8px; font-size:1.2rem;"
            >
            @if ($errors->has('otp'))
              <span class="front-help-block">
                <strong>{{ $errors->first('otp') }}</strong>
              </span>
            @endif
          </div>

          <button type="submit" class="btn btn-theme btn-block btn-lg">
            Verify
          </button>
        </form>

        <p class="text-center">
          <a href="{{ route('otp.resend') }}"
             onclick="event.preventDefault(); document.getElementById('resend-form').submit();">
            Resend OTP
          </a>
        </p>

        <form id="resend-form" action="{{ route('otp.resend') }}" method="POST" style="display:none;">
          @csrf
        </form>

        <p class="text-center">
          <a href="{{ route('login') }}">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
