@extends('front.layouts.app')

@section('content')
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="front-card" style="padding: 24px;">
        <h3 style="margin-top: 0; color: var(--theme-primary);">Verify OTP</h3>
        <p class="text-muted">Enter the 6-digit OTP sent to your email/phone.</p>
        @include('admin.components.messages')

        <form method="POST" action="{{ route('otp.verify') }}">
          @csrf
          <div class="form-group">
            <label for="otp">OTP</label>
            <input id="otp" type="text" name="otp" value="{{ old('otp') }}"
                   class="form-control{{ $errors->has('otp') ? ' is-invalid' : '' }}"
                   maxlength="6" pattern="[0-9]*" inputmode="numeric" placeholder="000000" required autofocus>
            @if ($errors->has('otp'))
              <span class="help-block text-danger"><strong>{{ $errors->first('otp') }}</strong></span>
            @endif
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-theme btn-block btn-lg">Verify</button>
          </div>
        </form>

        <p class="text-center" style="margin-top: 16px;">
          <a href="{{ route('otp.resend') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">Resend OTP</a>
        </p>
        <form id="resend-form" action="{{ route('otp.resend') }}" method="POST" style="display: none;">
          @csrf
        </form>

        <p class="text-center" style="margin-top: 14px;">
          <a href="{{ route('login') }}">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
@endsection
