@extends('layouts.app')

<!-- Main Content -->
@section('content')
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">

                <div class="header-container">
                    <h1 class="widget-header text-center">Reset Password</h1>

                    <p class="help-block text-muted text-center font12 mb20">Please enter registered email to receive
                        reset password link</p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="input" name="email" value="{{ old('email') }}" required
                               autofocus>

                        <label class="floating-label" for="email">Email</label>
                        @if ($errors->has('email'))
                            <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('email') }}</small>
                             </span>
                        @endif
                    </div>


                    <div class="form-group mt25">
                        <button type="submit" class="btn-outline-primary btn-block mt10">Send</button>
                    </div>
                </form>

            </div>

        </div>
    </div>

@endsection
