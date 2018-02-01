<script>
    function getAllBoards(projectId){
        chkClas = $('#projectBoards'+projectId).hasClass('dataIn');

        if(chkClas == false) {
            $.ajax({
                url: '{{route('getProjectBoards')}}',
                type: 'POST',
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                data: {_token: '{{csrf_token()}}', 'projectId': projectId},
                success: function (response) {
                    var myhtml = response;
                    $('#projectBoards' + projectId).html(myhtml);
                    $('#projectBoards' + projectId).addClass('dataIn');
                },
            });
        }
    }

    function getAllBoardsAndTaskProjectLabelId(){

        var project_name =    $(this).attr('prjectNameAttr');
        alert(project_name);

            $.ajax({
                url: '{{route('getAllBoardsAndTaskProjectLabel')}}',
                type: 'POST',
                data: {_token: '{{csrf_token()}}', 'projectId': projectId},
                statusCode: {
                    500: function (response) {
                        window.location = "{{route('error-page-500')}}"

                    },
                    404: function (response) {
                        window.location = "{{route('error-page-404')}}"
                    }
                },
                success: function (response) {
                    var myhtml = response;
                    $('#projectBoards' + projectId).html(myhtml);
                    $('#projectBoards' + projectId).addClass('dataIn');
                },
            });


    }

    $(function() {

        $('body').on('click','.rename-board', function() {
            var currentParent = $(this).closest('.sub-board-list-items');
            var labelValue = currentParent.find('.board-label').text();
            currentParent.find('#txtBox').removeClass('hide').show(); //show textbox
            currentParent.find('#txtBox input').val(labelValue);
            currentParent.find('.btn-submit-rename').removeClass('hide')
            currentParent.find('.btn-cancel-rename').removeClass('hide')
            $(this).addClass('hide')
            currentParent.find('.board-label').addClass('hide')
        });

        $('body').on('focus','#txtBox input', function() {
            var currentParent = $(this).closest('.sub-board-list-items');
            // $(this).val('');
            //currentParent.find('.btn-submit-rename').attr('disabled','disabled');
        })
        $('body').on('input','#txtBox input', function() {
            var currentParent = $(this).closest('.sub-board-list-items');
            if($(this).val() == ''){
                currentParent.find('.btn-submit-rename').attr('disabled','disabled').css('pointer-events','none');
            }
            if($(this).val() != '') {
                currentParent.find('.btn-submit-rename').removeAttr('disabled').css('pointer-events','initial');
            }
        })

        $('body').on('click', '.btn-cancel-rename', function(){
            var currentParent = $(this).closest('.sub-board-list-items');
            $(this).addClass('hide')
            currentParent.find('#txtBox').addClass('hide').hide(); //show textbox
            currentParent.find('.btn-submit-rename').addClass('hide')
            currentParent.find('.rename-board').removeClass('hide')
            currentParent.find('.board-label').removeClass('hide')
        })

        $('body').on('click','.btn-submit-rename', function() {
            var currentParent = $(this).closest('.sub-board-list-items');
            var board_title = currentParent.find('#txtBox input').val();
            currentParent.find('#txtBox').addClass('hide').hide(); //show textbox
            currentParent.find('.rename-board').removeClass('hide')
            var board_id = $(this).attr('id');
            $(this).addClass('hide')
            currentParent.find('.board-label').removeClass('hide')
            if(board_title != '') {
                $.ajax({
                    url: "{{route('update-board-title')}}",
                    data: {
                        _token: '{{csrf_token()}}',
                        board_id: board_id,
                        board_name: board_title
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
                        $('#boardNameId_' + data.id).text(data.result);
                        toastr.success('Board Title has been updated');
                        currentParent.find('.btn-cancel-rename').addClass('hide')
                    }
                });
            }
        });

    });
</script>
