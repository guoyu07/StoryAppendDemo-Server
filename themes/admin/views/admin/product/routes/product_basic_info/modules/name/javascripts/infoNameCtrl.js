controllers.InfoNameCtrl = function($scope, $rootScope, $http, $route, commonFactory) {
    $scope.data = {};
    $scope.local = {
        tab_path     : 'name',
        form_name    : 'basicinfo_name_form',
        path_name    : helpers.getRouteTemplateName($route.current),
        search_list   : {},
        section_head  : {
            title    : '商品基本信息',
            updateCb : function() {
                if($scope[$scope.local.form_name].$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else {
                    $scope.saveChanges();
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        import_status : {
            '-1' : '未处理过',
            '0'  : '待处理',
            '1'  : '处理中',
            '2'  : '已完成'
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.result = {
            city_code : $scope.$parent.result.info.city_code
        };

        $http.get($request_urls.getExpertList).success(function(data) {
            if(data.code == 200) {
                $scope.data.experts = data.data;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

        $scope.data.info = angular.copy($scope.$parent.result.info);
        $scope.data.import = angular.copy($scope.data.info.import);
        $scope.local.search_list.suppliers = angular.copy($scope.$parent.result.supplier_list);
        $scope.local.search_list.product_types = angular.copy(commonFactory.product_type);
    };

    $scope.getLabel = function(arr, prop, val, label) {
        var index = getIndexByProp(arr, prop, val);
        return index > -1 && (arr[index][label] ? arr[index][label] : '');
    };

    $scope.updateImport = function() {
        var url, post_data;
        if($scope.data.import.status == -1) {
            url = $request_urls.addGtaImport;
            post_data = {
                item_id   : $scope.result.supplier_product_id,
                city_code : $scope.result.city_code
            };
        } else {
            url = $request_urls.updateGtaImport;
            post_data = {
                auto_id : $scope.data.import.auto_id
            }
        }

        $http.post(url, post_data).success(function(data) {
            $rootScope.$emit('notify', {msg : data.msg});
            if(data.code == 200) {
                window.location.reload(); //TODO: remove reload
            }
        });
    };

    $scope.saveChanges = function() {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        var post_data = {};
        var keys = ['type', 'cn_name', 'en_name', 'cn_origin_name', 'en_origin_name', 'source_url', 'manager_name', 'supplier_id', 'expert_id'];
        keys.forEach(function(key) {
            post_data[key] = $scope.data.info[key];
        });

        $http.post($request_urls.updateBasicInfo, post_data).success(function(data) {
            if(data.code == 200) {
                window.location.reload(); //TODO: figure out changes need, use event instead
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name && dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('InfoNameCtrl', [
    '$scope', '$rootScope', '$http', '$route', 'commonFactory', controllers.InfoNameCtrl
]);