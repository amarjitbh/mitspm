@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                           SETTINGS
                        </span>
                        @if(Session::get('user_role')==\Config::get('constants.ROLE.SUPERADMIN') || Session::get('user_role')==\Config::get('constants.ROLE.ADMIN'))
                            <span class="pull-right">
                            <a href="{{route('projects.create')}}" class="btn btn-success btn-create-project btn-xs">Create
                                Project</a>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container alert-container">
        <div class="row">
            <div class="col-sm-12">
                @include('flash_message')
            </div>
        </div>
    </div>
    <div class="container">
        <div class="panel panel-default dashboard-panel-filter">

            <div class="panel-body">
                <div class="row mb25">

                    <div class="col-sm-12">
                        <div class="section-title font14 mb10">General Setting</div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sel1">Date format:</label>
                                <?php// pr($dateTimes[0]['meta_value']); die; ?>
                                <select class="form-control" id="selectDateFormat">
                                    <?php $dateFormats =   Config::get('constants.GENERAL_SETTING_DATE_FORMAT'); ?>
                                    @foreach($dateFormats as  $key => $dateFormat)
                                        <option  @if (isset($dateTimes[1]['meta_value']) && $dateTimes[1]['meta_value']==$dateFormat)
                                                 selected @endif value="{{$dateFormat}}">{{date($dateFormat)}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sel1">Time format:</label>
                                <select class="form-control" id="selectTimeFormat">
                                    <?php $timeFormats =   Config::get('constants.GENERAL_SETTING_TIME_FORMAT'); ?>
                                    @foreach($timeFormats as  $key => $timeFormat)
                                        <option @if (isset($dateTimes[0]['meta_value']) && $dateTimes[0]['meta_value'] == $timeFormat)
                                                selected @endif value="{{$timeFormat}}">{{date($timeFormat)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-default btn-xs btn-primary btn pull-right" id="companySetting">Submit</button>
                        </div>
                    </div>

                </div>
                <div class="row mb25">

                    <div class="col-sm-12">
                        <div class="section-title font14 mb10">Automatic Reporting</div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="table-responsive table-common-style">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Project</th>
                                <th width="8%">Report</th>
                                <th width="25%">Email</th>
                                <th width="20%"></th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($projects as $ind => $project)
                                <tr class="testing">
                                    <td>{{$ind+1}} <input type="hidden" id="company_{{$project->project_id}}"
                                                          value="{{$project->company_id}}"></td>
                                    <td class="font-600">{{$project->name}}</td>
                                    <td>
                                        <input id="report_{{$project->project_id}}" type="checkbox"
                                               {{!empty($project->report)? 'checked' : '' }} class="reports checkbox"
                                               name="reports" value="1">
                                    </td>
                                    <td><input id="email_{{$project->project_id}}" type="text"
                                               class="email form-control input-sm table-input-default"
                                               name="email" value="{{ !empty($project->email)? $project->email : '' }}">
                                    </td>
                                    <td>
                                        <button class="btn btn-default btn-xs btn-primary" onclick="updateUserSettings({{$project->project_id}})">
                                            Update
                                        </button>
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>

                </div>


            </div>
            <div class="panel-body hide">
                <div class="row mb25">
                    <div class="col-sm-12">

                        Automatic-Reporting
                        <table class="responcive" border="1">
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th>Report</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            @foreach($projects as $ind => $project)
                                <tr class="testing">
                                    <td>{{$ind+1}} <input type="hidden" id="company_{{$project->project_id}}" value="{{$project->company_id}}"></td>
                                    <td>{{$project->name}}</td>
                                    <td>
                                        <input id="report_{{$project->project_id}}" type="checkbox" {{!empty($project->report)? 'checked' : '' }} class="reports" name="reports" value="1">
                                    </td>
                                    <td><input id="email_{{$project->project_id}}" type="text" class="email" name="email" value="{{ !empty($project->email)? $project->email : '' }}"></td>
                                    <td><button onclick="updateUserSettings({{$project->project_id}})">update</button></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
        function updateUserSettings(id){
            var report = 0;
            if($('#report_'+id).is(':checked') == true){

                report = 1;
            }
            var email = $('#email_'+id).val();
            var company = $('#company_'+id).val();
            if(email == ''){
                toastr.error('Please enter email address');
            }else if(isEmail(email) == false){
                toastr.error('Please correct email address');
            }else{

                $.ajax({

                    'url'   :   '{{route("settings")}}',
                    'type'  :   'POST',
                    'data'  :   {'project_id' : id,'report':report,'email' : email,'company_id': company,'_token':'{{csrf_token()}}'},
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    'success':  function(data){
                        if(data == 100){

                            toastr.success('Autometic reporting email successfully updated');
                        }else{
                            toastr.error('Somthing went wrong please try again');
                        }
                    },
                });
            }
        }
        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        $('body').on('click','#companySetting',function(){
            var date =  $('#selectDateFormat').val();
            var time = $('#selectTimeFormat').val();
            $.ajax({
                'url'   :   '{{route("settings")}}',
                'type'  :   'POST',
                'data'  :   {'date' : date,'time':time,'_token':'{{csrf_token()}}','action':'company-date-time-formate'},
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                'success':  function(response){
                    if(response.success == true){
                        toastr.success('Date and time format saved successfully ');

                    }else{
                        toastr.error('somthing went wrong please try again');
                    }
                },
            });

        })
    </script>
@endsection