controllers.ProductNoticeCtrl = function($scope, $rootScope, $route, $location) {
    $scope.data = {};
    $scope.local = {
        tab_options : {
            tabs        : [
                {
                    path    : 'rule',
                    label   : '购买限制',
                    loading : false
                },
                {
                    path    : 'note',
                    label   : '购买提醒',
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

        if($route.current.params.hasOwnProperty('notice_type')) {
            tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', $route.current.params.notice_type);
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

app.controller('ProductNoticeCtrl', [
    '$scope', '$rootScope', '$route', '$location', controllers.ProductNoticeCtrl
]);