<script>

    $(function () {

        $('body').append('<button type="button" class="close attach-btn hide">&times;</button>');

        $('body').on('click', '.attach-btn', function() {
            $('#modalID').modal('hide');
            $('.attach-btn').addClass('hide');
            return false;
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
</script>