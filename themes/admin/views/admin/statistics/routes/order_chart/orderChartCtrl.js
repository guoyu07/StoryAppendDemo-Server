controllers.orderChartCtrl = function($scope, $rootScope, $http, $q, commonFactory) {
    var default_start = new Date();
    default_start.setDate(default_start.getDate() - 30);
    var default_end = new Date();

    $scope.data = [
        {
            "key"    : "订单金额",
            "values" : []
        },
        {
            "key"    : "订单数量",
            "values" : []
        }
    ];
    $scope.local = {
        /* Query Filter */
        query_filter       : {
            city_code    : '',
            product_id   : '',
            supplier_id  : '',
            country_code : '',
            date_end     : formatDate(default_end),
            date_start   : formatDate(default_start)
        },
        /* Chosen Dropdown Data */
        city_list          : [],
        country_list       : [],
        supplier_list      : [],
        product_type_list  : angular.copy(commonFactory.product_type),
        /* Filters */
        current_duration   : 3,
        durations_filters  : [
            {
                name : "今天",
                key  : 1
            },
            {
                name : "昨天",
                key  : 2
            },
            {
                name : "最近7天",
                key  : 3
            },
            {
                name : "最近30天",
                key  : 4
            },
            {
                name : "这个月",
                key  : 6
            },
            {
                name : "上个月",
                key  : 7
            },
            {
                name : "今年",
                key  : 8
            },
            {
                name : "去年",
                key  : 9
            },
            {
                name : "一段时间",
                key  : 5
            }
        ],
        duration_to_date   : default_end,
        duration_from_date : default_start,
        yaxis_filters      : [
            {
                name : "订单数量",
                key  : "sub_orders"
            },
            {
                name : "订单金额",
                key  : "sub_amount"
            }
        ],
        current_yaxis      : 0
    };
    $scope.chart = {
        data  : {
            sub_amount : [],
            sub_orders : []
        },
        xAxis : function() {
            return function(entry) {
                var curr_date = new Date(entry * 1000);
                return d3.time.format('%Y-%m-%d')(curr_date);
            }
        }
    };

    $scope.init = function() {

        commonFactory.getAjaxSearchSupplierList(true).then(function(data) {
            $scope.local.supplier_list = data;
        });
        commonFactory.getAjaxSearchCountryList(true).then(function(data) {
            $scope.local.country_list = data;
        });
        commonFactory.getAjaxSearchCityList(true).then(function(data) {
            $scope.local.city_list = data;
        });

        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '订单图表'
            }
        });

        $scope.fetchChartData(function() {
            $rootScope.$emit('loadStatus', false);
        });
    };

    $scope.fetchChartData = function(cb) {
        var url = $request_urls.orderListByDate;
        var query = {
            sort         : {},
            paging       : {
                start : 0,
                limit : 10000000
            },
            query_filter : $scope.local.query_filter
        };

        $http.post(url, query).success(function(values) {
            $scope.chart.data = values.data;
            cb && cb();

            $scope.processChartData();
        });
    };

    $scope.setFilter = function(key, filter_name) {
        if(filter_name == 'duration') {
            $scope.local.current_duration = key;
            $scope.setDurationDate();
            $scope.fetchChartData();
        } else if(filter_name == 'yaxis') {
            $scope.local.current_yaxis = key;
            $scope.renderChart();
        }
    };

    $scope.setDurationDate = function() {
        var today = new Date();
        var current_duration = $scope.local.durations_filters[$scope.local.current_duration].key;

        switch(current_duration) {
            case 1 :
                $scope.local.query_filter.date_start = formatDate(today);
                $scope.local.query_filter.date_end = formatDate(today);
                break;
            case 2 :
                today.setDate(today.getDate() - 1);
                $scope.local.query_filter.date_end = formatDate(today);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case 3 :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 7);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case 4 :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 30);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case 5 :
                $scope.local.query_filter.date_start = formatDate($scope.local.duration_from_date);
                $scope.local.query_filter.date_end = formatDate($scope.local.duration_to_date);
                break;
            case 6 : //这个月
                today.setDate(1);
                $scope.local.query_filter.date_start = formatDate(today);
                today.setMonth(today.getMonth() + 1);
                today.setDate(today.getDate() - 1);
                $scope.local.query_filter.date_end = formatDate(today);
                break;
            case 7 : //上个月
                today.setDate(1);
                today.setDate(today.getDate() - 1);
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(1);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case 8 : //今年
                today.setMonth(0);
                today.setDate(1);
                $scope.local.query_filter.date_start = formatDate(today);
                today.setMonth(11);
                today.setDate(31);
                $scope.local.query_filter.date_end = formatDate(today);
                break;
            case 9 : //去年
                today.setFullYear(today.getFullYear() - 1);
                today.setMonth(0);
                today.setDate(1);
                $scope.local.query_filter.date_start = formatDate(today);
                today.setMonth(11);
                today.setDate(31);
                $scope.local.query_filter.date_end = formatDate(today);
                break;
        }
    };

    $scope.processChartData = function() {
        var i, len;
        //Reset values
        $scope.data[0].values = [];
        $scope.data[1].values = [];

        //Corresponding Raw Data
        var dataset = $scope.chart.data;

        for(i = 0, len = dataset.length; i < len; i++) {
            $scope.data[0].values.push([dataset[i].order_date, parseInt(dataset[i]['sub_amount'], 10)]);
            $scope.data[1].values.push([dataset[i].order_date, parseInt(dataset[i]['sub_orders'], 10)]);
        }

        $scope.renderChart();
    };

    $scope.renderChart = function() {

        nv.addGraph(function() {
            var is_amount = $scope.local.current_yaxis == '1';

            var xval = function(d) {
                var now = new Date(d[0] * 1000);
                now.setTime(now.getTime() - (now.getTimezoneOffset() * 60 * 1000));
                return now;
            };
            var yval = function(d) {
                return d[1];
            };
            var xlabel = '日期';
            var ylabel = $scope.local.yaxis_filters[$scope.local.current_yaxis].name;

            var data = [( is_amount ? $scope.data[0] : $scope.data[1] )];
            var chart = nv.models.lineChart().margin({left : 150})//.interpolate( 'basis' ) 严重bug
                .useInteractiveGuideline(true).transitionDuration(350)  //how fast do you want the lines to transition?
                .showLegend(true)       //Show the legend, allowing users to turn on/off line series.
                .showYAxis(true)        //Show the y-axis
                .showXAxis(true)        //Show the x-axis
                .x(xval).y(yval);

            chart.xAxis.axisLabel(xlabel).tickFormat(function(d) {
                return d3.time.format('%Y-%m-%d')(new Date(d));
            });
            chart.yAxis.axisLabel(ylabel);

            d3.select('#order_line svg')   //Select the <svg> element you want to render the chart in.
                .datum(data)          //Populate the <svg> element with chart data...
                .call(chart);         //Finally, render the chart!

            nv.utils.windowResize(function() {
                chart.update();
            });

            return chart;
        });
    };

    $scope.init();
};

app.controller('orderChartCtrl', ['$scope', '$rootScope', '$http', '$q', 'commonFactory', controllers.orderChartCtrl]);