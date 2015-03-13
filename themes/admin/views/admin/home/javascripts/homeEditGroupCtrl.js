controllers.HomeEditGroupCtrl = function($scope, $rootScope, $http, commonFactory) {
    var confirmed = false;
    $scope.data = {
        home_group : {
            type : ''
        },
        items      : [],
        cities     : []
    };
    $scope.local = {
        search_pid         : '',
        selected_city      : '',
        search_in_progress : false,
        uploader           : {
            cover : {
                target    : $request_urls.updateHomeGroupItemImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            id : -1
                        }
                    ];
                }
            }
        },
        item_dnd           : {
            options  : {
                selector : '.carousel-image',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.items.splice(info.src_index, 1); //Remove item
                $scope.data.items.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateItemOrder();
            }
        },
        section_head       : {
            title    : '分组基本信息',
            is_edit  : false,
            updateCb : function() {
                $scope.local.section_head.is_edit = false;
                $scope.updateGroupInfo();
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        radio_switch       : {
            type : {
                name  : 'type',
                items : {
                    '1' : '商品',
                    '2' : '城市',
                    '3' : '活动商品'
                }
            }
        }
    };

    function getItemUploaderOptions(item, type) {
        if(type == 1 || type == 3) {
            //Editing option for product group
            item.editing = false;
        }

        //Uploader functionality for any group
        item.input_id = randomStr();

        //Uploader options for each item
        item.options = {
            target       : $request_urls.updateHomeGroupItemImage,
            input_id     : item.input_id,
            image_url    : item.cover_url,
            show_overlay : false,
            beforeCb     : function(event, i) {
                i.formData = [
                    {
                        id : item.id
                    }
                ];
            },
            triggerCb    : function() {
                return false;
            }
        };

        return item;
    }

    $scope.init = function() {
        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.data.cities = angular.copy(data);
        });
        $http.get($request_urls.fetchHomeGroup).success(function(data) {
            $rootScope.$emit('setBreadcrumb', {
                back : {},
                body : {
                    content : '首页分组编辑'
                }
            });
            if(data.code == 200) {
                $rootScope.$emit('loadStatus', false);
                //Group Info Data
                $scope.data.home_group = data.data.home_group;
                $scope.local.uploader.cover.image_url = $scope.data.home_group.cover_url;

                //Items
                $scope.data.items = data.data.items.map(function(elem) {
                    return getItemUploaderOptions(elem, $scope.data.home_group.type);
                });
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupInfo = function() {
        $http.post($request_urls.updateHomeGroup, $scope.data.home_group).success(function(data) {
            if(data.code == 200) {
                $scope.data.home_group = data.data;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.addItem = function() {
        var search_term = $scope.data.home_group.type == '1' || $scope.data.home_group.type == '3' ?
                          $scope.local.search_pid :
                          $scope.local.selected_city;

        if(search_term.trim().length > 0) {
            $scope.local.search_in_progress = true;

            $http.post($request_urls.addHomeGroupItem, {
                qs : search_term
            }).success(function(data) {
                $scope.local.search_in_progress = false;
                if(data.code == 200) {
                    var item = getItemUploaderOptions(data.data, $scope.data.home_group.type);

                    $scope.data.items.unshift(item);

                    if($scope.data.home_group.type == '1' ||
                       $scope.data.home_group.type == '3') $scope.local.search_pid = '';
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        } else {
            $rootScope.$emit('notify', {msg : '输入不能为空'});
        }
    };
    $scope.deleteItem = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        $http.post($request_urls.deleteHomeGroupItem, {
            id : $scope.data.items[index].id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.items.splice(index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateItemInfo = function(index) {
        $http.post($request_urls.updateHomeGroupItem, $scope.data.items[index]).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateItemOrder = function() {
        var order_info = [];
        for(var key in $scope.data.items) {
            order_info.push({
                id            : $scope.data.items[key].id,
                display_order : key
            });
        }

        $http.post($request_urls.updateHomeGroupItemOrder, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.toggleItemState = function(index) {
        if($scope.data.items[index].editing) {
            $scope.updateItemInfo(index);
        }
        $scope.data.items[index].editing = !$scope.data.items[index].editing;
    };
    $scope.triggerItemImageChange = function(item) {
        $('#' + item.options.input_id).trigger('click');
    };

    $scope.init();
    $scope.$watch('data.home_group.type', function(new_type, old_type) {
        if(!old_type || new_type == old_type) {
            return;
        }

        if(!confirmed) {
            if(window.confirm('更改内容类型会清除已有条目。确认更改?')) {
                confirmed = false;

                $http.post($request_urls.updateHomeGroupType, {
                    type : new_type
                }).success(function(data) {
                    if(data.code == 200) {
                        $scope.data.items = [];
                        $scope.data.home_group.type = new_type;
                    } else {
                        $rootScope.$emit('notify', {msg : data.msg});
                    }
                });
            } else {
                confirmed = true;

                $scope.data.home_group.type = old_type; //Triggered watch again
            }
        } else {
            confirmed = false; //To avoid confirmation box once cancel trigger
        }
    });
};

app.controller('HomeEditGroupCtrl', ['$scope', '$rootScope', '$http', 'commonFactory', controllers.HomeEditGroupCtrl]);