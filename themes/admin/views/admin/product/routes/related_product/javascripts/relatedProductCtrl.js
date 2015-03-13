controllers.RelatedProductCtrl = function($scope, $rootScope, $route, $http) {
    $scope.data = {};
    $scope.local = {
        input_tag : {
            addCb       : function(product_id, next) {
                $http.post($request_urls.addProductRelated, {
                    related_id : product_id
                }).success(function(data) {
                    if(data.code == 200) {
                        $scope.data.related.push(data.data);
                        next();
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            },
            deleteCb    : function(index) {
                if(!window.confirm('删除产品的关联？')) return;

                var product = $scope.data.related[index];
                $http.post($request_urls.deleteProductRelated, {
                    related_id : product.product_id
                }).success(function(data) {
                    if(data.code == 200) {
                        $scope.data.related.splice(index, 1);
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            },
            btn_text    : '关联商品',
            title_str   : 'name',
            placeholder : '商品ID'
        }
    };

    $scope.init = function() {
        $scope.data = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
    };


    $scope.init();
};

app.controller('RelatedProductCtrl', [
    '$scope', '$rootScope', '$route', '$http', controllers.RelatedProductCtrl
]);