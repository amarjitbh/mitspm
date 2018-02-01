@extends('layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/error.css') }}" />


    <div class="container-box">
        <div class="message-box">
            <div class="error-code">Error 404</div>
            <p class="heading">Something is Wrong</p>
            <p class="description">The page you are looking for was moved, removed, renamed or might never existed.</p>
            <p><a href="{{route('dashboard')}}" class="btn-outline-primary">Go Home</a></p>
        </div>

    </div>
@endsection
