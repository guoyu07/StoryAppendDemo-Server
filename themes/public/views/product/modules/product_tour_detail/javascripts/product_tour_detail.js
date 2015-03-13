var productTourDetailModel = avalon.define("productTourDetailCtrl", function(vm) {
    vm.data = {};
    vm.local = {};

    vm.DataInitializer = {
        'setData' : function(tour_plan, description, multi_day_general) {
            var data = {
                tour_plan         : tour_plan,
                description       : description,
                multi_day_general : multi_day_general
            }
            productTourDetailModel.data = data;
        }
    }

    vm.renderCallback = function() {
        new HiModal({
            dom     : 'product_tour_detail',
            type    : 'side',
            bg_type : 'white'
        });

        var toTop = [];
        var $nav = $('.day-nav');
        refreshToTop();

        function refreshToTop() {
            toTop = [];
            $content_item = $('#tour-content').children('.day');
            $.each($content_item, function(i, val) {
                toTop.push($($content_item[i]).offset().top - $(document).scrollTop() +
                           $('.tour-detail .content-col').scrollTop());
            });
        }

        $(document).on('click', '.day-nav .day-item', function() {
            var mate = $('#' + $(this).data('target'));
            var index = mate.index();
            $('.tour-detail .content-col').animate({
                scrollTop : toTop[index] + 2
            }, 600);
        });

        $(document).on('click', '.tour-outline .day-row', function() {
            var index = $(this).index();
            $('.tour-detail .content-col').animate({
                scrollTop : toTop[index] + 2
            }, 0);
        });

        $('.tour-detail .content-col').on('scroll', function() {
            var scrTop = $('.tour-detail .content-col').scrollTop();
            for(var i = 0; i < toTop.length; i++) {
                if((i + 1) >= toTop.length) {
                    if(scrTop > toTop[i]) {
                        $nav.children('.day-item').removeClass('active');
                        $nav.children('.day-item').eq(i).addClass('active');
                        break;
                    }
                }
                else if(scrTop >= toTop[i] && scrTop < toTop[i + 1]) {
                    $nav.children('.day-item').removeClass('active');
                    $nav.children('.day-item').eq(i).addClass('active');
                    break;
                }
            }
        });

        $('img').on('load', function() {
            refreshToTop();
        });
    };

    var PageInitializer = {

    };
});