@extends('layouts.app')
@section('title', ' - Register')

@section('content')


    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">

                    @include('flash_message')

                <div class="header-container">
                    <h1 class="widget-header text-center">Sign Up</h1>
                </div>
             <?php  if(!empty($parameter['unique_token']) && isset($parameter['unique_token'])){ ?>
                <form role="form" method="POST"
                      action="{{ url('/user-register-post?unique_token='.$parameter['unique_token'].'&projectId='.$parameter['projectId']) }}">
                        <input type="hidden" name="projectId" value="{{$parameter['projectId']}}">
                        <input type="hidden" name="inviteduserId" value="{{$parameter['invitedByUser']}}">
                        <input type="hidden" name="project_invite_id" value="{{!empty($projectInviteResult['project_invite_id']) ? $projectInviteResult['project_invite_id'] : ''}}">
                  <?php   }else{ ?>

                    <form role="form" method="POST" action="{{ route('user-register-post') }}">

<?php } ?>
                        {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <input id="name" type="text" class="input" name="name"
                                       value="{{ old('name') }}" required autofocus>

                        <label class="floating-label" for="name">Name</label>
                                @if ($errors->has('name'))
                                    <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('name') }}</small>
                                    </span>
                                @endif
                    </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <?php if (isset($projectInviteResult['email'])) {
                                    $readonly = 'readonly=true'; ?>
                                         <?php } else {
                                    $readonly = "";
                                } ?>
                                <input {{$readonly}} id="email" type="email" class="input" name="email"
                                       value="{{ isset($projectInviteResult['email'])?$projectInviteResult['email']:old('email') }}"
                                       required>

                                <label class="floating-label" for="name">Email Address</label>

                                    @if ($errors->has('email'))
                                        <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('email') }}</small>
                                    </span>
                                    @endif
                            </div>


                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input id="password" type="password" class="input" name="password"
                                       required>


                                <label class="floating-label" for="name">Password</label>
                                @if ($errors->has('password'))
                                    <span class="help-block mb0">
                                        <small class="font11">{{ $errors->first('password') }}</small>
                                    </span>
                                @endif
                            </div>


                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input id="password-confirm" type="password" class="input"
                                        name="password_confirmation" required>

                                <label class="floating-label" for="name">Confirm Password</label>
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <input id="phone" type="text" class="input"
                                        name="phone" value="{{old('phone')}}">

                                <label class="floating-label" for="name">Phone</label>
                            </div>
                            <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                <input id="company_id" type="hidden"
                                       name="company_id" required
                                       value="{{ isset($parameter['company_id'])?$parameter['company_id']:old('company_id') }}">
                                <input id="company" type="text" class="input"
                                        name="company" required {{!empty($parameter['company_id'])? 'readonly' : ''}}
                                       value="{{ isset($parameter['company_name'])?$parameter['company_name']:old('company') }}">


                                <label class="floating-label" for="name">Company</label>
                            </div>
                            <div class="form-group">
                                <select id="countries" name="countries" onchange="getCountriesTimeZone();" class="input">
                                        <option>Select Country</option>
                                        @if($countries)
                                            @foreach($countries as $country)
                                                <option value="{{$country->country_id}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                </select>

                                <label class="floating-label" for="name">Country</label>
                            </div>
                            <div class="form-group">
                                <select id="timezone" name="timezone" class="input">
                                        <option>Select Timezone</option>
                                </select>

                                <label class="floating-label" for="name">Time-zone</label>

                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-outline-primary btn-block mt10">Sign Up</button>
                            </div>




                </form>
                <div class="login-signup font12">
                    Already have account? <a href="{{route('login')}}" class="btn-link">Sign In Now</a>
                </div>
            </div>

        </div>
    </div>




    <div class="container hide">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>
                    <div class="panel-body">
                        <?php if(!empty($parameter['unique_token'])){?>
                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ url('/register'.'?unique_token='.$parameter['unique_token'].'&projectId='.$parameter['projectId']) }}">

                            <?php }else{ ?>
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">

                                <?php }?>
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Name</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control" name="name"
                                               value="{{ old('name') }}" required autofocus>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                    <div class="col-md-6">
                                        <?php if (isset($projectInviteResult['email'])) {
                                            $readonly = 'readonly=true'; ?>
                                         <?php } else {
                                            $readonly = "";
                                        } ?>
                                        <input {{$readonly}} id="email" type="email" class="form-control" name="email"
                                               value="{{ isset($projectInviteResult['email'])?$projectInviteResult['email']:old('email') }}"
                                               required>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password"
                                               required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password-confirm" class="col-md-4 control-label">Confirm
                                        Password</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control"
                                               name="password_confirmation" required>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Register
                                        </button>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('js-helper.user-register-js')
@endsection
