var cityModel = avalon.define("cityCtrl", function(vm) {
    vm.data = {};
    vm.local = {
        voffset              : 0,
        v2_tree              : [],
        header               : {},
        current_label        : '',
        current_v2_id        : '',
        current_tag_id       : '',
        current_group_id     : '',
        product_length_limit : {
            page            : 0,
            limit_mode      : 'cate',
            left_toload     : 0,
            top10_limit     : 0,
            limit_length    : 15,
            load_step_range : 10
        },
        is_show_more         : {
            'top_10'  : false,
            'package' : false
        },
        has_more_products    : {
            'top_10'  : false,
            'package' : false
        },
        search_key           : '',
        search_key_cache     : '',
        is_search_result     : false,
        search_result_length : 0
    };
    vm.is_favorite = '';

    // 页面渲染完成时回调
    vm.renderCallback = function() {
        // 关闭loading遮罩
        setTimeout(function() {
            $('.loading-mask, .loading-indicator').hide();
        }, 50);
        // 判断是否上一页是否在同一个城市中, 若是 则滚动到合适高度
        setScrollTop();
        // 滚动监听
        listenScroll();
        // 展开激活的tag树
        $('.cate .active').next().show();
    };

    // 判断是否上一页是否在同一个城市中, 若是 则滚动到合适高度
    var setScrollTop = function() {
        var cookie_city = document.cookie.replace(/(?:(?:^|.*;\s*)hi_city\s*\=\s*([^;]*).*$)|^.*$/, "$1");
        if(cookie_city == vm.data.city_code) {
            document.body.scrollTop = vm.local.voffset;
        }
        document.cookie = 'hi_city=' + vm.data.city_code + ';expires=Thu, 01-Jan-70 00:00:01 GMT'
    };

    // 滚动监听
    var listenScroll = function() {
        window.onscroll = function() {
            if(vm.local.product_length_limit.left_toload > 0) {
                var $city_content = $('.city-content');
                var scroll_top = $(window).scrollTop();
                var refer_top = $city_content.offset().top + $city_content.height() - $(window).height() - 200;
                if(scroll_top > refer_top) {
                    vm.local.product_length_limit.limit_length += vm.local.product_length_limit.load_step_range;
                    vm.local.product_length_limit.left_toload -= vm.local.product_length_limit.load_step_range;
                }
            }
        };
    };

    // 计算菊花在页面上的y距离
    var positionY = function() {
        var vOffset = vm.local.voffset;
        var scrollPos = $(document).scrollTop();
        var ctnHeight = $('.products.card-12.card-15-lg').height();
        var viewHeight = $(window).height();
        var loadingHeight = 32;

        // 滚动位置的百分比
        var scrollPer = ((scrollPos - vOffset) / ctnHeight);
        // 菊花在页面上的百分比
        var loadingPer = (1 - (loadingHeight / viewHeight)) / 2 * viewHeight / ctnHeight;

        var posYPer = parseInt((scrollPer + loadingPer) * 100, 10);

        $('.loading-indicator').css({
            'display'               : 'block',
            'background-position-y' : posYPer + '%'
        });
    };

    //展开收起不同的块
    vm.toggleShow = function(type) {
        var className = '.products-groups.' + (type == 'top_10' ? 'top-10' : type) + ' .more-products-group';
        if(vm.local.is_show_more[type]) { //收起
            $(className).slideUp(function() {
                vm.local.is_show_more[type] = !vm.local.is_show_more[type];
            });
        } else { //展开
            $(className).slideDown(function() {
                vm.local.is_show_more[type] = !vm.local.is_show_more[type];
            });
        }
    };

    // 张开二级tag
    vm.showSubTags = function(tag) {
        $(tag).next().slideDown(300);
    };

    // 块跳转
    vm.sectionRedirect = function(item) {
        if(item.is_tag) {
            vm.tagRedirect(item.tag_name, item.tag_id);
        } else {
            vm.groupRedirect(item.link_url);
        }
    };

    // v2跳转
    vm.v2Redirect = function(type) {
        if(cityModel.local.is_search_result) {
            document.cookie = "hi_city=" + vm.data.city_code;
            window.location.href = $request_urls.cityLink;
        } else if(vm.data.type == 'group' && window.location.hash == '') { //分组到城市
            document.cookie = "hi_city=" + vm.data.city_code;
            window.location.href = $request_urls.cityLink;
        } else { //tag到v2
            var done = function() {
                var className = '.products-groups.' + (type == 'top_10' ? 'top-10' : type);
                $('body, html').animate({scrollTop : $(className).offset().top}, 500);
                vm.local.current_v2_id = type;
                vm.local.current_tag_id = '';
            };

            if(!!window.location.hash) {
                positionY();
                DataInitializer.getCityData(function() {
                    done();
                    window.location.hash = '';
                });
            } else {
                done();
            }
        }
        cityModel.local.is_search_result = false;
    };

    // 跳转至新的Url，cookie记录当前city_code，用来给下一页判断是否是同一个城市
    vm.groupRedirect = function(url) {
        cityModel.local.is_search_result = false;
        document.cookie = "hi_city=" + vm.data.city_code;
        window.location.href = url;
    };

    // 判断当前页面是否是 城市根路径 或者 tag分类路径，若是 当前局部加载刷新，若不是 则跳转至新的Url
    vm.tagRedirect = function(tag_name, tag_id) {
        cityModel.local.is_search_result = false;
        if(vm.data.type == 'group' && window.location.hash == '') { //分组到城市
            document.cookie = "hi_city=" + vm.data.city_code;
            window.location.href = $request_urls.cityLink + '#' + tag_name;
        } else {
            function done() {
                vm.local.current_v2_id = '';
                $('body, html').animate({scrollTop : vm.local.voffset}, 500);
            }

            positionY();

            window.location.hash = tag_name;
            DataInitializer.getTagData(tag_id, tag_name, done);
        }
    };

    // 执行城市搜索
    vm.doCitySearch = function(key) {
        function done() {
            vm.local.current_v2_id = '';
            $('body, html').animate({scrollTop : vm.local.voffset}, 500);
        }

        DataInitializer.getCitySearchResult(key, done);
    }

    vm.doCityEnterSearch = function(key, e) {
        var e = e || window.event;
        if(e.keyCode == 13) {
            cityModel.doCitySearch(key);
        }
    }
});

var DataInitializer = {
    // 获取城市主页接口数据（商品分组部分）
    'getCityData'         : function(cb) {
        var url = $request_urls.getAllGroups;
        var successCb = function(data) {
            if(data.code == 200) {
                if(data.data.line) {
                    data.data.line.display_product = [];
                    data.data.line.product_count = data.data.line.products.length;
                    for(var li = 0; li < data.data.line.products.length; li++) {
                        data.data.line.display_product[li] = data.data.line.products[li];
                        if(data.data.line.display_product[li].show_prices.title) {
                            data.data.line.display_product[li].show_prices.title = data.data.line.display_product[li].show_prices.title.substr(1);
                        }
                        if(li == 3) {
                            break;
                        }
                    }
                }
                if(data.data.package) {
                    for(var pi = 0; pi < data.data.package.products.length; pi++) {
                        data.data.package.products[pi].show_prices.title = data.data.package.products[pi].show_prices.title.substr(1);
                    }
                }
                cityModel.data = data.data;
                PageInitializer.initPage(cb);
            }
        };
        this.getJson(url, successCb);
    },
    // 获取tag接口数据（局部刷新）
    'getTagData'          : function(tag_id, tag_name, cb) {
        var url = $request_urls.getOneTag.replace('000', tag_id);
        var successCb = function(data) {
            if(data.code == 200) {
                cityModel.data.type = data.data.type;
                cityModel.data.tag_id = data.data.tag_id;
                cityModel.data.products_groups = data.data.products_groups;
                cityModel.local.current_tag_id = tag_name;
                PageInitializer.initPage(cb);
            }
        };
        this.getJson(url, successCb);
    },
    // 获取tag接口数据（跳转刷新）
    'getTagDataByJump'    : function(tag_id) {
        var url = $request_urls.getOneTagComingFromGroup.replace('000', tag_id);
        var successCb = function(data) {
            if(data.code == 200) {
                cityModel.data = data.data;
                cityModel.local.current_tag_id = tag_id;
                PageInitializer.initPage();
            }
        };
        this.getJson(url, successCb);
    },
    // 获取搜索结果接口数据（局部刷新）
    'getCitySearchResult' : function(key, cb) {
        $.ajax({
            url      : $request_urls.search,
            type     : 'POST',
            cache    : true,
            dataType : 'JSON',
            data     : {
                words : key
            },
            success  : function(data) {
                if(data.code == 200) {
                    cityModel.local.search_key_cache = key;
                    cityModel.local.is_search_result = true;
                    cityModel.local.search_result_length = data.data.products.length;
                    var group = {
                        products : data.data.products
                    };
                    cityModel.data.type = 'tag';
                    //                cityModel.data.tag_id = 0;
                    cityModel.data.products_groups = [];
                    cityModel.data.products_groups.push(group);
                    console.log(cityModel.data.products_groups)
                    cityModel.local.current_tag_id = '';
                    PageInitializer.initPage(cb);
                }
            }
        });
    },
    //获取数据
    'getJson'             : function(url, successCb) {
        $.ajax({
            url      : url,
            type     : 'GET',
            cache    : true,
            dataType : 'JSON',
            success  : successCb
        });
    }
};

var PageInitializer = {
    // 初始化页面交互 ----------------------------------------------------------
    'initPage'              : function(cb) {
        // 判断是否是 城市主页（全部推荐分组），若是 则设置产品列表长度限制为 3， 若不是 则默认产品列表长度限制为 15
        this.setProductLengthLimit();
        // 设置breadcrumb的名称
        this.setGroupLabel();

        this.initHeader(cityModel.data);

        // V2配置
        if(cityModel.data.use_v2) {
            this.setV2();
        }

        cityModel.local.voffset = $('.city-breadcrumb').offset().top;

        setTimeout(function() {
            cityModel.renderCallback();
            cb && cb();
        }, 50);
    },
    'setV2'                 : function() {
        if(cityModel.data.top_10) {
            if(cityModel.data.top_10.products.length >= cityModel.local.product_length_limit.top10_limit) {
                cityModel.local.is_show_more.top_10 = false;
                cityModel.local.has_more_products.top_10 = true;
                cityModel.data.top_10.more_products = cityModel.data.top_10.products.splice(cityModel.local.product_length_limit.top10_limit, cityModel.data.top_10.products.length -
                                                                                                                                              cityModel.local.product_length_limit.top10_limit);
            }
        }
        if(cityModel.data.package) {
            if(cityModel.data.package.products.length >= cityModel.local.product_length_limit.limit_length) {
                cityModel.local.is_show_more.package = false;
                cityModel.local.has_more_products.package = true;
                cityModel.data.package.more_products = cityModel.data.package.products.splice(cityModel.local.product_length_limit.limit_length, cityModel.data.package.products.length -
                                                                                                                                                 cityModel.local.product_length_limit.limit_length);
            }
        }
    },
    // 判断是否是 多个商品分组，若是 则设置商品列表长度限制为 3， 若不是 则默认商品列表长度限制为 15
    'setProductLengthLimit' : function() {
        if(!cityModel.data.type) { //首页面
            cityModel.local.product_length_limit = {
                limit_mode   : 'cates',
                limit_length : 2,
                top10_limit  : 3
            };
            /*cityModel.local.product_length_limit.limit_mode = 'cates';
             cityModel.local.product_length_limit.limit_length = 2;
             cityModel.local.product_length_limit.top10_limit = 3;*/
        } else { //分组或者标签
            cityModel.local.product_length_limit = {
                limit_mode      : 'cate',
                left_toload     : cityModel.data.products_groups[0].products.length -
                                  cityModel.local.product_length_limit.limit_length,
                limit_length    : 15,
                load_step_range : 10
            };
        }
    },
    'setGroupLabel'         : function() {
        if(!cityModel.data.type) {
            cityModel.local.current_label = '城市热卖';
        } else {
            cityModel.local.current_label = cityModel.data.products_groups[0].name;
        }
    },
    'initHeader'            : function(data) {
        cityModel.local.header = {
            title     : data.city.cn_name,
            en_title  : data.city.en_name,
            image_url : data.city.city_image.banner_image_url
        };
    }
};


$(function() {
    // 初始化页面数据 -----------------------------------------------------------
    var initData = function() {
        var hash = window.location.hash.substr(1);
        if(hash == '') {
            // 获取城市主页接口数据（商品分组部分）
            DataInitializer.getCityData();
        } else {
            // 获取tag接口数据
            DataInitializer.getTagDataByJump(hash);
        }
    };
    initData();
});