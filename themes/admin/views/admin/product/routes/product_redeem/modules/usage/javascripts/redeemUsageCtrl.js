controllers.RedeemUsageCtrl = function($scope, $rootScope, $route, $location, $http, $sce) {
    $scope.local = {
        tab_path     : 'redeem_usage',
        form_name    : 'redeem_usage_form',
        path_name    : helpers.getRouteTemplateName($route.current),
        section_head : {
            usage : {
                title     : '使用方法',
                updateCb  : function() {
                    if($scope[$scope.local.form_name].$pristine) {
                        $scope.local.section_head.usage.is_edit = false;
                    } else if($scope[$scope.local.form_name].$valid) {
                        $scope.saveChanges();
                    } else {
                        $rootScope.$emit('notify', {msg : '有不正确的内容，请修复。'});
                    }
                }, editCb : function() {
                    $scope.local.section_head.usage.is_edit = true;
                }
            }
        }
    };

    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data.usage = decomposeMarkdown($scope.$parent.data.usage);
        $scope.data.usage.md_html = $sce.trustAsHtml($scope.data.usage.md_html);
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        $http.post($request_urls.productIntroduction, {
            usage : composeMarkdown($scope.data.usage)
        }).success(function(data) {
            if(data.code == 200) {
                $scope.$emit('setTabLoading', $scope.local.tab_path);
                $scope.local.section_head.usage.is_edit = false;
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == path_name && dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('RedeemUsageCtrl', [
    '$scope', '$rootScope', '$route', '$location', '$http', '$sce',
    controllers.RedeemUsageCtrl
]);