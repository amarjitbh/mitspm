@extends('layouts.app')
@section('title', ' - Login')

@section('content')
 <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">
                @include('flash_message')
                <div class="header-container">
                    <h1 class="widget-header text-center">Sign in</h1>
                </div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('user-login-post') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{isset($data['parameter']['projectId'])? $data['parameter']['projectId'] : ''}}">
                    <input type="hidden" name="unique_token" value="{{isset($data['parameter']['unique_token'])? $data['parameter']['unique_token'] : ''}}">
                    <input type="hidden" name="company_id" value="{{isset($data['parameter']['company_id'])? $data['parameter']['company_id'] : ''}}">
                    <input type="hidden" name="role" value="{{isset($data['parameter']['role'])? $data['parameter']['role'] : ''}}">
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input id="email" type="email" class="input" name="email" value="{{ isset($data['parameter']['email'])?$data['parameter']['email'] : old('email') }}"
                               required
                               {{isset($data['parameter']['email'])? 'readonly' : '' }}
                               autofocus>

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
                    <div class="form-group login-controls hide">
                        <input id="checkbox-1" class="checkbox" name="checkbox-1" type="checkbox">
                        <label for="checkbox-1" class="label">Keep me signed in</label>
                        <a class="forgot-password hide" href="{{ url('/password/reset') }}">Forgot password?</a>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-outline-primary btn-block mt10">Sign In</button>
                    </div>
                </form>
                <div class="login-signup font12 hide">
                    New Visitor? <a href="{{route('userregister')}}" class="btn-link">Sign Up Now</a>
                </div>

                {{--<div class="login-signup font12">--}}
                    {{--New Visitor? <a href="{{route('userregister')}}" class="btn-link">Sign Up Now</a>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
@endsection

