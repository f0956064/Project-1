@extends('front.layouts.app')

@section('content')
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="front-card" style="padding: 24px;">
        <h3 style="margin-top: 0; color: var(--theme-primary);">Register</h3>
        @include('admin.components.messages')

          <form method="POST" action="{{ url()->current() }}">
            @csrf

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="first_name">First name</label>
                  <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                         class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" required>
                  @if ($errors->has('first_name'))
                    <span class="help-block"><strong>{{ $errors->first('first_name') }}</strong></span>
                  @endif
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label for="last_name">Last name</label>
                  <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                         class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" required>
                  @if ($errors->has('last_name'))
                    <span class="help-block"><strong>{{ $errors->first('last_name') }}</strong></span>
                  @endif
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="phone">Phone</label>
              <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                     class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}">
              @if ($errors->has('phone'))
                <span class="help-block"><strong>{{ $errors->first('phone') }}</strong></span>
              @endif
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" type="email" name="email" value="{{ old('email') }}"
                     class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required>
              @if ($errors->has('email'))
                <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
              @endif
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="password">Password</label>
                  <input id="password" type="password" name="password"
                         class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required>
                  @if ($errors->has('password'))
                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                  @endif
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label for="password_confirmation">Confirm password</label>
                  <input id="password_confirmation" type="password" name="password_confirmation"
                         class="form-control" required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="refercode">Referral code (optional)</label>
              <input id="refercode" type="text" name="refercode" value="{{ old('refercode') }}"
                     class="form-control{{ $errors->has('refercode') ? ' is-invalid' : '' }}" placeholder="Enter referral code if you have one">
              @if ($errors->has('refercode'))
                <span class="help-block"><strong>{{ $errors->first('refercode') }}</strong></span>
              @endif
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-theme btn-block btn-lg">Create account</button>
              <p class="text-center" style="margin-top: 14px;">
                <a href="{{ request()->is('customer*') ? route('customer.login') : route('login') }}">Already registered? Login</a>
              </p>
            </div>
          </form>
      </div>
    </div>
  </div>
@endsection
