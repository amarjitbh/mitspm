@extends('layouts.app')
@section('title', ' - Company  List')

@section('content')
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-widget select-company">

                @include('flash_message')
                <div class="header-container">
                    <h1 class="widget-header text-center mb25">Select Company</h1>
                </div>

                <ul class="list-group mb0">
                    @foreach($CompaniesList as $list)
                    <a class="list-group-item"href="{{route('write-session',$list->company_id)}}">{{$list->name}} <i class="fa fa-chevron-right pull-right font12"></i></a>


                    @endforeach
                </ul>


            </div>

        </div>
    </div>
@endsection