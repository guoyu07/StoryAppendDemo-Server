controllers.ProductPriceCtrl = function($scope, $rootScope, $route, $location, pricePlanFactory) {
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
        var price_type = $route.current.params.price_type;

        $scope.result = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
        $scope.local.price_type = price_type;
        $scope.local.is_plan_edit = false;

        if(price_type) {
            tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', price_type);
        }

        if(tab_index > -1) {
            $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[tab_index];
        } else if(['edit_price_plan', 'edit_special_price_plan'].indexOf(price_type) > -1) {
            var path = price_type == 'edit_price_plan' ? 'price_plan_list' : 'special_price_plan_list';

            $scope.local.is_plan_edit = true;
            $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[getIndexByProp($scope.local.tab_options.tabs, 'path', path)];
        } else { //找不到去第一个tab
            $location.path('/' + $scope.local.path_name + '/' + $scope.local.tab_options.tabs[0].path);
        }

        pricePlanFactory.raw_data = angular.copy($route.current.locals.loadData);
        $scope.local.has_default_tickets = $rootScope.product.type == 10;
    };

    $scope.initTabs = function() {
        $scope.local.tab_options.tabs.push({
            path    : 'sale_attribute',
            label   : '商品属性',
            loading : false
        });
        $scope.local.tab_options.tabs.push({
            path    : 'special_code',
            label   : 'Special Code',
            loading : false
        });
        $scope.local.tab_options.tabs.push({
            path    : 'price_plan_list',
            label   : '价格计划',
            loading : false
        });
        $scope.local.tab_options.tabs.push({
            path    : 'special_price_plan_list',
            label   : '特价计划',
            loading : false
        });
        $scope.local.tab_options.tabs.push({
            path    : 'departure_point',
            label   : 'Departure Point',
            loading : false
        });
    };

    $scope.isEditable = function() {
        if($rootScope.product.status == 3) {
            $rootScope.$emit('notify', {msg: '上线商品不允许编辑此信息'});
            return false;
        }

        return true;
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

app.controller('ProductPriceCtrl', [
    '$scope', '$rootScope', '$route', '$location', 'pricePlanFactory', controllers.ProductPriceCtrl
]);