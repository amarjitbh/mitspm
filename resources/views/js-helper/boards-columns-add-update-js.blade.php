<script>
function editBoardColumn(columnId) {
            //alert(columnId);
            $('#panel-setting-' + columnId).hide();
            $('#panel-span-' + columnId).hide();
            $('#panel-done-' + columnId).show();
            $('#panel-input-' + columnId).show();
        }

        function updateBoardColumns(columnId) {

            var inputVal = $('#imp-val-' + columnId).val();
            if (inputVal == '') {
                $('#imp-val-' + columnId).css('border', '1px solid red');
                $('#imp-val-' + columnId).focus(function () {
                    $('#imp-val-' + columnId).css('border', '');
                });
                return false;
            }
            var valId = columnId;
            //alert(inputVal);return false;
            $.ajax({

                url: '{{route('updateBoardColumn')}}',
                type: 'POST',
                data: {_token: '{{csrf_token()}}', 'columnName': inputVal, 'columnId': valId},
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success: function (response) {

                    if (response == 100) {
                        $('#imp-val-' + columnId).val(inputVal);
                        $('#panel-span-' + columnId).text(inputVal);
                        $('#panel-setting-' + columnId).show();
                        $('#panel-span-' + columnId).show();
                        $('#panel-done-' + columnId).hide();
                        $('#panel-input-' + columnId).hide();
                        var newHtml= inputVal;
                        var colname= $('#'+columnId).html(newHtml);
                    }
                },
                error: function (fail) {
                    $.each(fail, function (ind, val) {

                        if (val.project_board_name) {
                            alert(val.project_board_name);
                        }
                    });
                },
            });
        }

        function cancleBoardColumns(columnId) {

            $('#panel-setting-' + columnId).show();
            $('#panel-span-' + columnId).show();
            $('#panel-done-' + columnId).hide();
            $('#panel-input-' + columnId).hide();
        }

        function removeBoardColumn(columnId) {

            if (confirm('Are you sure you want to delete this column ?')) {

                $.ajax({

                    url: '{{route('checkColumnData')}}',
                    type: 'POST',
                    data: {_token: '{{csrf_token()}}', 'columnId': columnId},
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function (responce) {

                        if (responce == 0) {
                            toastr.success('Column deleted successfully');
                            $('#boardColumnBox' + columnId).fadeOut('slow');
                            $('#'+columnId).closest('li').remove();
                        } else if (responce == 10) {

                            $('#columnNotRemove').modal();
                        } else {

                            $('#columnNames').html(responce);
                            $('#removeColumns').modal();
                            console.log(responce);
                        }
                    },
                })
            }
        }
        /*** addVariable is a global variable  Please don't remove it ***/
        var addVariable = 0;
        function AddNewColumn() {
            addVariable++;
            if (addVariable == 1) {
                var newHtml = '<div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass" id="newColumnId' + addVariable + '">\
                            <div class="panel panel-default panel-main h-100p mb0">\
                                <div id="panel-heading-30" class="panel-heading">\
                                    <h3 class="panel-title font13" style="color: black;">\
                                        <input type="text" id="newColumnName' + addVariable + '">\
                                        <button onclick="newColumnAdd(' + addVariable + ')">add</button>\
                                        <button onclick="newColumnRemove(' + addVariable + ')"><i class="fa fa-times-circle" aria-hidden="true"></i></button>\
                                    </h3>\
                                </div>\
                                <div class="panel-body mb0 p0">\
                                </div>\
                            </div>\
                        </div>';
                scrollWin();
                $('#boardRowDiv').append(newHtml);
            }
        }

        function scrollWin() {

            var width = $(window).width();
            //var width = $(window).outerWidth();
            var width = $('body').outerWidth();
            width = width+width;
            $(".h-scrollable").animate({scrollLeft: '+=' + width}, 'slow', 'linear');
        }

        function newColumnAdd(value) {

            var addColumnName = $('#newColumnName' + value).val();
            var boardID = $('#project_board_id').val();
            if (addColumnName != '') {
                //alert(addColumnName);return false;
                $.ajax({

                    url: '{{route('addNewColumn')}}',
                    type: 'POST',
                    data: {_token: '{{csrf_token()}}', 'boardId': boardID, 'columnName': addColumnName},
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function (responce) {
                        if(responce.success == true) {

                            toastr.success('New column added successfully');
                            $('#newColumnId' + value).html('');
                            $('#newColumnId' + value).after(responce.html);
                            $('#newColumnId' + value).remove();
                            setTimeout(function () {
                                var newColumnAttrClasId = responce.last_id;
                                var newColumnAttrClasName = addColumnName;
                                var newColumnNameHtml = '<li><a id="' + newColumnAttrClasId + '" href="#anch3" data-scroll class="showHideColumn font12 active-column-select" data-column-name="' + newColumnAttrClasName + '" data-column-id="' + newColumnAttrClasId + '" data-status="show" onclick="showHideColumn(' + newColumnAttrClasId + ')">' + newColumnAttrClasName + '</a></li>';
                                //alert(newColumnNameHtml);
                                //$('.columnNameList').append(newColumnNameHtml);
                                $('#sub1').append(newColumnNameHtml);
                                var newOpetions = '<option value="' + newColumnAttrClasId + '">' + newColumnAttrClasName + '</option>';
                                addVariable = 0;
                                $('#columns').append(newOpetions);
                                setTimeout(function () {
                                    $('body').find(".sortable").sortable();

                                }, 3000)

                                setTimeout(function () {
                                    location.reload();
                                }, 3000)

                            }, 100);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    },
                });
            }
        }

        function newColumnRemove(id) {
            $('#newColumnId' + id).remove();
            addVariable = 0;
        }
</script>