@extends('front.layouts.app')

@section('content')
<div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="front-card" style="padding: 24px;">
            <h3 style="margin-top: 0; color: var(--theme-primary);">{{ __('Login') }}</h3>
            @include('admin.components.messages')
            <form method="POST" action="{{ url()->current() }}">
                @csrf
                @php $isAdmin = request()->is('admin*'); $loginField = $isAdmin ? 'email' : 'mobile_or_email'; @endphp
                <div class="form-group">
                    <label for="{{ $loginField }}">{{ $isAdmin ? __('E-Mail Address') : __('Mobile or Email') }}</label>
                    <input id="{{ $loginField }}" type="{{ $isAdmin ? 'email' : 'text' }}" class="form-control{{ $errors->has($loginField) ? ' is-invalid' : '' }}" name="{{ $loginField }}" value="{{ old($loginField) }}" required autofocus placeholder="{{ $isAdmin ? '' : 'Phone number or email' }}">
                    @if ($errors->has($loginField))
                        <span class="help-block text-danger"><strong>{{ $errors->first($loginField) }}</strong></span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block text-danger"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-theme btn-block btn-lg">{{ __('Login') }}</button>
                </div>
                @if (Route::has('password.request'))
                    <p class="text-center"><a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a></p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
