var hotelPlusModel = avalon.define("hotelPlusCtrl", function(vm) {
    vm.data = {};
    vm.local = {
        header : {},
        current_label:''
    };

    vm.DataInitializer = {
        'getData' : function() {
            $.ajax({
                url      : $request_urls.getHotelplusDetail,
                dataType : "json",
                success  : function(data) {
                    if(data.code == 200) {
                        hotelPlusModel.data = data.data;
                        PageInitializer.initHeader(data.data);
                    } else {
                        alert(res.msg);
                    }
                }
            });
        }
    };

    vm.calculatePercent = function(number, cardinal) {
        number = (number == 0 ? cardinal : number);
        return (parseFloat(number, 10) / cardinal).toFixed(2) * 75 - 1;
    };//calculate the star length percent

    vm.renderCallback = function() {
        $('.loading-mask').hide();

        // 滚动监听
        var $nav = $('.hotel-tabs');
        var $nav_scrollTop = $nav.offset().top;
        var $nav_height = $nav.css('height').split('px')[0];
        $(window).on('scroll', function() {
            // 导航栏 fixed判断
            PageInitializer.toggleNavFixed($nav, $nav_scrollTop, $nav_height);
        });

        // init TAB 组件
        var $hi_nav = $('.hi-nav');
        if($hi_nav) {
            $.each($hi_nav, function(i, val) {
                new HINav($hi_nav[i]);
            });
        }
    };

    var PageInitializer = {
        initHeader : function(data) {
            hotelPlusModel.local.header = {
                title     : data.hotel_plus.title,
                en_title  : '',
                image_url : data.hotel_plus.image
            };
            hotelPlusModel.local.current_label = data.hotel_plus.title;
        },
        'toggleNavFixed' : function($nav, $nav_scrollTop, $nav_height) {
            var scrollTop = $(document).scrollTop();
            if(scrollTop >= $nav_scrollTop) {
                $nav.addClass('fixed');
                $('.our-highlights').css('margin-bottom',$nav_height+'px');
            } else {
                $nav.removeClass('fixed');
                $('.our-highlights').css('margin-bottom','0px');
            }
        }
    };
});

$(function() {
    hotelPlusModel.DataInitializer.getData();
})