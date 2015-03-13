controllers.priceTourOperationCtrl = function($scope, $rootScope, $http, commonFactory, $sce) {
    $scope.data = {
        'tour_date' : {}
    };
    $scope.local = {
        close_ranges  : {
            'singleday' : '单独固定日期',
            'weekday'   : '按周期循环',
            'range'     : '时间段'
        },
        radio_options : {
            'need_tour_date' : {
                name  : 'need_tour_date',
                class : 'inline',
                items : {
                    '1' : '需要',
                    '0' : '不需要'
                }
            }
        },
        section_head  : {
            tour_date : {
                is_edit  : false,
                title    : '',
                editCb   : function() {
                    if($scope.$parent.isEditable()) {
                        $scope.local.section_head.tour_date.is_edit = true;
                    }
                },
                updateCb : function() {
                    $scope.saveTourDate();
                }
            }
        }
    };

    function initOneOperation(one_operation) {
        one_operation.parts = commonFactory.decomposeCloseDate(one_operation.close_dates);
        one_operation.added_field = {
            'singleday' : '',
            'weekday'   : '',
            'range'     : {
                from_date : '',
                to_date   : ''
            }
        };
        one_operation.to_date = formatDate(one_operation.to_date);
        one_operation.from_date = formatDate(one_operation.from_date);
        one_operation.current_range = 'range';

        one_operation.dateFilter = (function(operation) {
            return function(date) {
                var from_date = new Date(operation.from_date);
                var to_date = new Date(operation.to_date);
                return (from_date <= date && date <= to_date);
            }
        })(one_operation);

        return one_operation;
    }

    function validCloseDates() {
        var len = $scope.data.tour_date.product_tour_operation.length;
        for(var i = 0; i < len; i++) {
            var close_dates = $scope.data.tour_date.product_tour_operation[i].close_dates;
            if(close_dates.length > 0) {
                var from_date = $scope.data.tour_date.product_tour_operation[i].from_date;
                var to_date = $scope.data.tour_date.product_tour_operation[i].to_date;

                var parts = close_dates.split(';');
                var parts_len = parts.length;
                for(var j = 0; j < parts_len; j++) {
                    var part = parts[j];
                    if(part.indexOf('-') > 0 && part.indexOf('/') == -1) {
                        if(part < from_date || part > to_date) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    $scope.init = function() {
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        $scope.data.tour_date = angular.copy($scope.$parent.result.date_rule);
        $scope.data.tour_date.product_tour_operation = $scope.data.tour_date.product_tour_operation.map(initOneOperation);

        $scope.updateTourDateTitle();
    };

    $scope.addTour = function() {
        $http.post($request_urls.productTourOperation).success(function(data) {
            if(data.code == 200) {
                $scope.data.tour_date.product_tour_operation.push(initOneOperation(data.data));
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.deleteTour = function(index) {
        var tour_id = $scope.data.tour_date.product_tour_operation[index].operation_id;
        $http.delete($request_urls.productTourOperation + tour_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.tour_date.product_tour_operation.splice(index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.saveTourDate = function() {
        if($scope.data.tour_date.product_date_rule.need_tour_date == '1' && validCloseDates() == false) {
            $rootScope.$emit('notify', {msg : '关闭日期不能超出售卖日期范围。'});
            return;
        }

        var result = commonFactory.isRangesValid($scope.data.tour_date.product_tour_operation);
        if(result.code != 200) {
            $rootScope.$emit('notify', {msg : result.msg});
            return;
        }

        $scope.data.tour_date.product_tour_operation = commonFactory.composeCloseDate($scope.data.tour_date.product_tour_operation).map(function(one_operation) {
            one_operation.to_date = formatDate(one_operation.to_date);
            one_operation.from_date = formatDate(one_operation.from_date);
            return one_operation;
        });

        $http.post($request_urls.saveDateRule, $scope.data.tour_date).success(function(data) {
            if(data.code == 200) {
                $scope.updateTourDateTitle(true);
                $scope.local.section_head.tour_date.is_edit = false;
                $scope.data.tour_date.product_tour_operation = $scope.data.tour_date.product_tour_operation.map(initOneOperation);
            } else {
                $rootScope.$emit('notify', {msg: data.msg});
            }
        });
    };
    $scope.updateTourDateTitle = function(use_sce) {
        if($scope.data.tour_date.product_date_rule.need_tour_date == '1') {
            $scope.local.section_head.tour_date.title = $scope.data.tour_date.cn_tour_date_title + "(" +
                                                        $scope.data.tour_date.en_tour_date_title + ")";
        } else {
            $scope.local.section_head.tour_date.title = "使用日期";
        }

        if(use_sce) {
            $scope.local.section_head.tour_date.title = $sce.trustAsHtml($scope.local.section_head.tour_date.title);
        }
    };

    $scope.changeRange = function(value, current_tour_operation) {
        current_tour_operation.current_range = value;
    };

    $scope.addCloseItem = function(current_tour_operation) {
        var str = '';
        var range = current_tour_operation.current_range;
        if(range == "range") {
            if(formatDate(current_tour_operation.added_field.range.from_date).trim() == '' ||
               formatDate(current_tour_operation.added_field.range.to_date).trim() == '') {
                alert("请填写完整的日期区间后再添加");
                return;
            }
            str = formatDate(current_tour_operation.added_field.range.from_date) + " - " +
                  formatDate(current_tour_operation.added_field.range.to_date);
            current_tour_operation.parts.range.push(str);
            current_tour_operation.added_field.range.from_date = '';
            current_tour_operation.added_field.range.to_date = '';
        } else if(range == "weekday") {
            if(current_tour_operation.added_field.weekday.trim() == '') {
                $rootScope.$emit('notify', {msg: '请填写后再添加'});
                return;
            }
            if(current_tour_operation.added_field.weekday.indexOf('1') != -1 ||
               current_tour_operation.added_field.weekday.indexOf('一') != -1) {
                str = '周1';
            } else if(current_tour_operation.added_field.weekday.indexOf('2') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('二') != -1) {
                str = '周2';
            } else if(current_tour_operation.added_field.weekday.indexOf('3') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('三') != -1) {
                str = '周3';
            } else if(current_tour_operation.added_field.weekday.indexOf('4') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('四') != -1) {
                str = '周4';
            } else if(current_tour_operation.added_field.weekday.indexOf('5') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('五') != -1) {
                str = '周5';
            } else if(current_tour_operation.added_field.weekday.indexOf('6') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('六') != -1) {
                str = '周6';
            } else if(current_tour_operation.added_field.weekday.indexOf('7') != -1 ||
                      current_tour_operation.added_field.weekday.indexOf('七') != -1) {
                str = '周7';
            } else {
                alert("格式错误，请重试");
                return;
            }

            if(current_tour_operation.parts.weekday.indexOf(str) == -1) {
                current_tour_operation.parts.weekday.push(str);
            }

            current_tour_operation.added_field.weekday = '';
        } else if(range == "singleday") {
            if(formatDate(current_tour_operation.added_field.singleday).trim() == '') {
                alert("请选择日期后再添加");
                return;
            }
            var date_str = formatDate(current_tour_operation.added_field.singleday);
            if(current_tour_operation.parts.singleday.indexOf(date_str) == -1) {
                current_tour_operation.parts.singleday.push(date_str);
            }
            current_tour_operation.added_field.singleday = '';
        }
    };
    $scope.deleteCloseItem = function(item_index, tour_index, part_type) {
        if(window.confirm("确定要删除此条不可购买日期吗？")) {
            $scope.data.tour_date.product_tour_operation[tour_index].parts[part_type].splice(item_index, 1);
        }
    };


    $scope.init();
};

app.controller('priceTourOperationCtrl', [
    '$scope', '$rootScope', '$http', 'commonFactory', '$sce', controllers.priceTourOperationCtrl
]);