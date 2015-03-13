controllers.ProductSeoCtrl = function($scope, $rootScope, $route, $http) {
    $scope.data = {};
    $scope.local = {
        keywords     : [],
        section_head : {
            seo : {
                title    : '商品SEO',
                updateCb : function() {
                    var form_name = 'seo_form';
                    if($scope[form_name].$pristine) {
                        $scope.local.section_head.seo.is_edit = false;
                    } else if($scope[form_name].$valid) {
                        $scope.saveChanges();
                    } else {
                        $rootScope.$emit('notify', {msg : '请填写必填项'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.seo.is_edit = true;
                }
            }
        }
    };


    function processKeywords(string) {
        return string.replace(/，/g, ',').split(',').map(function(elem) {
            return elem.trim();
        }).filter(function(elem) {
            return elem.length > 0;
        });
    }

    $scope.init = function() {
        $scope.data = angular.copy($route.current.locals.loadData);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
    };

    $scope.saveChanges = function(cb) {
        $scope.data.seo.keywords = $scope.local.keywords.join(',');

        $http.post($request_urls.productSeo, $scope.data.seo).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.seo.is_edit = false;
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.$watch(function() {
        return $scope.data.seo.keywords;
    }, function(keywords) {
        $scope.local.keywords = processKeywords(keywords);
    });

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('ProductSeoCtrl', [
    '$scope', '$rootScope', '$route', '$http', controllers.ProductSeoCtrl
]);