controllers.priceSaleRuleCtrl = function($scope, $rootScope, $http, $route) {
    $scope.data = {};
    $scope.local = {
        label         : {},
        tab_path      : 'sale_pattern',
        form_name     : 'sale_pattern_form',
        path_name     : helpers.getRouteTemplateName($route.current),
        section_head  : {
            sale_pattern : {
                title    : '售卖方式',
                editCb   : function() {
                    if($scope.$parent.isEditable()) {
                        $scope.local.section_head.sale_pattern.is_edit = true;
                    }
                },
                updateCb : function() {
                    if($scope[$scope.local.form_name].$invalid) {
                        $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                    } else {
                        $scope.saveChanges();
                    }
                }
            }
        },
        radio_options : {
            is_packaged : {
                name     : 'sale_in_package',
                items    : {
                    '0' : '不按套出售',
                    '1' : '按套出售'
                },
                callback : function() {
                    $scope.toggleSaleInPackage();
                }
            },
            child_only  : {
                name     : 'is_independent',
                items    : {
                    "0" : '否',
                    "1" : '是'
                },
                callback : function(new_val) {
                    if(new_val == '0' && $scope.data.ticket_rule[$scope.adult_index].is_independent == 0) {
                        $rootScope.$emit('notify', {msg : '儿童与成人不能同时为不可单独售卖'});
                        $scope.data.ticket_rule[$scope.child_index].is_independent = 1
                    }
                }
            },
            adult_only  : {
                name     : 'is_independent',
                items    : {
                    "0" : '否',
                    "1" : '是'
                },
                callback : function(new_val) {
                    if(new_val == '0' && $scope.data.ticket_rule[$scope.child_index].is_independent == 0) {
                        $rootScope.$emit('notify', {msg : '儿童与成人不能同时为不可单独售卖'});
                        $scope.data.ticket_rule[$scope.adult_index].is_independent = 1
                    }
                }
            }
        }
    };

    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data = angular.copy($scope.$parent.result.sale_rule);
        $scope.initDescription();
    };
    $scope.initDescription = function() {
        $scope.local.label.summary = '';

        if($scope.data.sale_rule.sale_in_package == '1') {
            $scope.local.label.unit = '套';
            $scope.local.label.summary = '包含\n';

            $scope.data.package_rule.forEach(function(one_rule) {
                $scope.local.label.summary += one_rule.ticket_type.cn_name + '票 x ' + one_rule.quantity + '\n';
            });
        } else {
            $scope.local.label.unit = '人';

            if($scope.data.ticket_rule.length == 1) {
                $scope.data.ticket_type = 1;
                $scope.local.label.summary = '可单独购买' + $scope.data.ticket_rule[0].ticket_type.cn_name;
            } else if($scope.data.ticket_rule.length == 2) {
                $scope.data.ticket_type = 2;

                $scope.data.ticket_rule.forEach(function(one_rule, index) {
                    if(one_rule.ticket_type.ticket_id == 2) {
                        $scope.adult_index = index;
                    } else if(one_rule.ticket_type.ticket_id == 3) {
                        $scope.child_index = index;
                    }

                    if(one_rule.is_independent == 1) {
                        $scope.local.label.summary += '不';
                    }
                    $scope.local.label.summary += '可单独购买\t' + one_rule.ticket_type.cn_name + '票';
                });
            } else {
                $scope.data.ticket_type = 3;
                var independent = [];
                var dependent = [];

                $scope.data.ticket_rule.forEach(function(one_rule) {
                    if(one_rule.is_independent == 1) {
                        independent.push(one_rule.ticket_type.cn_name);
                    } else {
                        dependent.push(one_rule.ticket_type.cn_name);
                    }
                });

                if(independent.length) {
                    $scope.local.label.summary += '可单独购买\t' + independent.join('/') + '票\n';
                }
                if(dependent.length) {
                    $scope.local.label.summary += '不可单独购买\t' + dependent.join('/') + '票';
                }
            }
        }
    };

    $scope.toggleIndependent = function(index) {
        $scope.data.ticket_rule[index].is_independent = $scope.data.ticket_rule[index].is_independent == '1' ? '0' : '1';
    };

    $scope.toggleSaleInPackage = function() {
        if($scope.data.package_rule == undefined) {
            if($scope.data.sale_rule.sale_in_package == '1') {
                $scope.data.package_rule = $scope.data.ticket_rule.map(function(one_rule) {
                    return {
                        product_id      : one_rule.product_id,
                        base_product_id : "0",
                        ticket_id       : one_rule.ticket_id,
                        quantity        : "0",
                        ticket_type     : {
                            ticket_id : one_rule.ticket_type.ticket_id,
                            cn_name   : one_rule.ticket_type.cn_name,
                            en_name   : one_rule.ticket_type.en_name
                        }
                    };
                });
            } else {
                $scope.data.ticket_rule = $scope.data.package_rule.map(function(one_rule) {
                    return {
                        product_id     : one_rule.product_id,
                        ticket_id      : one_rule.ticket_id,
                        age_range      : '',
                        description    : '',
                        is_independent : '0',
                        min_num        : '0',
                        ticket_type    : {
                            ticket_id : one_rule.ticket_type.ticket_id,
                            cn_name   : one_rule.ticket_type.cn_name,
                            en_name   : one_rule.ticket_type.en_name
                        }
                    };
                });
            }
        }

        $scope.initDescription();
    };

    $scope.saveChanges = function() {
        if(!$scope.$parent.isEditable()) return;

        if($scope.data.ticket_type == "2" && $scope.data.sale_rule.sale_in_package == "0") {
            if($scope.data.ticket_rule[$scope.adult_index].is_independent == "1") {
                $scope.data.ticket_rule[$scope.child_index].min_num = '0';
            } else if($scope.data.ticket_rule[$scope.child_index].min_num == '0') {
                alert("成人票不可单独售卖，最少包含儿童数不能为0");
                return;
            }

            if($scope.data.ticket_rule[$scope.child_index].is_independent == "1") {
                $scope.data.ticket_rule[$scope.adult_index].min_num = '0';
            } else if($scope.data.ticket_rule[$scope.adult_index].min_num == '0') {
                alert("儿童票不可单独售卖，最少包含成人数不能为0");
                return;
            } else if($scope.data.ticket_rule[$scope.adult_index].min_num > $scope.data.sale_rule.min_num) {
                alert("最少包含成人数不能大于起定人数");
                return;
            }
        } else if($scope.data.sale_rule.sale_in_package == "1") {
            var total_pticket = 0;
            for(var index in $scope.data.package_rule) {
                total_pticket += $scope.data.package_rule[index].quantity;
            }
            if(total_pticket == 0) {
                $rootScope.$emit('notify', {msg: '请填写票种数量'});
                return;
            }
        }


        $http.post($request_urls.saveSaleRule, $scope.data).success(function(data) {
            if(data.code == 200) {
                alert(data.msg);
                $scope.data = data.data;
                $scope.initDescription();
                $scope.local.section_head.sale_pattern.is_edit = false;
            }
            if(data.code == 401) {
                window.location.reload();
            }
        });
    };


    $scope.init();
};

app.controller('priceSaleRuleCtrl', [
    '$scope', '$rootScope', '$http', '$route', controllers.priceSaleRuleCtrl
]);