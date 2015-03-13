controllers.priceDeparturePointCtrl = function($scope, $http, commonFactory, $rootScope) {
    //region Local Variables
    var weekday = ['周1', '周2', '周3', '周4', '周5', '周6', '周7'];
    $scope.data = {};
    $scope.local = {
        weekday          : angular.copy(weekday),
        templates        : {
            plan_item       : {
                time              : '00:00',
                to_date           : '',
                from_date         : '',
                short_time        : '',
                product_id        : '',
                departures        : [],
                valid_region      : '',
                departure_code    : '',
                additional_limit  : angular.copy(weekday),
                departure_plan_id : ''
            },
            departure_plan  : {
                plans        : [],
                to_date      : '',
                from_date    : '',
                valid_region : ''
            },
            departure_point : {
                intervals       : '',
                telephone       : '',
                product_id      : '',
                language_id     : '',
                description     : '',
                last_service    : '',
                first_service   : '',
                address_lines   : '',
                departure_code  : '',
                departure_point : ''
            }
        },
        section_head     : {
            departure_point : {
                title    : '',
                editCb   : function() {
                    if($scope.data.departure.has_departure == '1' && $scope.data.departure.plan_list.length == 0) {
                        $scope.addDeparturePlan();
                    }
                    $scope.local.section_head.departure_point.is_edit = true;
                },
                updateCb : function() {
                    $scope.saveChanges();
                }
            }
        },
        radio_options    : {
            valid_region  : {
                name     : 'valid_region',
                items    : {
                    '1' : '自定义生效区间',
                    '0' : '整个生效区间'
                },
                callback : function(new_val) {
                    if($scope.data.departure.plan_list.length > 1 && new_val == 0) {
                        if(!window.confirm("此操作将清除多余的departure point计划。点击‘确定’继续操作")) {
                            $scope.data.departure.valid_region = '1';
                            return;
                        }

                        $scope.data.departure.plan_list.splice(1);
                    }
                    if(new_val == 0) {
                        //Reset date
                        $scope.data.departure.plan_list[0].plans[0].valid_region = new_val;
                        $scope.data.departure.plan_list[0].to_date = $scope.local.date_rule.to_date;
                        $scope.data.departure.plan_list[0].from_date = $scope.local.date_rule.min_date;
                    } else if(new_val == 1 && $scope.data.departure.plan_list.length == 0) {
                        $scope.addDeparturePlan(new_val);
                    }
                }
            },
            has_departure : {
                name     : 'has_departure',
                items    : {
                    '0' : '不需要',
                    '1' : '需要'
                },
                callback : function(new_val) {
                    if(new_val == '0') {
                        if(!window.confirm("此操作将导致departure point数据被清除。点击‘确定’继续操作")) {
                            $scope.data.departure.has_departure = '1';
                            return;
                        }
                        $scope.data.departure.plan_list = [];
                        $scope.data.departure.valid_region = '';
                        $scope.data.departure.en_departure_title = '';
                        $scope.data.departure.cn_departure_title = '';
                    } else {
                        $scope.addDeparturePlan('0');
                    }
                }
            }
        },
        uploader_options : {
            excel : {
                target      : $request_urls.uploadDeparturePoints,
                input_id    : 'excel_upload',
                accept_type : 'application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                filterCb    : function(item) {
                    var size_result = item.size / 1024 / 1024 < 5;

                    if(!size_result) {
                        $rootScope.$emit('notify', {msg : '上传文件不能超过5MB'});
                    }

                    return size_result;
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    $scope.local.uploader_options.excel.in_progress = false;
                    uploader.queue = [];

                    if(response.code == 200) {
                        var all_items = response.data;
                        var current_plan, current_item;

                        all_items.forEach(function(new_item) {
                            $scope.addDeparturePlanItem($scope.local.current_plan_index);
                            current_plan = $scope.data.departure.plan_list[$scope.local.current_plan_index];
                            current_item = current_plan.plans[current_plan.plans.length - 1];

                            current_item.departures[0].departure_point = new_item.en_name;
                            current_item.departures[1].departure_point = new_item.cn_name;
                        });
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        }
    };
    //endregion

    //region Init
    $scope.init = function() {
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        $scope.data.departure = angular.copy($scope.$parent.result.departure_point);
        $scope.local.date_rule = angular.copy($scope.$parent.result.sale_date);
        $scope.local.templates.plan_item.to_date = $scope.local.date_rule.to_date;
        $scope.local.templates.plan_item.from_date = $scope.local.date_rule.from_date;

        $scope.initDepartureData();
    };
    $scope.initDepartureData = function() {
        if(!$scope.data.departure.plan_list) {
            $scope.data.departure.plan_list = [];
        }

        $scope.local.section_head.departure_point.title = $scope.data.departure.cn_departure_title + '(' + $scope.data.departure.en_departure_title + ')';
    };
    //endregion

    //region Helpers
    $scope.dateFilter = function(date) {
        var from_date = new Date($scope.local.date_rule.from_date);
        var to_date = new Date($scope.local.date_rule.to_date);
        return (from_date <= date && date <= to_date);
    };
    $scope.triggerUpload = function(plan_index) {
        $scope.local.current_plan_index = plan_index;
        $('#' + $scope.local.uploader_options.excel.input_id).trigger('click');
    };
    //endregion

    //region Departure Plan Actions
    $scope.addDeparturePlan = function(valid_region) {
        valid_region = valid_region || $scope.data.departure.valid_region;
        var new_plan = angular.copy($scope.local.templates.departure_plan);

        new_plan.to_date = $scope.local.date_rule.max_date;
        new_plan.from_date = $scope.local.date_rule.min_date;
        new_plan.valid_region = valid_region;

        var default_item_count = 4;
        var plan_index = $scope.data.departure.plan_list.push(new_plan) - 1;

        for(var i = 0; i < default_item_count; i++) {
            $scope.addDeparturePlanItem(plan_index);
        }
    };
    $scope.deleteDeparturePlan = function(plan_index) {
        if($scope.data.departure.plan_list.length <= 1) {
            $rootScope.$emit('notify', {msg: '至少包含一个生效区间'});
            return;
        }
        if(!window.confirm('确定删除这条记录吗？\r 点击‘确定’删除')) {
            return;
        }

        $scope.data.departure.plan_list.splice(plan_index, 1);
    };
    //endregion

    //region Departure Plan Item Actions
    $scope.addDeparturePlanItem = function(plan_index) {
        var plan = $scope.data.departure.plan_list[plan_index];
        var new_item = angular.copy($scope.local.templates.plan_item);
        var departure_en = angular.copy($scope.local.templates.departure_point);
        departure_en.language_id = '1';
        var departure_cn = angular.copy($scope.local.templates.departure_point);
        departure_cn.language_id = '2';

        new_item.to_date = plan.to_date;
        new_item.from_date = plan.from_date;
        new_item.departures = [departure_en, departure_cn];
        new_item.valid_region = $scope.data.departure.valid_region;

        plan.plans.push(new_item);
    };
    $scope.deleteDeparturePlanItem = function(plan_index, item_index) {
        var plan = $scope.data.departure.plan_list[plan_index];

        if(plan.plans.length < 2) {
            $rootScope.$emit('notify', {msg: '至少要有一个departure point在此生效区间中。'});
            return;
        }
        if(!window.confirm('删除后不可恢复。\n点击“确定”来删除。')) {
            return;
        }

        plan.plans.splice(item_index, 1);
    };
    $scope.toggleDay = function(day, plan_index, item_index) {
        var item = $scope.data.departure.plan_list[plan_index].plans[item_index];
        var index = item.additional_limit.indexOf(day);

        if(index > -1) {
            item.additional_limit.splice(index, 1);
        } else {
            item.additional_limit.push(day);
        }
    };
    //endregion

    //region Save Changes
    $scope.saveChanges = function() {
        if($scope.data.departure.has_departure == '1') {
            if($scope.data.departure.en_departure_title == '' || $scope.data.departure.cn_departure_title == '') {
                $rootScope.$emit('notify', {msg : '显示名称不能为空。'});
                return;
            }
            if($scope.hasDuplicate()) {
                $rootScope.$emit('notify', {msg : '同一生效区间内有重复的departure point，请检查后再进行保存。'});
                return;
            }
            if($scope.data.departure.valid_region == '1') {
                var result = commonFactory.isInsideDuration($scope.data.departure.plan_list, {
                    from_date : $scope.local.date_rule.min_date,
                    to_date   : $scope.local.date_rule.max_date
                });

                if(result.code != 200) {
                    $rootScope.$emit('notify', {msg: result.msg});
                    return;
                }
            }

            $scope.data.departure.plan_list = $scope.data.departure.plan_list.map(function(one_plan) {
                one_plan.from_date = $scope.data.departure.valid_region == '0' ? $scope.local.date_rule.min_date : one_plan.from_date;
                one_plan.to_date = $scope.data.departure.valid_region == '0' ? $scope.local.date_rule.max_date : one_plan.to_date;

                one_plan.plans = one_plan.plans.map(function(one_item) {
                    one_item.to_date = formatDate(one_plan.to_date);
                    one_item.from_date = formatDate(one_plan.from_date);
                    one_item.valid_region = $scope.data.departure.valid_region;
                    one_item.departure_code = one_item.departure_code.toString();
                    one_item.additional_limit = one_item.additional_limit.sort().join(";");

                    return one_item;
                });

                return one_plan;
            });
        }


        $http.post($request_urls.departurePlans, $scope.data.departure).success(function(data) {
            if(data.code == 200) {
                $scope.data.departure = angular.copy(data.data);
                $scope.local.section_head.departure_point.is_edit = false;

                $scope.initDepartureData();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.hasDuplicate = function() {
        var one_list, one_plan, list_index, plan_index, list_plans, departure_str;

        for(list_index in $scope.data.departure.plan_list) {
            one_list = $scope.data.departure.plan_list[list_index];
            list_plans = [];
            for(plan_index in one_list.plans) {
                one_plan = one_list.plans[plan_index];
                departure_str = one_plan.departures[1].departure_point + one_plan.time;
                if(list_plans.indexOf(departure_str) > -1) {
                    return true;
                } else {
                    list_plans.push(departure_str);
                }
            }
        }
    };
    //endregion


    $scope.init();
};

app.controller('priceDeparturePointCtrl', [
    '$scope', '$http', 'commonFactory', '$rootScope', controllers.priceDeparturePointCtrl
]);