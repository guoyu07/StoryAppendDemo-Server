var editProductBundleCtrl = function($scope, $rootScope, $route, $http) {
    $scope.data = {
        hotel_products     : {},
        contains_products  : {},
        recommend_products : {},
        groups_list        : {}
    };
    $scope.local = {
        product_id       : '',
        radio_options    : {
            bundle_type  : {
                name  : 'group_type',
                items : {
                    '2' : '套餐包含商品',
                    '3' : '独享特惠',
                    '1' : '酒店多选一'
                }
            },
            present_type : {
                name  : 'count_type',
                items : {
                    '1' : '每套配送一个',
                    '2' : '每单配送一个',
                    '3' : '每人配送一个'
                }
            }
        },
        present_type     : [
            {
                'id'   : '1',
                'name' : '每套配送一个'
            },
            {
                'id'   : '2',
                'name' : '每单配送一个'
            },
            {
                'id'   : '3',
                'name' : '每人配送一个'
            }
        ],
        product_edit_url : request_urls.edit
    };
    $scope.onlyNumbers = /^\d+$/;

    $scope.slideProductDndCallback = function(info, dst_index) {
        if(dst_index.indexOf('-') != -1) {
            var dst_info = dst_index.split('-');
            var src_info = info.srcIndex.split('-');

            if(dst_info[0] != src_info[0])
                return;

            var item = $scope.data[src_info[0]].items[src_info[1]];
            $scope.data[src_info[0]].items.splice(src_info[1], 1);
            $scope.data[dst_info[0]].items.splice(dst_info[1], 0, item);

            $scope.updateProductOrder(dst_info[0]);
        }
    };

    $scope.slideProductDndOptions = {
        selector : '.bound-product-info',
        offset   : 0
    };

    $scope.updateProductOrder = function(group_name) {
        var new_index = 1;
        for(var i = 0; i < $scope.data[group_name].items.length; i++) {
            $scope.data[group_name].items[i].display_order = new_index;
            new_index++;
        }

        $http.post(request_urls.bundleItemChangeOrder, $scope.data[group_name].items).success(function(data) {
            if(data.code != 200) {
                alert(data.msg);
            }
        });
    };

    $scope.init = function() {
        $http.get(request_urls.getBundleList).success(function(data) {
            if(data.code != 200) {
                alert(data.msg);
                return;
            }

            if(data.data.length > 0) {
                var groups_list = data.data;
                for(var i = 0; i < groups_list.length; i++) {
                    groups_list[i].binding_product_id = "";
                    groups_list[i].is_editing = false;

                    switch(groups_list[i].group_type) {
                        case '1' :
                            $scope.data.hotel_products = angular.copy(groups_list[i]);
                            break;
                        case '2' :
                            $scope.data.contains_products = angular.copy(groups_list[i]);
                            break;
                        case '3' :
                            $scope.data.recommend_products = angular.copy(groups_list[i]);
                            break;
                        default :
                            break;
                    }
                }
            }

            if(data.data.length < 3) {
                var product_id = request_urls.getBundleList.substr(request_urls.getBundleList.length - 4, 4);
                if (Object.keys($scope.data.hotel_products).length == 0) {
                    $scope.data.hotel_products = $scope.createDefaultGroups(product_id, 1);
                }
                if (Object.keys($scope.data.contains_products).length == 0) {
                    $scope.data.contains_products = $scope.createDefaultGroups(product_id, 2);
                }
                if (Object.keys($scope.data.recommend_products).length == 0) {
                    $scope.data.recommend_products = $scope.createDefaultGroups(product_id, 3);
                }
                $scope.updateGroupOrder();
            }
        });
    };

    $scope.addBundle = function(group_name) {
        var product_id = $scope.data[group_name].binding_product_id;

        if(product_id > 0) {
            for(var p in $scope.data[group_name].items) {
                if($scope.data[group_name].items[p].binding_product_id == product_id) {
                    alert("添加绑定的商品已经在此分组中，请勿重复添加。");
                    return;
                }
            }

            $http.get(request_urls.getProduct.substr(0, request_urls.getProduct.length - 4) +
                      product_id).success(function(data) {
                if(data.code == 200 && data.data) {
                    var new_bundle = {
                        bundle_id          : "",
                        binding_product_id : "",
                        discount_type      : "F",
                        discount_amount    : "0",
                        count_type         : "1",
                        count              : "1",
                        display_order      : "",
                        product            : {}
                    };
                    new_bundle.binding_product_id = product_id;
                    new_bundle.product = {
                        product_id  : product_id,
                        description : {
                            name : data.data.name
                        }
                    };
                    $scope.data[group_name].items.splice($scope.data[group_name].items.length, 0, new_bundle);
                    $scope.updateProductOrder(add_in_group);
                } else {
                    alert("商品不存在");
                }
            });
        } else {
            return;
        }


        $scope.data[group_name].binding_product_id = "";
    };

    $scope.unbindProduct = function(product_index, group_name) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        if($scope.data[group_name].items[product_index].bundle_id == "") {
            $scope.data[group_name].items.splice(product_index, 1);
            return;
        }

        var group = $scope.data[group_name];
        var url = (request_urls.deleteBundleProduct.replace("bundle_000", group.bundle_id)).replace("product_000", group.items[product_index].binding_product_id);

        $http.get(url).success(function(data) {
            if(data.code != 200) {
                alert(data.msg);
            } else {
                $scope.data[group_name].items.splice(product_index, 1);
                $scope.updateProductOrder(group_name);
            }
        });
    };

    $scope.submitGroupChange = function(group_name) {
        var group = angular.copy($scope.data[group_name]);

        if(group.items.length == 0) {
            alert("请添加绑定商品再保存");
            return;
        }

        $http.post(request_urls.saveBundle, group).success(function(data) {
            if(data.code == 200) {
                alert("保存成功");
                $scope.data[group_name].is_editing = false;
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.editGroup = function(group_name) {
        $scope.data[group_name].is_editing = true;
    };

    $scope.createDefaultGroups = function(product_id, group_type) {
        var template = {
            bundle_id          : "",
            product_id         : product_id,
            top_group_title    : "",
            top_group_alias    : "",
            group_id           : "",
            group_title        : "",
            group_type         : "",
            is_editing         : true,
            binding_product_id : "",
            items              : []
        };

        if(group_type == 1) {
            template.group_type = '1';
            template.group_title = "酒店多选一";
            return template;
        } else if(group_type == 2) {
            template.group_type = '2';
            template.group_title = "套餐包含商品";
            return template;
        } else if(group_type == 3) {
            template.group_type = '3';
            template.group_title = "独享特惠";
            return template;
        }
    };

    $scope.updateGroupOrder = function() {
        $scope.data.hotel_products.group_id = 1;
        $scope.data.contains_products.group_id = 2;
        $scope.data.recommend_products.group_id = 3;
    };

    $scope.init();
};

angular.module('ProductEditApp').controller('editProductBundleCtrl', [
    '$scope', '$rootScope', '$route', '$http', editProductBundleCtrl
]);