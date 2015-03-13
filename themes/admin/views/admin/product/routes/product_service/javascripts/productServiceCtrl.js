controllers.ProductServiceCtrl = function($scope, $rootScope, $route, $location) {
    $scope.local = {
        tab_options : {
            tabs             : [],
            current_tab      : '',
            setCallback : function(tab) {
                $location.path('/' + $scope.local.path_name + '/' + tab.path);
            }
        }
    };


    $scope.init = function() {
        $scope.initTabs();

        var tab_index;

        $scope.result = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);

        if($route.current.params.hasOwnProperty('service_type')) {
            tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', $route.current.params.service_type);
        }

        if(tab_index > -1) {
            $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[tab_index];
        } else { //找不到去第一个tab
            $location.path('/' + $scope.local.path_name + '/' + $scope.local.tab_options.tabs[0].path);
        }
    };

    $scope.initTabs = function() {
        var product_type = $rootScope.product.type;

        $scope.local.tab_options.tabs.push({
            path    : 'include',
            label   : '服务包含',
            loading : false
        });

        if(product_type == 3) { //通票
            $scope.local.tab_options.tabs.push({
                path    : 'pass_classic',
                label   : '详细介绍 － 经典景点',
                loading : false
            });
            $scope.local.tab_options.tabs.push({
                path    : 'pass_other',
                label   : '详细介绍 － 景点列表',
                loading : false
            });
        } else if(product_type == 4) { //Hop On Hop Off
            $scope.local.tab_options.tabs.push({
                path    : 'pass_other',
                label   : '详细介绍 － 景点列表',
                loading : false
            });
        } else if(product_type == 8) { //酒店套餐

        } else if(product_type == 9) { //多日游
            $scope.local.tab_options.tabs.push({
                path    : 'introduce_multi_day_general',
                label   : '行程介绍',
                loading : false
            });
            $scope.local.tab_options.tabs.push({
                path    : '_tourplan',
                label   : '详细图文',
                loading : false
            });
        } else { //其他
            $scope.local.tab_options.tabs.push({
                path    : '_tourplan',
                label   : '详细介绍',
                loading : false
            });
        }
    };

    $scope.$on('setTabLoading', function(e, tab_path) {
        e.preventDefault();
        e.stopPropagation();

        var tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', tab_path);
        if(tab_index > -1) {
            $scope.local.tab_options.tabs[tab_index].loading = !$scope.local.tab_options.tabs[tab_index].loading;
        }
    });


    $scope.init();
};

app.controller('ProductServiceCtrl', [
    '$scope', '$rootScope', '$route', '$location', controllers.ProductServiceCtrl
]);