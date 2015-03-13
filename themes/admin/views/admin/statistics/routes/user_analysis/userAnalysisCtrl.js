controllers.userAnalysisCtrl = function($scope, $rootScope, $http) {
    //set default date
    var today = new Date();
    var default_start, default_end, default_start_compare, default_to_compare;
    //用format过的date来new一个Date
    default_end = new Date(formatDate(today));
    var now = new Date(formatDate(today));
    default_start = new Date(now.setDate(now.getDate() - 30));
    default_to_compare = new Date(now.setDate(now.getDate() - 1));
    default_start_compare = new Date(now.setDate(now.getDate() - 30));

    $scope.chart_data = [
        //store the chart data
    ];

    $scope.table_data = [];

    $scope.local = {
        chart_color          : ['#0a80ce', '#f7b542'],
        chart_loading_status : true,
        contrast_status      : 0,
        current_duration     : 3,
        durations_filters    : [
            {
                name : '今天',
                key  : 1
            },
            {
                name : '昨天',
                key  : 2
            },
            {
                name : '最近7天',
                key  : 3
            },
            {
                name : '最近30天',
                key  : 4
            },
            {
                name : '一段时间',
                key  : 5
            }
        ],
        //expanded = 0 展开图标收起 , = 1 展开图标展开
        target_expanded      : 0,
        targets_set          : [
            {
                target_name : '数量分析',
                key         : 1
            },
            {
                target_name : '占比分析',
                key         : 2
            },
            {
                target_name : '金额分析',
                key         : 3
            }
        ],
        targets_filters      : [
            /*
             * name : 指标名， type : 用于 query_filter ， ref : 属于哪个指标集,
             */
            {
                name   : '新注册用户数量',
                target : 1,
                key    : 1
            },
            {
                name   : '生成订单用户数量',
                target : 2,
                key    : 1
            },
            {
                name   : '成交用户数量',
                target : 3,
                key    : 1
            },
            {
                name   : '首次成交用户数量',
                target : 4,
                key    : 1
            },
            {
                name   : '回头客数量',
                target : 5,
                key    : 1
            },
            {
                name   : '首次成交用户占比',
                target : 6,
                key    : 2
            },
            {
                name   : '订单失败用户比例',
                target : 7,
                key    : 2
            },
            {
                name   : '用户平均成交金额',
                target : 8,
                key    : 3
            }
        ],
        query_filter         : {
            from_date         : default_start,
            to_date           : default_end,
            compare_from_date : '',
            compare_to_date   : '',
            target            : 1
        }
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '用户分析'
            }
        });

        $scope.fetchChartData(function() {
            $rootScope.$emit('loadStatus', false);
        });
        $scope.fetchAnalysisData();
    };
    $scope.compare = function() {
        $scope.local.contrast_status = $scope.local.contrast_status % 2 == 1 ? 0 : 1;
        //compare的时候 contrast_status = 1时给compare_from_date,compare_to_date设上初始默认值（在最前面有定义过），= 0 时把这两个设为空
        if($scope.local.contrast_status == 1) {
            // $scope.local.query_filter.compare_from_date = default_start_compare;
            //$scope.local.query_filter.compare_to_date = default_to_compare;
            $scope.setDurationDate(5);
        } else {
            $scope.local.query_filter.compare_from_date = '';
            $scope.local.query_filter.compare_to_date = '';
            //每次改变了query_filter里面的值，都要fetch data,上面的if分支setDurationDate已有fetch data了
            $scope.fetchChartData();
            $scope.fetchAnalysisData();
        }
    };

    $scope.setFilter = function(idx) {
        $scope.local.current_duration = idx;
        $scope.setDurationDate();
    };

    $scope.setDurationDate = function(index) {
        //index 专门为选择对比数据时间的时候设定传入的值，只会往case 6和7走
        var today = new Date();
        if(index) {
            $scope.local.current_duration = 4;
        }
        var current_duration = index || $scope.local.durations_filters[$scope.local.current_duration].key;
        var from_date, to_date, compare_from_date, compare_to_date, duration, temp;
        switch(current_duration) {
            case 1 :
                to_date = from_date = new Date();
                //昨天
                compare_to_date = compare_from_date = new Date(today.setDate(today.getDate() - 1));
                break;
            case 2 :
                //昨天
                to_date = from_date = new Date(today.setDate(today.getDate() - 1));
                //前天
                compare_from_date = compare_to_date = new Date(today.setDate(today.getDate() - 1));
                break;
            case 3 :
                //今天
                to_date = new Date();
                //一个礼拜前
                from_date = new Date(today.setDate(today.getDate() - 7));
                //一个礼拜再往前一天
                compare_to_date = new Date(today.setDate(today.getDate() - 1));
                //两个礼拜前
                compare_from_date = new Date(today.setDate(today.getDate() - 7));
                break;
            case 4 :
                //今天
                to_date = new Date();
                //30天前
                from_date = new Date(today.setDate(today.getDate() - 30));
                //30天前再往前一天
                compare_to_date = new Date(today.setDate(today.getDate() - 1));
                //再往前30天
                compare_from_date = new Date(today.setDate(today.getDate() - 30));
                break;
            case 5 :
                //直接填时间段  需要计算duration，case 6、7类似
                //formatDate是为了后日期均为yyyy-MM-dd,这样new Date出来的时间相减duration就为刚好为整天数的毫秒数
                //不会为出现同一天不同时刻减出来的duration毫秒数不同而导致下面compare date 计算不同
                to_date = new Date(formatDate($scope.local.query_filter.to_date));
                from_date = new Date(formatDate($scope.local.query_filter.from_date));
                duration = to_date - from_date;
                if($scope.local.contrast_status == 1) {
                    //deep copy 避免设置compare date的时候会修改from_date
                    temp = angular.copy(from_date);
                    compare_to_date = new Date(temp.setDate(temp.getDate() - 1));
                    compare_from_date = new Date(Date.parse(compare_to_date) - duration);
                }
                break;
            //在对比的第一个时间框里选择compare_from_date才会进入这种情况
            case 6 :
                to_date = new Date(formatDate($scope.local.query_filter.to_date));
                from_date = new Date(formatDate($scope.local.query_filter.from_date));
                duration = to_date - from_date;
                compare_from_date = new Date(formatDate($scope.local.query_filter.compare_from_date));
                compare_to_date = new Date(Date.parse(compare_from_date) + duration);
                break;
            //在对比的第二个时间框里选择compare_to_date才会进入这种情况
            case 7 :
                to_date = new Date(formatDate($scope.local.query_filter.to_date));
                from_date = new Date(formatDate($scope.local.query_filter.from_date));
                duration = to_date - from_date;
                compare_to_date = new Date(formatDate($scope.local.query_filter.compare_to_date));
                compare_from_date = new Date(Date.parse(compare_to_date) - duration);
                break;
        }
        $scope.local.query_filter.to_date = to_date;
        $scope.local.query_filter.from_date = from_date;
        if($scope.local.contrast_status == 1) {
            $scope.local.query_filter.compare_from_date = compare_from_date;
            $scope.local.query_filter.compare_to_date = compare_to_date;
        }
        //时间设置完后直接取数据
        $scope.fetchChartData();
        $scope.fetchAnalysisData();
    };

    $scope.$watch('local.query_filter.target', function() {
        $scope.fetchChartData();
        $scope.local.target_expanded = 0;
    }, true);

    $scope.fetchChartData = function(cb) {
        var url = $request_urls.getUserAnalysis;
        var query = {
            query_filter : angular.copy($scope.local.query_filter)
        };
        query.query_filter.from_date = formatDate(query.query_filter.from_date);
        query.query_filter.to_date = formatDate(query.query_filter.to_date);
        if(query.query_filter.compare_from_date != '') {
            query.query_filter.compare_from_date = formatDate(query.query_filter.compare_from_date);
            query.query_filter.compare_to_date = formatDate(query.query_filter.compare_to_date);
        }
        $scope.local.chart_loading_status = true;

        $http.post(url, query).success(function(values) {
            var i, base_data = values.data['base_data'], chart_data = [];

            if($scope.local.contrast_status == 0) {
                chart_data = [
                    {
                        'key'    : $scope.local.targets_filters[$scope.local.query_filter.target - 1].name,
                        //            'area'   : true,
                        'values' : []
                    }
                ];
                for(i in base_data) {
                    chart_data[0].values.push([
                        base_data[i]['group_date'], parseFloat(base_data[i]['group_value'])
                    ]);
                }
            } else {
                //因为下面xAxisTickFormat 时间戳转换要乘以1000  所以这里要除以1000 等下计算的时候才会抵消
                var duration = ($scope.local.query_filter.from_date - $scope.local.query_filter.compare_from_date) /
                               1000;

                //先把数据放进chart_data,等处理好后放进$scope.chart_data
                chart_data = [
                    {
                        'key'    : $scope.local.targets_filters[$scope.local.query_filter.target - 1].name,
                        'values' : []
                    },
                    {
                        'key'    : formatDate($scope.local.query_filter.compare_from_date) + '到' +
                                   formatDate($scope.local.query_filter.compare_to_date) + '对比数据',
                        'values' : []
                    }
                ];
                var compare_data = values.data['compare_data'];
                for(i in base_data) {
                    chart_data[0].values.push([
                        base_data[i]['group_date'], parseFloat(base_data[i]['group_value'])
                    ]);
                }
                for(i in compare_data) {
                    chart_data[1].values.push([
                        compare_data[i]['group_date'] + duration,
                        parseFloat(compare_data[i]['group_value'])
                    ]);
                }
            }

            $scope.chart_data = chart_data;
            $scope.local.chart_loading_status = false;
            cb && cb();
        });
    };

    $scope.fetchAnalysisData = function() {
        var url = $request_urls.getUserAnalysisSummary;
        var query = {
            query_filter : angular.copy($scope.local.query_filter)
        };
        query.query_filter.from_date = formatDate(query.query_filter.from_date);
        query.query_filter.to_date = formatDate(query.query_filter.to_date);
        if(query.query_filter.compare_from_date != '') {
            query.query_filter.compare_from_date = formatDate(query.query_filter.compare_from_date);
            query.query_filter.compare_to_date = formatDate(query.query_filter.compare_to_date);
        }

        $http.post(url, query).success(function(data) {
            //每次调用都要用这个存储，用之前清空 不能放在post外面 因为是异步的，可能两次调用时间比较短,数据还没进来就两次清除了table_data，然后两次数据就都叠在一起
            $scope.table_data = [];
            var i = 0, len = $scope.local.targets_filters.length, name, base_data, compare_data, trend;
            //还没有对比，属性只有一个base_data
            if($scope.local.contrast_status == 0) {

                for(; i < len; i++) {
                    name = $scope.local.targets_filters[i].name;
                    base_data = data.data[i + 1]['base_data'];
                    $scope.table_data.push([name, base_data]);
                }
            } else {
                //对比
                for(; i < len; i++) {
                    name = $scope.local.targets_filters[i].name;
                    base_data = data.data[i + 1]['base_data'];
                    compare_data = data.data[i + 1]['compare_data'];
                    trend = data.data[i + 1]['trend'];

                    if(trend === false) {
                        trend = '-';
                    } else if(trend === 0) {
                        trend = '持平';
                    } else if(trend > 0) {
                        trend = '上涨' + (Math.abs(trend) * 100).toFixed(2) + '%';
                    } else {
                        trend = '下降' + (Math.abs(trend) * 100).toFixed(2) + '%';
                    }
                    $scope.table_data.push([name, base_data, compare_data, trend]);
                }
            }
        })

    };

    $scope.xAxisTickFormat = function() {
        return function(d) {
            //add this to adjust the legend overlap, its wired but worked, 取巧方法，不通用
            d3.select('.nvd3.nv-legend .nv-series').attr('transform', 'translate(-40,5)');
            return d3.time.format('%Y-%m-%d')(new Date(d * 1000));
        }
    };

    $scope.init();

};

app.controller('userAnalysisCtrl', ['$scope', '$rootScope', '$http', controllers.userAnalysisCtrl]);