$(function(){
    /*Menu-toggle*/
    $("#menu-toggle1, #menu-toggle2").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
    });

    /*Scroll Spy*/
//$('body').scrollspy({ target: '#spy', offset:80});

    /*Smooth link animation*/
    /*
     $('a[href*=\\#]:not([href=\\#])').click(function() {
     if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {

     var target = $(this.hash);
     target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
     if (target.length) {
     $('html,body').animate({
     scrollTop: target.offset().top
     }, 1000);
     return false;
     }
     }
     });*/


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
       /* $( "ul.droptrue" ).sortable({
            connectWith: "ul"
        });

        $( "ul.dropfalse" ).sortable({
            connectWith: "ul",
            dropOnEmpty: false
        });*/

       // $( "#sortable1, #sortable2" ).disableSelection();
   /*     $(function () {
            $("#sortable").sortable({
                update: function (event, ui) {
                    var order = $(this).sortable('serialize');
                    alert(order);
                    $(document).on("click", "button", function () { //that doesn't work
                        $.ajax({
                            data: order,
                            type: 'POST',
                            url: 'saverank.php'
                        });
                    });
                }
            }).disableSelection();
            $('button').on('click', function () {
                var r = $("#sortable").sortable("toArray");
                var a = $("#sortable").sortable("serialize", {
                    attribute: "id"
                });
                console.log(r, a);
            });
        });*/
        function panelHeight(){
            //var panelHeight = $('.panel-main').innerHeight();
            var panelHeading = $('.panel-main .panel-heading').innerHeight();
            $('.panel-body').css({ 'height': 'calc(100% - ' + panelHeading+ 'px)' });
        }
        panelHeight();
        $(window).resize(function(){
            panelHeight();
        });
    });

});


