controllers.ServiceTourplanCtrl = function($scope, $rootScope, $http, $route) {
    $scope.data = {};
    $scope.local = {
        dnd                 : {
            options  : {
                selector : '.group-content',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.dndCallback(info, dst_index);
            }
        },
        tab_path            : 'pass_other',
        form_name           : 'service_tour_form',
        path_name           : helpers.getRouteTemplateName($route.current),
        days_list           : Array(15).join('a').split('a').map(function(val, key) {
            return key + 1;
        }),
        section_head        : {
            tour : {
                is_edit  : false,
                title    : '基本信息',
                editCb   : function() {
                    $scope.local.section_head.tour.is_edit = true;
                },
                updateCb : function() {
                    $scope.saveChanges();
                }
            }
        },
        radio_options       : {
            display_type : {
                name  : 'display_type',
                items : {
                    0 : '简单图文',
                    1 : '时间轴图文'
                }
            },
            is_online    : {
                name  : 'is_online',
                items : {
                    0 : '不上线',
                    1 : '上线'
                }
            }
        },
        uploader_options    : {
            'class'   : 'tour_plan_uploader',
            target    : $request_urls.uploadImage,
            input_id  : 'img_uploader',
            beforeCb  : function(event, item) {
                var tour_item = $scope.local.is_simple_plan ?
                                $scope.items[$scope.local.current_item_index] :
                                $scope.data.plans[$scope.local.current_plan_index].groups[$scope.local.current_group_index].items[$scope.local.current_item_index];

                item.formData = [
                    {
                        item_id : tour_item.item_id
                    }
                ];

                return item;
            },
            successCb : function(event, xhr, item, response, uploader) {
                var tour_item = $scope.local.is_simple_plan ?
                                $scope.items[$scope.local.current_item_index] :
                                $scope.data.plans[$scope.local.current_plan_index].groups[$scope.local.current_group_index].items[$scope.local.current_item_index];

                uploader.queue = [];
                tour_item.image_url = response.data;
                $scope.local.uploader_options.in_progress = false;

                $rootScope.$emit('notify', {
                    msg : response.code == 200 ? '上传成功' : '上传失败'
                });
            }
        },
        //Keep Track of Current Items
        current_plan_index  : -1,
        current_item_index  : -1,
        current_group_index : -1
    };


    //初始化
    $scope.init = function() {
        if(!$scope.$parent.result) return;

        $scope.data = $scope.$parent.result.tour;

        $scope.initData();
    };
    $scope.initData = function() {
        $scope.local.old_data = {
            total_days   : $scope.data.total_days,
            display_type : $scope.data.display_type
        };
        $scope.local.is_simple_plan = $scope.data.display_type == 0;

        if($scope.data.plans.length > 0) { // plans不为空图文初始化
            $scope.local.current_plan_index = 0;

            //排序 && 配置editing字段
            $scope.data.plans = $scope.data.plans.map(function(plan) {
                plan.groups.sort( function( a, b ) {
                              return a.display_order - b.display_order;
                            } );
                plan.groups = reOrder(plan.groups);
                plan.groups = plan.groups.map(function(group) {
                    group.editing = false;
                    group.items = group.items.map(function(item) {
                        item.editing = false;

                        return item;
                    });

                    return group;
                });

                return plan;
            });

            if($scope.local.is_simple_plan) {
                //简单图文 - 只取第一天第一个分组的内容
                if($scope.data.plans[0].groups.length && $scope.data.plans[0].groups[0].items.length) {
                    $scope.items = $scope.data.plans[0].groups[0].items;
                } else {
                    $scope.items = [];
                }
            }
        } else { //没有plans时进入编辑态
            $scope.local.section_head.tour.is_edit = true;
        }
    };

    //其他
    $scope.switchDayPlan = function(index) {
        $scope.local.current_plan_index = index;
    };

    $scope.changeTotalDays = function() {
        //新的天数比现在的少－腰斩之
        if($scope.data.total_days < $scope.data.plans.length) {
            $scope.data.plans.splice($scope.data.total_days, $scope.data.plans.length - $scope.data.total_days);
        }
        //新的天数比现在的多
        for(var i = $scope.data.plans.length; i < $scope.data.total_days; i++) {
            $scope.data.plans.push({
                title      : '',
                plan_id    : '',
                the_day    : i + 1,
                total_days : $scope.data.total_days
            });
        }
    };

    $scope.sanityCheck = function() {
        var sane = $scope.local.current_plan_index !== -1;

        if(!sane) {
            $rootScope.$emit('notify', {msg : "请先编辑基本信息！"});
        }

        return sane;
    };

    //分组
    $scope.addGroup = function(group_index) {
        if(!$scope.sanityCheck()) return;

        $http.post($request_urls.addTourPlanGroup, {
            'plan_id' : $scope.data.plans[group_index].plan_id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.plans[group_index].groups.push({
                    time     : '',
                    title    : '',
                    editing  : true,
                    group_id : data.data.group_id
                });
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.insertGroup = function(plan, group) {
        if(!$scope.sanityCheck()) return;

        $http.post($request_urls.insertTourPlanGroup, {
            'plan_id'  : plan.plan_id,
            'group_id' : group.group_id
        }).success(function(data) {
            if(data.code == 200) {
                plan.groups.push({
                    time     : '',
                    title    : '',
                    editing  : true,
                    group_id : data.data.new_group_id
                });

                //Ordering
                for(var i in data.data.groups) {
                    plan.groups[i].display_order = data.data.groups[i].display_order;
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.toggleGroup = function(group_index) {
        if(!$scope.sanityCheck()) return;

        var current_group = $scope.data.plans[$scope.local.current_plan_index].groups[group_index];

        if(current_group.editing == false) {
            current_group.editing = !current_group.editing;
        } else {
            $http.post($request_urls.updateTourPlanGroup, current_group).success(function(data) {
                if(data.code == 200) {
                    current_group.editing = !current_group.editing;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };
    $scope.deleteGroup = function(group_index) {
        if(!$scope.sanityCheck() || !window.confirm('删除后数据不可恢复。\n点击确定来删除。')) return;

        var current_plan = $scope.data.plans[$scope.local.current_plan_index];

        $http.post($request_urls.deleteGroup + current_plan.groups[group_index].group_id).success(function(data) {
            if(data.code == 200) {
                current_plan.groups.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //图文项
    $scope.addItem = function(group_index) {
        if(!$scope.sanityCheck()) return;

        var current_plan = $scope.data.plans[$scope.local.current_plan_index];
        var post_data;

        if($scope.local.is_simple_plan) {
            post_data = {'plan_id' : current_plan.plan_id};
        } else {
            post_data = {'group_id' : current_plan.groups[group_index].group_id};
        }

        $http.post($request_urls.addTourPlanItem, post_data).success(function(data) {
            if(data.code == 200) {
                if($scope.local.is_simple_plan) {
                    $scope.items.push({
                        editing : true,
                        item_id : data.data.item_id
                    });
                } else {
                    current_plan.groups[group_index].items = current_plan.groups[group_index].items || [];
                    current_plan.groups[group_index].items.push({
                        editing : true,
                        item_id : data.data.item_id
                    });
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.toggleItem = function(group_index, item_index) {
        if(!$scope.sanityCheck()) return;

        var current_group = $scope.data.plans[$scope.local.current_plan_index].groups[group_index];
        var current_item = $scope.local.is_simple_plan ? $scope.items[item_index] : current_group.items[item_index];

        if(current_item.editing == false) {
            current_item.editing = !current_item.editing;
        } else {
            $http.post($request_urls.updateTourPlanItem, current_item).success(function(data) {
                if(data.code == 200) {
                    current_item.editing = !current_item.editing;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };
    $scope.deleteItem = function(group_index, item_index) {
        if(!$scope.sanityCheck() || !window.confirm('删除后数据不可恢复。\n点击确定来删除。')) return;

        var current_group = $scope.data.plans[$scope.local.current_plan_index].groups[group_index];
        var current_item = $scope.local.is_simple_plan ? $scope.items[item_index] : current_group.items[item_index];

        $http.post($request_urls.deleteItem + current_item.item_id).success(function(data) {
            if(data.code == 200) {
                if($scope.local.is_simple_plan) {
                    $scope.items.splice(item_index, 1);
                } else {
                    current_group.items.splice(item_index, 1);
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //图片
    $scope.changeImage = function(group_index, item_index) {
        $scope.local.current_item_index = item_index;
        $scope.local.current_group_index = group_index;
        $('#' + $scope.local.uploader_options.input_id).trigger('click');
    };
    $scope.deleteImage = function(group_index, item_index) {
        if(!window.confirm('是否删除图片？')) return;

        var current_group = $scope.data.plans[$scope.local.current_plan_index].groups[group_index];
        var current_item = $scope.local.is_simple_plan ? $scope.items[item_index] : current_group.items[item_index];

        $http.post($request_urls.deleteImage + current_item.item_id).success(function(data) {
            if(data.code == 200) {
                current_item.image_url = '';
                $rootScope.$emit('notify', {msg : "删除图片成功！"});
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //顺序
    $scope.updateOrder = function() {
        var item_order = [], current_plan, items_len;

        if($scope.local.is_simple_plan) {
            items_len = $scope.items.length;
            for(var m = 0; m < items_len; m++) {
                item_order.push({
                    item_id       : $scope.items[m].item_id,
                    group_id      : $scope.items[m].group_id,
                    display_order : m + 1
                });
            }
        } else {
            current_plan = $scope.data.plans[$scope.local.current_plan_index];
            for(var i = 0; i < current_plan.groups.length; i++) {
                items_len = current_plan.groups[i].items.length;
                for(var j = 0; j < items_len; j++) {
                    item_order.push({
                        item_id       : current_plan.groups[i].items[j].item_id,
                        group_id      : current_plan.groups[i].items[j].group_id,
                        display_order : j + 1
                    });
                }
            }
        }

        if(item_order.length > 0) {
            $http.post($request_urls.updateItemsOrder, item_order).success(function(data) {
                if(data.code != 200) {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };
    $scope.dndCallback = function(info, dst_index) {
        if(angular.isNumber(dst_index) || dst_index.indexOf('-') == -1) { // without group
            $scope.items.splice(info.src_index, 1);
            $scope.items.splice(dst_index, 0, info.src_item);
        } else {
            var dst_parts = dst_index.split('-');
            var src_parts = info.src_index.split('-');

            var src_group_index = src_parts[0], src_item_index = src_parts[1];
            var dst_group_index = dst_parts[0], dst_item_index = dst_parts[1];

            var current_plan = $scope.data.plans[$scope.local.current_plan_index];
            var source_group = current_plan.groups[src_group_index];
            var destination_group = current_plan.groups[dst_group_index];

            source_group.items.splice(src_item_index, 1);
            info.src_item.group_id = destination_group.group_id;
            destination_group.items.splice(dst_item_index, 0, info.src_item);
        }

        $scope.updateOrder();
    };

    //保存
    $scope.saveChanges = function(cb) {
        if(!$scope.data.cn_schedule || $scope.data.cn_schedule.trim().length == 0) {
            $rootScope.$emit('notify', {msg : "请输入标题!"});
            return;
        }
        if($scope.local.old_data.total_days != $scope.data.total_days ||
           $scope.local.old_data.display_type != $scope.data.display_type) {
            if(!window.confirm('改变显示样式或减少游玩天数会清除部分原有录入图文内容，是否继续？')) return;
        }

        $scope.$emit('setTabLoading', $scope.local.tab_path);

        $scope.local.is_simple_plan = $scope.data.display_type == 0;

        //更换天数
        if($scope.local.is_simple_plan) {
            $scope.data.total_days = 0;
        } else {
            if($scope.data.total_days == 0) {
                $rootScope.$emit('notify', {msg : "请选择游玩天数!"});
                return;
            }
        }
        //如果是从简单图文转换成时间轴
        if(!$scope.local.is_simple_plan && $scope.local.old_data.display_type == 0) {
            $scope.data.plans[0].the_day = 1;
            $scope.data.plans[0].total_days = $scope.data.total_days;
        }

        $http.post($request_urls.addTourPlan, {
            plans       : $scope.data.plans,
            is_online   : $scope.data.is_online,
            total_days  : $scope.data.total_days,
            cn_schedule : $scope.data.cn_schedule
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data = data.data;
                $scope.initData();

                $scope.local.section_head.tour.is_edit = false;
                $scope.$emit('setTabLoading', $scope.local.tab_path);
                cb ? cb() : $rootScope.$emit('resetDirty');
            } else if(data.code == 401) {
                window.location.reload();
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

app.controller('ServiceTourplanCtrl', [
    '$scope', '$rootScope', '$http', '$route',
    controllers.ServiceTourplanCtrl
]);