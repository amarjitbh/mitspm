$(function(){
    /*Menu-toggle*/
    $("#menu-toggle1, #menu-toggle2").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
    });



/*Navbar Height*/

    function navbarHeightSet(){
        var navbarHeight = $('.top-navbar > .navbar-custom').innerHeight();
        var sidebarWidth = $('#sidebar-wrapper').innerWidth();
        var expandedViewHeader = $('.expanded-view .tile').innerHeight();
        console.log(sidebarWidth);
        $('#wrapper').css('padding-top',navbarHeight);
        $('.dashboard-header').css('padding-top',navbarHeight);
        $('.expanded-view').css({
            'top':navbarHeight ,
            'left':sidebarWidth ,
            'width': 'calc(100% - ' + sidebarWidth+ 'px)'
        });
        $('.expanded-view .content-section .board-panel-common').css({
            'height': 'calc(100vh - ' + (navbarHeight+expandedViewHeader)+ 'px)'
        });

        console.log(sidebarWidth)
    }
    navbarHeightSet();
    $(window).resize(function(){
        navbarHeightSet();
    });

    /*Sortable*/

    $(".sortable").sortable({
        connectWith: ".drop, .draggable",
        revert:'invalid'
    });

    $('.selectpicker').selectpicker({
        iconBase: 'fa'
    });


    function loadTooltip() {

        $('[data-tooltip="true"]').tooltip({
            trigger: 'hover'
        });
    }
    loadTooltip()

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

    formInput.change(function(){
        checkEmpty($(this));
    });

    /*Dropmenu Effect with Animate.css*/
    /*data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" add as data attribute to element*/
    var dropdownSelectors = $('.dropdown, .dropup');

    // Custom function to read dropdown data
    // =========================
    function dropdownEffectData(target) {
        // @todo - page level global?
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





});

function addtxtClass(){
    var textTarget = $('.board-panel-body-common textarea');
    setTimeout(function(){
        autosize(textTarget);
    },100);
}
addtxtClass();
NProgress.configure({ showSpinner: false });
NProgress.start();

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
toastr.success('Successfully submitted');