controllers.CouponEditCtrl = function($scope, commonFactory, $http, $rootScope, $filter) {
    var reset_value = 0;
    var input_tag_options = {
            btn_text    : '关联商品',
            title_str   : 'name',
            placeholder : '商品ID'
        },
        input_tag_options_city = {
            btn_text    : '关联城市',
            title_str   : 'name',
            placeholder : '城市ID'
        },
        input_tag_options_country = {
            btn_text    : '关联国家',
            title_str   : 'name',
            placeholder : '国家ID'
        };

    function isEmpty(string) {
        return !!string && parseInt(string);
    }

    function setLimit(expr) {
        return !!expr ? $scope.local.limit : $scope.local.unlimit;
    }

    $scope.data = {
        coupon : {}
    };
    $scope.local = {
        //限制－1，不限制－0
        limit            : '1',
        unlimit          : '0',
        is_limit_changed : false,
        overlay          : {
            has_overlay  : false,
            grid_options : {
                data    : [],
                label   : {
                    getHead : function(col) {
                        return col.label;
                    },
                    getBody : function(col, i, record) {
                        if(col.name == 'amount') {
                            return $filter('number')(record[col.name], 2);
                        } else {
                            return record[col.name].toString();
                        }
                    }
                },
                request : {
                    api_url       : $request_urls.fetchCouponHistory,
                    record_filter : ['order_id', 'customer_id', 'amount', 'date_added']
                },
                columns : [
                    {
                        name  : 'order_id',
                        width : '10%',
                        label : '订单号'
                    },
                    {
                        name  : 'customer_id',
                        width : '30%',
                        label : '用户ID'
                    },
                    {
                        name  : 'amount',
                        width : '30%',
                        label : '折扣金额'
                    },
                    {
                        name  : 'date_added',
                        width : '30%',
                        label : '使用日期'
                    }
                ]
            }
        },
        input_tag        : {},
        breadcrumb       : {
            back : {},
            body : {
                content : '编辑优惠券'
            }
        },
        section_head     : {
            info  : {
                title    : '基本信息',
                is_edit  : false,
                updateCb : function() {
                    if($scope.coupon_info.$pristine) {
                        $scope.local.section_head.info.is_edit = false;
                    } else if($scope.coupon_info.$valid) {
                        $scope.local.section_head.info.is_edit = false;
                        $scope.updateCoupon();
                    } else {
                        $rootScope.$emit('notify', {msg : '优惠券内容有误。请检查完再提交'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.info.is_edit = true;
                }
            },
            rules : {
                title    : '使用规则限制',
                is_edit  : false,
                updateCb : function() {
                    if($scope.coupon_rules.$pristine && !$scope.local.is_limit_changed) {
                        $scope.local.section_head.rules.is_edit = false;
                    } else if($scope.coupon_rules.$valid) {
                        $scope.local.section_head.rules.is_edit = false;
                        $scope.updateCoupon();
                    } else {
                        $rootScope.$emit('notify', {msg : '优惠券规则有误。请检查完再提交'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.rules.is_edit = true;
                }
            }
        },
        radio_switch     : {
            use_type                 : { //使用方式
                name  : 'use_type',
                items : {
                    '1' : '现金抵用',
                    '2' : '渠道OP',
                    '3' : '测试'
                }
            },
            coupon_status            : { //优惠券状态
                name  : 'status',
                items : {
                    '0' : '禁用',
                    '1' : '启用'
                }
            },
            discount_type            : { //优惠方式
                name  : 'type',
                items : {
                    'P' : '折扣',
                    'F' : '现金减免'
                }
            },
            user_limit               : { //使用人数限制
                name  : 'user_limit',
                items : {
                    '0' : '多人同用一张',
                    '1' : '只给一位客户使用'
                }
            },
            max_usage_limit          : { //总共使用次数限制
                name  : 'max_usage_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            per_user_usage_limit     : { //每个用户使用次数限制
                name  : 'per_user_usage_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            order_total_limit        : { //订单最低金额限制
                name  : 'order_total_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            min_quantity_limit       : { //订单最低商品数量限制
                name  : 'min_quantity_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            max_quantity_limit       : { //订单最高商品数量限制
                name  : 'max_quantity_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            login_limit              : { //登录限制
                name  : 'logged',
                items : {
                    '1' : '需要',
                    '0' : '不需要'
                }
            },
            product_allowed_limit    : { //允许使用商品限制
                name  : 'product_allowed_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            },
            product_allowed_limit_v2 : {
                name     : 'valid_type',
                items    : {
                    '1' : '商品券',
                    '2' : '城市券',
                    '3' : '国家券',
                    '0' : '全球券'
                },
                notice   : true,
                comments : {
                    '1' : '限定优惠券应用或不应用于哪些商品',
                    '2' : '限定优惠券应用或不应用于哪些城市',
                    '3' : '限定优惠券应用或不应用于哪些国家',
                    '0' : '全球商品均可使用'
                }
            },
            product_limit_logic      : {
                name  : 'limit_type',
                items : {
                    '1' : '允许使用',
                    '0' : '不允许使用'
                }
            },
            product_banned_limit     : { //不允许使用商品限制
                name  : 'product_banned_limit',
                items : {
                    '1' : '限制',
                    '0' : '不限制'
                }
            }
        },
        user_search      : '',
        selected_country : '',
        is_new_coupon    : false,
        is_search_user   : true
    };
    //  coupon limited product
    $scope.local.input_tag.can_use = angular.copy(input_tag_options);
    $scope.local.input_tag.cant_use = angular.copy(input_tag_options);
    $scope.local.input_tag.can_use.addCb = function(product_id, next) {
        $scope.addCouponTarget('1', '1', product_id, next);
    };
    $scope.local.input_tag.can_use.deleteCb = function(index) {
        $scope.deleteCouponProduct('1', index);
    };
    $scope.local.input_tag.cant_use.addCb = function(product_id, next) {
        $scope.addCouponTarget('0', '1', product_id, next);
    };
    $scope.local.input_tag.cant_use.deleteCb = function(index) {
        $scope.deleteCouponProduct('0', index);
    };
    //  coupon limited city
    $scope.local.input_tag.city_can_use = {
        btn_text    : '关联城市',
        title_str   : 'name',
        placeholder : '城市ID',
        select      : {
            value_prop  : 'city_code',
            label_prop  : 'select_label',
            placeholder : '点击选择城市'
        },
        addCb       : function(product_id, next) {
            $scope.addCouponTarget('1', '2', product_id, next);
        },
        deleteCb    : function(index) {
            $scope.deleteCouponProduct('1', index);
        }
    };
    $scope.local.input_tag.city_cant_use = {
        btn_text    : '关联城市',
        title_str   : 'name',
        placeholder : '城市ID',
        select      : {
            value_prop  : 'city_code',
            label_prop  : 'select_label',
            placeholder : '点击选择城市'
        },
        addCb       : function(product_id, next) {
            $scope.addCouponTarget('0', '2', product_id, next);
        },
        deleteCb    : function(index) {
            $scope.deleteCouponProduct('1', index);
        }
    };
    //  coupon limited country
    $scope.local.input_tag.country_can_use = {
        btn_text    : '关联国家',
        title_str   : 'name',
        placeholder : '国家ID',
        select      : {
            value_prop  : 'country_code',
            label_prop  : 'cn_name',
            placeholder : '点击选择国家'
        },
        addCb       : function(product_id, next) {
            $scope.addCouponTarget('1', '3', product_id, next);
        },
        deleteCb    : function(index) {
            $scope.deleteCouponProduct('1', index);
        }
    };
    $scope.local.input_tag.country_cant_use = {
        btn_text    : '关联国家',
        title_str   : 'name',
        placeholder : '国家ID',
        select      : {
            value_prop  : 'country_code',
            label_prop  : 'cn_name',
            placeholder : '点击选择国家'
        },
        addCb       : function(product_id, next) {
            $scope.addCouponTarget('0', '3', product_id, next);
        },
        deleteCb    : function(index) {
            $scope.deleteCouponProduct('1', index);
        }
    };


    $scope.init = function() {
        $http.get($request_urls.coupon).success(function(data) {
            if([200, 401].indexOf(data.code) > -1) {
                $scope.data.coupon = angular.copy(data.data);

                if(data.code == 401) {
                    $scope.local.is_new_coupon = true;
                    $scope.local.section_head.info.is_edit = true;
                    $scope.local.breadcrumb.body.content = '新增优惠券';

                    $scope.data.coupon.date_start = new Date();
                    $scope.data.coupon.date_end = new Date($scope.data.coupon.date_start.getTime() +
                                                           ( 1000 * 60 * 60 * 24 ));
                } else {
                    //Options initialization
                    $scope.data.coupon.user_limit = setLimit(isEmpty($scope.data.coupon.customer_id));
                    $scope.data.coupon.uses_total = parseInt($scope.data.coupon.uses_total || 0, 10);
                    $scope.data.coupon.uses_customer = parseInt($scope.data.coupon.uses_customer || 0, 10);

                    $scope.data.coupon.login_limit = setLimit(isEmpty($scope.data.coupon.logged));
                    $scope.data.coupon.max_usage_limit = setLimit(isEmpty($scope.data.coupon.uses_total));
                    $scope.data.coupon.per_user_usage_limit = setLimit(isEmpty($scope.data.coupon.uses_customer));

                    $scope.data.coupon.total = parseFloat($scope.data.coupon.total);
                    $scope.data.coupon.discount = parseFloat($filter('number')($scope.data.coupon.discount, 2));
                    $scope.data.coupon.order_total_limit = setLimit(isEmpty($scope.data.coupon.total));

                    $scope.data.coupon.product_min = parseInt($scope.data.coupon.product_min || 0, 10);
                    $scope.data.coupon.product_max = parseInt($scope.data.coupon.product_max || 0, 10);
                    $scope.data.coupon.min_quantity_limit = setLimit(isEmpty($scope.data.coupon.product_min));
                    $scope.data.coupon.max_quantity_limit = setLimit(isEmpty($scope.data.coupon.product_max));

                    $scope.local.breadcrumb.back.clickCb = function() {
                        window.location = $request_urls.back;
                    };

                    commonFactory.getAjaxSearchCityList().then(function(data) {
                        $scope.data.cities = getGroupBy(data, 'city_en_name');
                        $scope.data.cities = $scope.data.cities.map(function(city) {
                            city.select_label = city.city_name + ' ' + city.city_pinyin;

                            return city;
                        });
                    });
                    commonFactory.getAjaxSearchCountryList().then(function(data) {
                        $scope.data.countries = getGroupBy(data, 'en_name');
                    });
                }

                $rootScope.$emit('loadStatus', false);
                $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb);
            } else {
                $rootScope.$emit('errorStatus', true);
            }
        });
    };

    $scope.toggleHistory = function(has_overlay) {
        $scope.local.overlay.has_overlay = has_overlay;
        $rootScope.$emit('overlay', has_overlay);
    };

    $scope.afterStart = function(date) {
        if(!$scope.data.coupon.date_start) return true;
        var start_date = new Date($scope.data.coupon.date_start);
        return date.getTime() >= start_date.getTime();
    };

    $scope.updateCoupon = function() {
        $scope.data.coupon.date_start = formatDate($scope.data.coupon.date_start);
        $scope.data.coupon.date_end = formatDate($scope.data.coupon.date_end);
        $http.post($request_urls.coupon, $scope.data.coupon).success(function(data) {
            if(data.code == 200 && $scope.local.is_new_coupon) {
                console.log(data);
                window.location = data.data.edit_url;
            }
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.generateCouponCode = function() {
        $scope.data.coupon.code = randomStr(10);
    };

    $scope.toggleUserSearch = function() {
        if($scope.local.is_search_user) {
            if(parseInt($scope.local.user_search, 10) == $scope.local.user_search) {
                $http.get($request_urls.isUserValid + $scope.local.user_search).success(function(data) {
                    if(data.code == 200) {
                        $scope.data.coupon.customer_id = data.data.customer_id;
                        $scope.data.coupon.customer_email = data.data.email;
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            } else {
                $http.get($request_urls.getEmail + $scope.local.user_search).success(function(data) {
                    if(data.code == 200) {
                        $scope.data.coupon.customer_id = data.data;
                        $scope.data.coupon.customer_email = $scope.local.user_search;
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            }
        }

        $scope.local.is_search_user = !$scope.local.is_search_user;
    };

    $scope.addCouponTarget = function(use_type, coupon_type, target_id, cb) {
        $http.post($request_urls.getLimitIdName, {
            valid_type : coupon_type,
            id         : target_id
        }).success(function(data) {
            if(data.code == 200) {
                var is_again = 0;
                for(var i = 0; i < $scope.data.coupon.limit_ids.length; i++) {
                    if(data.data.id == $scope.data.coupon.limit_ids[i].id) {
                        is_again = 1;
                        break;
                    }
                }
                if(is_again == 0) {
                    $scope.data.coupon.limit_ids.push({
                        id   : data.data.id,
                        name : data.data.name
                    });
                } else {
                    $rootScope.$emit('notify', {msg : '关联重复'});
                }
                cb();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteCouponProduct = function(use_type, index) {
        var target = $scope.data.coupon.limit_ids;
        target.splice(index, 1);
    };

    var i = 0, j = 0;
    $scope.$watch('data.coupon.valid_type', function() {
        i < 2 ? i++ : $scope.data.coupon.limit_ids = [];
    });
    $scope.$watch('data.coupon.limit_type', function() {
        j < 2 ? j++ : $scope.data.coupon.limit_ids = [];
    });
    $scope.$watch('data.coupon.limit_ids', function() {
        $scope.local.is_limit_changed = true;
    });
    $scope.$watch('data.coupon', function(new_coupon) {
        if(new_coupon.user_limit == $scope.local.unlimit) {
            $scope.data.coupon.customer_id = reset_value;
            $scope.data.coupon.customer_email = '';
        }
        if(new_coupon.max_usage_limit == $scope.local.unlimit) {
            $scope.data.coupon.uses_total = reset_value;
        }
        if(new_coupon.per_user_usage_limit == $scope.local.unlimit) {
            $scope.data.coupon.uses_customer = reset_value;
        }
        if(new_coupon.order_total_limit == $scope.local.unlimit) {
            $scope.data.coupon.total = reset_value;
        }
        if(new_coupon.min_quantity_limit == $scope.local.unlimit) {
            $scope.data.coupon.product_min = reset_value;
        }
        if(new_coupon.max_quantity_limit == $scope.local.unlimit) {
            $scope.data.coupon.product_max = reset_value;
        }
    }, true);

    $scope.init();
};
directives.couponDiscount = function() {
    var linkFunc = function(scope, elm, attrs, ctrl) {
        ctrl.$parsers.unshift(function(viewValue) {
            if(scope.data.coupon.type == 'P') {
                if(parseInt(viewValue, 10) > 100) {
                    ctrl.$setValidity('couponDiscount', false);
                    return undefined;
                }
            }
            ctrl.$setValidity('couponDiscount', true);
            return viewValue;
        });
    };

    return {
        link    : linkFunc,
        scope   : false,
        require : 'ngModel'
    };
};

app.controller('CouponEditCtrl', [
    '$scope', 'commonFactory', '$http', '$rootScope', '$filter', controllers.CouponEditCtrl
]);
app.directive('couponDiscount', directives.couponDiscount);