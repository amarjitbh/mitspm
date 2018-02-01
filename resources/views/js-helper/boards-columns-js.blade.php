<div id="modalID" class="modal fade image-popup-class" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content modal-preview-image">
            <div class="img-append"></div>

        </div>

    </div>
</div>

<script>
    $('#createTask').click(function(){
        $('#submitType').val('insert');
        $('#alert_fail').hide();
        $('#subject').val('');
        $('#description').val('');
        $('#submit').removeAttr('onclick');
        $('#submit').attr('onclick','taskSubmit()');
        $('#addTask').modal(
                {
                    backdrop: 'static',
                    keyboard: false
                }
        );
        $('#addTask').on('shown.bs.modal', function (e) {
            $('#taskForm')[0].reset();
        })
    });
    $('#taskForm').on('keypress', function(e) {
        return e.which !== 13;
    });

    function taskSubmit(){
        /*** ADD-Task  ***/
        var formData = new FormData();
        formData.append('file', $('#imageFile')[0].files[0]);
        var columns = $('#columns').val();
        var submitType = $('#submitType').val();
        var subject = $('#subject').val();
        var description = $('#description').val();
        var project_id = $('#project_id').val();
        var project_board_id = $('#project_board_id').val();
        var priority = $('#priority').val();
        //var users = $('#users').val();
        var userss = [];
        var tt = '';
        $(".users"+tt+":checked").each(function(i){
            userss[i] = $(this).val();
        });


        formData.append('project_id',project_id);
        formData.append('project_board_id',project_board_id);
        formData.append('project_board_column_id',columns);
        formData.append('description',description);
        formData.append('submitType',submitType);
        formData.append('subject',subject);
        formData.append('users',userss);
        formData.append('priority',priority);
        formData.append('action','add-task-history');
        if(columns == ''){

            $('#alert_fail').fadeIn('slow');
            $('#alert_fail').html('Board-column field is required');
            $('#columns').change(function(){

                $('#alert_fail').fadeOut('slow');
                $('#alert_fail').html('');
            });
            return false;
        }else {

            $.ajax({
                type: 'POST',
                url: '{{route('add-task')}}',
                beforeSend: function () {

                    //$('.c-loader').removeClass('loader-hide');
                    $('#submit').attr('onclick', '');
                },
                headers: {
                    'X-CSRF-Token': '{{csrf_token()}}'
                },
                data     : formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success: function (responce) {
                    //console.log(responce);
                    if (responce.success == '100') {

                        $('#subject').val('');
                        $('#description').val('');
                        var asgnUsers = responce.users;
                        //console.log(asgnUsers);
                        var userData = '';
                        $.each(responce.proUser, function (a, v) {
                            chked = '';
                            $.each(asgnUsers, function (i, c) {

                                if (c.id == v.id) {

                                    chked += 'checked';
                                }
                            });
                            //userData += '<input type="checkbox" '+chked+' class="userDataId_'+responce.tsk_id+'" value="'+ v.id+'" > '+ v.name+'';

                            userData += '<div><input type="checkbox" ' + chked + ' id="lb_' + v.id + '" onchange="checkchecked(' + v.id + ')" class="checkbox userDataId_' + responce.tsk_id + '" value="' + v.id + '" > <label for="lb_' + v.id + '" class="label">' + v.name + '</label></div>';

                        });
                        var timers = '';
                        $.each(asgnUsers, function (i, c) {

                            if (c.id == responce.authUserid) {

                                timers = '<button class="timer timer_button timer_start" type="button" value="Play" data-project-task-id="' + responce.tsk_id + '" onclick=""></button><span class="time_duration clockTimer">00:00:00</span>';
                            }/*else{
                             timers = '<span class="time_duration clockTimer">00:00:00</span>';
                             }*/
                        });

                        if($.isEmptyObject(timers) || timers == ''){

                            timers = '<span class="time_duration clockTimer">00:00:00</span>';
                        }
                        var prioritySet = '';
                        $.each(responce.priorityConstant, function (index, value) {
                            if (index == responce.priority) {
                                prioritySet = value;
                                return false;
                            }
                        });
                        if (submitType == 'insert') {

                            //$('.' + responce.column).append('<li class="list-group-item font12 ui-sortable-handle"><button onclick="editTask('+responce.tsk_id+')">-</button> <a id="sub_'+responce.tsk_id+'" href="#">' + subject + '</a></li>');
                            var newHtml = '<li class="list-group-item font12 ui-sortable-handle" data-order-id="1" id="projectTask_' + responce.tsk_id + '">\
                                            <div class="panel-group mb0" id="accordion" role="tablist" aria-multiselectable="true">\
                                                <div class="panel panel-default board-panel-common">\
                                                    <div class="panel-heading" role="tab" id="headingOne">\
                                                        <div class="panel-title">\
                                                         <span class="caret-icon" id="caretIcon_'+responce.tsk_id+'">\
                                                         <i id="taskLoaderId_'+responce.tsk_id+'" class="fa fa-spinner fa-spin hide" style="font-size:10px"></i>\
                                                            <a  id="sub_' + responce.tsk_id + '" onclick="editTask(' + responce.tsk_id + ')" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' + responce.tsk_id + '" aria-expanded="false" aria-controls="collapse' + responce.tsk_id + '"></a>\
                                                         </span>\
                                                         <span class="overflow-ellipse title-overflow" id="title_' + responce.tsk_id + '">\
                                                            ' + subject + '\
                                                         </span>\
                                                         <span class="text-right">\
                                                          <span class="timer-main test">\
                                                            ' + timers + '\
                                                            </span>\
                                                          </span>\
                                                          </span>\
                                                        </div>\
                                                    </div>\
                                                    <div id="collapse' + responce.tsk_id + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' + responce.tsk_id + '" aria-expanded="false" style="height: 0px;">\
                                                        <div class="panel-body board-panel-body-common" id="appendDataId' + responce.tsk_id + '" style="height: calc(100% - 35px);">\
                                                            /*****  Panel removed ----- now this panel get detail from editText  *****/ \
                                                        </div>\
                                                </div>\
                                            </div>\
                                            </div>\
                                      </li>';

                            var activeRoute = '{{isActiveRoute('my-tasks')}}';
                            if(activeRoute == 'active'){

                                $.each(asgnUsers, function (i, c) {

                                    if (c.id == responce.authUserid) {
                                        $('.myCls' + columns).prepend(newHtml);
                                    }
                                });

                            }else if(activeRoute != 'active'){

                                $('.myCls' + columns).prepend(newHtml);
                            }

                            toastr.success('Task ' + subject + ' created successfully');

                        } else {

                            $('#sub_' + submitType).text(subject);
                        }
                        $('#addTask').modal('hide');
                    } else if (responce.success == '1') {

                        $('#alert_fail').html('unauthorized permission');
                        $('#alert_fail').show();
                    } else {

                        $('#alert_fail').html('Please try again , there is some error');
                        $('#alert_fail').show();
                    }
                    setTimeout(function () {

                        //$('.c-loader').addClass('loader-hide');
                        $('#submit').attr('onclick', 'taskSubmit()');
                    }, 1000);

                    $('.checkbox').each(function (ind, val) {
                        $(this).prop('checked', false);
                    });
                    $('#columns').val('');

                },
                error: function (ress) {
                    $.each(ress, function (ind, val) {

                        if (!$.isEmptyObject(val.subject)) {

                            $('#alert_fail').html(val.subject);
                            $('#alert_fail').show();
                            return false;

                        } else if (!$.isEmptyObject(val.users)) {

                            $('#alert_fail').html(val.users);
                            $('#alert_fail').show();
                            return false;
                        } else {

                            $('#alert_fail').html('Please try again , there is some error');
                            $('#alert_fail').show();
                        }
                    });

                    setTimeout(function () {

                        $('#submit').attr('onclick', 'taskSubmit()');
                    }, 1000);
                },
            });
        }
    }

    function editTask(taskId){
        //alert(taskId);
        /***  Show Task Detail  ***/
        var scrollableDiv =  $("div.h-scrollable");
        var sidebarWidth = $("#sidebar-wrapper").outerWidth() || 0;
        var wrapper = scrollableDiv.outerWidth();
        var triggerElement = $('#projectTask_'+taskId).parents('.boardColumnDivClass').eq(0).attr('id');
        var currentColumnElement = $('#'+triggerElement);
        var singleColumnWidth = currentColumnElement.outerWidth();
        var singleColumnOffset = currentColumnElement.offset();
        var singleColumnOffsetLeft = 0;
        if( $(window).width() < 767 ) {
            singleColumnOffsetLeft = Math.floor(singleColumnOffset.left);
        }
        else {
            singleColumnOffsetLeft = Math.floor(singleColumnOffset.left) - sidebarWidth;
        }
        var targetOffsetColumnPosition = singleColumnWidth + singleColumnOffsetLeft;
        var scrollBy = targetOffsetColumnPosition - wrapper;
        var getCurrentScrollBarPosition = scrollableDiv.scrollLeft();
        if(targetOffsetColumnPosition > wrapper){
            scrollableDiv.animate({ scrollLeft: getCurrentScrollBarPosition + scrollBy }, 600);
        }

        //console.log(`sidebarWidth = ${sidebarWidth}, wrapper = ${wrapper} , scrollBy = ${scrollBy}, singleColumnOffsetLeft = ${singleColumnOffsetLeft}, targetOffsetColumnPosition = ${targetOffsetColumnPosition}, getCurrentScrollBarPosition = ${getCurrentScrollBarPosition}`)

        var clas = $('#sub_'+taskId).attr('class');
        if(clas == 'collapsed') {
            jQuery('.modal-img').attr("id","myModalImg"+taskId);
            jQuery('.modal-content').attr("id","img01"+taskId);
            jQuery('.caption-img').attr("id","caption-img"+taskId);

            $.ajax({

                url: '{{route('getTaskDetail')}}',
                type: 'POST',
                data: {_token: '{{csrf_token()}}', 'task_id': taskId,'page':'board-detail'},
                dataType: 'JSON',
                beforeSend: function() {
                    $('#sub_'+taskId).addClass('hide');
                    $('#taskLoaderId_'+taskId).removeClass('hide')
                    // $('#appendTaskLoader_'+taskId).append('<i class="fa fa-spinner fa-spin" id="taskLoaderId_'+taskId+'" style="font-size:10px"></i>');
                },
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success: function (res) {




                    if (res.success == 100) {

                         setTimeout( function(){
                        $('#sub_'+taskId).removeClass('hide');
                        $('#taskLoaderId_'+taskId).addClass('hide');
                            },500)



                        var login_user_id = '{{ Auth::user()->id }}';
                        var task_pause = $('#projectTask_'+taskId).find('.timer-main').find('.timer_button').val();
                        //alert(task_id);
                        //alert(res.isUserAdmin+'=='+res.userRole);
                        var userData = '';
                        asgnUsers = res.asgnUser;
                        if(!$.isEmptyObject(res.users)) {
                            userData += '<p for="assgnUser" class="mb5 font-600">Members</p>';
                            $.each(res.users, function (a, v) {
                                chked = '';
                                $.each(asgnUsers, function (i, c) {

                                    if (c.user_id == v.id) {

                                        chked += 'checked';
                                    }


                                    if (task_pause == "Pause") {

                                        if(c.user_id == login_user_id){

                                            setTimeout(function() {
                                                $('#appendDataId'+taskId).find('.assignedUserList').find('.checkbox-user').find('input#lb_'+login_user_id).attr("disabled", true);
                                            },100)


                                        }
                                    }

                                });
                                userData += '<div class="checkbox checkbox-user"><input id="lb_' + v.id + '"  onchange="checkchecked(' + v.id + ')"  type="checkbox" name="username" ' + chked + ' class="checkbox userDataId_' + res.data.project_task_id + '" value="' + v.id + '" > <label for="lb_' + v.id + '" class="label">' + v.name + '</label></div>';
                            });
                        }




                        if(res.userRole == 3 && res.isUserAdmin == 1){
                            //alert();
                            chkedd = '';
                            $.each(asgnUsers, function (i, c) {

                                if (c.user_id == res.superAdminForUsers.id) {

                                    chkedd += 'checked';
                                }
                            });
                            userData += '<div class="checkbox checkbox-user"><input id="lb_' + res.superAdminForUsers.id + '"  onchange="checkchecked(' + res.superAdminForUsers.id + ')"  type="checkbox" disabled name="username" ' + chkedd + ' class="checkbox userDataId_' + res.data.project_task_id + '" value="' + res.superAdminForUsers.id + '" > <label for="lb_' + res.superAdminForUsers.id + '" class="label">' + res.superAdminForUsers.name + '</label></div>';
                        }
                        $('#columns').val(res.data.project_board_column_id);
                        $('#subject').val(res.data.subject);
                        $('#description').val(res.data.description);
                        $('#project_id').val(res.data.project_id);
                        $('#project_board_id').val(res.data.project_board_id);

                        formHtmlData = '';
                        var taskFile= '';
                        filePath    = '{{asset('/images/thumbnail/')}}';
                        if(!$.isEmptyObject(res.data.file)){

                            taskFile = '<a href="javascript:void(0);" data-src = "'+filePath+'/'+res.data.file+'" file-id='+res.data.project_task_id +' class="comment-img btn btn-link font10"><i class="fa fa-paperclip"></i> 1 attachment</a>';

                        }
                        var allComments = '';
                        $.each(res.tskComments, function (ti, tval) {
                            //alert(tval.posted_by_user_id+'==='+res.sesId);
                            //alert(res.dateFormat+' '+res.timeFormat);

                            var dateFormat = res.dateFormat;
                            var timeFormat = res.timeFormat;
                            var commnetFile = '';
                            if(!$.isEmptyObject(tval.file)){

                                /*commnetFile =  '<img width="50" height="50" src="'+filePath+'/'+tval.file+'" id='+tval.task_comment_id+' class="comment-img">';*/
                                commnetFile =  '<a href="javascript:void(0); onclick="removeComment(' +tval.task_comment_id+ ')"" data-src = "'+filePath+'/'+tval.file+'" id='+tval.task_comment_id+' class="comment-img btn btn-link font10"><i class="fa fa-paperclip"></i> 1 attachment</a>';

                            }

                            if(tval.posted_by_user_id == res.sesId) {

                                //alert('2==>'+dateFormat+'=='+timeFormat);
                                allComments += '<li class="list-group-item" id="taskCommentId_'+tval.task_comment_id+'">\
                                                 <span class="remove-comment"><a href="#" onclick="removeComment(' +tval.task_comment_id+ ')"><i class="fa fa-close"></i></a></span>\
                                                <div class="heading"><span class="member-name font-600 text-capitalize">' + tval.name + '</span> <span class="time-stamp pull-right font10 text-lowercase">'+tval.date+'</span></div>\
                                                <div class="message" id="userComment' + tval.task_comment_id + '">' + tval.comment + '<span class="view-attachment-file">'+commnetFile+'</span></div>\
                                                <span id="editCommnt' + tval.task_comment_id + '" class="ecomment hide" onclick="updateComment(' + tval.task_comment_id + ',' + res.data.project_task_id + ')">Edit</span>\
                                            </li>';
                            }else{

                                allComments += '<li class="list-group-item" id="taskCommentId_' + tval.task_comment_id + '">\
                                                <span class="remove-comment"><a href="#" onclick="removeComment(' +tval.task_comment_id+ ')"><i class="fa fa-close"></i></a></span>\
                                                <div class="heading"><span class="member-name font-600 text-capitalize">' + tval.name + '</span> <span class="time-stamp pull-right font10 text-lowercase">'+tval.date+'</span></div>\
                                                <div class="message" id="userComment' + tval.task_comment_id + '">' + tval.comment + '<span class="view-attachment-file">\
                                                '+commnetFile+'</span></div>\
                                            </li>';
                            }
                        });

                        /***  Show task to specific login user  ****/
                        var removeTaskData = '';//alert(res.data.created_by+'==='+res.sesId+'==='+res.isUserAdmin);
                        if(res.data.created_by == res.sesId){
                            removeTaskData = '<div class="board-action-btn text-right">\
                                                    <div class="btn-group btn-group-xs smlBtnGroup" id="smlBtnGroup'+res.data.project_task_id+'">\
                                                        <a href="javascript:void(0);" class="btn btn-default btn-no-radius" title="Expand View" onclick="showExpendView('+res.data.project_task_id+')">\
                                                            <i class="fa fa-expand" aria-hidden="true"></i>\
                                                        </a>\
                                                        <button type="button" class="btn btn-default btn-no-radius" data-tooltip="true" data-placement="bottom" title="Delete Board"  onclick="removeTask('+res.data.project_task_id+')">\
                                                            <i class="fa fa-trash" aria-hidden="true" ></i>\
                                                        </button>\
                                                    </div>\
                                               </div>';
                        }else{
                            removeTaskData = '<div class="board-action-btn text-right">\
                                                    <div class="btn-group btn-group-xs smlBtnGroup" id="smlBtnGroup'+res.data.project_task_id+'">\
                                                        <a href="javascript:void(0);" class="btn btn-default btn-no-radius" title="Expand View" onclick="showExpendView('+res.data.project_task_id+')">\
                                                            <i class="fa fa-expand" aria-hidden="true"></i>\
                                                        </a>\
                                                    </div>\
                                               </div>';

                        }

                        /*** Periorities ***/
                        var usersPeriorities = '';
                        //console.log(res.pariorities);
                        $.each(res.pariorities , function(pind,pval){

                            if(res.data.priority == pind){

                                radioChecked = "checked";
                            }else{
                                radioChecked='';
                            }

                            if(pind%2 == 0) {

                                usersPeriorities += '<div class="col-sm-4 pl0">\
                                        <div class="radio radio-user">\
                                         <label for="radio' + pind + '_' + res.data.project_task_id + '">\
                                            <input type="radio" name="radiopriority" id="radio' + pind + '_' + res.data.project_task_id + '" class="mt0 radiopriority' + res.data.project_task_id + '" value="' + pind + '" '+radioChecked+'>\
                                            '+pval+'\
                                         </label>\
                                        </div>\
                                        </div>';
                            }else{

                                usersPeriorities += '<div class="col-sm-4 pl0">\
                                        <div class="radio radio-user">\
                                         <label for="radio' + pind + '_' + res.data.project_task_id + '">\
                                            <input type="radio" name="radiopriority" id="radio' + pind + '_' + res.data.project_task_id + '" class="mt0 radiopriority' + res.data.project_task_id + '" value="' + pind + '" '+radioChecked+'>\
                                            '+pval+'\
                                         </label>\
                                        </div>\
                                        </div>';
                            }
                        });
                        /***  Users-View  ***/
                        if(res.userRole == 3 && res.isUserAdmin == 0){
                            var formHtml = '<div>' + formHtmlData + '\
                            <div class="board-form-common form-non-editable">\
                                <div>\
                                    '+removeTaskData+'\
                                </div>\
                                <div class="form-group">\
                                    <label for="project_title_' + res.data.project_task_id + '" class="font-600">Title</label>\
                                     <div class="title" id="project_title_' + res.data.project_task_id + '">' + res.data.subject + '</div>\
                                </div>\
                                <div class="form-group">\
                                    <label for="project_description_' + res.data.project_task_id + '" class="font-600">Description</label>\
                                    <div class="description" id="project_description_' + res.data.project_task_id + '">' + res.data.description + '</div>\
                                </div>\
                            </div>\
                        </div>\
                        <div>\
                            <form class="board-form-common">\
                                <div class="form-group">\
                                    <label for="update_desc" class="font-600">Comments</label>\
                                    <ul class="list-group comment-section" id="showComments_' + res.data.project_task_id + '">' + allComments + '</ul>\
                                    <textarea required placeholder="Add a comment" class="form-control input-sm no-resize autoExpand" id="add_comment_' + res.data.project_task_id + '"  ></textarea>\
                                    <input type="hidden" id="hidComment'+res.data.project_task_id+'">\
                                            <div class="form-group upload-doc-main mb0">\
                                            <div class="input-group"><input class="upload-doc hide"  type="file" name="image" accept="image/*" ><label><div class="btn btn-xs btn-default font10 bradius0"><i class="fa fa-upload"></i> Choose File</div> </label></div>\
                                            <p class="upload-file-label text-left mb0"></p>\
                                             <a href="#" class="clear-data hide"><i class="fa fa-close"></i></a></div>\
                                    <div class="text-right mt5 mb5">\
                                        <span class="data-loading mr5"><i class="fa fa-spinner fa-spin font12"></i></span>\
                                        <button class="btn btn-xs btn-primary comment-btn-submit" type="button" onclick="addComment(' + res.data.project_task_id + ')">Add Comment</button>\
                                    </div>\
                                    <span id="commentError'+res.data.project_task_id+'" style="color:red;"></span>\
                                </div>\
                            </form>\
                        </div>';

                        }else {

                            var formHtml = '<form class="board-form-common form-editable">\
                                <div class="form-group mb5">\
                                <div class="board-action-btn text-right">\
                               <div class="btn-group btn-group-xs .smlBtnGroup" id="smlBtnGroup'+res.data.project_task_id+'">\
                                    <a href="javascript:void(0);" class="btn btn-default btn-no-radius" title="Expand View" onclick="showExpendView('+res.data.project_task_id+')">\
                                        <i class="fa fa-expand" aria-hidden="true"></i>\
                                    </a>\
                                    \<a href="javascript:void(0);" class="btn btn-default btn-no-radius" title="Activity Logs" onclick="showExpendViewHistory('+res.data.project_task_id+')">\
                                        <i class="fa fa-list" aria-hidden="true"></i>\
                                    </a>\
                                    <button type="button" class="btn btn-default btn-no-radius" data-tooltip="true" data-placement="bottom" title="Delete Task"  onclick="removeTask('+res.data.project_task_id+')">\
                                        <i class="fa fa-trash" aria-hidden="true"></i>\
                                    </button>\
                                    </div>\
                                </div>\
                                </div>\
                                <div class="form-group">\
                                    <label for="update_sub" class="font-600">Title</label>\
                                   <span style="float:right; color:#262626;">'+res.data.created_task_date+' </span>\
                                    <textarea  class="form-control input-sm no-resize autoExpand" rows="1" id="update_sub_' + res.data.project_task_id + '" value="">' + res.data.subject + '</textarea>\
                                </div>\
                                <div class="form-group">\
                                    <label for="update_desc" class="font-600">Description</label>\
                                    <textarea class="form-control input-sm no-resize autoExpand"   id="update_desc_' + res.data.project_task_id + '">' + res.data.description + '</textarea>\
                                </div>\
                                <div class="form-group">'+taskFile+'</div>\
                                <div class="form-group">\
                                    <div class="assignedUserList">'+userData+'</div>\
                                </div>\
                                <div class="form-group" style="display:none">\
                                    <p for="assgnUser" class="mb0 font-600">Set Priority</p>\
                                    '+usersPeriorities+'\
                                </div>\
                                <div class="clearfix"></div>\
                                 <div id="roleErr'+res.data.project_task_id+'" style=""></div>\
                                    <button id="updateBtn'+res.data.project_task_id+'" onclick="updateTask(' + res.data.project_task_id + ')" type="button" class="btn btn-default btn-success btn-xs ml0 mt10 mb5">Update</button>\
                            </form>\
                            <div>\
                                    <form class="board-form-common">\
                                    <div class="form-group">\
                                        <label class="font-600">Comments</label>\
                                        <ul class="list-group comment-section" id="showComments_' + res.data.project_task_id + '">' + allComments + '</ul>\
                                        <textarea required class="form-control input-sm no-resize autoExpand" id="add_comment_' + res.data.project_task_id + '"  ></textarea>\
                                        <input type="hidden" id="hidComment'+res.data.project_task_id+'">\
                                        <div class="text-right mt5 mb5">\
                                            <div class="form-group upload-doc-main mb0">\
                                                <div class="input-group"><input class="upload-doc"  type="file" name="image" accept="image/*" ><label><div class="btn btn-xs btn-default font10 bradius0 browse"><i class="fa fa-upload"></i> Choose File</div> </label></div>\
                                            <p class="upload-file-label text-left mb0"></p>\
                                            <a href="#" class="clear-data hide"><i class="fa fa-close"></i></a></div>\
                                            <span class="data-loading mr5">\
                                            <i class="fa fa-spinner fa-spin font12"></i></span>\
                                            <button class="btn btn-primary btn-xs comment-btn-submit" type="button" onclick="addComment(' + res.data.project_task_id + ')">Add Comment</button>\
                                            </div>\
                                        <div class="text-right mt5 mb5"><button id="close_button_'+res.data.project_task_id+'" class="btn btn-default hide btn-xs" type="button" onclick="closeExpandView(' + res.data.project_task_id + ')">Close</button></div>\
                                        <span id="commentError'+res.data.project_task_id+'" style="color:red;"></span>\
                                    </div>\
                                </form>\
                            </div>';
                        }
                        $('#appendDataId' + taskId).html(formHtml);
                        addtxtClass();
                        //loadTooltip();
                    }
                },
            });
        }
    }

    function uploaddoc(){

        /*var fd = new FormData();
         fd.append('_token','{{csrf_token()}}');
         $.ajax({
         url : '{{route('uploadTaskDoc')}}',
         type: "POST",             // Type of request to be send, called as method
         data :fd,                 // Data sent to server, a set of key/value pairs (i.e. form fields and values)
         //contentType: false,       // The content type used when sending data to the server.
         //cache: false,             // To unable request pages to be cached
         //processData:false,        // To send DOMDocument or non processed data file it is set to false
         success: function(data)   // A function to be called if request succeeds
         {
         alert(data);
         /!* $('#loading').hide();
         $("#message").html(data);*!/
         }
         });*/
        /*  var formData = new FormData();
         formData.append('file', $('input[type=file]')[0].files[0]);
         //formData.append('_token','{{csrf_token()}}');

         $.ajax({
         url : '{{route('uploadTaskDoc')}}',
         type: 'POST',
         data: formData,
         headers: {
         'X-CSRF-Token': '{{csrf_token()}}'
         },
         contentType: false,       // The content type used when sending data to the server.
         cache: false,             // To unable request pages to be cached
         processData:false,        // To send DOMDocument or non processed data file it is set to false
         success: function (data) {
         alert(data)
         },
         });

         return false;*/

    }

    function updateTask(tid){
        var login_user_id = '{{ Auth::user()->id }}';

        var board_id = $("#projectTask_"+tid).attr("task-board-id");

        //alert(login_user_id);
        //alert(tid);return false;
        $('#updateBtn'+tid).prop('disabled',true);
        //$('#updateBtn'+tid).html('<i class="fa fa-cog fa-spin fa-3x fa-fw" style="font-size: 22px;"></i>');
        //$('#updateBtn'+tid).attr('onclick','');
        var update_sub = $('#update_sub_'+tid).val();
        var update_desc = $('#update_desc_'+tid).val();
        var chkUser = [];


        $('.userDataId_'+tid+':checked').each(function(i){
            chkUser[i] = $(this).val();
        });
        //console.log(chkUser);
        var prioritychk = [];
        $('.radiopriority'+tid+':checked').each(function(i){
            prioritychk = $(this).val();
        });
        var project_id = '#projectTask_'+tid;
        $(project_id).find('.timer-main button').remove('');
        var project_id = '#projectTask_'+tid;



        $.ajax({
            url : '{{route('updateTask')}}',
            type : 'POST',
            data :{_token: '{{csrf_token()}}','subject': update_sub,'description':update_desc,
                'users':chkUser,'taskId':tid,'priority':prioritychk,'action':'update-task-history'},
            dataType : 'JSON',
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            success : function(result){

                if(result == 100) {

                    $.each(chkUser, function(ind,val){
                        var play_value = '<button class="timer timer_button timer_start" type="button" value="Play" data-project-task-id="'+tid+'" data-project-board-id="'+board_id+'" onclick=""></button>';
                        if(val == login_user_id){
                            $(project_id).find('.timer-main button').remove('');
                            $(project_id).find('.timer-main').prepend(play_value);
                        }
                    });




                    $('#title_' + tid).text(' '+update_sub);
                    $('#update_sub_' + tid).text(update_sub);
                    $('#update_desc_' + tid).text(update_desc);
                    $('#project_title_' + tid).text(update_sub);
                    $('#project_description_' + tid).text(update_desc);

                    $('#roleErr'+tid).slideDown('slow').css('color','green').html('<i class="fa fa-check font14"></i> Successfully updated');
                    /*$('#roleErr'+tid)
                     $('#roleErr'+tid);*/

                    /*** change top box priority color  ***/
                    $('.radiopriority'+tid).removeAttr('checked');

                    $('#radio'+prioritychk+'_'+tid).attr('checked','true');

                    if(prioritychk == 1){
                        $('.priorityColor_'+tid).css('background','#cc4632');

                    }else if(prioritychk == 2){
                        $('.priorityColor_'+tid).css('background','#ee5d72');
                    }else if(prioritychk == 3){
                        $('.priorityColor_'+tid).css('background','#f3933f');
                    }else if(prioritychk == 4){
                        $('.priorityColor_'+tid).css('background','#707070');
                    }else if(prioritychk == 5){
                        $('.priorityColor_'+tid).css('background','#999999');
                    }
                    setTimeout(function() {
                        $('#roleErr'+tid).text('');
                        $('#roleErr'+tid).css('color','');
                    },1000)
                    $('#updateBtn'+tid).prop('disabled',false);
                }
                if(result == 0){

                    $('#roleErr'+tid).css('color','red');
                    $('#roleErr'+tid).text('Sorry You have no permission to update data');
                    $('#roleErr'+tid).css('color','');
                    $('#updateBtn'+tid).prop('disabled',false);
                }
            },
            error : function(errors){
                $.each(errors,function(ind,val){

                    if(!$.isEmptyObject(val.subject)) {

                        $('#roleErr'+tid).slideDown('slow').css('color','red').html('<i class="fa fa-exclamation-circle text-danger font14"></i> '+ val.subject);

                        $('#updateBtn'+tid).prop('disabled',false);
                        return false;

                    }else if(!$.isEmptyObject(val.users)){

                        $('#roleErr'+tid).slideDown('slow').css('color','red').html('<i class="fa fa-exclamation-circle text-danger font14"></i> '+ val.users);

                        $('#updateBtn'+tid).prop('disabled',false);
                        return false;
                    }else{

                        $('#roleErr'+tid).slideDown('slow').css('color','red').html('<i class="fa fa-exclamation-circle text-danger font14"></i> Please try again , there is some error');

                        $('#updateBtn'+tid).prop('disabled',false);
                    }
                });
                setTimeout(function() {
                    $('#roleErr'+tid).text('');
                    $('#roleErr'+tid).css('color','');
                },1000)
                $('#updateBtn'+tid).prop('disabled',false);
            },
        });
    }

    function addComment(tid){

//        var ext = $('#my_file_field').val().split('.').pop().toLowerCase();
//        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
//            alert('invalid extension!');
//        }
        var formData = new FormData();
        formData.append('file', $('input[type=file]')[0].files[0]);
        var userId      = '{{$userId}}';
        var comments    = $('#add_comment_'+tid).val();
        var hidcomment_id  = $('#hidComment'+tid).val();
        var chkUser = [];
        $('.userDataId_'+tid+':checked').each(function(i){
            chkUser[i] = $(this).val();
        });

        formData.append('users',chkUser);
        formData.append('userId',userId);
        formData.append('comment',comments);
        formData.append('taskId',tid);
        formData.append('comment_id',hidcomment_id);
        formData.append('action','comment-task-history');
        filePath    = '{{asset('/images/thumbnail/')}}';

        //alert(tid+'--'+'--'+userId+'--'+comments+'--'+hidcomment_id);
        $.ajax({

            url      : '{{route('taskComments')}}',
            type     : 'POST',
            data     : formData,
            headers: {
                'X-CSRF-Token': '{{csrf_token()}}'
            },
            dataType : 'JSON',
            beforeSend: function(){

                if (comments != '') {
                    $('.data-loading').addClass('active');
                    $('.comment-btn-submit').attr('disabled','disabled');
                }
                else {
                    return false;
                }

            },
            contentType: false,
            cache: false,
            processData:false,
            success  : function (responce) {

                $('.data-loading').removeClass('active');
                $('.comment-btn-submit').removeAttr('disabled','disabled');
                if(responce.success == 100) {
                    $('#add_comment_'+tid).val('');
                    $('#hidComment'+tid).val('');

                    $('#editCommnt'+responce.commentId).attr('onclick','');
                    $('#editCommnt'+responce.commentId).attr('onclick','updateComment('+responce.commentId+','+tid+')');
                    $('#editCommnt'+responce.commentId).text('Edit');
                    $('#hidComment'+tid).val('');

                    //console.log(responce);
                    if(hidcomment_id ==''){
                        var CommnetImage = '';
                        if(!$.isEmptyObject(responce.file)){
                            /*CommnetImage =  '<img width="50" height="50" src="'+filePath+'/'+responce.file+'" id='+responce.commentId+' class="comment-img">';*/
                            CommnetImage =  '<a href="javascript:void(0);" data-src = "'+filePath+'/'+responce.file+'" id='+responce.commentId+' class="comment-img btn btn-link font10"><i class="fa fa-paperclip"></i> 1 attachment</a>';
                        }

                        var cmtDt = '<li class="list-group-item" id="taskCommentId_' + responce.commentId+ '">\
                                    <span class="remove-comment" id="taskCommentId_"'+responce.commentId+'><a href="#" onclick="removeComment(' + responce.commentId + ')"><i class="fa fa-close"></i></a></span>\
                                    <div class="heading"><span class="member-name font-600 text-capitalize">'  + responce.user.name + '</span> <span class="time-stamp pull-right font10">'+responce.created_at+'</span></div>\
                                    <div class="message" id="userComment' + responce.commentId + '">' + responce.comment + '<span class="view-attachment-file">'+CommnetImage+'</span></div>\
                                    \
                                    <span class="hide" id="editCommnt' + responce.commentId + '" onclick="updateComment(' + responce.commentId + ',' + tid + ')" style="cursor: pointer;color: red;float: right;">Edit</span>\
                            </li>';
                        $('#showComments_' + tid).append(cmtDt);
                    }else{

                        $('#userComment'+responce.commentId).text(responce.comment);
                    }

                    $('.upload-doc').val('');
                    $('.clear-data').addClass('hide');
                    $('.upload-file-label').text('');
                }else{

                    $('#commentError'+tid).text('Please try again');
                    setTimeout(function(){
                        $('#commentError'+tid).text('');
                    },800);
                }

                addtxtClassUpdate()
            },
            error : function (responce){
                $.each(responce,function(ind,val){

                    if(!$.isEmptyObject(val.comment)){
                        $('#commentError'+tid).text(val.comment);
                        return false;
                    }else{
                        $('#commentError'+tid).text('Please try again');
                    }

                });
                setTimeout(function(){
                    $('#commentError'+tid).text('');
                },800);
            },
        });
    }

    function updateComment(cid,tid){

        var txt = $('.cancleCls').text();

        if(txt == '') {
            var comment = $('#userComment' + cid).text();
            $('#add_comment_' + tid).val(comment);
            $('#editCommnt' + cid).attr('onclick', '');
            $('#editCommnt' + cid).addClass(' '+cid);
            $('#editCommnt' + cid).addClass('cancleCls');
            $('#editCommnt' + cid).attr('onclick', 'cancelComment(' + cid + ',' + tid + ')');
            $('#editCommnt' + cid).text('cancel');
            $('#hidComment' + tid).val(cid);
        }else{


            var onc = $('.cancleCls').attr('class');

            var success = onc.replace('ecomment', '');
            var successOne = success.replace('cancleCls', '');
            var trm  = successOne.replace(/ /g,'');
            cancelComment(trm,tid);

            updateComment(cid,tid);
        }

        addtxtClassUpdate()
    }

    function cancelComment(cid,tid){
        //alert(cid+'=='+tid);
        $('#add_comment_'+tid).val('');
        $('#editCommnt' + cid).removeClass('cancleCls');
        $('#editCommnt'+cid).attr('onclick','');
        $('#editCommnt'+cid).attr('onclick','updateComment('+cid+','+tid+')');
        $('#editCommnt'+cid).text('Edit');
        $('#hidComment'+tid).val('');
    }


    /* function loadTooltip() {

     $('[data-tooltip="true"]').tooltip({
     trigger: 'hover'
     });
     }*/
    function addtxtClassUpdate(){
        var textTarget = $('.board-panel-body-common textarea ');
        setTimeout(function(){
            autosize(textTarget);
            autosize.update(textTarget);
        },100);
    }
    function showExpendView(taskId){
        expndView = $('#appendDataId'+taskId).html();
        $('#expendedViewBody').html(expndView);
        $('#smlBtnGroup'+taskId).hide();
        $('#close_button_'+taskId).removeClass('hide');
        $('.closeExpandView').attr('onclick','closeExpandView("'+taskId+'")');
        $('.expView').fadeIn('slow');
        $('#expendedViewBody').fadeIn('slow');
        $('#appendDataId'+taskId).html('');
        addtxtClassUpdate()
    }

    function showExpendViewHistory(taskId){
        expndView = $('.test-body')
        expndView.fadeIn('slow');
        $('.closeExpandHistoryView').attr('onclick','closeExpandHistoryView("'+taskId+'")');
        $.ajax({
            url: '{{route('get_history_log')}}',
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                'taskId': taskId,

            },
            success: function (response) {
                $('#historyLogData').html('');
                $('#historyLogData').append(response);
            }
        });

    }


    function closeExpandHistoryView(taskId){

        expndView = $('#showExpendViewHistory').html();
        $('#appendDataId'+taskId).html(expndView);
        $('.expView').fadeOut('slow');
        $('#smlBtnGroup'+taskId).show();
        $('.board-action-btn div').removeAttr('style');
        $('#showExpendViewHistory').html('');
        addtxtClassUpdate()

    }

    function closeExpandView(taskId){

        expndView = $('#expendedViewBody').html();
        $('#appendDataId'+taskId).html(expndView);
        $('.expView').fadeOut('slow');
        $('#smlBtnGroup'+taskId).show();

        $('.board-action-btn div').removeAttr('style');

        $('#expendedViewBody').html('');
        $('body').find('#close_button_'+taskId).addClass('hide');
        addtxtClassUpdate()

    }

    function checkchecked(id){
        if($('#lb_'+id).is(':checked') == false){
            $('#lb_'+id).removeAttr('checked');
        }else{
            $('#lb_'+id).attr('checked','true');
        }
    }

    function removeTask(taskId){

        if(taskId) {

            if(confirm("Are you sure you want to delete this task !")) {

                $.ajax({

                    url: '{{route('removeTask')}}',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        'taskId': taskId,
                    },
                    dataType: 'JSON',
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function (response) {
                        //alert(response);return false;
                        if (response.success == 100) {
                            $('#projectTask_' + taskId).hide();
                            toastr.success('Task deleted successfully');
                        } else {
                            alert("sorry something wrong please reload page and try again");
                        }
                    }
                });
            }
        }
    }
    var ajaxInProgress = false;
    function loadMoreData(page,projectID,projectBoardId,columnId, alltask,ths,boardTask,getcolumn){

        var columnCheckValue = [];
        $('.chkb_'+projectBoardId+':checked').each(function(ind,val) {
            columnCheckValue.push($(this).val());
        });
        if(typeof(columnCheckValue) != "undefined" && columnCheckValue !== null && !$.isEmptyObject(columnCheckValue)) {
            columnId = columnCheckValue;
            getcolumn = 'getcolumn';

        }
        //alert(columnId);
        if(ajaxInProgress){ return; };
        ajaxInProgress = true;

        $.ajax(
                {
                    url: '?page=' + page,
                    data:{
                        _token: '{{csrf_token()}}',
                        project_id:projectID,
                        action:'board-with-task',
                        project_board_id:projectBoardId,
                        column_id:columnId
                    },
                    type: "get",
                    beforeSend: function()
                    {
                        $('.ajax-load').show();
                    }
                })
                .done(function(data)
                {


                    ajaxInProgress = false;
                    if(boardTask) {
                        if (!$.trim(data)) {

                            if (!$.isEmptyObject(columnId)) {


                                $('.myCls' + columnId).addClass('no-more-data');
                            }else{

                                $('#board_'+projectBoardId).addClass('no-more-data');
                            }
                        }
                    }

                    if(data == " "){

                        $("#board_"+projectBoardId).html("No more records found");
                        return;
                    }

                    if(typeof(columnCheckValue) != "undefined" && columnCheckValue !== null && !$.isEmptyObject(columnCheckValue)) {

                        $("#board_" + projectBoardId).append(data);
                    }else{

                        $('.ajax-load').hide();
                        if (columnId) {
                            $("#board_" + projectBoardId).html('');
                        } else {
                            if (alltask) {
                                $("#board_" + projectBoardId).html('');

                            }
                        }
                        if (!$.isEmptyObject(data)) {
                            if(data){
                                if(columnId == 'all') {
                                    $("#board_" + projectBoardId).append(data);
                                }
                            }

                            if (columnId) {
                                $('.myCls' + columnId).append(data);

                            } else {

                                if(columnId != 'all') {
                                    $("#board_" + projectBoardId).append(data);
                                }
                            }
                        }
                    }
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('Data not found..');
                    ajaxInProgress = false;
                });
    }



    var boardColumnId = '';
    $('div.panel-main .panel-body ul.list-group').bind('scroll', function () {

        //console.log($(this).scrollTop(), Math.floor($(this).innerHeight()),$(this)[0].scrollHeight);
        boardColumnId = $(this).attr('data-board-column-id');

        if($.isEmptyObject(boardColumnId)) {

            if ($(this).scrollTop() + $(this).innerHeight() + 1 >= $(this)[0].scrollHeight) {


                var projectID = $('.load-more').attr('projectID');
                var projectBoardId = $(this).attr('data-bid');
                var page =   $('#boardHiddenField'+projectBoardId).val();
                if (page >= 1) {
                    pageNumber = parseInt(page) + 1;
                    $('#boardHiddenField'+projectBoardId).val(pageNumber);
                } else {

                    var custom_coverage = $("input#boardHiddenField");
                    custom_coverage.val(1);

                }

                if($(this).hasClass('no-more-data') == false) {

                    loadMoreData(page, projectID, projectBoardId, '', '', this, 'boardTask');
                }

            }
        }else{

            lastPage = 0;
            if ($(this).scrollTop() + $(this).innerHeight() + 1 >= $(this)[0].scrollHeight) {

                var projectID = $(this).attr('data-project-id');
                var projectBoardId = $(this).attr('data-board-id');
                var page =   $('#boardHiddenField'+boardColumnId).val();
                lastPage = localStorage.getItem('lastPage');
                if(isNaN(lastPage)){
                    lastPage = 1;
                }
                if (lastPage != page) {

                    pageNumber = parseInt(lastPage)+1;
                    $('#boardHiddenField' + boardColumnId).val(pageNumber);
                    //}
                } else {
                    $('#boardHiddenField'+boardColumnId).val(0);
                }
                if($(this).hasClass('no-more-data') == false){
                    localStorage.setItem("lastPage", page);
                    //alert('2==>'+page);
                    //localStorage.setItem("lastColumn", boardColumnId);
                    loadMoreData(page, projectID,projectBoardId,boardColumnId,'','',this,'boardTask');
                }
            }
        }
    });


    /*
     $('body').on('click', '.get-all-tasks', function (e) {

     var projectID = $('#getProjectIdHiddenField').val();

     var projectBoardId = $(e.target).closest('.panel-default').find('ul.list-group').attr('data-bid');

     loadMoreData(0, projectID, projectBoardId, '', 1);
     })
     */


    var dataRecieved = 0;
    var arrayBox = [];
    function getAllColumnWithTask(boardId){

        var found = jQuery.inArray(boardId, arrayBox );

        if (boardId == dataRecieved) {

        } else {

            if (found == -1) {
                $.ajax({
                    url: '{{route('getBoardColumn')}}',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        'boardId': boardId,
                        'action': 'all-board-column',
                    },
                    success: function (response) {
                        //alert(response);
                        $('body').find('#column_board_id_' + boardId).html('');
                        //$('#panel-setting-4').addClass('open');
                        $('body').find('#column_board_id_' + boardId).append(response);
                        dataRecieved = !dataRecieved;
                        // setTimeout(function () {

                        //getAllColumnWithTask(boardId);
                        // $('.columnWithTask').trigger('click');
                        // }, 600);
                    }

                });
            }
        }
        if(!arrayBox.includes(boardId)) {
            arrayBox.push(boardId);
        }

        dataRecieved = boardId
    }



    /*function allTaskColumn(projectBoardId,columnId, object){

     var column_id = $(object).attr('id');
     $('#'+ column_id).closest('.dropdown-menu').find('.optionSelected').removeClass('optionSelected');
     $('#'+ column_id).addClass('optionSelected');


     if(columnId) {

     loadMoreData(0,0, projectBoardId, columnId, '');
     }

     }*/
    function allTaskColumn(projectBoardId,columnId, object){

        $("#board_" + projectBoardId).html('');
        $('#boardHiddenField'+projectBoardId).val('2');

        localStorage.setItem("lastPage", '2');
        $('.coloumn_checkbox').each(function(ind,val){

            $(this).prop('checked',false)
        });

        $('#chk_'+columnId).prop('checked',true);

        if(object) {
            var column_id = $(object).attr('id');

            $('#'+ column_id).closest('.dropdown-menu').find('.optionSelected').removeClass('optionSelected');
            $('#'+ column_id).addClass('optionSelected');

            if(columnId) {

                loadMoreData(0,0, projectBoardId, columnId, '','','','getcolumn');
            }
        }

        if(columnId) {

            loadMoreData(0,0, projectBoardId, columnId, '','','','getcolumn');
        }
    }

    function convertDateTimeFormat(){

        return Date.parse('2010-10-18, 10:06 AM');

    }

    function toTimeZone(date,format) {
        //var format = 'Y-m-d g:i:a';
        //alert(format);
        //var forMat = format;
        //return moment(date).format(format);
        //return moment(time, format).tz(zone).format(format);
    }

    $( document ).ready(function(e) {

        $('.panel-default').find('.panel-body input[name=board_hidden_field]').val('1');

    })

    $(function () {

        $('body').append('<button type="button" class="close attach-btn hide">&times;</button>');

        $('body').on('click', '.attach-btn', function() {
            $('#modalID').modal('hide');
            $('.attach-btn').addClass('hide');
        })

        function positionCloseBtn() {
            var btnCloseAlign = $('.attach-btn');
            var imgSelected = $(".img-append img");
            var imgPreview = imgSelected.width();
            var imgPreviewPos = imgSelected.offset();
            var imgPreviewLeft = imgPreviewPos.left;
            var imgPreviewTop = imgPreviewPos.top - 8;
            var btnPosition = imgPreview + imgPreviewLeft - 8;
            btnCloseAlign.css('transform','translate('+ btnPosition  + 'px,'+ imgPreviewTop + 'px)');
        }



        $('#modalID').on('shown.bs.modal', function (e) {
            $('.attach-btn').removeClass('hide');
            positionCloseBtn();

        })
        $('#modalID').on('hide.bs.modal', function (e) {
            $('.attach-btn').addClass('hide');
            positionCloseBtn();

        })



    });

    $('body').on('click','.comment-img',function(e){
        e.stopPropagation();
        var path =  $(this).attr('data-src');
        var ret = path.replace('/thumbnail','');
        var imgAppend = $(".img-append");
        $('#modalID').modal('show')
        imgAppend.html('');
        imgAppend.append('<img src ='+ret+' class="img-responsive" alt="preview">' );


    })

    function removeComment(commentId){
        if (commentId != '') {
            if (confirm("Are you sure you want to delete this comment !")) {
                //$('body').find('#userComment'+ commentId).remove('');
                $.ajax({
                    url: '{{route('taskComments')}}',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        'commentId': commentId,
                        'action': 'action-remove-comment',
                    },
                    success: function (response) {

                        $('body').find('#taskCommentId_' + commentId).remove('');

                    }

                });
            }
        }
    }

    $(function() {
        $(document).on('click', '.browse', function(){
            var file = $(this).parent().parent().find('.upload-doc');
            file.trigger('click');
        });
        $(document).on('change', '.upload-doc', function(){
            $(this).parent().parent().find('.upload-file-label').text($(this).val().replace(/C:\\fakepath\\/i, ''));
            $('.clear-data').removeClass('hide');
        });
        $(document).on('click', '.clear-data', function() {
            $('.upload-file-label').text('');
            $('.upload-doc').val('');
            $(this).addClass('hide');
        })
    })

    $(function() {
        var addTaskModal = $('#addTask');
        $('.open-create-task-form').on('click', function(e){
            e.stopPropagation();
            var getColumnId = $(this).attr('data-id');
            //console.log(getColumnId)
            addTaskModal.modal('show');
            addTaskModal.on('shown.bs.modal', function (e) {
                $('#columns').val(getColumnId)
            })
        })
        addTaskModal.on('shown.bs.modal', function (e) {
            $('#taskForm')[0].reset();
        })
    })
</script>

