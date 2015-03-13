controllers.InfoLocationCtrl = function($scope, $rootScope, $http, $route) {
    $scope.data = {};
    $scope.local = {
        edit_group : true,
        dnd               : {
            options  : {
                selector : '.location-block',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.locations.splice(info.src_index, 1); //Remove item
                $scope.data.locations.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateLocationsOrder();
            }
        },
    };
    $scope.latLng = /^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/;

    $scope.init = function() {
        if(!$scope.$parent.result) return;
        $scope.data.locations = angular.copy($scope.$parent.result.locations);
        for(var index in $scope.data.locations){
            $scope.data.locations[index].edit = false;
        }
    };

    $scope.addLocation = function() {
        var display_order = $scope.data.locations.length + 1;
        $http.post($request_urls.productSightseeing, {id : '', display_order : display_order}).success(function(data) {
            if(data.code == 200) {
                var new_location = data.data;
                new_location.edit = false;
                $scope.data.locations.push(new_location);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteLocation = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var location_id = $scope.data.locations[index].id;
        $http.delete($request_urls.productSightseeing + '&id=' + location_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.locations.splice(index,1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.toggleGroupEdit = function(index) {
        var current_location = $scope.data.locations[index];

        if(current_location.edit == false) {
            $scope.data.locations[index].edit = true;
        } else if(!current_location.latlng) {
            $rootScope.$emit('notify', {msg : '请正确输入景点坐标'});
        } else {
            $http.post($request_urls.productSightseeing, current_location).success(function(data) {
                if(data.code == 200) {
                    $scope.data.locations[index].edit = false;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }

    };

    $scope.updateLocationsOrder = function() {
        var order_info = [];
        for(var key in $scope.data.locations) {
            order_info.push({
                id      : $scope.data.locations[key].id,
                display_order : (parseInt(key,10) + 1)
            });
        }

        $http.post($request_urls.sightseeingDisplayOrder, order_info).success(function(data) {
            if(data.code == 200) {
                for(var current_index in $scope.data.locations) {
                    var current_id = $scope.data.locations[current_index].id;
                    var order_index = getIndexByProp(order_info, 'id', current_id);
                    $scope.data.locations[current_index].display_order = order_info[order_index].display_order;
                }
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

app.controller('InfoLocationCtrl', [
    '$scope', '$rootScope', '$http', '$route', controllers.InfoLocationCtrl
]);