controllers.RedeemPlaceCtrl = function($scope, $rootScope, $route, $location, $http, $sce) {
    $scope.local = {
        tab_path      : 'redeem_place',
        form_name     : 'redeem_place_form',
        path_name     : helpers.getRouteTemplateName($route.current),
        section_head  : {
            place : {
                title    : '兑换地点',
                is_edit  : false,
                updateCb : function() {
                    if($scope[$scope.local.form_name].$valid) {
                        $scope.submitChanges();
                        $scope.local.section_head.place.is_edit = false;
                        $rootScope.$emit('resetDirty');
                    } else {
                        $rootScope.$emit('notify', {msg : '请填写必填项'});
                    }
                },
                editCb   : function() {
                    $scope.local.section_head.place.is_edit = true;
                }
            }
        },
        radio_options : {
            need_special : {
                name  : 'need_special',
                items : {
                    '0' : '不需要',
                    '1' : '需要'
                }
            }
        }
    };


    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data = $scope.$parent.result.place;
        $scope.albums = {
            special : {
                album_id        : parseInt($scope.data.pick_ticket_album_id, 10),
                album_info      : $scope.data.pick_ticket_album_info,
                pick_ticket_map : $scope.data.pick_ticket_map,
                group           : $scope.data.pt_group_info ? JSON.parse($scope.data.pt_group_info) : [],
                landinfos       : $scope.data.landinfos,
                need_album      : $scope.data.need_pick_ticket_album
            }
        };
        $scope.local_model = {
            need_special          : $scope.albums.special.need_album,
            valid_special         : $scope.albums.special.album_id,
            pick_ticket_map       : $scope.albums.special.pick_ticket_map,
            edit_pick_ticket_map  : 0,
            pick_ticket_map_id    : 'pick_ticket_map',
            points                : [],
            mapinfo               : {zoom : 10, center : []},
            link_progress_special : false
        };

        if($scope.local_model.valid_special) {
            $scope.groups = $scope.albums.special.group;
            $scope.albums.special.album_id = parseInt($scope.albums.special.album_id, 10);

            $scope.renewLands($scope.albums.special.landinfos);
        }

        $scope.local_model.actions = {save : $scope.savePickTicketMap, cancel : $scope.cancelEditPickTicketMap};
    };


    $scope.addGroup = function() {
        if(!$scope.groups) {
            $scope.groups = [];
        }

        $scope.groups.push({
            items : angular.copy($scope.items),
            title : $scope.groups.length == 0 ? '兑换地点' : ''
        });
    };

    $scope.delGroup = function(index) {
        $scope.groups.splice(index, 1);
    };

    $scope.toggleItem = function(id, items) {
        var index = items.indexOf(id.toString());
        if(index === -1) {
            items.push(id);
        } else {
            items.splice(index, 1);
        }
    };

    $scope.renewLands = function(landinfos) {
        $scope.items = landinfos.map(function(elem) {
            return elem.landinfo_id;
        });
    };

    $scope.updateSpecialAlbum = function() {
        $scope.local_model.valid_special = false;
        $scope.local_model.link_progress_special = true;

        $http.post($request_urls.updateProductPickTicketAlbum, {
            need_pick_ticket_album : $scope.local_model.need_special,
            pick_ticket_album_id   : $scope.albums.special.album_id
        }).success(function(data) {
            $scope.local_model.link_progress_special = false;
            if(data.code == 200) {
                $scope.albums.special.album_info = data.data;
                $scope.albums.special.landinfos = data.data.landinfos;
                $scope.local_model.valid_special = true;

                $scope.renewLands(data.data.landinfos);

                if(!!data.data.pt_group_info) {
                    $scope.groups = data.data.pt_group_info;
                } else {
                    $scope.groups = [
                        {
                            title : '兑换地点', items : angular.copy($scope.items)
                        }
                    ];

                    $scope.submitChanges();
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.getPickTicketMapPoints = function() {
        function getLocation(landinfo_id) {
            var location = [];
            angular.forEach($scope.albums.special.landinfos, function(value, key) {
                if(value.landinfo_id == landinfo_id) {
                    location = value.location.split(',');
                }
            });
            return location;
        }

        var points = [];
        angular.forEach($scope.groups, function(value, key) {
            angular.forEach(value.items, function(item, index) {
                var location = getLocation(item);
                if(location.length == 2) {
                    points.push(location);
                }
            });
        });
        return points;
    };

    $scope.addPickTicketMap = function() {
        $scope.local_model.points = $scope.getPickTicketMapPoints();

        $scope.local_model.edit_pick_ticket_map = 1;
    };

    $scope.editPickTicketMap = function() {
        $scope.local_model.points = $scope.getPickTicketMapPoints();
        $scope.local_model.edit_pick_ticket_map = 1;
    };

    $scope.savePickTicketMap = function() {
        // save map in server end and get the url of pick ticket map.
        $http.post($request_urls.savePickTicketMap, {
            center : $scope.local_model.mapinfo.center,
            zoom   : $scope.local_model.mapinfo.zoom,
            points : $scope.local_model.points
        }).success(function(data) {
            if(data.code == 200) {
                $scope.albums.special.pick_ticket_map = data.data.pick_ticket_map;
                $scope.local_model.pick_ticket_map = data.data.pick_ticket_map;
                $scope.local_model.edit_pick_ticket_map = 0;
            } else {
                alert(data.msg);
                $scope.local_model.edit_pick_ticket_map = 0;
            }
        });
    };

    $scope.cancelEditPickTicketMap = function() {
        $scope.local_model.edit_pick_ticket_map = 0;
    };

    $scope.submitChanges = function() {
        var message = '';
        var post_data = {
            need_pick_ticket_album : $scope.local_model.need_special.toString(),
            pick_ticket_album_id   : !!$scope.albums.special.album_id ? $scope.albums.special.album_id : "0",
            pt_group_info          : $scope.local_model.need_special == '0' ? [] : $scope.groups
        };
        for(var key in post_data.pt_group_info) {
            delete post_data.pt_group_info[key].$$hashKey; //Remove Angular Prop
        }
        post_data.pt_group_info = JSON.stringify(post_data.pt_group_info);


        if(post_data.need_pick_ticket_album == '1' && parseInt(post_data.pick_ticket_album_id, 10) == 0) {
            message += '接送点专辑ID不能为空';
        }
        if(message.length > 0) {
            $rootScope.$emit('notify', {msg : message});
        } else {
            $http.post($request_urls.savePickTicketAlbumInfoAll, post_data).success(function(data) {
                if(data.code == 200) {
                    if(data.data.landinfos) {
                        $scope.renewLands(data.data.landinfos);
                    }

                    $scope.albums.special.album_id = data.data.pick_ticket_album_info.album_id;
                    if(data.data.pick_ticket_album_info.album_id) {
                        $scope.albums.special.album_info.link = data.data.pick_ticket_album_info.link;
                        $scope.albums.special.album_info.title = data.data.pick_ticket_album_info.title;
                    }

                    try {
                        $scope.groups = JSON.parse(data.data.pt_group_info);
                    } catch(e) {
                        $scope.groups = [];
                    }
                }

                $rootScope.$emit('notify', {msg : data.msg});
            });
        }
    };
    //
    //    $scope.saveChanges = function(cb) {
    //        var post_data = {
    //            need_pick_ticket_album : $scope.local_model.need_special.toString(),
    //            pick_ticket_album_id   : !!$scope.albums.special.album_id ? $scope.albums.special.album_id : "0",
    //            pt_group_info          : $scope.local_model.need_special == '0' ? [] : $scope.groups,
    //        };
    //        $http.post($request_urls.savePickTicketAlbumInfoAll, post_data).success(function(data) {
    //            if(data.code == 200) {
    //                $scope.local.section_head.place.is_edit = false;
    //                cb ? cb() : $rootScope.$emit('resetDirty');
    //            }
    //        });
    //    };

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name &&
           dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveChanges(callback);
        }
    });


    $scope.init();
};

app.controller('RedeemPlaceCtrl', [
    '$scope', '$rootScope', '$route', '$location', '$http', '$sce',
    controllers.RedeemPlaceCtrl
]);