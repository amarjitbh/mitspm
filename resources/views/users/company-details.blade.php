@extends('layouts.app')
@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                           Company Detail
                        </span>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">

        <div class="panel panel-default dashboard-panel-filter manage-project-add-people">


            <div class="panel-body pt0">

                <div class="row">


                    <div class="table-responsive table-common-style">
                        <table class="table table-striped mb0">
                            <thead>
                            <tr>
                                <th>Company List</th>
                                <th>Total Member</th>
                                <th>Admin Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($companyList != '')
                                @foreach($companyList as $company)
                                    <tr>
                                        <td>{{$company->name}}</td>
                                        <td>{{$company->user_count}}</td>
                                        <td>{{$company->email}}</td>

                                    </tr>
                                @endforeach
                            @else
                                <p>Not data found</p>
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>


            </div>
        </div>
    </div>

@endsection