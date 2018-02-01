@extends('layouts.app')
@section('title', ' - Create Board')

@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            Create Board - Admin
                        </span>
                        <span class="pull-right">

                            <?php if($user_role != \Config::get('constants.ROLE.USER')){ ?>
                            <a href="{{route('projects.create')}}"  class="btn btn-success btn-create-project btn-xs">Create New Project</a>
                            <?php } ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container alert-container">
        <div class="row">
            <div class="col-sm-12">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb-bar">

            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="active">Create Board</li>
                    </ol>
                </div>
            </div>

        </div>
        <div class="panel panel-default">


            <div class="panel-body">

                <div class="row mb25">

                    <form action="{{route('add-board')}}" method="POST" id="board-form">
                        {{ csrf_field() }}

                        {!! Form::hidden('project_id',$projectId,array('class'=>'form-control',
                                                                 'id'=>'project_id',
                                                                 'placeholder'=> 'Project ID',
                                                                 ''
                                                               )
                                           )
                        !!}
                        <div class="col-sm-6">
                        <div class="form-group form-group-sm">
                            <label class="control-label font-500">Board name</label>
                            {!! Form::text('project_board_name',null,array('class'=>'form-control',
                                                                  'id'=>'project_board_name',
                                                                  'placeholder'=> 'Board Name',
                                                                  'required' => true
                                                                )
                                            )
                            !!}
                        </div>
                            <div class="form-group form-group-sm">
                                <label class="control-label font-500">Description</label>
                                {!! Form::textarea('description',null,array('class'=>'form-control no-resize',
                                                                      'id'=>'description',
                                                                      'placeholder' => 'Description',
                                                                      'rows' => '2',

                                                                    )
                                                )
                                !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <p id="column_error" style="color:red !important; display:none;">Please fill the empty column name</p>
                            <div class="form-group form-group-sm" id="columnsBox">
                                <label class="control-label font-500 w-100p">Columns <span class="pull-right pr20"><a  href="javascript:void(0);" onclick="addBoards();"><i class="fa fa-plus font10"></i> Add more</a></span></label>

                                <?php $boardColumns = \Config::get('constants.BOARD_COLUMNS');
                                    $i = 1;
                                ?>
                                @foreach($boardColumns as $bColumns)
                                    <div class="input-group mb5 columns {{$i}}" id="{{$i}}">
                                            {!! Form::text('columns[]',$bColumns,array('class'=>'form-control col text-capitalize',
                                                                                     'id'=>'$bColumns',
                                                                                     'value' => '$bColumns',

                                                                                   )
                                                               )
                                            !!}
                                            <span class="input-group-btn add-board-remove-btn">
                                                <button type="button" id="iceboxBtn" class="mybtn1 btn btn-no-border removeBtn btn-xs" onclick="removeBoards('{{$i}}')" >
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                    </div>
                                    <?php $i++;?>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-sm-4 hide">
                            <div class="form-group form-group-sm">
                                <label class="control-label font-500 w-100p">Create new columns <span class="pull-right"><a href="javascript:void(0);" onclick="addBoards();">Add-column</a></span></label>
                                <div id="columnsBox">
                                    <div class="boardIn">
                                        <input type="text" name="columns[]" class="col form-control 1" >
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                            <div class="col-sm-12">
                                <div class="form-group mb0">
                                    <button class="btn btn-sm btn-default btn-success" type="button" id="submit" onclick="submitData();" name="submit" >Submit</button>
                                    <a class="btn btn-default btn-sm btn-close" href="{{ route('dashboard') }}">Cancel</a>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    <script>

        function addBoards(){

            clslenthChk = $('.columns').length;
            clslenth = parseInt(clslenthChk)+parseInt(1);
            myId = $('.'+clslenth).attr('id');
            if(clslenth == 2){
                $('.removeBtn').show();
                var ncounto = parseInt(clslenth)-parseInt(1);
                $('.columns').addClass('input-group');
            }
            if(!$.isEmptyObject(myId)){
                if(myId == clslenth){

                    clslenth = parseInt(clslenth)+parseInt(1);
                }
            }
            var myhtml = '<div class="input-group mb5  columns col '+clslenth+'" id="'+clslenth+'">\
                                <input class="form-control col text-capitalize" id="" value="" name="columns[]" type="text" value="'+clslenth+'">\
                                    <span class="input-group-btn add-board-remove-btn">\
                                    <button type="button" id="" class="btn removeBtn mybtn btn-no-border btn-xs" onclick="removeBoards('+clslenth+')">\
                                        <i class="fa fa-times" aria-hidden="true"></i>\
                                    </button>\
                                </span>\
                          </div>';

            $('#columnsBox').append(myhtml);
            $(".columns").each(function() {
                var chk = $(this).hasClass('rIn');
                if(chk == false) {
                    //$(this).after(delBox);
                    $('.removeBtn').addClass('mybtn'+clslenth);
                    $(this).addClass('rIn');
                }
            });
        }

        function removeBoards(ths){

            clslenth = $('.columns').length;
            if(clslenth == 1){
                $('.mybtn'+ths).hide();
            }else if(clslenth == 2){

                $('.'+ths).remove();
                $('.removeBtn').hide();
                var ncount = parseInt(clslenth)-parseInt(1);
                setTimeout(function() {
                    $('.input-group').removeClass('input-group');
                },50);
            }else{
                $('.'+ths).remove();
            }
        }

        function submitData(){

            //var valcolumns = $(".col").val().length;
            var inputBox = $(".text-capitalize").val().length;

            var col = '1';

            $(".text-capitalize").each(function(){
                //alert($(this).val());
                if($(this).val() == ''){
                    col = '0';
                    return false;
                }
            });
            if($('#project_board_name').val() == ''){
                $('#project_board_name').attr('style','border: 1px solid red !important');
                $('#project_board_name').focus(function(){

                    $('#project_board_name').css('border','');
                });
                return false;
            }else if(inputBox == '0' || col == '0'){


                $('#column_error').show();
                $(".col").focus(function(){

                    $('#column_error').show();
                })
                $('.columns').prop('required',true);
                setTimeout(function(){
                    $('#column_error').fadeOut('slow');
                },1000);
                return false;
            }else {
                //alert('chal gaya');return false;
                $('.c-loader').removeClass('loader-hide');
                $('#submit').attr('type','submit');
                $('#board-form').submit();
            }
        }
    </script>
@endsection
