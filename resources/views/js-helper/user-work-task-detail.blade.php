<div id="modalID" class="modal fade image-popup-class" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content modal-preview-image">
            {{--<a type="button" href="#" class="close close-img-vew pull-right" onclick="$('#modalID').modal('hide');">&times;</a>--}}

            <div class="img-append">

            </div>

        </div>

    </div>
</div>

<script>

    function showImage(e) {
          var path =  $(e).attr('data-img-path')
         var ret = path.replace('thumbnail','');
         $('.image-popup-class').modal('show')
            var imgAppend = $(".img-append");
        imgAppend.html('');
        imgAppend.append('<img src ='+ret+' class="img-responsive" alt="preview">' );
         //$( ".img-append" ).css('background-image', 'url(' +ret+ ')');
    }

    function showTaskImage(e) {
        var path =  $(e).attr('data-src')
        var ret = path.replace('thumbnail','');
        $('.image-popup-class').modal('show')
        var imgAppend = $(".img-append");
        imgAppend.html('');
        imgAppend.append('<img src ='+ret+' class="img-responsive" alt="preview">' );
        //$( ".img-append" ).css('background-image', 'url(' +ret+ ')');
    }


    function getActivityLogs(projectTaskId,userId){

        $('.logs-task').html('');

        $.ajax({
            'url': '{{route('getUserTaskLogs')}}',
            'type': 'POST',
            'data': {'_token': '{{csrf_token()}}', 'task_id': projectTaskId, 'user_id': userId},
            'dataType': 'JSON',
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            success: function (response) {

                if(response.success == 100) {
                    $.each(response.taskLogs, function (li, lv) {

                        taskLogHtml = ' <div class="task-logs">\
                                         <div>\
                                         <span class="member-name">' + lv.name + '</span>\
                                         <span class="pull-right time-stamp font10">' + lv.date + '</span>\
                                         </div>\
                                         <p class="logs-comment mb0">' + lv.message + '</p>\
                                         </div>';

                        $('.logs-task').append(taskLogHtml);
                    });
                }

            }
        });
    }



        var getDataAttr;
        var userId;

        function getData(id,userid) {

            /*getDataAttr  = this.getAttribute('data-id');
            userId  = this.getAttribute('data-user-id');*/

            getDataAttr = id;
            userId = userid;



            if(!$.isEmptyObject(getDataAttr)){

                $('.comment-section').html('');
                $('.assignedUserList').html('');
//$('.activity_logs').after('');
                $("table").find("tr:gt(0)").remove();
                var commnetHtml = '';
                var userHtml = '';
                $('.activity-logs-section').hide();
                $('.activity_logs_show').show();
                $.ajax({
                    'url'   : '{{route('getTaskDetail')}}',
                    'type'  :   'POST',
                    'data'  : {'_token': '{{csrf_token()}}','task_id' :  getDataAttr,'user_id' : userId},
                    'dataType'  : 'JSON',
                    statusCode: {
                        500: function (response) {
                            //window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },

                    success : function (response){

                        if(response.success == '100'){

                            filePath    = '{{asset('/images/thumbnail/')}}';
                            $('#task_title').text(response.data.subject);
                            $('#project_name').text(response.project_name);
                            $('#board_name').text(response.board_name);
                            $('#task_description').text(response.data.description);
                            $('.timming-total').text(response.total_task_timming);
                            if(!$.isEmptyObject(response.data.file)){
                                $('.view-attachment-file').html('<a href="javascript:void(0);" onclick="showTaskImage(this)" data-src = "'+filePath+'/'+response.data.file+'" class="comment-img btn btn-link font10"><i class="fa fa-paperclip"></i> 1 attachment</a>');
                            }
                            else{
                                $('.view-attachment-file').html('');
                            }
                            $('.activity_logs_show').attr('onclick','getActivityLogs('+response.data.project_task_id+','+response.user_id+')');

                            $.each(response.asgnUser , function (index,value) {

                                userHtml = ' <span class="label label-default"> '+value.name+' </span> ';
                                $('.assignedUserList').append(userHtml);

                            });

                            $.each(response.tskComments , function (indexs,values) {

                                filePath    = '{{asset('/images/thumbnail/')}}';
                                commnetImgFile = '';
                                if(!$.isEmptyObject(values.file)){

                                    //commnetImgFile =  '<a href="javascript:void(0);" onclick="showImage(this)" data-img-path="'+filePath+'/'+values.file+'"> <img width="50" height="50" src="'+filePath+'/'+values.file+'" id='+values.task_comment_id+' class="comment-img"></a>';

                                    commnetImgFile =  '<a href="javascript:void(0);" onclick="showImage(this)" data-img-path="'+filePath+'/'+values.file+'" id='+values.task_comment_id+' class="comment-img btn btn-link font10"><i class="fa fa-paperclip"></i> 1 attachment</a>';
                                    //alert(commnetImgFile);

                                }

                                commnetHtml ='<li class="list-group-item">\
    <div class="heading"><span class="member-name font-500 text-capitalize">'+values.name+'</span>\
        <span class="time-stamp pull-right font10"> '+values.date+'</span>\
    </div>\
    <div class="message" id="userComment4">'+values.comment+'</div>\
    <div class="message" id="userComment4"><span class="view-attachment-file">'+commnetImgFile+'</span></div>\
</li>';

                                $('.comment-section').append(commnetHtml);
                            });
                            var timeHtml = '';
                            $.each(response.taskTimmings , function (ind,val) {
                                dayTotal = val.dayTotal;
                                var localValue = '';
                                localStorage.setItem("localValue", 0);
                                $.each(dayTotal,function(i,va){
                                    //alert(va);
                                    //mtotals += toSeconds(va);
                                    var parts = va.split(':');
                                    totalSec = (+parts[0]) * 60 * 60 + (+parts[1]) * 60 + (+parts[2]);
                                    localValue = localStorage.getItem("localValue");
                                    if(i > 0){

                                        var oldVal = localStorage.getItem("localValue");
                                        totalSec = parseInt(oldVal)+parseInt(totalSec);
                                    }
                                    localStorage.setItem("localValue", totalSec);
                                });

                                timeHtml += '<tr id="date_'+val.date+'">';
                                if(val.date == response.todayDate){

                                    timeHtml += '<th colspan="2">Today</th>';

                                    timeHtml += '<th class="text-right timming-total">'+toHHMMSS(totalSec)+'</th>';
                                }else if(val.date == response.yesterdayDate){

                                    timeHtml += '<th colspan="2">Yesterday</th>';
                                    timeHtml += '<th class="text-right timming-total">'+toHHMMSS(totalSec)+'</th>';
                                }else{

                                    timeHtml += '<th colspan="2">'+val.date+'</th>';
                                    timeHtml += '<th class="text-right timming-total">'+toHHMMSS(totalSec)+'</th>';


                                }

                                var atimeHtml= '';
                                $.each(val.timeAry , function (index,value) {
                                    //alert(value.time_start_time);
                                    timeHtml += '<tr>\
                                     <td><span class="time-label start-time-label"></span> '+value.time_start_time+'</td>\
                                     <td><span class="time-label end-time-label"></span> '+value.time_end_time+'</td>\
                                     <td align="right">'+value.time_total+'</td>\
                                     </tr>';
                                });
                                timeHtml += '</tr>';
                            });
                            $('#table_timming').after(timeHtml);
                            $('#view-task-detail').modal();
                        }
                    },
                })
            }
        }

        function toSeconds( time ) {
            var parts = time.split(':');
            return (+parts[0]) * 60 * 60 + (+parts[1]) * 60 + (+parts[2]);
        }

        function toHHMMSS(sec) {

            var sec_num = parseInt(sec, 10); // don't forget the second parm
            var hours   = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            if (seconds < 10) {seconds = "0"+seconds;}
            var time    = hours+':'+minutes+':'+seconds;
            return time;
        }



        $('.activity-logs-section').hide();
        var showActivityLogs = $('.btn-show-section');
        showActivityLogs.on('click', function () {
            $('.activity-logs-section').show()
        });

        $('.btn-hide-activity-logs').on('click', function() {
            $('.activity-logs-section').hide();
            showActivityLogs.show();
        })


       /* var paginateSizeSm = $('.paginate-size-sm');

        paginateSizeSm.find('ul.pagination').addClass('pagination-sm');*/

    //})


</script>

