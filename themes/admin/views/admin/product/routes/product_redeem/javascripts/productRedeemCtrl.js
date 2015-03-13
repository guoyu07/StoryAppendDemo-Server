controllers.ProductRedeemCtrl = function($scope, $rootScope, $route, $location, $http) {
    $scope.local = {
        tab_options : {
            tabs        : [
                {
                    path    : 'usage',
                    label   : '使用方法',
                    loading : false
                },
                {
                    path    : 'place',
                    label   : '兑换地点',
                    loading : false
                }
            ],
            current_tab : '',
            setCallback : function(tab) {
                $location.path('/' + $scope.local.path_name + '/' + tab.path);
            }
        },
        radio_options : {
            status : {
                name     : 'status',
                items    : {
                    '0' : '禁用',
                    '1' : '启用'
                },
                callback : function(status) {
                    $http.post($request_urls.productIntroduction, {
                        status : status
                    }).success(function(data) {
                        if(data.code != 200) {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        }
    };

    $scope.init = function() {
        var tab_index;

        $scope.result = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
        $scope.data = $scope.result.all_data;

        if($route.current.params.hasOwnProperty('redeem_type')) {
            tab_index = getIndexByProp($scope.local.tab_options.tabs, 'path', $route.current.params.redeem_type);
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

app.controller('ProductRedeemCtrl', [
    '$scope', '$rootScope', '$route', '$location', '$http', controllers.ProductRedeemCtrl
]);