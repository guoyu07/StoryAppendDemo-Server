controllers.InfoCityCtrl = function($scope, $rootScope, $http, $route) {
    $scope.data = {};
    $scope.local = {
        tab_path     : 'city',
        form_name    : 'basicinfo_city_form',
        path_name    : helpers.getRouteTemplateName($route.current),
        select_tag   : {
            other_cities : {
                btn_text    : '关联城市',
                title_str   : 'city_name',
                placeholder : '选择城市',
                select      : {
                    value_prop  : 'city_code',
                    label_prop  : 'select_label',
                    placeholder : '点击选择城市'
                },
                addCb       : function(city_code, next) {
                    var city_index = getIndexByProp($scope.data.info.other_cities, 'city_code', city_code);
                    if(city_index > -1) {
                        $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                    } else {
                        $http.post($request_urls.otherCity, {
                            'city_code' : city_code
                        }).success(function(data) {
                            if(data.code == 200) {
                                city_index = getIndexByProp($scope.local.search_list.cities, 'city_code', city_code);
                                var city = $scope.local.search_list.cities[city_index];
                                $scope.data.info.other_cities.push({
                                    'city_code' : city.city_code,
                                    'city_name' : city.city_name
                                });
                                next();
                            } else {
                                $rootScope.$emit('notify', {msg : data.msg});
                            }
                        });
                    }
                },
                deleteCb    : function(index) {
                    if(window.confirm('取消与所选城市的关联？')) {
                        var city_code = $scope.data.info.other_cities[index].city_code;
                        $http.delete($request_urls.otherCity + city_code).success(function(data) {
                            if(data.code == 200) {
                                $scope.data.info.other_cities.splice(index, 1);
                            } else {
                                $rootScope.$emit('notify', {msg : data.msg});
                            }
                        });
                    }
                }
            }
        },
        search_list  : {},
        section_head : {
            title    : '商品所属城市',
            updateCb : function() {
                if($scope[$scope.local.form_name].$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope[$scope.local.form_name].$valid) {
                    $scope.saveChanges();
                } else {
                    $rootScope.$emit('notify', {msg : '请填写必填项'});
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data.info = angular.copy($scope.$parent.result.info);
        $scope.local.search_list.cities = $scope.$parent.result.city_list.map(function(city) {
            city.select_label = city.city_name + ' ' + city.city_pinyin;

            return city;
        });
    };

    $scope.getLabel = function(arr, prop, val, label) {
        var index = getIndexByProp(arr, prop, val);
        return index > -1 && (arr[index][label] ? arr[index][label] : '');
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        $http.post($request_urls.updateBasicInfo, {
            city_code : $scope.data.info.city_code
        }).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.is_edit = false;

                $scope.$emit('setTabLoading', $scope.local.tab_path);
                cb ? cb() : $rootScope.$emit('resetDirty');
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

app.controller('InfoCityCtrl', [
    '$scope', '$rootScope', '$http', '$route', controllers.InfoCityCtrl
]);