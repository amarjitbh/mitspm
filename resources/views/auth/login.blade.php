@extends('layouts.app')
@section('content')
 <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close">
                            &times;
                        </button>
                        <i class="fa fa-check-circle"></i> &nbsp;
                        {!! Session::get('success') !!}
                    </div>
                @endif
                <div class="header-container">
                    <h1 class="widget-header text-center">Sign in</h1>
                </div>
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="input" name="email" value="{{ old('email') }}"
                               required autofocus>

                        <label class="floating-label" for="email">Email</label>
                        @if ($errors->has('email'))
                            <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('email') }}</small>
                             </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input class="input" type="password" id="password" name="password" required >

                        <label class="floating-label" for="password">Password</label>
                        @if ($errors->has('password'))
                            <span class="help-block mb0">
                            <small class="font11">{{ $errors->first('password') }}</small>
                        </span>
                        @endif
                    </div>


                    <div class="form-group login-controls">
                        <input id="checkbox-1" class="checkbox" name="checkbox-1" type="checkbox">
                        <label for="checkbox-1" class="label">Keep me signed in</label>
                        <a class="forgot-password" href="{{ url('/password/reset') }}">Forgot password?</a>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-outline-primary btn-block mt10">Sign In</button>
                    </div>
                </form>
                <div class="login-signup font12">
                    New Visitor? <a href="{{route('register')}}" class="btn-link">Sign Up Now</a>
                </div>
            </div>

        </div>
    </div>
@endsection
