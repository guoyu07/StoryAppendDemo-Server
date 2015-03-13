!function ($) {
    'use strict';

    var HINavObject = [];

    function HINav(element) {
        this.$nav = $(element),
        this.$content = $('#' + this.$nav.data('bind'));

        this.init();
        this.switchControl();
        this.scrollControl();
        HINavObject.push(this);

    }

    HINav.prototype.init = function() {
        this.refreshToTop();
    }

    HINav.prototype.switchControl = function() {
        var self = this;
        $(document).on('click', '.hi-nav .nav-item', function() {
            var mate = $('#' + $(this).data('target'));
            var index = mate.index();
            $('html, body').animate({
                scrollTop: self.toTop[index] + 2
            }, 400);
        });
    }

    HINav.prototype.scrollControl = function() {
        var self = this;
        $(window).on('scroll', function() {
            var scrTop = $(document).scrollTop();
            var toTop = self.toTop;
            for (var i = 0; i < toTop.length; i++) {
                if ((i + 1) >= toTop.length) {
                    if (scrTop > toTop[i]) {
                        self.$nav.children('.nav-item').removeClass('active');
                        self.$nav.children('.nav-item').eq(i).addClass('active');
                        break;
                    }
                } 
                else if (scrTop >= toTop[i] && scrTop < toTop[i + 1]) { 
                    self.$nav.children('.nav-item').removeClass('active');
                    self.$nav.children('.nav-item').eq(i).addClass('active');
                    break;
                }
            }
        });
    }

    HINav.prototype.refreshToTop = function() {
        var toTop = [];
        var $nav = this.$nav,
            $content_item = this.$content.children('.content-item');
        $.each($content_item, function(i, val) {
            toTop.push($($content_item[i]).offset().top - $nav.height());
        });
        this.toTop = toTop;
    }


    window.HINav = HINav;
    $(function() {
//        var HINavObject = [];
//        var $hi_nav = $('.hi-nav');
//        if ($hi_nav) {
//            $.each($hi_nav, function(i, val) {
//                console.log($hi_nav[i]);
//                HINavObject.push(new HINav($hi_nav[i]));
//            });
//        }

        $('img').on('load', function() {
            if (HINavObject.length > 0) {
                for (var i = 0; i < HINavObject.length; i++) {
                    HINavObject[i].refreshToTop();
                };
            }
        });
    });

}(jQuery);






