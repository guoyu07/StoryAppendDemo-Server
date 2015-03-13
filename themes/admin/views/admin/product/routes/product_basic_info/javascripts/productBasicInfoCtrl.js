controllers.ProductBasicInfoCtrl = function($scope, $rootScope, $route, $location) {
    $scope.data = {};
    $scope.local = {
        tab_options : {
            tabs        : [
                {
                    path    : 'name',
                    label   : '商品名称／供应商',
                    loading : false
                },
                {
                    path    : 'city',
                    label   : '关联城市',
                    loading : false
                },
                {
                    path    : 'tag',
                    label   : '商品标签',
                    loading : false
                },
                {
                    path    : 'image',
                    label   : '商品图片',
                    loading : false
                },
                {
                    path    : 'location',
                    label   : '景点位置',
                    loading : false
                }
            ],
            current_tab : '',
            setCallback : function(tab) {
                $location.path('/' + $scope.local.path_name + '/' + tab.path);
            }
        }
    };


    $scope.init = function() {
        var tab_index;

        $scope.result = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);

        if($route.current.params.hasOwnProperty('info_type')) {
            tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', $route.current.params.info_type);
        }

        if(tab_index > -1) {
            $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[tab_index];
        } else { //找不到去第一个tab
            $location.path('/' + $scope.local.path_name + '/' + $scope.local.tab_options.tabs[0].path);
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

app.controller('ProductBasicInfoCtrl', [
    '$scope', '$rootScope', '$route', '$location', controllers.ProductBasicInfoCtrl
]);