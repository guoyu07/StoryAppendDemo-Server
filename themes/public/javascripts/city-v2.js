/**
 * Created by buuyii on 14-8-10.
 */

var dataFactory = new HitourDataFactory($request_urls.getProducts, function (data) {
    var groups = data.product_groups;
    for (var i = 0; i < groups.length; i++) {
        if (groups[i].products == "") {
            groups.splice(i, 1);
            i--;
        }
    }
    if (data.city_image == null) {
        data.city_image = {};
    }
    data.product_groups.sort(function (a, b) {
        if (a.type > b.type) {
            return 1;
        } else if (a.type == b.type) {
            return 0;
        } else {
            return -1;
        }
    });
    return data;
});

var cityInfo = new ViewModel('cityInfo', 'en_name, cn_name,country_name, country_url, city_image|{}');
var cityRecommend = new ViewModel('cityRecommend', 'rec_item|{}');
var cityCategory = new ViewModel('cityCategory', 'groups|[]');

cityInfo.bindData(dataFactory.getData(new DataAdapter({
    country_name: 'country.cn_name',
    country_url: 'country.link_url'
})));

cityRecommend.bindData(dataFactory.getData(new DataAdapter({
    rec_item: function (val, src) {
        var groups = src.product_groups;
        if (groups.length > 1 && groups[1].type == 2) {
            var item = src.product_groups[1];
            return item;
        } else {
            return groups[0];
        }
    }
})));

cityCategory.bindData(dataFactory.getData(new DataAdapter({
        groups: function (val, src) {
            var gps = src.product_groups;
            if (gps.length > 2) {
                var the_gps = [];
                for (var i = 0; i < gps.length; i++) {
                    if (gps[i].type != 1 && gps[i].type != 2) {
                        the_gps.push(gps[i]);
                    }
                }
                return the_gps;
            } else {
                return gps.slice(0, 1);
            }
        }
    }))).then(function (data) {
        $(function () {
            $(document.body).scrollTop = 0;
            $('.loading-mask').hide();
        });
    });

// 页面交互 ------------------------------------------------
$(function () {
    // toggle tab_bar
    $(document).on('scroll', function () {
        var sTop = $(window).scrollTop(),
            $cates = $('.city-category'),
            $tab_bar = $('.tab_bar-fixed');
        if (sTop > $cates.eq(0).offset().top - 58) {
            $('.tab_bar-fixed').css('top', '0px');
        } else {
            $('.tab_bar-fixed').css('top', '-60px');
        }
        if (sTop < $cates.eq(0).offset().top + $cates.eq(0).height()) {
            var $active_cate = $('.city-category.active');
            var $active_tab = $tab_bar.find('.active');
            $active_cate.removeClass('active');
            $active_tab.removeClass('active');
            $cates.eq(0).addClass('active');
            $tab_bar.find('li').eq(0).addClass('active');
        } else if (sTop >= $('.city-category.active').offset().top + $('.city-category.active').height()) {
            var $active_cate = $('.city-category.active');
            var $active_tab = $tab_bar.find('.active');
            $active_cate.removeClass('active').next().addClass('active');
            $active_tab.removeClass('active').next().addClass('active');
        } else if (sTop < $('.city-category.active').offset().top - 55) {
            var $active_cate = $('.city-category.active');
            var $active_tab = $tab_bar.find('.active');
            $active_cate.removeClass('active').prev().addClass('active');
            $active_tab.removeClass('active').prev().addClass('active');
        }
    });
    $(document).on('click', '.tab_bar-fixed li', function () {
        var i = $(this).index(),
            y = $('.city-category').eq(i).offset().top - 55;
        $('body,html').animate({ scrollTop: y }, 500);
    });

// recommend slider
    $(document).on('click', '.city-recommend .to_right', function () {
        var slider_ctn = $('.rec-slider-ctn').children();
        slider_ctn.eq(0).css('left', '-540px');
        slider_ctn.eq(1).css('left', '0px');
        slider_ctn.eq(2).css('left', '540px');
        setTimeout(function () {
            slider_ctn.eq(0).css('left', '1070px');
            $('.rec-slider-ctn').append(slider_ctn.eq(0));
        }, 300);
    });
    $(document).on('click', '.city-recommend .to_left', function () {
        var last_item = $('.rec-slider-ctn .rec-item:last-child');
        last_item.css('left', '-540px');
        $('.rec-slider-ctn').prepend(last_item);
        var slider_ctn = $('.rec-slider-ctn').children();
        setTimeout(function () {
            slider_ctn.eq(0).css('left', '0px');
            slider_ctn.eq(1).css('left', '540px');
            slider_ctn.eq(2).css('left', '1070px');
        }, 1);
    });


    var h = 1000,
        $backTop = $('.back-top').on('click', function () {
            $('body,html').animate({scrollTop: 0}, 500);
        });
    $(document).on('scroll', function () {
        var st = document.body.scrollTop || document.documentElement.scrollTop;
        if (st >= h) {
            $backTop.addClass('display');
        }
        else {
            $backTop.removeClass('display');
        }
    });
});
