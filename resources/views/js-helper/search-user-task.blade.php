<script>
    $('#search').bind('keypress', function(e) {
        var search_value =  $('#search').val();
        if(e.keyCode==13 && search_value != '' ){

            var search_project_id = ($('#searchProjectId').val() !== undefined) ?  +$('#searchProjectId').val()  : '';

            var  search_board_id = ($('#searchProjectBoardId').val() !== undefined) ?  +$('#searchProjectBoardId').val() : '';

            if(search_value != '') {

                var url =   '{{ route('search-task') }}?project_id=' + search_project_id  +'&board_id='+ search_board_id +'&keyword=' + search_value;
                window.location = url;
            }
        }
    });

    $(function()
    {
        src = "{{ route('search') }}";
        search_project_id = $('#searchProjectId').val();
        search_board_id = $('#searchProjectBoardId').val();
        $("#search").autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {term : request.term, search_project_id : search_project_id, search_board_id : search_board_id},
                    statusCode: {
                        500: function (response) {
                            window.location = "{{route('error-page-500')}}"

                        },
                        404: function (response) {
                            window.location = "{{route('error-page-404')}}"
                        }
                    },
                    success: function(data) {
                        if(data.board_id != ''){
                            response(data);

                        }
                        else{
                            var result = [
                                {
                                    label: 'No matches found',
                                    board_id: ''
                                }
                            ];
                            response(result);
                            $('#autoCompleteSearchBox').find('.ui-menu-item').css('pointer-events','none');

                        }
                    }
                });
            },
            autoFocus: false,
            appendTo: "#autoCompleteSearchBox",
            minLength: 2,
            select: function(request, response) {
                $.each(response,function(ind,val){

                    if (val.board_id > 0) {
                        window.location = '{{ route('search-task') }}?project_id='+val.project_id+'&board_id='+val.board_id+'&keyword='+val.value;
                    } else {
                        setTimeout(function() {
                                    $('body').find('#search').val('')
                                }, 100
                        );
                    }
                });
            }
        });
    });


    $('#searchButton').click(function(){
        var search_value =  $('#search').val();
        var search_project_id = ($('#searchProjectId').val() !== undefined) ? $('#searchProjectId').val() : '';

        var  search_board_id = ($('#searchProjectBoardId').val() !== undefined) ? $('#searchProjectBoardId').val() : '';

        if(search_value != '') {

            var url =   '{{ route('search-task') }}?project_id=' + search_project_id  +'&board_id='+ search_board_id +'&keyword=' + search_value;

            window.location = url;
        }
    });
</script>