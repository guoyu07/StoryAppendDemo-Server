controllers.ProductQnaCtrl = function($scope, $rootScope, $route, $http, $sce) {
    var form_name = 'qna_form';

    $scope.data = {};
    $scope.local = {
        section_head : {
            qna : {
                title    : '常见问题',
                updateCb : function() {
                    if($scope[form_name].$invalid) {
                        $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                    } else {
                        $scope.saveChanges();
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.qna.is_edit = true;
                }
            }
        }
    };


    $scope.init = function() {
        $scope.data = angular.copy($route.current.locals.loadData);
        $scope.data.qna.qa = decomposeMarkdown($scope.data.qna.qa);
        $scope.data.qna.qa.md_html = $sce.trustAsHtml($scope.data.qna.qa.md_html);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
    };

    $scope.saveChanges = function(cb) {
        $http.post($request_urls.updateProductQna, {
            qa : composeMarkdown($scope.data.qna.qa)
        }).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.qna.is_edit = false;
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('ProductQnaCtrl', [
    '$scope', '$rootScope', '$route', '$http', '$sce', controllers.ProductQnaCtrl
]);