@extends('layouts.app')

@section('content')
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">

                <div class="header-container">
                    <h1 class="widget-header text-center">Reset Password</h1>
                </div>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="input" name="email" value="{{ $email or old('email') }}" required autofocus>

                        <label class="floating-label" for="email">Email</label>
                        @if ($errors->has('email'))
                            <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('email') }}</small>
                             </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="input" name="password" required>

                        <label class="floating-label" for="password">Password</label>
                        @if ($errors->has('password'))
                            <span class="help-block mb0">
                            <small class="font11">{{ $errors->first('password') }}</small>
                        </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input id="password-confirm" type="password" class="input" name="password_confirmation" required>

                        <label class="floating-label" for="password-confirm">Confirm Password</label>
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block mb0">
                            <small class="font11">{{ $errors->first('password_confirmation') }}</small>
                        </span>
                        @endif
                    </div>



                    <div class="form-group">
                        <button type="submit" class="btn-outline-primary btn-block mt10">Reset Password</button>
                    </div>
                </form>

            </div>

        </div>
    </div>

@endsection
