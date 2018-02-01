<div class="modal fade jp-custom-slide-modal" id="view-task-detail" data-backdrop="true" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="expanded-view view-task-detail">

                <div class="content-section">
                    <div class="panel-main-heading">
                        Task Details
                        <div class="task-detail-info">
                            <div class="other-info">
                                <table class="table mb0">
                                    <tr>
                                        <td width="50%"><span class="info-item">Project Name : <span id="project_name"></span></span></td>
                                        <td width="50%"><span class="info-item">Board Name : <span id="board_name"></span></span></td>
                                    </tr>


                                </table>


                                {{--<div class="task-detail-info">--}}
                                    {{--<div class="other-info">--}}
                                        {{--<table class="table mb0">--}}
                                            {{--<tbody><tr>--}}
                                                {{--<td width="50%"><span class="info-item">Project Name : <span id="project_name">Project Management</span></span></td>--}}
                                                {{--<td width="50%"><span class="info-item">Board Name : <span id="board_name">Main Board</span></span></td>--}}
                                            {{--</tr>--}}


                                            {{--</tbody></table>--}}

                                    {{--</div>--}}
                                {{--</div>--}}




                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default view-detail-common">

                        <div class="panel-body board-panel-body-common" id="appendDataId12">

                            <form class="board-form-common form-editable">

                                <div class="form-group"><label class="font-600">Title</label>
                                        <textarea disabled
                                                  id="task_title"
                                                  class="form-control input-sm no-resize autoExpand"
                                                  rows="1"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="font-600">Description</label>
                                        <textarea disabled
                                                  id="task_description"
                                                  class="form-control input-sm no-resize autoExpand"></textarea>
                                </div>
                                <div class="form-group">
                                  <span class="view-attachment-file"></span>
                                </div>
                                <div class="form-group"><label
                                            class="mb0 font-600">Members</label>
                                    <div class="clearfix"></div>
                                    <div class="assignedUserList">
                                        <span class="label label-default">Amarjit</span>
                                        <span class="label label-default">Pankhi</span>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  class="mb0 font-600 label-block">Time Logs
                                            <span class="pull-right legends-common">
                                                <span class="time-label start-time-label"></span> Start Time &nbsp; <span class="time-label end-time-label"></span> End Time</span></label>
                                    <div class="clearfix"></div>
                                    <div class="table-responsive table-common-style table-view-task-detail">
                                        <table class="table font12">
                                            <tbody>
                                            <tr id="table_timming">
                                                {{--<th colspan="2">Total-time</th>
                                                <th class="text-right timming-total" width="30%"></th>--}}
                                            </tr>
                                            <tr>
                                                <td><span class="time-label start-time-label"></span> 12:00</td>
                                                <td><span class="time-label end-time-label"></span> 12:30</td>
                                                <td align="right">1 Hour</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                            <div>
                                <form class="board-form-common">
                                    <div class="form-group"><label
                                                class="font-600">Comments</label>
                                        <ul class="list-group comment-section">
                                        </ul>
                                    </div>
                                </form>
                                <form class="board-form-common">

                                    <a href="javascript:void(0);" class="btn-show-section activity_logs_show">Show Activity Logs</a>
                                    <div class="form-group activity-logs-section">
                                        <label class="font-600 label-block">Activity Logs
                                                <span class="pull-right">
                                                    <a href="javascript:void(0);" class="btn-link text-capitalize btn-hide-activity-logs">Hide</a>
                                                </span>
                                        </label>
                                        <div class="logs-task">
                                            <div class="task-logs">
                                                <div>
                                                    <span class="member-name">SuperAdmin</span><span class="pull-right time-stamp font10">2017-08-03 05:35:21</span>
                                                </div>
                                                <p class="logs-comment mb0">SuperAdmin has stop task the Icebox Task One</p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{--<div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Modal title</h4>
              </div>
              <div class="modal-body">

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
              </div>--}}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

