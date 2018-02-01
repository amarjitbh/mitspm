@extends('layouts.app')
@section('title', ' - Change  Password')

@section('content')
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget">
                <div class="header-container">
                    <h1 class="widget-header text-center">Change Password</h1>
                </div>

                <?php echo Form::open(['route' => 'postChangePassword','class'=>'form-login']);?>
                <div class="form-group">
                    @include('flash_message')
                </div>
                <div class="form-group">
                    {{ Form::password('old_password', array('id' => 'inputOldPassword','class'=>'input')) }}
                    <label for="inputOldPassword" class="floating-label">Old Password <span class="text-danger">*</span></label>
                </div>
                <div class="form-group">
                    {{ Form::password('password', array('id' => 'inputPassword','class'=>'input')) }}
                    <label for="inputPassword" class="floating-label">New Password <span class="text-danger">*</span></label>

                </div>
                <div class="form-group">

                    {{ Form::password('confirm_password', array('id' => 'inputconfirmPassword','class'=>'input')) }}
                    <label for="inputconfirmPassword" class="floating-label">Confirm Password <span class="text-danger">*</span></label>

                </div>
                <br>
                <div class="form-group">
                    <div class="mb20">
                        {{ Form::submit('Change password',array('class'=>'btn-outline-primary btn-block mt10'))}}
                    </div>
                    <div class="mb20">
                        <a class="btn-outline-default btn-block mt10" href="{{ route('dashboard') }}">Cancel</a>

                    </div>
                </div>
                <?php  echo Form::close(); ?>
                <div class="clearfix"></div>

            </div>
        </div>
    </div>
    </div>
@endsection