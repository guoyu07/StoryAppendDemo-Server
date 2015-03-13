controllers.NoticeRuleCtrl = function($scope, $rootScope, $http, $route, $sce) {
    var _default_range = '0day';
    var _dropdown_options = {
        class : 'inline',
        items : {
            'day'   : '日',
            'month' : '月',
            'year'  : '年'
        }
    };
    var _redeem_options = {
        '3' : '固定日期',
        '2' : '一段时间',
        '1' : '使用日期当日',
        '4' : '使用日期后'
    };

    $scope.data = {};
    $scope.local = {
        tab_path         : 'rule',
        form_name        : 'notice_rule_form',
        path_name        : helpers.getRouteTemplateName($route.current),
        section_head     : {
            title    : '购买限制',
            updateCb : function() {
                if($scope[$scope.local.form_name].$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope[$scope.local.form_name].$valid) {
                    $scope.saveChanges();
                } else {
                    $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        radio_options    : {
            buy_in_advance   : { //是否需要提前购买
                name  : 'buy_in_advance_radio',
                items : {
                    '0' : '不需要',
                    '1' : '需要'
                }
            },
            advance_day_type : { //提前购买日期类型
                name  : 'day_type',
                class : 'inline',
                items : {
                    '0' : '自然日',
                    '1' : '工作日'
                }
            },
            ship_immediately : { //是否立刻发货
                name  : 'lead_time_radio',
                items : {
                    '0' : '立刻发货',
                    '1' : '不立刻发货'
                }
            },
            ship_day_type    : { //发货日期类型
                name  : 'shipping_day_type',
                class : 'inline',
                items : {
                    '0' : '立刻发货',
                    '1' : '不立刻发货'
                }
            },
            sale_range       : { //最远购买时间
                name  : 'sale_range_type',
                items : {
                    '0' : '不限制',
                    '1' : '限制'
                }
            },
            redeem_type      : {
                name     : 'redeem_type',
                class    : 'redeem with-notice',
                items    : _redeem_options,
                notice   : true,
                comments : {
                    '1' : '＊用户只能在自己填写的使用日期当日兑换。',
                    '2' : '＊请指定一个时间段，用户在此时间段内可以兑换。',
                    '3' : '＊请指定一个具体日期，用户在此日期前可以兑换。',
                    '4' : '＊用户只能在自己填写的使用日期后一段时间内兑换。'
                }
            },
            return_type      : {
                name  : 'return_type',
                items : {
                    '0' : '不可以退',
                    '1' : '商品失效前可退',
                    '2' : '使用日期前可退'
                }
            }
        },
        dropdown_options : {
            sale_range      : angular.copy(_dropdown_options),
            fixed_duration  : angular.copy(_dropdown_options),
            range_duration  : angular.copy(_dropdown_options),
            offset_duration : angular.copy(_dropdown_options)
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        var result = angular.copy($scope.$parent.result.rules);

        $scope.data.date_rules = {};
        $scope.data.rule_desc = result.rule_desc;
        $scope.data.redeem_limit = result.redeem_limit;
        $scope.data.return_limit = result.return_limit;

        for(var key in result.rule_desc) {
            $scope.data.rule_desc[key] = $sce.trustAsHtml(result.rule_desc[key]);
        }

        ['day_type', 'need_tour_date', 'sale_range_type', 'shipping_day_type'].forEach(function(key) {
            $scope.data.date_rules[key] = result.sale_date_rule[key];
        });

        $scope.data.date_rules.lead_time = $scope.getDuration(result.sale_date_rule.lead_time);
        $scope.data.date_rules.lead_time.min = 1;
        $scope.data.date_rules.lead_time_radio = $scope.stringToRadio(result.sale_date_rule.lead_time);

        $scope.data.date_rules.sale_range_duration = $scope.getDurationForDropdown(result.sale_date_rule.sale_range);

        $scope.data.date_rules.buy_in_advance = $scope.getDuration(result.sale_date_rule.buy_in_advance);
        $scope.data.date_rules.buy_in_advance.min = 1;
        $scope.data.date_rules.buy_in_advance_radio = $scope.stringToRadio(result.sale_date_rule.buy_in_advance);

        if(['1970-01-01', '0000-00-00'].indexOf($scope.data.redeem_limit.expire_date) > -1) {
            $scope.data.redeem_limit.expire_date = formatDate(new Date());
        }
        $scope.data.redeem_limit.fixed_duration = $scope.getDurationForDropdown(result.redeem_limit.duration);
        $scope.data.redeem_limit.range_duration = $scope.getDurationForDropdown(result.redeem_limit.duration);

        $scope.data.return_limit.offset_duration = $scope.getDurationForDropdown(result.return_limit.offset);
    };

    $scope.getDuration = function(duration_val, default_unit) {
        var result = {};
        if(duration_val) {
            duration_val = duration_val.toLowerCase();
        }

        if(!duration_val || duration_val == '0day' || duration_val == '0') {
            result.qty = 0;
            result.unit = default_unit || 'day';
        } else {
            result.qty = parseInt(duration_val, 10);
            result.unit = duration_val.match(/[a-z]+/gi)[0];
        }
        if(result.unit !== 'day' && result.unit != 'month' && result.unit != 'year') {
            result.unit = 'day';
        }

        return result;
    };

    $scope.getDurationForDropdown = function(duration_val, default_unit) {
        var duration = $scope.getDuration(duration_val, default_unit);
        return {
            input  : duration.qty,
            option : duration.unit
        };
    };

    $scope.getDurationFromDropdown = function(duration) {
        if(duration) {
            if(duration.input !== undefined) {
                return duration.input + duration.option;
            } else {
                return duration.qty + duration.unit;
            }
        }
    };

    $scope.stringToRadio = function(limit) {
        limit = limit.toLowerCase();
        return ['0day', '0month', '0year'].indexOf(limit) > -1 ? '0' : '1';
    };

    $scope.afterStart = function(date) {
        var start_date = new Date();
        return date.getTime() > start_date.getTime();
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        var product_id = $scope.data.redeem_limit.product_id;
        var post_data = {
            redeem_limit   : {
                product_id  : product_id,
                redeem_type : $scope.data.redeem_limit.redeem_type,
                expire_date : formatDate($scope.data.redeem_limit.expire_date),
                usage_limit : $scope.data.redeem_limit.usage_limit
            },
            return_limit   : {
                offset      : $scope.getDurationFromDropdown($scope.data.return_limit.offset_duration),
                product_id  : product_id,
                return_type : $scope.data.return_limit.return_type
            },
            sale_date_rule : {
                day_type          : $scope.data.date_rules.day_type,
                lead_time         : $scope.data.date_rules.lead_time_radio == 1 ? $scope.getDurationFromDropdown($scope.data.date_rules.lead_time) : _default_range,
                sale_range        : $scope.data.date_rules.sale_range_type == 1 ? $scope.getDurationFromDropdown($scope.data.date_rules.sale_range_duration) : _default_range,
                product_id        : product_id,
                buy_in_advance    : $scope.data.date_rules.buy_in_advance_radio == 1 ? $scope.getDurationFromDropdown($scope.data.date_rules.buy_in_advance) : _default_range,
                sale_range_type   : $scope.data.date_rules.sale_range_type,
                shipping_day_type : $scope.data.date_rules.shipping_day_type
            }
        };

        if(post_data.redeem_limit.redeem_type == 2) {
            post_data.redeem_limit.duration = $scope.getDurationFromDropdown($scope.data.redeem_limit.fixed_duration);
        } else if(post_data.redeem_limit.redeem_type == 4) {
            post_data.redeem_limit.duration = $scope.getDurationFromDropdown($scope.data.redeem_limit.range_duration);
        } else {
            post_data.redeem_limit.duration = _default_range;
        }

        var message = '';
        if([2, 4].indexOf(+post_data.redeem_limit.redeem_type) > -1 && post_data.redeem_limit.duration[0] == '0') {
            message += '兑换时间段不能为0日／月／年。<br />';
        }
        if(post_data.return_limit.return_type == 1 && post_data.return_limit.offset[0] == '0') {
            message += '退换时间段不能为0日／月／年。<br />';
        }
        if(post_data.sale_date_rule.sale_range_type == 1 && post_data.sale_date_rule.sale_range[0] == '0') {
            message += '购买时间段不能为0日／月／年。<br />';
        }

        if(message) {
            $rootScope.$emit('notify', {msg : message});
        } else {
            $http.post($request_urls.postEditProductRule, post_data).success(function(data) {
                if(data.code == 200) {
                    $scope.local.section_head.is_edit = false;

                    $scope.$emit('setTabLoading', $scope.local.tab_path);
                    for(var key in data.data) {
                        $scope.data.rule_desc[key] = $sce.trustAsHtml(data.data[key]);
                    }

                    cb ? cb() : $rootScope.$emit('resetDirty');
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name && dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('NoticeRuleCtrl', [
    '$scope', '$rootScope', '$http', '$route', '$sce', controllers.NoticeRuleCtrl
]);