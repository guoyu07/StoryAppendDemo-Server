controllers.ServiceIncludeCtrl = function($scope, $rootScope, $route, $location, $http, $sce) {
    var tab_path = 'service_include';
    var form_name = 'service_include_form';
    var path_name = helpers.getRouteTemplateName($route.current);

    $scope.data = {};
    $scope.local = {
        path_name    : path_name,
        section_head : {
            service_include : {
                is_edit  : false,
                title    : '服务包含',
                editCb   : function() {
                    $scope.local.section_head.service_include.is_edit = true;
                },
                updateCb : function() {
                    /* TODO:: always invalid ?
                    if($scope[form_name].$invalid) {
                        $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                    } else {*/
                        $scope.saveChanges();
                    //}
                }
            }
        }
    };

    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data.desc = $scope.$parent.result.desc;
        $scope.data.desc.cn_service_include = decomposeMarkdown($scope.data.desc.cn_service_include);
        $scope.data.desc.cn_service_include.md_html = $sce.trustAsHtml($scope.data.desc.cn_service_include.md_html);
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', tab_path);

        var post_data = {
            cn_benefit         : $scope.data.desc.cn_benefit,
            cn_summary         : $scope.data.desc.cn_summary,
            cn_description     : $scope.data.desc.cn_description,
            cn_service_include : composeMarkdown($scope.data.desc.cn_service_include)
        };

        $http.post($request_urls.updateProductIntroduction, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.$emit('setTabLoading', tab_path);
                $scope.local.section_head.service_include.is_edit = false;
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == path_name && dirty_info.dirty_forms[form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('ServiceIncludeCtrl', [
    '$scope', '$rootScope', '$route', '$location', '$http', '$sce',
    controllers.ServiceIncludeCtrl
]);