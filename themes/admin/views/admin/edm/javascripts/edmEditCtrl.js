controllers.EDMEditCtrl = function($scope, $http, $rootScope) {
    $scope.data = {
        base : {
            title       : '',
            small_title : '',
            title_link  : '',
            description : ''
        }
    };
    $scope.local = {
        group_edit       : false,
        product_input    : '',
        current_group    : '-1',
        add_in_progress  : false,
        dnd              : {
            group   : {
                options  : {
                    selector : '.group-list-container .one-block',
                    offset   : 0
                },
                callback : function(info, dst_index) {
                    $scope.data.groups.splice(info.src_index, 1); //Remove item
                    $scope.data.groups.splice(dst_index, 0, info.src_item); //Insert item
                    $scope.updateGroupOrder();
                }
            },
            product : {
                options  : {
                    selector : '.group-edit-container .carousel-image',
                    offset   : 0
                },
                callback : function(info, dst_index) {
                    //Remove item
                    $scope.data.groups[$scope.local.current_group].group_products.splice(info.src_index, 1);
                    //Insert item
                    $scope.data.groups[$scope.local.current_group].group_products.splice(dst_index, 0, info.src_item);
                    $scope.updateGroupProductOrder();
                }
            }
        },
        section_head     : {
            title    : '基本信息',
            is_edit  : false,
            updateCb : function() {
                if($scope.edm_info.$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope.edm_info.$valid) {
                    $scope.updateBaseInfo();
                } else {
                    $rootScope.$emit('notify', {msg : 'EDM内容有误。请检查完再提交'});
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        uploader_options : {
            cover : {
                target    : $request_urls.updateCoverImage,
                image_url : ''
            }
        }
    };

    $scope.init = function() {
        $rootScope.$emit('setBreadcrumb', {
            back : {},
            body : {
                content : '编辑EDM模版'
            }
        });

        $http.get($request_urls.getEdmDetail).success(function(data) {
            if(data.code == 200) {
                if(!data.data.title) {
                    $scope.local.section_head.is_edit = true;
                }

                $scope.data.base.title = data.data.title;
                $scope.data.base.title_link = data.data.title_link;
                $scope.data.base.small_title = data.data.small_title;
                $scope.data.base.date_update = data.data.date_update;
                $scope.data.base.description = data.data.description;
                $scope.local.uploader_options.cover.image_url = data.data.banner_image;

                for(var i = 0, len1 = data.data.groups.length; i < len1; i++) {
                    for(var j = 0, len2 = data.data.groups[i].group_products.length; j < len2; j++) {
                        data.data.groups[i].group_products[j] = processProduct(data.data.groups[i].group_products[j], data.data.groups[i].group_id);
                    }
                }

                $scope.data.groups = data.data.groups;

                $rootScope.$emit('loadStatus', false);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //Base Info
    $scope.updateBaseInfo = function() {
        $http.post($request_urls.updateBaseInfo, $scope.data.base).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.preview = function() {
        window.open($request_urls.previewEdm, '_blank');
    };

    //Group Action
    $scope.addGroup = function() {
        $http.post($request_urls.addGroup, {
            display_order : $scope.data.groups.length
        }).success(function(data) {
            if(data.code == 200) {
                data.data.group_products = [];
                $scope.data.groups.push(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteGroup = function(group_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var group_id = $scope.data.groups[group_index].group_id;
        $http.delete($request_urls.deleteGroup + group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupInfo = function(cb) {
        $http.post($request_urls.updateGroup, {
            group_id    : $scope.data.groups[$scope.local.current_group].group_id,
            title       : $scope.data.groups[$scope.local.current_group].title,
            title_link  : $scope.data.groups[$scope.local.current_group].title_link,
            display_order:$scope.data.groups[$scope.local.current_group].display_order
        }).success(function(data) {
            if(data.code == 200) {
                cb && cb();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupOrder = function() {
        var order_info = [];
        for(var key in $scope.data.groups) {
            order_info.push({
                group_id      : $scope.data.groups[key].group_id,
                display_order : key
            });
        }

        $http.post($request_urls.updateGroupOrder, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.toggleGroupEdit = function() {
        if($scope.local.group_edit) {
            $scope.updateGroupInfo(function() {
                $scope.local.group_edit = false;
            });
        } else {
            $scope.local.group_edit = true;
        }
    };

    $scope.setCurrentGroup = function(index) {
        $scope.local.current_group = index;

        var watershed = $('#group_watershed').offset().top;
        if($(window).scrollTop() < watershed) {
            $(window).scrollTop(watershed);
        }
    };

    //Product Action
    function processProduct(product, group_id) {
        product.edit = false;

        product.product_image = product.product_image && product.product_image.indexOf('qiniu') > -1 ?
                                product.product_image + '?imageView2/5/w/580/h/250' : product.product_image;

        //Uploader functionality for any group
        product.input_id = randomStr();

        //Uploader options for each item
        product.options = {
            target       : $request_urls.updateGroupProductImage,
            input_id     : product.input_id,
            image_url    : product.product_image,
            show_overlay : false,
            beforeCb     : function(event, i) {
                i.formData = [
                    {
                        group_id   : group_id,
                        product_id : product.product_id
                    }
                ];
            },
            triggerCb    : function() {
                return false;
            }
        };

        return product;
    }

    $scope.addGroupProduct = function() {
        $scope.local.add_in_progress = true;
        var group_id = $scope.data.groups[$scope.local.current_group].group_id;
        $http.post($request_urls.addGroupProduct, {
            group_id      : group_id,
            product_id    : $scope.local.product_input.trim(),
            display_order : $scope.data.groups[$scope.local.current_group].group_products.length
        }).success(function(data) {
            $scope.local.add_in_progress = false;
            if(data.code == 200) {
                var product = processProduct(data.data, group_id);
                $scope.data.groups[$scope.local.current_group].group_products.push(product);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteGroupProduct = function(product_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var current_group = $scope.data.groups[$scope.local.current_group];
        $http.post($request_urls.deleteGroupProduct, {
            group_id   : current_group.group_id,
            product_id : current_group.group_products[product_index].product_id
        }).success(function(data) {
            if(data.code == 200) {
                current_group.group_products.splice(product_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupProduct = function(product_index, cb) {
        var current_product = $scope.data.groups[$scope.local.current_group].group_products[product_index];
        $http.post($request_urls.updateGroupProductInfo, {
            group_id            : current_product.group_id,
            product_id          : current_product.product_id,
            display_order       : current_product.display_order,
            product_description : current_product.product_description,
            product_name        : current_product.product_name,
            product_link        : current_product.product_link
        }).success(function(data) {
            if(data.code == 200) {
                cb && cb();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupProductOrder = function() {
        var order_info = [];
        var current_group = $scope.data.groups[$scope.local.current_group];
        var all_products = current_group.group_products;
        for(var key in all_products) {
            order_info.push({
                product_id    : all_products[key].product_id,
                display_order : key
            });
        }

        $http.post($request_urls.updateGroupProductOrder + current_group.group_id, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.toggleGroupProductEdit = function(product_index) {
        var current_product = $scope.data.groups[$scope.local.current_group].group_products[product_index];
        if(current_product.edit) {
            $scope.updateGroupProduct(product_index, function() {
                current_product.edit = false;
            });
        } else {
            current_product.edit = true;
        }
    };

    $scope.triggerGroupProductImageChange = function(current_product) {
        $('#' + current_product.options.input_id).trigger('click');
    };


    $scope.init();
};

app.controller('EDMEditCtrl', ['$scope', '$http', '$rootScope', controllers.EDMEditCtrl]);