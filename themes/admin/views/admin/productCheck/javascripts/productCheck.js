controllers.ProductCheckCtrl = function($scope, $rootScope, $http) {
    $scope.data = {};
    $scope.local = {
        tab_options : {
            tabs        : [
                {
                    path    : 'validate_all',
                    label   : '总体检查',
                    loading : false
                },
                {
                    path    : 'date_rule',
                    label   : '日期规则检查',
                    loading : false
                }
            ],
            current_tab : '',
            setCallback : function(tab) {
                $scope.local.tab_path = tab.path;
                $scope.local.tab_options.current_tab = tab;
            }
        }
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '上线商品检查'
            }
        });

        $scope.local.product_edit_base_url = $request_urls.productEdit;
        $scope.local.tab_options.current_tab = $scope.local.tab_options.tabs[0];
        $scope.local.tab_path = $scope.local.tab_options.current_tab.path;


        $http.get($request_urls.validateAll).success(function(data) {
            $rootScope.$emit('loadStatus', false);
            if(data.code == 200) {
                $scope.data.validate_all = data.data;
            }
        });

        $http.get($request_urls.checkDateRule).success(function(data) {
            if(data.code == 200) {
                $scope.data.date_rule = data.data;
            }
        });


    };


    $scope.init();
};

app.controller('ProductCheckCtrl', ['$scope', '$rootScope', '$http', controllers.ProductCheckCtrl]);
