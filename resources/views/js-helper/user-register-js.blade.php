<script>

    function getCountriesTimeZone(){

        var countryId = $('#countries').val();

        $.ajax({

            url  : '{{route("getTimeZone")}}',
            type : 'POST',
            data : {_token: '{{csrf_token()}}','country_id': countryId},
            statusCode: {
                500: function (response) {
                    window.location = "{{route('error-page-500')}}"

                },
                404: function (response) {
                    window.location = "{{route('error-page-404')}}"
                }
            },
            success : function(response){
                if(response != 0){

                    $('#timezone').html(response);
                    //console.log(response);
                }
            }
        })
    }
</script>