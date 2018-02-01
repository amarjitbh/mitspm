$(function(){
    /*Menu-toggle*/
    $("#menu-toggle1, #menu-toggle2").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
    });



/*Navbar Height*/

    function navbarHeightSet(){
        var navbarHeight = $('.top-navbar > .navbar-custom').innerHeight();
        $('#wrapper').css('padding-top',navbarHeight);
    }
    navbarHeightSet();
    $(window).resize(function(){
        navbarHeightSet();
    });

    /*Sortable*/
    $( function() {
        $( "ul.droptrue" ).sortable({
            connectWith: "ul"
        });



        function panelHeight(){
            //var panelHeight = $('.panel-main').innerHeight();
            var panelHeading = $('.panel-main .panel-heading').innerHeight();
            $('.panel-main > .panel-body').css({ 'height': 'calc(100% - ' + panelHeading+ 'px)' });
        }
        panelHeight();
        $(window).resize(function(){
            panelHeight();
        });
    });

    (function($) {
        var formInput = $(".input");

        function checkEmpty(checkInput) {
            if(checkInput.val()) {
                checkInput.addClass("has-text");
            } else {
                checkInput.removeClass("has-text");
            }
        }

        formInput.each(function(){
            checkEmpty($(this));
        });

        formInput.change(function(){
            checkEmpty($(this));
        });
    })(jQuery);

    /*https://silviomoreto.github.io/bootstrap-select/options/*/
    $('.selectpicker').selectpicker({
        iconBase: 'fa'

    });

});


