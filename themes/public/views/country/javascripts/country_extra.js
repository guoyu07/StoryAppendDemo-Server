var countryExtraModel = avalon.define("countryExtraCtrl", function(vm) {
    vm.data = {};
    vm.local = {
        active_nav        : 0,
        current_cities    : [],
        has_secondary_nav : false
    };
    var carousel_objects = [];
    var nav_top = 0;
    var navs = [];

    vm.DataInitializer = {
        'getData'        : function() {
            var self = this;
            $.ajax({
                url      : $request_urls.countryTabs,
                dataType : "json",
                success  : function(res) {
                    if(res.code == 200) {
                        var data = countryExtraModel.DataInitializer.addSubTabFlag(res.data);
                        navs.length = data.tabs.length;
                        countryExtraModel.data = countryExtraModel.DataInitializer.addSubLevelTab(data);
                    } else {
                        alert(res.msg);
                    }
                }
            });
        },
        'addSubTabFlag'  : function(data) {
            for(var i = 0; i < data.tabs.length; i++) {
                for(var j = 0; j < data.tabs[i].groups.length; j++) {
                    if(data.tabs[i].groups[j].type == 5) {
                        data.tabs[i].groups[j].active_tab = 0;
                    }
                }
            }
            return data;
        },
        'addSubLevelTab' : function(data) {
            var tab_city_codes, tab_city_map = {};

            for(var one_tab, i = 0; i < data.tabs.length, one_tab = data.tabs[i]; i++) {
                tab_city_codes = [];
                for(var one_group, j = 0; j < one_tab.groups.length, one_group = one_tab.groups[j]; j++) {
                    if(one_group.city_code && tab_city_codes.indexOf(one_group.city_code) == -1) {
                        tab_city_codes.push(one_group.city_code);
                        tab_city_map[one_group.city_code] = {
                            city_code : one_group.city_code,
                            city_name : one_group.city_cn_name,
                            city_link : one_group.city_link_url
                        };
                    }
                }

                if(tab_city_codes.length > 1) {
                    data.tabs[i].cities = [];
                    for(var k = 0; k < tab_city_codes.length; k++) {
                        data.tabs[i].cities.push(tab_city_map[tab_city_codes[k]]);
                    }
                }
            }

            return data;
        }
    };

    vm.renderCallback = function(action) {
        $('.loading-mask').hide();
        PageInitializer.initCarousels();
        PageInitializer.ifNavFixed();
    };

    var PageInitializer = {
        'initCarousels' : function() {
            carousel_objects = [];
            var $sub_groups = $('.hi-carousel.group-wrap');
            for(var i = 0; i < $sub_groups.length; i++) {
                var id = $sub_groups.eq(i).attr('id');
                var $item = $('#' + id).find('.carousel-item');
                var item_length = $item.length;
                var wrap_length = 1020 / $item.width();
                if(item_length > wrap_length) {
                    carousel_objects.push(new HiCarousel({
                        dom  : '#' + id,
                        type : 'scroll',
                        time : 400
                    }));
                } else {
                    $('#' + id).find('.to').hide();
                }
            }
        },
        'ifNavFixed'    : function() {
            var $nav = $('.country-extra-nav');
            nav_top = $('.country-extra-tab-content').offset().top - $nav.height();
            $(document).on('scroll', function() {
                var scrTop = $(document).scrollTop();
                if(scrTop > nav_top) {
                    $nav.addClass('fixed');
                } else {
                    $nav.removeClass('fixed');
                }
            });
        }
    };

    vm.switchNavTab = function(index) {
        vm.local.active_nav = index;
        vm.local.current_cities = [];
        vm.local.has_secondary_nav = false;
        if(vm.data.tabs[index].cities) {
            vm.local.current_cities = vm.data.tabs[index].cities;
            vm.local.has_secondary_nav = true;
        }
        if(navs[index] != -1) {
            PageInitializer.initCarousels();
            navs[index] = -1;
        }
        $('html, body').scrollTop(nav_top);
    };
    vm.switchGroupTab = function(group, index) {
        group.active_tab = index;
    };
});

$(function() {
    countryExtraModel.DataInitializer.getData();
});