@extends('front.layouts.app')

@section('content')
<div class="auth-card">
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="front-card">
        <h3>Register</h3>

        @include('admin.components.messages')

        <form method="POST" action="{{ url()->current() }}">
          @csrf

          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="front-form-label">First name</label>
                <input
                  type="text"
                  name="first_name"
                  value="{{ old('first_name') }}"
                  class="front-form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                  placeholder="First name"
                  required
                >
                @if ($errors->has('first_name'))
                  <span class="front-help-block">
                    <strong>{{ $errors->first('first_name') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="front-form-label">Last name</label>
                <input
                  type="text"
                  name="last_name"
                  value="{{ old('last_name') }}"
                  class="front-form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                  placeholder="Last name"
                  required
                >
                @if ($errors->has('last_name'))
                  <span class="front-help-block">
                    <strong>{{ $errors->first('last_name') }}</strong>
                  </span>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="front-form-label">Phone</label>
            <input
              type="text"
              name="phone"
              value="{{ old('phone') }}"
              class="front-form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
              placeholder="Phone number"
            >
            @if ($errors->has('phone'))
              <span class="front-help-block">
                <strong>{{ $errors->first('phone') }}</strong>
              </span>
            @endif
          </div>

          <div class="form-group">
            <label class="front-form-label">Email</label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              class="front-form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
              placeholder="Email address"
              required
            >
            @if ($errors->has('email'))
              <span class="front-help-block">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="front-form-label">Password</label>
                <input
                  type="password"
                  name="password"
                  class="front-form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                  placeholder="Password"
                  required
                >
                @if ($errors->has('password'))
                  <span class="front-help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="front-form-label">Confirm password</label>
                <input
                  type="password"
                  name="password_confirmation"
                  class="front-form-control"
                  placeholder="Confirm password"
                  required
                >
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-theme btn-block btn-lg">
            Create account
          </button>

          <p class="text-center">
            <a href="{{ request()->is('customer*') ? route('customer.login') : route('login') }}">
              Already registered? Login
            </a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
