angular_routes.statistics = function($routeProvider) {
    $routeProvider.when('/basic_order', {
        templateUrl : 'basicOrder.html',
        controller  : 'basicOrderCtrl'
    }).when('/order_chart', {
        templateUrl : 'orderChart.html',
        controller  : 'orderChartCtrl'
    }).when('/order_complaint', {
        templateUrl : 'orderComplaint.html',
        controller  : 'orderComplaintCtrl'
    }).when('/activities_order', {
        templateUrl : 'activitiesOrder.html',
        controller  : 'activitiesOrderCtrl'
    }).when('/user_analysis', {
        templateUrl : 'userAnalysis.html',
        controller  : 'userAnalysisCtrl'
    }).when('/product_qa', {
        templateUrl : 'productFeedback.html',
        controller  : 'productFeedbackCtrl'
    }).otherwise({
        redirectTo : '/basic_order'
    });
};

controllers.StatisticsCtrl = function($scope, $rootScope, $location) {
    $scope.data = {};
    $scope.local = {
        menu_items    : [
            {
                id    : 'basic_title',
                label : '概览',
                group : true
            },
            {
                id    : 'statistics_title',
                label : '订单统计',
                group : true
            },
            {
                id    : 'basic_order',
                label : '订单数量与销售金额',
                group : false
            },
            {
                id    : 'order_chart',
                label : '订单趁势分析',
                group : false
            },
            {
                id    : 'order_complaint',
                label : '投诉/退订分析',
                group : false
            },
            {
                id    : 'operation_title',
                label : '运营',
                group : true
            },
            {
                id    : 'activities_order',
                label : '活动分析',
                group : false
            },
            {
                id    : 'user_analysis',
                label : '用户分析',
                group : false
            },
            {
                id    : 'product_title',
                label : '商品',
                group : true
            },
            {
                id    : 'product_qa',
                label : 'Q&A',
                group : false
            }
        ],
        current_route : ''
    };

    $scope.goToItem = function(route) {
        $location.path('/' + route);
    };

    $scope.init = function() {
        $scope.local.current_route = 'basicOrder';
    };

    $scope.init();

    $scope.$on('$routeChangeSuccess', function() {
        $scope.local.current_route = $location.path().substring(1);
    });
};

app.controller('StatisticsCtrl', ['$scope', '$rootScope', '$location', controllers.StatisticsCtrl]);
app.config(['$routeProvider', angular_routes.statistics]);