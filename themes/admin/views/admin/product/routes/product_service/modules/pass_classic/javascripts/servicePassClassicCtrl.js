controllers.ServicePassClassicCtrl = function($scope, $rootScope, $route, $http) {
    $scope.data = {};
    $scope.local = {
        tab_path       : 'pass_classic',
        form_name      : 'service_pass_classic_form',
        path_name      : helpers.getRouteTemplateName($route.current),
        valid_land     : false,
        album_map_id   : 'album_map',
        album_points   : [],
        album_mapinfo  : {zoom : 10, center : []},
        album_actions  : {
            save   : function() {
                $scope.saveAlbumMap();
            },
            cancel : function() {
                $scope.local.edit_album_map = false;
            }
        },
        linking_album  : false,
        edit_album_map : false,
        section_head   : {
            album : {
                title    : '景点关联',
                updateCb : function() {
                    if($scope[$scope.local.form_name].$pristine) {
                        $scope.local.section_head.album.is_edit = false;
                    } else if($scope[$scope.local.form_name].$valid) {
                        $scope.saveChanges();
                    } else {
                        $rootScope.$emit('notify', {msg : '请填写必填项'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.album.is_edit = true;
                }
            }
        },
        radio_options  : {
            need_album : {
                name  : 'need_album',
                items : {
                    '0' : '不需要',
                    '1' : '需要'
                }
            }
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data.album = $scope.$parent.result.album;
        $scope.data.album.album_id = +$scope.data.album.album_id;
        $scope.local.valid_land = !!$scope.data.album.album_info;
    };

    $scope.updateLandAlbum = function() {
        $scope.local.valid_land = false;
        $scope.local.linking_album = true;
        $http.post($request_urls.updateProductAlbum, {
            album_id   : $scope.data.album.album_id,
            need_album : $scope.data.album.need_album
        }).success(function(data) {
            $scope.local.linking_album = false;
            if(data.code == 200) {
                $scope.data.album.album_info = data.data;
                $scope.local.valid_land = true;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.getAlbumMapPoints = function() {
        $scope.local.album_points = [];
        $scope.data.album.album_points.map(function(point) {
            var location = point.split(',');
            if(location.length == 2) {
                $scope.local.album_points.push(location);
            }
        });
    };

    $scope.editAlbumMap = function() {
        $scope.getAlbumMapPoints();
        $scope.local.edit_album_map = true;
    };

    $scope.saveAlbumMap = function() {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        $http.post($request_urls.saveAlbumMap, {
            zoom   : $scope.local.album_mapinfo.zoom,
            center : $scope.local.album_mapinfo.center,
            points : $scope.local.album_points
        }).success(function(data) {
            $scope.local.edit_album_map = false;

            if(data.code == 200) {
                $scope.$emit('setTabLoading', $scope.local.tab_path);
                $scope.data.album.album_map = data.data.album_map;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.saveChanges = function(cb) {
        $scope.$emit('setTabLoading', $scope.local.tab_path);

        var post_data = {
            album_id          : $scope.data.album.album_id || '0',
            need_album        : $scope.data.album.need_album,
            album_name        : $scope.data.album.album_name,
            landinfo_md       : $scope.data.album.landinfo_md,
            landinfo_md_title : $scope.data.album.landinfo_md_title
        };

        if(post_data.need_album == '1' && post_data.album_id == 0) {
            $rootScope.$emit('notify', {msg: '景点专辑ID不能为空'});
        }

        $http.post($request_urls.saveAlbumInfoAll, post_data).success(function(data) {
            if(data.code == 200) {
                if(data.data.landinfos) {
                    $scope.renewLands(data.data.landinfos);
                }

                $scope.data.album.album_id = data.data.album_info.album_id;
                $scope.data.album.album_info = data.data.album_info;

                $scope.local.section_head.album.is_edit = false;
                $scope[$scope.local.form_name].$pristine = true;
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

app.controller('ServicePassClassicCtrl', [
    '$scope', '$rootScope', '$route', '$http',
    controllers.ServicePassClassicCtrl
]);