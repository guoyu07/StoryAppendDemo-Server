controllers.PromotionEditCtrl = function($scope, $rootScope, $http) {
    $scope.data = {
        base : {
            title       : '',
            description : ''
        },
        seo  : {
            title       : '',
            keywords    : '',
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
                    $scope.data.groups[$scope.local.current_group].promotion_product.splice(info.src_index, 1);
                    //Insert item
                    $scope.data.groups[$scope.local.current_group].promotion_product.splice(dst_index, 0, info.src_item);
                    $scope.updateGroupProductOrder();
                }
            }
        },
        section_head     : {
            title    : '基本信息',
            is_edit  : false,
            updateCb : function() {
                if($scope.promotion_info.$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope.promotion_info.$valid) {
                    $scope.updateBaseInfo();
                } else {
                    $rootScope.$emit('notify', {msg : '内容有误。请检查完再提交'});
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        uploader_options : {
            cover  : {
                'class'   : 'cover',
                target    : $request_urls.updatePromotionBanner,
                image_url : ''
            },
            mobile : {
                'class'   : 'mobile',
                target    : $request_urls.updatePromotionMobileBanner,
                image_url : ''
            }
        }
    };

    $scope.init = function() {
        var breadcrumb = {
            back : {
                part_content : '<span class="i i-eye"></span> '
            },
            body : {
                content : '编辑活动 － '
            }
        };

        $http.get($request_urls.getPromotionDetail).success(function(data) {
            var default_date = '0000-00-00 00:00:00';
            if(data.code == 200) {
                breadcrumb.body.content += data.data.name;
                breadcrumb.back.partClickCb = function() {
                    window.open($request_urls.baseUrl + $request_urls.previewPromotion, '_blank');
                };

                $scope.data.base.title = data.data.title;
                $scope.data.base.description = data.data.description;
                $scope.data.base.attach_url = data.data.attach_url;
                $scope.data.seo = data.data.seo;
                $scope.data.rule = data.data.promotion_rule[0];
                $scope.data.groups = data.data.promotion_group || [];
                $scope.data.preview = {
                    desktop : $request_urls.onlineUrl + $request_urls.viewPromotion,
                    mobile  : $request_urls.onlineUrl + $request_urls.previewMobilePromotion
                };
                if(data.data.is_hotelplus) {
                    $scope.data.hotelplus = {
                        info     : data.data.hotelplus,
                        products : data.data.hotelplus_products
                    };
                    $scope.local.product_input = $scope.data.hotelplus.products[0].product_id;
                }

                if(!$scope.data.seo) {
                    $scope.data.seo = {
                        title       : '',
                        keywords    : '',
                        description : ''
                    };
                }
                $scope.data.rule.end_date = $scope.data.rule.end_date == default_date ? new Date(0) :
                                            $scope.data.rule.end_date;
                $scope.data.rule.start_date = $scope.data.rule.start_date == default_date ? new Date(0) :
                                              $scope.data.rule.start_date;

                $scope.local.has_date = data.data.promotion_rule[0].start_date != default_date;
                $scope.local.uploader_options.cover.image_url = data.data.image;
                $scope.local.uploader_options.mobile.image_url = data.data.mobile_image;

                $rootScope.$emit('setBreadcrumb', breadcrumb);
                $rootScope.$emit('loadStatus', false);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //Base Info
    $scope.updateBaseInfo = function() {
        $http.post($request_urls.promotion, {
            seo            : $scope.data.seo,
            title          : $scope.data.base.title,
            description    : $scope.data.base.description,
            attach_url     : $scope.data.base.attach_url,
            promotion_rule : $scope.data.rule
        }).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.viewCity = function() {
        if(!$scope.data.hotelplus) return;
        window.open($request_urls.editCityUrl + $scope.data.hotelplus.info.city_code, '_blank');
    };

    //Group Action
    $scope.addGroup = function() {
        $http.post($request_urls.promotionGroup, {
            display_order : $scope.data.groups.length
        }).success(function(data) {
            if(data.code == 200) {
                data.data.promotion_product = [];
                $scope.data.groups.push(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteGroup = function(group_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var group_id = $scope.data.groups[group_index].group_id;
        $http.delete($request_urls.promotionGroup + group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupInfo = function(cb) {
        var current_group = $scope.data.groups[$scope.local.current_group];
        $http.post($request_urls.promotionGroup + current_group.group_id, current_group).success(function(data) {
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

        $http.post($request_urls.updatePromotionGroupOrder, order_info).success(function(data) {
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
    $scope.addGroupProduct = function() {
        $scope.local.add_in_progress = true;
        var group_id = $scope.data.groups[$scope.local.current_group].group_id;
        $http.post($request_urls.promotionGroupProduct, {
            group_id      : group_id,
            product_id    : $scope.local.product_input.trim(),
            display_order : $scope.data.groups[$scope.local.current_group].promotion_product.length
        }).success(function(data) {
            $scope.local.add_in_progress = false;
            if(data.code == 200) {
                $scope.data.groups[$scope.local.current_group].promotion_product.push(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.deleteGroupProduct = function(product_index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        var current_group = $scope.data.groups[$scope.local.current_group];
        $http.post($request_urls.deletePromotionGroupProduct, {
            group_id   : current_group.group_id,
            product_id : current_group.promotion_product[product_index].product_id
        }).success(function(data) {
            if(data.code == 200) {
                current_group.promotion_product.splice(product_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupProductOrder = function() {
        var order_info = [];
        var current_group = $scope.data.groups[$scope.local.current_group];
        var all_products = current_group.promotion_product;
        for(var key in all_products) {
            order_info.push({
                product_id    : all_products[key].product_id,
                display_order : key
            });
        }

        $http.post($request_urls.updatePromotionGroupProductOrder +
                   current_group.group_id, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };


    $scope.init();
};

app.controller('PromotionEditCtrl', ['$scope', '$rootScope', '$http', controllers.PromotionEditCtrl]);