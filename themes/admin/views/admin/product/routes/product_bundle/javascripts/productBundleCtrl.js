controllers.ProductBundleCtrl = function($scope, $rootScope, $route) {
    var template = {
        tag : {
            message      : '',
            is_edit      : false,
            is_error     : false,
            in_progress  : false,
            placeholder  : '商品ID',
            search_text  : '',
            button_label : '挂接'
        }
    };
    var bundle_map = {
        hotel         : 0,
        complimentary : 1,
        optional      : 2
    };

    $scope.data = {};
    $scope.local = {
        input_tag     : [
            angular.copy(template.tag),
            angular.copy(template.tag),
            angular.copy(template.tag)
        ],
        radio_options : {
            complimentary_type : {
                name  : 'count_type',
                items : {
                    '1' : '每套配送一个',
                    '2' : '每单配送一个',
                    '3' : '每人配送一个'
                }
            }
        }
    };

    $scope.init = function() {
        $scope.local.input_tag[bundle_map.hotel].placeholder = '酒店商品ID';

        $scope.data = {
            bundles : angular.copy($route.current.locals.loadData.bundles)
        };
    };

    $scope.toggleBundle = function(group_index) {
        $scope.local.input_tag[group_index].is_edit = !$scope.local.input_tag[group_index].is_edit;

        if(!$scope.local.input_tag[group_index].is_edit) {
            $scope.saveChanges();
        }
    };

    $scope.saveChanges = function() {

    };


    $scope.init();
};

app.controller('ProductBundleCtrl', [
    '$scope', '$rootScope', '$route', controllers.ProductBundleCtrl
]);