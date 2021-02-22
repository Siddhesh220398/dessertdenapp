@extends('admin.layouts.auth')

@section('content')
<form class="login-form" action="{{ route('login') }}" method="post">
    @csrf
    <h3 class="form-title">Login to your account</h3>
    <div class="form-group @error('username') has-error @enderror">
        <label class="control-label visible-ie8 visible-ie9">Email Address</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email Address" name="email" value="{{ old('email') }}" />
        </div>
        @error('email')
            <span class="help-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group @error('password') has-error @enderror">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" />
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-actions">
        <label class="rememberme mt-checkbox mt-checkbox-outline">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} /> Remember me
            <span></span>
        </label>
        <button type="submit" class="btn green pull-right"> Login </button>
    </div>
    {{-- <div class="forget-password">
        <h4>Forgot your password ?</h4>
        <p> no worries, click
            <a href="{{ route('password.request') }}"> here </a> to reset your password.
        </p>
    </div> --}}
</form>
@endsection
