@extends('layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/error.css') }}" />



    <div class="container-box">
        <div class="message-box">
            <div class="error-code">Error 500</div>
            <p class="heading">Internal Server Error</p>
            <p class="description">Sorry, we're having some technical issues (as you can see) try to refresh the page.</p>
            <p><a href="javascript:void(0);" onclick = ' location.reload();' class="btn-outline-primary">Refresh Page</a> &nbsp;<a href="javascript:void(0);" class="btn-outline-primary">Go Home</a></p>

        </div>

    </div>
@endsection
