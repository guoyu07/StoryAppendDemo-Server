controllers.priceSpecialCodeCtrl = function($scope, $rootScope, $route, $http, $q, $sce, $location) {
    //region Model Initialization
    var template = {
        group : {
            status        : '1',
            is_new        : true,
            group_id      : '',
            cn_title      : '',
            en_title      : '',
            display_order : '',
            special_items : []
        },
        item  : {
            status               : '1',
            cn_name              : '',
            en_name              : '',
            group_id             : '',
            description          : '',
            special_code         : '',
            display_order        : '',
            mapping_product_id   : '',
            product_origin_name  : '',
            mapping_special_code : '',
            item_limit           : {
                min_pax_num   : '',
                max_pax_num   : '',
                limit_pax_num : '0'
            }
        },
        limit : {
            min_pax_num   : '',
            max_pax_num   : '',
            limit_pax_num : '0'
        }
    };
    $scope.data = {};
    $scope.local = {
        dnd           : {
            options  : {
                selector : '.one-special-code-group',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.groups.splice(info.src_index, 1); //Remove item
                $scope.data.groups.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateGroupsOrder();
            }
        },
        package       : {},
        overlay       : {
            item        : {},
            item_index  : -1,
            group_index : -1,
            has_overlay : false
        },
        tab_path      : 'special_code',
        form_name     : 'price_special_code_form',
        path_name     : helpers.getRouteTemplateName($route.current),
        status_map    : {
            '0' : {
                label        : '已禁用',
                'class'      : 'disable',
                action_label : '禁用',
                toggle_label : '启用'
            },
            '1' : {
                label        : '已启用',
                'class'      : 'enable',
                action_label : '启用',
                toggle_label : '禁用'
            }
        },
        section_head  : {
            special_code : {
                title    : '',
                editCb   : function() {
                    if($scope.$parent.isEditable()) {
                        $scope.local.section_head.special_code.is_edit = true;
                    }
                },
                updateCb : function() {
                    $scope.saveGroupChanges();
                }
            }
        },
        radio_options : {
            limit_pax_num : {
                name  : 'limit_pax_num',
                items : {
                    '0' : '不需要填写',
                    '1' : '需要填写'
                }
            }
        },
        is_hotel_plus : false
    };
    //endregion


    $scope.init = function() {
        if(!$scope.$parent.result || Object.keys($scope.$parent.result).length == 0) return;

        $scope.data = angular.copy($scope.$parent.result.special_groups);
        $scope.status_index = {};

        $scope.local.is_package = $rootScope.product.type == 8;
        $scope.local.is_charter = $rootScope.product.type == 10;
        $scope.local.is_cpic = $rootScope.product.supplier_id == 89;

        if(!$scope.data.groups) {
            $scope.data.groups = [];
        } else {
            $scope.data.groups = $scope.data.groups.map(function(one_group, group_index) {
                formatOneGroup(one_group, group_index);
                one_group.special_items = one_group.special_items.map(function(one_item, index) {
                    one_item.display_order = index + 1; //保证顺序数字正确
                    one_item = formatPaxNum(one_item);

                    return one_item;
                });

                return one_group;
            });
        }

        $scope.initSectionTitle();

        $scope.local.is_hotel_plus = $root_product.type == 7;

        if($scope.local.is_package) {
            $scope.initPackageSpecial();
        }
    };

    function formatOneGroup(one_group, group_index) {
        $scope.status_index[one_group.group_id] = {
            status      : one_group.status,
            item_status : {}
        };

        one_group.special_items.forEach(function(one_item, item_index) {
            $scope.status_index[one_group.group_id].item_status[one_item.special_code] = one_item.status;

            var current_group = $scope.data.groups[group_index];
            var current_item = current_group.special_items[item_index];
            current_item.special_code = one_item.special_code;

            if($scope.local.is_charter && !current_item.item_limit) {
                current_item.item_limit = angular.copy(template.limit);
                current_item.item_limit.group_id = current_group.group_id;
            }
        });
    }

    function formatPaxNum(one_item) {
        if(one_item.item_limit) {
            one_item.item_limit.min_pax_num = +one_item.item_limit.min_pax_num;
            one_item.item_limit.max_pax_num = +one_item.item_limit.max_pax_num;
        }

        return one_item;
    }

    function charterCheck() {
        var result, group_items_disabled, group_items_status;
        var has_enabled = false,
            has_disabled = false,
            has_missing_limit = false,
            has_disabled_group_items = false;
        result = {
            redirect       : false,
            allow_continue : true
        };

        $scope.data.groups = $scope.data.groups.map(function(one_group, group_index) {
            if($scope.status_index.hasOwnProperty(one_group.group_id)) {
                group_items_status = [];
                group_items_disabled = [];

                if($scope.status_index[one_group.group_id].status == '0' && one_group.status == '1') { //From disabled to enabled
                    has_enabled = true;
                } else if($scope.status_index[one_group.group_id].status == '1' && one_group.status == '0') { //From enabled to disabled
                    has_disabled = true;
                }

                one_group.special_items = one_group.special_items.map(function(one_item) {
                    if(group_index != 0) {
                        one_item.item_limit = false;
                    } else {
                        if(!one_item.item_limit && one_item.status == '1' && !has_missing_limit) {
                            has_missing_limit = true;
                        }
                    }

                    if($scope.status_index[one_group.group_id].item_status.hasOwnProperty(one_item.special_code)) {
                        group_items_status.push(one_item.status);
                        if($scope.status_index[one_group.group_id].item_status[one_item.special_code] !=
                           one_item.status) {
                            group_items_disabled.push(one_item.status);
                        }
                        if($scope.status_index[one_group.group_id].item_status[one_item.special_code] == '0' &&
                           one_item.status == '1') {
                            has_enabled = true;
                        }
                    }

                    return one_item;
                });

                if(one_group.status == '1') {
                    //如果分组的special code有更变，而且里面全是禁用的special code；则提示更新计划
                    if(!has_disabled_group_items) {
                        has_disabled_group_items = group_items_disabled.length > 0 &&
                                                   group_items_disabled.indexOf('1') == -1;
                    }
                    //如果分组没有special code或者所有special code都是禁用状态，则把分组禁用
                    if(one_group.special_items.length == 0 || group_items_status.indexOf('1') == -1) {
                        one_group.status = '0';
                    }
                }
            }

            return one_group;
        });

        if(has_enabled) {
            result.redirect = true;
            $rootScope.$emit('notify', {msg : '你已经启用之前禁用的special code／group，请在保存后更新价格计划。'});
        }
        if(has_disabled) {
            result.redirect = true;
            $rootScope.$emit('notify', {msg : '你已经禁用之前启用的special group，价格计划将会被清空，请在保存后重新编写价格计划。'});
        }
        if(has_disabled_group_items) {
            result.redirect = true;
            $rootScope.$emit('notify', {msg : '你已经禁用某个分组的所有special code，价格计划将会被清空，请在保存后重新编写价格计划。'});
        }
        if(has_missing_limit) {
            result.allow_continue = false;
            $rootScope.$emit('notify', {msg : '第一组special缺少人数限制，请填写后再保存。'});
        }

        return result;
    }

    $scope.initSectionTitle = function(use_sce) {
        if($scope.local.is_charter) {
            $scope.local.section_head.special_code.title = 'Special Code Group';
        } else {
            if($scope.data.has_special == '1') {
                $scope.local.section_head.special_code.title = $scope.data.groups[0].cn_title + " (" +
                                                               $scope.data.groups[0].en_title + ")";
            } else {
                $scope.local.section_head.special_code.title = 'Special Code';
            }

            if(use_sce) {
                $scope.local.section_head.special_code.title = $sce.trustAsHtml($scope.local.section_head.special_code.title);
            }
        }
    };

    $scope.isEditable = function() {
        return $scope.$parent.isEditable() && $scope.local.section_head.special_code.is_edit;
    };

    //region Package
    $scope.initPackageSpecial = function() {
        $http.get($request_urls.getBundleHotelSpecial).success(function(data) {
            if(data.code == 200) {
                $scope.local.package = angular.copy(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.onSelectHotel = function() {
        var hotel_special = $scope.local.package.special_code[$scope.local.overlay.item.mapping_product_id];

        if(!hotel_special.need_special_code || (hotel_special.need_special_code &&
                                                !($scope.local.overlay.item.mapping_special_code in
                                                  hotel_special.special_codes))) {
            $scope.local.overlay.item.mapping_special_code = '';
        }
    };

    $scope.showHotelRoom = function() {
        if($scope.local.package.special_code && $scope.local.overlay.item.mapping_product_id) {
            return $scope.local.package.special_code[$scope.local.overlay.item.mapping_product_id].need_special_code;
        }
    };
    //endregion

    //region Charter Bus
    $scope.showCharterPassengerLimit = function() {
        return $scope.local.is_charter && $scope.local.overlay.group_index == 0;
    };
    //endregion

    //region Group
    $scope.addGroup = function() {
        if(!$scope.isEditable()) return;

        var new_group = angular.copy(template.group);
        new_group.display_order = $scope.data.groups.length;
        $scope.data.groups.push(new_group);

        $scope[$scope.local.form_name].$pristine = false;
    };

    $scope.canAddGroup = function() {
        return $scope.local.section_head.special_code.is_edit &&
               ($scope.data.groups.length == 0 || $rootScope.product.type == 10);
    };

    $scope.deleteGroup = function(index) {
        $scope.data.groups.splice(index, 1);
    };

    $scope.updateGroupsOrder = function() {
        reOrder($scope.data.groups);
        $http.post($request_urls.productSpecialGroupOrder, $scope.data.groups).success(function(data) {
            if(data.code == 200) {
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.setGroupStatus = function(group_index, status_id) {
        $scope.data.groups[group_index].status = status_id;
    };

    $scope.saveGroupChanges = function(cb) {
        if(!$scope.isEditable()) return;

        if($scope.local.is_charter) {
            var check_result = charterCheck();
            if(!check_result.allow_continue) return;
        }

        $http.post($request_urls.productSpecialGroup, $scope.data.groups).success(function(data) {
            if(data.code == 200) {
                $scope[$scope.local.form_name].$pristine = true;
                $scope.local.section_head.special_code.is_edit = false;
                if(!$scope.local.is_charter) {
                    $scope.initSectionTitle(true);
                }

                data.data.forEach(function(one_group, index) {
                    $scope.data.groups[index].group_id = one_group.group_id;
                    formatOneGroup(one_group, index);
                });

                cb && cb();

                if(check_result.redirect) {
                    $location.path('/ProductPrice/price_plan_list');
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //endregion

    //region Group Item
    $scope.canAddItem = function(group_index) {
        if($scope.local.section_head.special_code.is_edit) {
            return $scope.data.groups[group_index].is_new ||
                   (!$scope.data.groups[group_index].is_new && group_index > -1);
        }
        return false;
    };

    function setOverlayItem(item_index, group_index, item) {
        if(item_index == -1 && item) {
            $scope.local.overlay.item = angular.copy(item);
            $scope.local.overlay.item_index = $scope.data.groups[group_index].special_items.length;
        } else {
            $scope.local.overlay.item = angular.copy($scope.data.groups[group_index].special_items[item_index]);
            $scope.local.overlay.item_index = item_index;
        }

        $scope.local.overlay.group_index = group_index;

        $scope.toggleEditItem(true);
    }

    $scope.addItem = function(group_index) {
        if(!$scope.isEditable()) return;

        var item = angular.copy(template.item);
        item.group_id = $scope.data.groups[group_index].group_id;
        item.display_order = $scope.data.groups[group_index].special_items.length + 1;
        item.item_limit.group_id = $scope.data.groups[group_index].group_id;

        setOverlayItem(-1, group_index, item);
    };

    $scope.editItem = function(group_index, item_index) {
        if(!$scope.isEditable()) return;

        setOverlayItem(item_index, group_index);
    };

    $scope.toggleEditItem = function(start_edit, confirm_save) {
        if(start_edit) {
            $scope.local.overlay.has_overlay = true;
        } else if(confirm_save) {
            if($scope[$scope.local.form_name].$invalid) {
                $rootScope.$emit('notify', {msg : '请修正后再提交'});
                return;
            }
            $scope.local.overlay.has_overlay = false;
            $scope[$scope.local.form_name].$pristine = false;

            $scope.data.groups[$scope.local.overlay.group_index].special_items[$scope.local.overlay.item_index] = angular.copy(formatPaxNum($scope.local.overlay.item));
        } else {
            $scope.local.overlay.has_overlay = false;
        }
    };

    $scope.toggleItemStatus = function(group_index, item_index) {
        var current_group = $scope.data.groups[group_index];
        var current_item = current_group.special_items[item_index];

        var new_status = current_item.status == '1' ? '0' : '1';
        current_item.status = current_item.status == '1' ? '0' : '1';

        current_item.status = new_status;
    };

    $scope.updateItemsOrder = function(group_index, item_index) {
        if(!$scope.isEditable()) return;

        var current_group = $scope.data.groups[group_index];
        var current_item = current_group.special_items[item_index];
        var old_index = item_index;
        var new_index = current_item.display_order;

        var min = 1, max = current_group.special_items.length;

        if(new_index >= min && new_index <= max) { //合法操作
            var tmp_set = current_group.special_items.splice(old_index, 1); //Delete from origin position
            current_group.special_items.splice(new_index - 1, 0, tmp_set[0]); //Insert at new position

            $scope[$scope.local.form_name].$pristine = false;
            reOrder(current_group.special_items, true);
        } else { //值不合法，恢复原来的值
            current_item.display_order = +old_index + 1;
            $rootScope.$emit('notify', {
                msg : "不能大于最大值" + max
            });
        }
    };
    //endregion

    $rootScope.$on('clearDirty', function(e, dirty_info, callback) {
        if(dirty_info && dirty_info.path_name == $scope.local.path_name &&
           dirty_info.dirty_forms[$scope.local.form_name]) {
            $scope.saveGroupChanges(callback);
        }
    });


    $scope.init();
};

app.controller('priceSpecialCodeCtrl', [
    '$scope', '$rootScope', '$route', '$http', '$q', '$sce', '$location', controllers.priceSpecialCodeCtrl
]);