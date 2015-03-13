controllers.basicOrderCtrl = function($scope, $rootScope, $http, commonFactory) {
    var default_end = new Date();
    var default_start = new Date();
    default_start.setDate(default_start.getDate() - 30);

    var default_query_filter = {
        date_end     : formatDate(default_end),
        date_start   : formatDate(default_start),
        date_type    : 'date_added',
        city_code    : '',
        product_id   : '',
        supplier_id  : '',
        country_code : ''
    };

    $scope.data = {
        total_order_count : 0,
        total_sale_amount : 0
    };
    $scope.local = {
        product_show          : true,
        city_list             : [],
        country_list          : [],
        supplier_list         : [],
        query_filter          : angular.copy(default_query_filter),
        city_grid_options     : {
            data       : [],
            query      : {
                sort         : {
                    city_orders : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : $scope.local.query_filter
            },
            label      : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == '') {
                        return ($scope.local.city_grid_options.query.paging.start + j + 1).toString();
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request    : {
                api_url : $request_urls.orderSearch + 'city'
            },
            columns    : [
                {
                    name     : '',
                    width    : '5%',
                    label    : '',
                    use_sort : false
                },
                {
                    name     : 'name',
                    width    : '32%',
                    label    : '城市',
                    use_sort : false
                },
                {
                    name     : 'city_orders',
                    width    : '25%',
                    label    : '订单数量',
                    use_sort : true
                },
                {
                    name     : 'city_amount',
                    width    : '25%',
                    label    : '销售金额',
                    use_sort : true
                }
            ],
            pagination : {
                hide_extremity    : true,
                total_page_limits : 6
            }
        },
        country_grid_options  : {
            data       : [],
            query      : {
                sort         : {
                    country_orders : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : $scope.local.query_filter
            },
            label      : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == '') {
                        return ($scope.local.country_grid_options.query.paging.start + j + 1).toString();
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request    : {
                api_url : $request_urls.orderSearch + 'country'
            },
            columns    : [
                {
                    name     : '',
                    width    : '5%',
                    label    : '',
                    use_sort : false
                },
                {
                    name     : 'name',
                    width    : '32%',
                    label    : '国家',
                    use_sort : false
                },
                {
                    name     : 'country_orders',
                    width    : '25%',
                    label    : '订单数量',
                    use_sort : true
                },
                {
                    name     : 'country_amount',
                    width    : '25%',
                    label    : '销售金额',
                    use_sort : true
                }
            ],
            pagination : {
                hide_extremity    : true,
                total_page_limits : 6
            }
        },
        product_grid_options  : {
            data    : [],
            query   : {
                sort         : {
                    product_orders : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : $scope.local.query_filter
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == '') {
                        return ($scope.local.product_grid_options.query.paging.start + j + 1).toString();
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request : {
                api_url : $request_urls.orderSearch + 'product'
            },
            columns : [
                {
                    name  : '',
                    width : '5%',
                    label : '序号'
                },
                {
                    name  : 'product_id',
                    width : '7%',
                    label : '商品ID'
                },
                {
                    name  : 'name',
                    width : '45%',
                    label : '商品名称'
                },
                {
                    name  : 'city_name',
                    width : '10%',
                    label : '所属城市'
                },
                {
                    name     : 'product_orders',
                    width    : '10%',
                    label    : '订单数量',
                    use_sort : true
                },
                {
                    name     : 'product_amount',
                    width    : '10%',
                    label    : '销售金额',
                    use_sort : true
                }
            ]
        },
        supplier_grid_options : {
            data       : [],
            query      : {
                sort         : {
                    supplier_orders : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : $scope.local.query_filter
            },
            label      : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == '') {
                        return ($scope.local.supplier_grid_options.query.paging.start + j + 1).toString();
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request    : {
                api_url : $request_urls.orderSearch + 'supplier'
            },
            columns    : [
                {
                    name     : '',
                    width    : '5%',
                    label    : '',
                    use_sort : false
                },
                {
                    name     : 'name',
                    width    : '32%',
                    label    : '供应商',
                    use_sort : false
                },
                {
                    name     : 'supplier_orders',
                    width    : '25%',
                    label    : '订单数量',
                    use_sort : true
                },
                {
                    name     : 'supplier_amount',
                    width    : '25%',
                    label    : '销售金额',
                    use_sort : true
                }
            ],
            pagination : {
                hide_extremity    : true,
                total_page_limits : 6
            }
        },
        durations_filters     : [
            {
                name : "今天",
                key  : "today"
            },
            {
                name : "昨天",
                key  : "yesterday"
            },
            {
                name : "最近7天",
                key  : "last_week"
            },
            {
                name : "最近30天",
                key  : "last_month"
            },
            {
                name : "一段时间",
                key  : "custom_duration"
            }
        ],
        current_duration      : 'last_month',
        duration_to_date      : default_end,
        duration_from_date    : default_start,
        radio_options         : {
            name  : 'date_type',
            items : {
                'date_added'    : '按下单日期',
                'date_modified' : '按发货日期'
            },
            callback : function() {
                $scope.filterStatics();
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
        $scope.local.product_type_list = angular.copy(commonFactory.product_type);

        $scope.initStaticAmount(angular.copy($scope.local.query_filter));

        $scope.local.city_grid_options.query.query_filter = $scope.local.query_filter;
        $scope.local.country_grid_options.query.query_filter = $scope.local.query_filter;
        $scope.local.product_grid_options.query.query_filter = $scope.local.query_filter;
        $scope.local.supplier_grid_options.query.query_filter = $scope.local.query_filter;

        $rootScope.$emit('loadStatus', false);
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '订单统计'
            }
        });
    };

    $scope.filterStatics = function() {
        $scope.initStaticAmount($scope.local.query_filter);

        $scope.local.product_grid_options.fetchData();

        if(!$scope.local.query_filter.product_id) {
            $scope.local.city_grid_options.fetchData();
            $scope.local.country_grid_options.fetchData();
            $scope.local.supplier_grid_options.fetchData();
            $scope.local.product_show = true;
        } else {
            $scope.local.product_show = false;
        }
    };

    $scope.initStaticAmount = function(query) {
        $scope.data.total_order_count = 0;
        $scope.data.total_sale_amount = 0;

        $http.post($request_urls.orderSummary, {
            query_filter : query
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.total_order_count = data.data.total_orders == null ? 0 : data.data.total_orders;
                $scope.data.total_sale_amount = data.data.total_amount == null ? 0 : data.data.total_amount;
            }
        });
    };

    $scope.switchDuration = function(duration_index) {
        $scope.local.current_duration = duration_index;
        $scope.setDurationDate(duration_index);
    };

    $scope.setDurationDate = function(duration_index) {
        var today = new Date();

        switch(duration_index) {
            case $scope.local.durations_filters[0].key :
                $scope.local.query_filter.date_start = formatDate(today);
                $scope.local.query_filter.date_end = formatDate(today);
                break;
            case $scope.local.durations_filters[1].key :
                today.setDate(today.getDate() - 1);
                $scope.local.query_filter.date_end = formatDate(today);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case $scope.local.durations_filters[2].key :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 7);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case $scope.local.durations_filters[3].key :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 30);
                $scope.local.query_filter.date_start = formatDate(today);
                break;
            case $scope.local.durations_filters[4].key :
                $scope.local.query_filter.date_start = formatDate($scope.local.duration_from_date);
                $scope.local.query_filter.date_end = formatDate($scope.local.duration_to_date);
                break;
            default :
                break;
        }

        $scope.filterStatics();
    };

    $scope.updateDuration = function() {
        $scope.setDurationDate('custom_duration');
    };

    $scope.init();
};

app.controller('basicOrderCtrl', ['$scope', '$rootScope', '$http', 'commonFactory', controllers.basicOrderCtrl]);