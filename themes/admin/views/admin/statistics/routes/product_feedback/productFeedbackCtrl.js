controllers.productFeedbackCtrl = function($scope, $rootScope, $http, commonFactory) {
    var default_start = new Date();
    default_start.setDate(default_start.getDate() - 30);
    var default_end = new Date();

    $scope.data = {};
    $scope.local = {
        edit_answer     : false,
        dialog_todo_index : 0,
        query_filter          : {
            country_code : '',
            city_code    : '',
            supplier_id  : '',
            date_start   : formatDate(default_start),
            date_end     : formatDate(default_end),
            product      : ''
        },
        supplier_list         : [],
        country_list          : [],
        city_list             : [],
        ask_grid_options  : {
            data    : [],
            query   : {
                sort         : {
                    ask : 0
                },
                paging       : {
                    start : 0,
                    limit : 10
                },
                query_filter : {
                    country_code : '',
                    city_code    : '',
                    supplier_id  : '',
                    date_start   : formatDate(default_start),
                    date_end     : formatDate(default_end),
                    product      : ''
                }
            },
            label   : {
                getHead : function(col, i) {
                    return col.label;
                },
                getBody : function(col, i, record, j) {
                    if(col.name == '') {
                        return ($scope.local.ask_grid_options.query.paging.start + j + 1).toString();
                    } else if(col.name == 'product_id' || col.name == 'product_name') {
                        return '<a target="_blank" href="admin/product/detail?product_id='+record['product_id'].toString()+'#/ProductFeedback">'+ record[col.name].toString() +'</a>';
                    } else {
                        return record[col.name].toString();
                    }
                }
            },
            request : {
                api_url : $request_urls.getFeedback
            },
            columns : [
                {
                    name  : 'product_id',
                    width : '7%',
                    label : '商品ID'
                },
                {
                    name  : 'product_name',
                    width : '45%',
                    label : '商品名称'
                },
                {
                    name  : 'city_name',
                    width : '10%',
                    label : '所属城市'
                },
                {
                    name     : 'ask_num',
                    width    : '10%',
                    label    : '提问总数',
                    use_sort : true
                },
                {
                    name     : 'ask_wait_num',
                    width    : '10%',
                    label    : '未解决',
                    use_sort : true
                }
            ]
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
            },
            {
                name : "待处理",
                key  : "todo"
            }
        ],
        current_duration      : 'last_month',
        duration_from_date    : default_start,
        duration_to_date      : default_end,
        total_question_count  : 0
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

        $scope.initStaticAmount(angular.copy($scope.local.query_filter));

        $rootScope.$emit('loadStatus', false);
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : 'Q&A统计'
            }
        });
    };

    $scope.filterStatics = function() {
        var query = angular.copy($scope.local.query_filter);
        $scope.local.ask_grid_options.query.query_filter = query;

        $scope.initStaticAmount(query);
        $scope.local.ask_grid_options.fetchData();
    };

    $scope.getUnReply = function() {
        $http.get($request_urls.getFeedback).success(function(data) {
            if(data.code == 200 ) {
                $scope.local.todo_feedback = data.data.data;
                $scope.local.total_question_count = $scope.local.todo_feedback.length;
                for(var todo_index in $scope.local.todo_feedback){
                    $scope.local.todo_feedback[todo_index].date_expected = formatDate($scope.local.todo_feedback[todo_index].date_expected);
                }
            }
        });
    };

    $scope.initStaticAmount = function(query) {

        var post_data = {
            query_filter : query
        };

        $http.post($request_urls.getFeedback, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.total_question_count = 0;
                for(var ask_index in data.data.data){
                    $scope.local.total_question_count += data.data.data[ask_index].ask_num;
                }
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
                $scope.filterStatics();
                break;
            case $scope.local.durations_filters[1].key :
                today.setDate(today.getDate() - 1);
                $scope.local.query_filter.date_end = formatDate(today);
                $scope.local.query_filter.date_start = formatDate(today);
                $scope.filterStatics();
                break;
            case $scope.local.durations_filters[2].key :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 7);
                $scope.local.query_filter.date_start = formatDate(today);
                $scope.filterStatics();
                break;
            case $scope.local.durations_filters[3].key :
                $scope.local.query_filter.date_end = formatDate(today);
                today.setDate(today.getDate() - 30);
                $scope.local.query_filter.date_start = formatDate(today);
                $scope.filterStatics();
                break;
            case $scope.local.durations_filters[4].key :
                $scope.local.query_filter.date_start = formatDate($scope.local.duration_from_date);
                $scope.local.query_filter.date_end = formatDate($scope.local.duration_to_date);
                $scope.filterStatics();
                break;
            case $scope.local.durations_filters[5].key :
                $scope.getUnReply();
                break;
            default :
                break;
        }

    };

    $scope.updateDuration = function() {
        $scope.setDurationDate('custom_duration');
    };

    $scope.editAnswer = function( ask_id ) {
        $scope.local.dialog_todo_index = getIndexByProp($scope.local.todo_feedback,'ask_id',ask_id);
        $scope.local.edit_answer = !$scope.local.edit_answer;
    };

    $scope.saveAnswer = function( flag ) {
        if(flag){
            var postData = $scope.local.todo_feedback[$scope.local.dialog_todo_index];
            if(postData.answer != ''){
                $http.post($request_urls.saveFeedback, postData).success(function(data) {
                    if(data.code == 200) {
                        $scope.local.todo_feedback.splice($scope.local.dialog_todo_index,1);
                        $scope.local.total_question_count = $scope.local.todo_feedback.length;
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            }
        }
        $scope.local.edit_answer = !$scope.local.edit_answer;
    };

    $scope.init();
};

app.controller('productFeedbackCtrl', ['$scope', '$rootScope', '$http', 'commonFactory', controllers.productFeedbackCtrl]);