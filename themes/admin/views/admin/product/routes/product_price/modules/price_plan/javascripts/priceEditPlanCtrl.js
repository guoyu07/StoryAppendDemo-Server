//TODO: notify before leave page
controllers.priceEditPlanCtrl = function($scope, $rootScope, $http, $location, commonFactory, pricePlanFactory) {
    //region Variables
    $scope.data = {};
    $scope.local = {
        overlay             : {
            frequency   : {},
            has_overlay : false
        },
        weekdays            : {
            'wd1'   : '周一',
            'wd2'   : '周二',
            'wd3'   : '周三',
            'wd4'   : '周四',
            'wd5'   : '周五',
            'wd6'   : '周六',
            'wd7'   : '周日',
            'wdall' : '全部'
        },
        form_name           : 'edit_plan_form',
        section_head        : {
            title    : '',
            is_edit  : true,
            updateCb : function() {
                if($scope[$scope.local.form_name].$invalid) {
                    $rootScope.$emit('notify', {msg : '请填写必填项'});
                } else {
                    $scope.saveChanges();
                }
            }
        },
        radio_options       : {
            'valid_region'      : {
                name  : 'valid_region',
                items : {
                    '0' : '整个区间生效',
                    '1' : '自定义生效区间'
                }
            },
            'need_tier_pricing' : {
                name     : 'need_tier_pricing',
                items    : {
                    '0' : '不需要',
                    '1' : '需要'
                },
                callback : function() {
                    //Reset row span
                    $scope.data.current_plan = pricePlanFactory.formatPlan($scope.data.current_plan);
                }
            }
        },
        frequency_sale_rule : [
            {
                value : '1',
                label : '售卖'
            },
            {
                value : '0',
                label : '不售卖'
            }
        ]
    };
    //endregion

    //region Initialization
    $scope.init = function() {
        //Sanity Check
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        if(!$scope.$parent.isEditable()) return;

        //Data Declaration
        $scope.data = angular.copy($scope.$parent.result);
        $scope.local.plan_info = angular.copy($scope.$parent.result.plan_info);
        $scope.has_default_tickets = $scope.$parent.local.has_default_tickets;

        //Plan Info Data
        var init_data = pricePlanFactory.initPlanInfo($scope.local.plan_info, $scope.$parent.result.is_special_plan, Object.keys($scope.$parent.result.current_plan).length == 0);
        for(var key in init_data) {
            $scope.local[key] = init_data[key];
        }

        if($scope.local.config.is_new_plan) {
            $scope.data.current_plan = {
                items             : [],
                to_date           : '',
                from_date         : '',
                valid_region      : '0',
                price_plan_id     : '',
                need_tier_pricing : '0'
            };
            if($scope.local.config.is_special_plan) {
                $scope.data.current_plan.valid_region = '1'; //特价默认自定义时间段
                $scope.data.current_plan.slogan = '';
                $scope.data.current_plan.reseller = '';
            }

            $scope.local.plan_index = $scope.data.price_plans.push($scope.data.current_plan) - 1;
        } else {
            $scope.local.plan_index = getIndexByProp($scope.data.price_plans, 'price_plan_id', $scope.data.current_plan.price_plan_id);
        }

        $scope.initTitle();
        $scope.data.plans = $scope.$parent.result.price_plans.map(pricePlanFactory.formatPlan);
        $scope.data.current_plan = pricePlanFactory.formatPlan($scope.data.current_plan);
    };
    $scope.initTitle = function() {
        $scope.local.title = $scope.local.config.is_special_plan ? '特价' : '价格';
        $scope.local.plan_title = $scope.local.title + '计划' + (+$scope.local.plan_index + 1);

        var title_str = $scope.local.plan_title + '&nbsp;&nbsp;';
        if($scope.data.current_plan.valid_region == 1 && $scope.data.current_plan.from_date && $scope.data.current_plan.to_date) {
            title_str += '<span style="font-size: 14px;">(' + formatDate($scope.data.current_plan.from_date) + ' - ' + formatDate($scope.data.current_plan.to_date) + ')</span>';
        }
        if($scope.local.config.is_special_plan) {
            title_str += '&nbsp;&nbsp;<span style="font-size: 14px;">渠道：' + $scope.data.current_plan.reseller + '口号：' + $scope.data.current_plan.slogan + '</span>';
        }

        $scope.local.section_head.title = title_str;
    };
    //endregion

    //region User Interaction
    //region Filter
    $scope.fromDateFilter = function(date) {
        var from_date = new Date($scope.local.plan_info.from_date);
        var to_date = new Date($scope.local.plan_info.to_date);
        return (from_date <= date && date <= to_date);
    };
    $scope.toDateFilter = function(date) {
        return $scope.fromDateFilter(date) ? new Date($scope.data.current_plan.from_date).getTime() <= date : false;
    };
    //endregion

    //region Label
    $scope.getPlanRangeLabel = function(plan) {
        return pricePlanFactory.getPlanRangeLabel(plan);
    };
    $scope.getFrequencyLabel = function(frequency) {
        return pricePlanFactory.getFrequencyLabel(frequency);
    };
    $scope.getSpecialCodeLabel = function(special_code) {
        return pricePlanFactory.getSpecialCodeLabel(special_code);
    };
    $scope.getRowSpanByCode = function(special_id) {
        return pricePlanFactory.getRowSpanByCode(special_id);
    };
    //endregion

    //region Frequency
    function setAllDays(days_set) {
        days_set = [];
        for(var d in $scope.local.weekdays) {
            if(d == 'wdall') continue;
            days_set.push(d);
        }

        return days_set;
    }

    $scope.changeRule = function(new_rule) {
        if(new_rule.value == '1') {
            $scope.local.overlay.frequency.days = setAllDays($scope.local.overlay.frequency.days);
        }
    };
    $scope.toggleSelection = function(item, item_set) {
        var index = item_set.indexOf(item);
        if(index > -1) {
            item_set.splice(index, 1);
        } else {
            item_set.push(item);
        }
    };
    $scope.toggleEditFrequency = function(item_special, confirm_save) {
        var current_frequency;

        if(item_special) {
            $scope.local.overlay.has_overlay = true;

            current_frequency = angular.copy($scope.data.current_plan.special_code_frequency[item_special]);
            $scope.local.overlay.frequency.rule = current_frequency.length > 0 ? '1' : '0';
            $scope.local.overlay.frequency.days = pricePlanFactory.processFrequency(current_frequency);
            $scope.local.overlay.current_special = item_special;

            if($scope.local.overlay.frequency.days.indexOf('wdall') > -1) { //如果是全部，就摊开
                $scope.local.overlay.frequency.days = setAllDays($scope.local.overlay.frequency.days);
            }
        } else {
            $scope.local.overlay.has_overlay = false;
        }

        if(confirm_save) {
            $scope.data.current_plan.special_code_frequency[$scope.local.overlay.current_special] = $scope.local.overlay.frequency.rule == '1' ? angular.copy($scope.local.overlay.frequency.days) : [];
        }
    };
    //endregion

    function isItemValid(item) {
        return item.frequency.length ? pricePlanFactory.isItemPriceValid(item) : true;
    }
    $scope.saveChanges = function() {
        if($scope[$scope.local.form_name].$pristine) {
            $scope.local.section_head.is_edit = false;
        }

        var post_data = angular.copy($scope.data.current_plan);
        var update_url = $scope.local.config.is_special_plan ? $request_urls.productPricePlanSpecial : $request_urls.productPricePlan;

        post_data.to_date = formatDate(post_data.to_date);
        post_data.from_date = formatDate(post_data.from_date);
        if(post_data.valid_region == 1) {
            var result = commonFactory.isInsideDuration([post_data], $scope.local.plan_info);
            if(result.code < 200) {
                $rootScope.$emit('notify', {msg : result.msg});
                return;
            }
        }

        for(var item, i = 0, len = post_data.items.length; i < len, item = post_data.items[i]; i++) {
            if($scope.local.config.has_special_code && item.special_code in post_data.special_code_frequency) {
                item.frequency = post_data.special_code_frequency[item.special_code].join(';');
            } else {
                item.frequency = '';
            }

            if(!isItemValid(item) && [1, 2, 99].indexOf(+item.ticket_id) > -1) {
                $rootScope.$emit('notify', {msg : '成人票，套票，和一种票的价格都不能为都为0。请填写后再保存。'});
                return;
            }
        }

        $http.post(update_url + post_data.price_plan_id, post_data).success(function(data) {
            if(data.code == 200) {
                $location.path('ProductPrice/' + ($scope.local.config.is_special_plan ? 'special_price_plan_list' : 'price_plan_list'));
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //endregion


    $scope.init();
};

app.controller('priceEditPlanCtrl', [
    '$scope', '$rootScope', '$http', '$location', 'commonFactory', 'pricePlanFactory', controllers.priceEditPlanCtrl
]);