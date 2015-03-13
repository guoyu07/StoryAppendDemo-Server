controllers.activitiesOrderCtrl = function($scope, $rootScope, $http, commonFactory) {
    $scope.data = {
        summary           : {
            total_orders         : 0,
            total_success_amount : 0,
            problem_order_rate   : '0%',
            total_success_orders : 0,
            problem_order_counts : 0
        },
        activity_duration : {
            start_date : '',
            end_date   : ''
        }
    };

    $scope.local = {
        grid_options    : {
            data    : [],
            table   : {
                table_id : 'activity_grid'
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == 'index') {
                        return $scope.local.grid_options.query.paging.start + j + 1;
                    }
                    if(col.name == 'problem_order_rate') {
                        var rate = (record[col.name] * 100).toString();
                        if(rate.length > 5) {
                            rate = rate.substr(0, 5);
                        }
                        return rate + "%";
                    }
                    return record[col.name].toString();
                }
            },
            query   : {
                sort         : {
                    'product_orders' : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : {
                    activity_id : 0,
                    date_start  : '',
                    date_end    : '',
                    product_id  : ''
                }
            },
            request : {
                api_url : $request_urls.getActivityOrderList
            },
            columns : [
                {
                    name     : 'index',
                    width    : '8%',
                    label    : '序号',
                    use_sort : false
                },
                {
                    name     : 'product_id',
                    width    : '10%',
                    label    : '商品id',
                    use_sort : false
                },
                {
                    name     : 'name',
                    width    : '40%',
                    label    : '商品名称',
                    use_sort : false
                },
                {
                    name     : 'product_orders',
                    width    : '12%',
                    label    : '成功订单数量',
                    use_sort : true
                },
                {
                    name     : 'product_amount',
                    width    : '12%',
                    label    : '成功订单总额',
                    use_sort : true
                },
                {
                    name     : 'problem_order_rate',
                    width    : '18%',
                    label    : '异常订单占比',
                    use_sort : true
                }
            ]
        },
        activities_list : []
    };


    $scope.init = function() {
        $http.get($request_urls.getActivityList).success(function(data) {
            if(data.code == 200) {
                $scope.local.activities_list = data.data;
                var latest_index = $scope.local.activities_list.length - 1;
                $scope.local.grid_options.query.query_filter.activity_id = $scope.local.activities_list[latest_index].activity_id;
                $scope.data.activity_duration.start_date = formatDate($scope.local.activities_list[latest_index].start_date);
                $scope.data.activity_duration.end_date = formatDate($scope.local.activities_list[latest_index].end_date);
                $scope.local.grid_options.query.query_filter.date_start = $scope.local.activities_list[latest_index].start_date;
                $scope.local.grid_options.query.query_filter.date_end = $scope.local.activities_list[latest_index].end_date;
            }

            $scope.getStaticsSummary();
        });
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '活动分析'
            }
        });
    };

    $scope.filterActivity = function() {
        $scope.local.grid_options.query.paging.start = 0;
        if($scope.local.grid_options.query.query_filter.date_start != '') {
            $scope.local.grid_options.query.query_filter.date_start = formatDate($scope.local.grid_options.query.query_filter.date_start);
        }
        if($scope.local.grid_options.query.query_filter.date_end != '') {
            $scope.local.grid_options.query.query_filter.date_end = formatDate($scope.local.grid_options.query.query_filter.date_end);
        }
        $scope.local.grid_options.fetchData();
        $scope.getStaticsSummary();
    };

    $scope.changeActivities = function() {
        var activity_id = $scope.local.grid_options.query.query_filter.activity_id;
        for(var i in $scope.local.activities_list) {
            if($scope.local.activities_list[i].activity_id == activity_id) {
                $scope.local.grid_options.query.query_filter.activity_id = $scope.local.activities_list[i].activity_id;
                $scope.data.activity_duration.start_date = formatDate($scope.local.activities_list[i].start_date);
                $scope.data.activity_duration.end_date = formatDate($scope.local.activities_list[i].end_date);
            }
        }
        $scope.filterActivity();
    }

    $scope.getStaticsSummary = function() {
        $http.post($request_urls.getActivityOrderSummary, $scope.local.grid_options.query).success(function(data) {
            if(data.code == 200) {
                var rate = (data.data.problem_order_rate * 100).toString();
                if(rate.length > 5) {
                    rate = rate.substr(0, 5);
                }
                $scope.data.summary.problem_order_rate = rate + "%";
                $scope.data.summary.problem_order_counts = data.data.problem_order_counts ?
                                                           data.data.problem_order_counts :
                                                           0;
                $scope.data.summary.total_orders = data.data.total_orders ? data.data.total_orders : 0;
                $scope.data.summary.total_success_amount = data.data.total_success_amount ?
                                                           data.data.total_success_amount :
                                                           0;
                $scope.data.summary.total_success_orders = data.data.total_success_orders ?
                                                           data.data.total_success_orders :
                                                           0;
                $rootScope.$emit('loadStatus', false);
            }
        });
    };

    $scope.init();
};

app.controller('activitiesOrderCtrl', [
    '$scope', '$rootScope', '$http', 'commonFactory', controllers.activitiesOrderCtrl
]);