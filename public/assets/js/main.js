$(function(){
    /*Menu-toggle*/
    $("#menu-toggle1, #menu-toggle2").off();
    $("#menu-toggle1, #menu-toggle2").on('click', function() {
        /*alert('ok')*/
        $("#wrapper").toggleClass("active");
    });

    $(".pagination").rPage();


    /*Navbar Height*/

    var dashboardHeader = $('.dashboard-header');

    function navbarHeightSet(){

        var dashboardHeaderHeight = dashboardHeader.innerHeight();
        var navbarHeight = $('.top-navbar > .navbar-custom').innerHeight();
        var dashboardHeight = dashboardHeader.innerHeight();
        var sidebarWidth = $('#sidebar-wrapper').innerWidth();
        var expandedViewHeader = $('.expanded-view .tile').innerHeight();
        //  console.log(sidebarWidth);
        $('#wrapper').css('padding-top',navbarHeight);
        dashboardHeader.css('padding-top',navbarHeight);

        if(sidebarWidth) {
            $('.expanded-view').css({
                'top':navbarHeight ,
                'left':sidebarWidth ,
                'width': 'calc(100% - ' + sidebarWidth+ 'px)'
            });
        }
        else {
            $('.expanded-view').css({
                'top':navbarHeight ,
                'width': '100%'
            });
        }

        $('.expanded-view .content-section .board-panel-common').css({
            'height': 'calc(100vh - ' + (navbarHeight+expandedViewHeader)+ 'px)'
        });

        $('.jp-custom-slide-modal').css({
            'top': (navbarHeight+dashboardHeight)+ 'px'
        });

        $('.jp-custom-slide-modal .modal-content').css({
            'height': 'calc(100% - ' + (navbarHeight+dashboardHeight)+ 'px)',
            'overflow': 'auto'
        });
        $('.jp-custom-modal-slide-left').css({
            'top': (navbarHeight)+ 'px'
        });

        $('.jp-custom-modal-slide-left .modal-content').css({
            'height': 'calc(100% - ' + (navbarHeight)+ 'px)',
            'overflow': 'auto'
        });

        if(dashboardHeader.length > 0) {
            $('body').css('padding-top', dashboardHeaderHeight + navbarHeight + 15 +'px');
        }


        //   console.log(sidebarWidth)
    }
    navbarHeightSet();
    $(window).resize(function(){
        navbarHeightSet();
    });


        $(window).scroll(function() {
            if($(window).scrollTop() > 10) {
                if(dashboardHeader.length > 0) {
                    dashboardHeader.addClass('add-box-shadow')
                }
            }
            else {
                dashboardHeader.removeClass('add-box-shadow')
            }
        })


    var jpModalSlide = $('.jp-custom-slide-modal');
    var jpModalImage = $('#modalID');
    var jpModalSlideLeft = $('.jp-custom-modal-slide-left');


    /*Jp custom-slide*/
    jpModalSlide.on('show.bs.modal', function (e) {
        $('body').addClass('jp-custom-slide');
    });
    jpModalSlide.on('shown.bs.modal', function (e) {
        $('.modal-backdrop').removeClass('fade, in')
    });
    jpModalSlide.on('hide.bs.modal', function (e) {
        $('body').removeClass('jp-custom-slide');
    });

    $("body").on('click', function(e) {

        $(this).find('.modal-backdrop').remove();
        jpModalSlide.modal('hide');
        jpModalSlideLeft.modal('hide');

    });

    jpModalImage.on('click', function() {
        return false
    });

    jpModalSlide.on('click', function() {
        //e.stopPropagation();
        return false

    });
    /*Jp custom slide end*/

    /*jpModalSlideLeft*/
    jpModalSlideLeft.on('shown.bs.modal', function (e) {
        $('.modal-backdrop').removeClass('fade, in')
    });
    jpModalSlideLeft.on('show.bs.modal', function (e) {
        $('body').addClass('jp-custom-slide1');
    });

    jpModalSlideLeft.on('hide.bs.modal', function (e) {
        $('body').removeClass('jp-custom-slide1');
    });

    $('#createTask').on('click', function(e) {
        e.stopPropagation();
    });

    jpModalSlideLeft.on('click', function(e) {
        e.stopPropagation();
    });
    /*jpModalSlideLeft End*/



    /*Sortable*/
    /* $( function() {
     $( "ul.droptrue" ).sortable({
     connectWith: "ul"
     });
     */
    $('.selectpicker').selectpicker({
        iconBase: 'fa'
    });


    function loadTooltip() {

        $('[data-tooltip="true"]').tooltip({
            trigger: 'hover'
        });
    }

    function panelHeight(){
        //var panelHeight = $('.panel-main').innerHeight();
        var panelHeading = $('.panel-main .panel-heading').innerHeight();
        $('.panel-main > .panel-body').css({ 'height': 'calc(100% - ' + panelHeading+ 'px)' });
    }
    panelHeight();
    $(window).resize(function(){
        panelHeight();
    });


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
    $(window).on('load', function() {
        setTimeout(function() {
            formInput.each(function(){
                checkEmpty($(this));
            });
        },300)

    })

    formInput.change(function(){
        checkEmpty($(this));
    });

    /*Dropmenu Effect with Animate.css*/
    /*data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" add as data attribute to element*/
    var dropdownSelectors = $('.dropdown, .dropup');

    // Custom function to read dropdown data
    // =========================
    function dropdownEffectData(target) {

        var effectInDefault = null,
            effectOutDefault = null;
        var dropdown = $(target),
            dropdownMenu = $('.dropdown-menu', target);
        var parentUl = dropdown.parents('ul.nav');

        // If parent is ul.nav allow global effect settings
        if (parentUl.length > 0) {
            effectInDefault = parentUl.data('dropdown-in') || null;
            effectOutDefault = parentUl.data('dropdown-out') || null;
        }

        return {
            target:       target,
            dropdown:     dropdown,
            dropdownMenu: dropdownMenu,
            effectIn:     dropdownMenu.data('dropdown-in') || effectInDefault,
            effectOut:    dropdownMenu.data('dropdown-out') || effectOutDefault,
        };
    }

    // Custom function to start effect (in or out)
    // =========================
    function dropdownEffectStart(data, effectToStart) {
        if (effectToStart) {
            data.dropdown.addClass('dropdown-animating');
            data.dropdownMenu.addClass('animated');
            data.dropdownMenu.addClass(effectToStart);
        }
    }

    // Custom function to read when animation is over
    // =========================
    function dropdownEffectEnd(data, callbackFunc) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        data.dropdown.one(animationEnd, function() {
            data.dropdown.removeClass('dropdown-animating');
            data.dropdownMenu.removeClass('animated');
            data.dropdownMenu.removeClass(data.effectIn);
            data.dropdownMenu.removeClass(data.effectOut);

            // Custom callback option, used to remove open class in out effect
            if(typeof callbackFunc == 'function'){
                callbackFunc();
            }
        });
    }

    // Bootstrap API hooks
    // =========================
    dropdownSelectors.on({
        "show.bs.dropdown": function () {
            // On show, start in effect
            var dropdown = dropdownEffectData(this);
            dropdownEffectStart(dropdown, dropdown.effectIn);
        },
        "shown.bs.dropdown": function () {
            // On shown, remove in effect once complete
            var dropdown = dropdownEffectData(this);
            if (dropdown.effectIn && dropdown.effectOut) {
                dropdownEffectEnd(dropdown, function() {});
            }
        },
        "hide.bs.dropdown":  function(e) {
            // On hide, start out effect
            var dropdown = dropdownEffectData(this);
            if (dropdown.effectOut) {
                e.preventDefault();
                dropdownEffectStart(dropdown, dropdown.effectOut);
                dropdownEffectEnd(dropdown, function() {
                    dropdown.dropdown.removeClass('open');
                });
            }
        }
    });



    $('.boardColumnDropMenu, .subMenuList, .dropdown-menu').perfectScrollbar();
    $('.panel-main .panel-body ul.list-group').perfectScrollbar();

    NProgress.configure({ showSpinner: false });
//NProgress.start();

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "showEasing": "swing",
        "hideEasing": "swing",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
//toastr.success('You are Done');


});
function addtxtClass(){
    var textTarget = $('.board-panel-body-common textarea ,.modal-common-style textarea ');
    setTimeout(function(){
        autosize(textTarget);
    },100);
}
$('.modal-common-style').on('shown.bs.modal', function () {
    addtxtClass()
});
/*$('.subMenuList').niceScroll({
 cursorcolor: "rgba(255,255,255,0.4)",
 cursorwidth: "6px",
 background: 'rgba(0, 0, 0, 0.6)',
 cursorborderradius: "0",
 cursorborder: "1px solid rgba(255,255,255,0.4)"
 })*/
/*$('.h-scrollable').niceScroll({
 cursorcolor: "rgba(255,255,255,0.4)",
 cursorwidth: "6px",
 background: 'rgba(0, 0, 0, 0.6)',
 cursorborderradius: "0",
 cursorborder: "1px solid rgba(255,255,255,0.4)",
 autohidemode: 'false'
 })*/

/*$('.navbar-nav .dropdown-menu').niceScroll({
 cursorcolor: "rgba(0,0,0,0.15)",
 cursorwidth: "6px",
 background: 'rgba(0,0,0,0.08)',
 cursorborderradius: "0",
 cursorborder: "1px solid rgba(0,0,0,0.15)"
 })*/





