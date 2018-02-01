<script>

    function showHideColumn(projectBoardColumnId) {
        NProgress.start();
        var status = $('body').find("[data-column-id=" + projectBoardColumnId + "]").attr('data-status');
        var columnName = $('body').find("[data-column-id=" + projectBoardColumnId + "]").attr('data-column-name');
        //alert(columnName);
        $("#"+projectBoardColumnId).blur();
        $.ajax({
            url: '{{route('remove-add-board-column')}}',
            type: 'POST',
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            data: {
                _token: '{{csrf_token()}}',
                'board_column_id': projectBoardColumnId,
                'status': status
            },

            success: function (response) {
                if (response.status == 2) {

                    var status = $('body').find("[data-column-id=" + projectBoardColumnId + "]").attr('data-status', 'hide');

                    if ($('#boardColumnBox' + projectBoardColumnId).html()) {

                        $('#boardColumnBox' + projectBoardColumnId).hide();
                        $('#' + projectBoardColumnId).removeClass('active-column-select');
                    } else {
                        $.ajax({
                            url: '{{route('ajax-get-column-task-detail')}}',
                            type: 'POST',
                            data: {
                                _token: '{{csrf_token()}}',
                                'board_column_id': projectBoardColumnId,
                                'columnName': columnName,
                            },
                            statusCode: {
                                500: function (response) {
                                    window.location = "{{route('error-page-500')}}"

                                },
                                404: function (response) {
                                    window.location = "{{route('error-page-404')}}"
                                }
                            },
                            success: function (response) {
                                $('#boardRowDiv').append(response);
                                $('#' + projectBoardColumnId).removeClass('active-column-select');
                            },
                        })
                    }
                } else if (response.status == 1) {
                    var status = $('body').find("[data-column-id=" + projectBoardColumnId + "]").attr('data-status', 'show');
                    //console.log(status);return false;
                    inputBox = $('.inputValues').length;
                    //console.log(inputBox);
                    var lastValue = '';
                    $('.inputValues').each(function(ind,val){

                        if($(this).val() < projectBoardColumnId){
                            lastValue = $(this).val();
                        }
                    });
                    $('body').find();
                    if ($('#boardColumnBox' + projectBoardColumnId).html()) {
                        $('#boardColumnBox' + projectBoardColumnId).show();
                        $('#' + projectBoardColumnId).addClass('active-column-select');

                    } else {

                        $.ajax({
                            url: '{{route('ajax-get-column-task-detail')}}',
                            type: 'POST',
                            data: {
                                _token: '{{csrf_token()}}',
                                'board_column_id': projectBoardColumnId,
                                'columnName': columnName,
                            },
                            statusCode: {
                                500: function (response) {
                                    window.location = "{{route('error-page-500')}}"

                                },
                                404: function (response) {
                                    window.location = "{{route('error-page-404')}}"
                                }
                            },
                            success: function (response) {
                                if(!$.isEmptyObject(lastValue)){

                                    $('#boardColumnBox'+lastValue).after(response);
                                }else{

                                    $('#boardRowDiv').append(response);
                                }
                                $('#' + projectBoardColumnId).addClass('active-column-select');
                            },
                        })
                    }
                } else if (response.status == 4) {
                    toastr.warning('You are already working in task.Please Pause to hide selected column');
                }

            },
        })
        NProgress.done();
        NProgress.remove();
    }


    //on click of play bwlow function will be called
    function startlogIn(projectTaskId, test, id, check,callBack) {
        NProgress.start();
        var value = $(test).val();

        var url = "{{route('ajax-start-task-loggin')}}";
        if (id) {
            id = id;
        } else {
            id = 0;
        }
        if (check) {
            var check = true;
        } else {
            var check = false;
        }
        $.ajax({
            url: url,
            data: {
                _token: '{{csrf_token()}}',
                project_task_id: projectTaskId,
                value: value,
                check: check,
                new_project_task_id: id,
                action:'start-task-history',
            },
            type: "POST",
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            success: function (data) {

                if (data.response == 2) {
                    if (confirm(data.message)) {
                        var id = $('.timer[value=Pause]').attr('data-project-task-id');
                        endlogIn(projectTaskId, test, id, check = true);
                    }
                } else if (data.response == 1) {

                    $('.timer[value=Pause]').addClass('timer_start').removeClass('timer_pause');
                    $('.timer[value=Pause]').next('span').removeClass('stopwatch');
                    $('.timer[value=Pause]').val('Play');
                    $(test).attr('onClick', "");
                    $(test).val('Pause');
                    $(test).addClass('timer_pause').removeClass('timer_start');
                    $(test).next('span').addClass('stopwatch');
                    $(test).next('span').val(data.time);
                    $(test).parents('.panel-title').eq(0).find('span').val(data.time);
                    $(test).attr('onClick', clearInterval(timerVar));
                    startTimer(data.time);
                }  else {
                    console.log(data.message);
                }
                NProgress.done();
                NProgress.remove();
            },
            error: function (response) {
                //console.log(response);
            },
        });
    }

    //on click of pause below function will be called
    function endlogIn(projectTaskId, test, id, check) {

        if (id) {
            id = id;
        } else {
            id = 0;
        }

        if (check) {
            var check = true;
        } else {
            var check = false;
        }

        var url = "{{route('ajax-end-task-loggin')}}";
        var value = $(test).val();
            //alert(value);
        $.ajax({
            url: url,
            data: {
                _token: '{{csrf_token()}}',
                project_task_id: projectTaskId,
                value: value,
                check: check,
                new_project_task_id: id,
                action:'stop-task-history'
            },
            type: "POST",
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            success: function (data) {

                if (data.response == 2) {

                } else if (data.response == 1) {
                    if (check) {

                        //  alert('start'+projectTaskId);     alert('pause'+id);  alert(check);
                        if (id) {
                            var idd = id;
                        } else {
                            var idd = $('.timer[value=Play]').attr('data-project-task-id');
                        }
                        //fetch id of  task to be started
                        startlogIn(idd, test, projectTaskId, check,'calBack');
                    }
                    $(test).val('Play');
                    $(test).addClass('timer_start').removeClass('timer_pause');
                    $(test).next('span').removeClass('stopwatch');
                    $(test).next('span').val(data.time);


                } else if (data.response == 0) {

                    //if (confirm(data.message)) {
                        //alert(id);
                        $.ajax({
                            url: "{{route('ajax-start-task-loggin')}}",
                            data: {
                                _token: '{{csrf_token()}}',
                                project_task_id: projectTaskId,
                                alreadyAssigned: 1
                            },
                            type: "POST",
                            statusCode: {
                                500: function (response) {
                                    window.location = "{{route('error-page-500')}}"

                                },
                                404: function (response) {
                                    window.location = "{{route('error-page-404')}}"
                                }
                            },
                            success: function (data) {

                                 if (data.response == 3) {
                                    $(test).attr('onClick', "");
                                    $(test).val('Pause');
                                    $(test).addClass('timer_pause').removeClass('timer_start');
                                    $(test).next('span').addClass('stopwatch');
                                    $(test).next('span').val(data.time);

                                    $(test).attr('onClick', clearInterval(timerVar));
                                    startTimer(data.time);
                                }
                            }
                        });
                    //}
                }
            },
            error: function (response) {
                //console.log(response);
            },
        });
    }

    //draggable and droppable using ajax
    $(function () {
        $('body').on('click', '.timer', function () {
            //alert('asdf');
            //$('.timer').click(function (event) {
            var value = ($(this).val());
            var projectTaskId = ($(this).attr('data-project-task-id'));

            var stoptimer = ($('.stopwatch').html());
            var chkUser = [];
            $('.userDataId_'+projectTaskId+':checked').each(function(i){
                chkUser[i] = $(this).val();

            });

            if (value == "Play") {

                startlogIn(projectTaskId, this);
            }
            else if (value == "Pause") {
                endlogIn(projectTaskId, this);
            }
        });

        //sortable

        function makeSortable() {
            $(".sortable").sortable({


                connectWith: ".drop, .draggable",
                cancel: '.board-panel-body-common',
                revert: 'invalid',

                over: function (e, ui) {



                    var board_column_id = ui.item.closest('ul').attr('data-board-column-id');
                    $('#columnIdFrom').val(board_column_id);
                    var ulHtml = ui.item.closest('ul');
                    var id = ui.item.closest('li').attr('id');
                    var ProTaskIdArray = [];
                    var ProTaskId = "";
                    $(ulHtml).find('li').each(function (k, v) {
                        console.log(v);
                        ProTaskId = $(v).attr('id');
                        if(!$.isEmptyObject(ProTaskId)) {
                                var project_task_id = ProTaskId.split('_');
                                ProTaskIdArray.push(project_task_id['1']);
                        }
                    });
                /*    $.ajax({
                        url: "{{route('ajax-sort-drag-task-order')}}",
                        data: {
                            _token: '{{csrf_token()}}',
                            board_column_id: board_column_id,
                            project_task_id: ProTaskIdArray,
                        },
                        type: "POST",

                        success: function (data) {
                            //  console.log(ui.item.closest('ul').length);
                            var length = $(ulHtml).children('li').length;

                            var i = 1;
                            $(ulHtml).find('li').each(function (k, v) {
                                var taskId = $(v).attr('id');
                                if (i <= length) {
                                    var liHtml = $("#" + taskId).attr('data-order-id', i); //asigning new order id
                                }
                                i++;
                            });
                        },
                        error: function (response) {
                            //console.log(response);
                        },
                    });*/

                },

                stop: function (e, ui) {



                    var id = ui.item.closest('li').attr('id');
                    var project_task_id = id.split('_');
                    var board_column_id = ui.item.closest('ul').attr('data-board-column-id');
                    var board_column_id_from = $('#columnIdFrom').val();
                    var url = "{{route('ajaxProjectTaskLoggin')}}";
                    $.ajax({
                        url: url,
                        data: {
                            _token: '{{csrf_token()}}',
                            project_task_id: project_task_id['1'],
                            board_column_id: board_column_id,
                        },
                        type: "POST",
                        statusCode: {
                            500: function (response) {
                                window.location = "{{route('error-page-500')}}"

                            },
                            404: function (response) {
                                window.location = "{{route('error-page-404')}}"
                            }
                        },
                        success: function (data) {
                            $(id).addClass('test');
                            var ulHtml = ui.item.closest('ul').attr('data-board-column-id');
                            var ullihtml = $('body').find("[data-board-column-id=" + board_column_id + "]");
                            var id = ui.item.closest('li').attr('id');
                            var ProTaskIdArray = [];
                            var project_task_ids= [];
                            var ProTaskId = "";
                            $(ullihtml).find('li.task-class').each(function (k, v) {
                                var ProTaskId = $(v).attr('id');
                                    var project_task_id = ProTaskId.split('_');
                                    project_task_ids = id.split('_');
                                    ProTaskIdArray.push(project_task_id['1']);

                            });
                            $.ajax({
                                url: "{{route('ajax-sort-drag-task-order')}}",
                                data: {
                                    _token: '{{csrf_token()}}',
                                    board_column_id: board_column_id,
                                    project_task_id: ProTaskIdArray,
                                    project_task_id_single: project_task_ids['1'],
                                    board_column_id_from:board_column_id_from,
                                    action:'move-task-history'
                                },
                                type: "POST",
                                statusCode: {
                                    500: function (response) {
                                        window.location = "{{route('error-page-500')}}"

                                    },
                                    404: function (response) {
                                        window.location = "{{route('error-page-404')}}"
                                    }
                                },
                                success: function (data) {
                                    var length = $(ulHtml).children('li').length;
                                    var i = 1;
                                    $(ullihtml).find('li.task-class').each(function (k, v) {
                                        var taskId = $(v).attr('id');
                                        if (i <= length) {
                                            var liHtml = $("#" + taskId).attr('data-order-id', i); //asigning new order id
                                        }
                                        i++;
                                    });
                                },
                                error: function (response) {
                                    //console.log(response);
                                },
                            });


                        },
                        error: function (response) {
                            //console.log(response);
                        },
                    });
                },

            });
        }
        makeSortable();


    });


    var timerVar = setInterval(countTimer, 1000); //1000 for 1 seconds

    var chk = $('.stopwatch').html();
    if (chk) {
        var a = chk.split(':'); // split it at the colons
        var totalSeconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a [2]);
    }
    function startTimer(time) {
        timerVar = setInterval(countTimer, 1000);
        a = time.split(':'); // split it at the colons
        totalSeconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a [2]);
    }

    //function to start countdown timer
    function countTimer() {
        chk = $('.stopwatch').html();
        if (chk) {
            var a = chk.split(':'); // split it at the colons
            var totalSeconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a [2]);
        }
        ++totalSeconds;

        /* var leftover = totalSeconds;
         var days = Math.floor(leftover / 86400);
         //how many seconds are left
         leftover = leftover - (days * 86400);
         var hour = Math.floor(totalSeconds / 3600) % 24;*/
        var hour = Math.floor(totalSeconds / 3600);
        var minute = Math.floor(totalSeconds / 60) % 60;
        var sec = totalSeconds % 60;
        if (hour.toString().length == 1) {
            var hours = "0" + hour;
        } else {
            var hours = hour;
        }
        if (sec.toString().length == 1) {
            var secs = "0" + sec;
        } else {
            var secs = sec;
        }
        if (minute.toString().length == 1) {
            var minutes = "0" + minute;
        } else {
            var minutes = minute;
        }

        var result = hours + ":" + minutes + ':' + secs;
        $('body').find(".stopwatch").html(result);
    }
</script>

