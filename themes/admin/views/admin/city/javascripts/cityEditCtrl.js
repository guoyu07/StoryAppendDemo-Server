controllers.CityEditCtrl = function($scope, $http, $rootScope, $q, $timeout, commonFactory) {
    $scope.local = {
        dnd               : {
            options  : {
                selector : '.user-groups .one-block',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.groups.user.splice(info.src_index, 1); //Remove item
                $scope.data.groups.user.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateGroupOrder();
            }
        },
        dnd_app            : {
            options  : {
                selector : '.app-groups .one-block',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.groups.app.splice(info.src_index, 1); //Remove item
                $scope.data.groups.app.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateAppGroupOrder();
            }
        },
        edit              : {
            pre   : {
                index : -1,
                edit  : false
            },
            user  : {
                index : -1,
                edit  : false
            },
            top   : {
                index : 0,
                edit  : false
            },
            hotel : {
                index : 0,
                edit  : false
            },
            line  : {
                index : 0,
                edit  : false
            },
            app   : {
                index : -1,
                edit  : false
            }
        },
        menus             : {
            '1' : {
                label   : '基本信息',
                loading : false
            },
            '2' : {
                label   : '商品分组',
                loading : false
            },
            '3' : {
                label   : '热卖商品',
                loading : false
            },
            '4' : {
                label   : '酒店/套餐',
                loading : false
            },
            '5' : {
                label   : '线路商品',
                loading : false
            },
            '6' : {
                label   : '玩法文章',
                loading : false
            },
            '7' : {
                label   : 'APP分组',
                loading : false
            }
        },
        articles          : [],
        group_type        : ['user', 'pre', 'top', 'hotel', 'line', 'app'],
        breadcrumb        : {
            back : {
                part_content : '<span class="i i-eye"></span> '
            },
            body : {
                content : '编辑城市 － '
            }
        },
        product_url       : $request_urls.editProductUrl,
        article_url       : $request_urls.editArticleUrl,
        search_text       : '',
        promotion_id      : '',
        lookup_promotion  : false,
        group_status      : {
            '1' : {
                label : '编辑中'
            },
            '2' : {
                label : '已生效'
            }
        },
        current_menu      : '1',
        section_head      : {
            basic_info     : {
                title    : '基本信息',
                updateCb : function() {
                    if($scope.city_seo.$pristine) {
                        $scope.local.section_head.basic_info.is_edit = false;
                    } else if($scope.city_seo.$valid) {
                        $scope.updateCitySeo();
                    } else {
                        $rootScope.$emit('notify', {msg : '请填写SEO必填项'});
                    }
                }
            },
            city_promotion : {
                title    : '酒店／套餐 － 商品聚合',
                updateCb : function() {
                    if($scope.city_promotion_form.$pristine) {
                        $scope.local.section_head.city_promotion.is_edit = false;
                    } else if($scope.city_promotion_form.$valid) {
                        $scope.updateCityPromotion();
                    } else {
                        $rootScope.$emit('notify', {msg : '请填写必填项'});
                    }
                }
            }
        },
        radio_options     : {
            promotion_status : {
                name  : 'status',
                items : {
                    '0' : '编辑中',
                    '1' : '已生效'
                }
            }
        },
        uploader_options  : {
            city_banner    : {
                target    : $request_urls.addOrUpdateCityImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 1
                        }
                    ];
                }
            },
            city_cover     : {
                target    : $request_urls.addOrUpdateCityImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 2
                        }
                    ];
                }
            },
            city_cover_app     : {
                target    : $request_urls.addOrUpdateCityImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 3
                        }
                    ];
                }
            },
            city_cover_strip    : {
                target    : $request_urls.addOrUpdateCityImage,
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            type : 4
                        }
                    ];
                }
            },
            pre_group_img  : {
                target    : $request_urls.productGroupImage,
                input_id  : 'pre-group-upload',
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            group_id : $scope.data.groups.pre[$scope.local.edit.pre.index].group_id
                        }
                    ];
                },
                triggerCb : function() {
                    if($scope.local.edit.pre.edit == true && $scope.local.edit.pre.index > -1) {
                        $('#' + $scope.local.uploader_options.pre_group_img.input_id).trigger('click');
                    }
                }
            },
            user_group_img : {
                target    : $request_urls.productGroupImage,
                input_id  : 'user-group-upload',
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            group_id : $scope.data.groups.user[$scope.local.edit.user.index].group_id
                        }
                    ];
                },
                triggerCb : function() {
                    if($scope.local.edit.user.edit == true && $scope.local.edit.user.index > -1) {
                        $('#' + $scope.local.uploader_options.user_group_img.input_id).trigger('click');
                    }
                }
            },
            app_group_img : {
                target    : $request_urls.productGroupImage,
                input_id  : 'app-group-upload',
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            group_id : $scope.data.groups.app[$scope.local.edit.app.index].group_id
                        }
                    ];
                },
                triggerCb : function() {
                    if($scope.local.edit.app.edit == true && $scope.local.edit.app.index > -1) {
                        $('#' + $scope.local.uploader_options.app_group_img.input_id).trigger('click');
                    }
                }
            },
            hotel_plus_img : {
                target    : $request_urls.updateCityPromotionCover,
                image_url : ''
            }
        },
        products_in_group : []
    };
    $scope.data = {
        seo      : {},
        city     : {},
        cities   : [],
        chosen   : {
            current_cities : []
        },
        groups   : {
            pre   : [],
            user  : [],
            top   : [],
            hotel : [],
            line  : [],
            app   : []
        },
        columns  : {
            experience : [],
            itinerary  : []
        },
        products : {},
        articles : []
    };

    $scope.init = function() {
        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.data.cities = data.map(function(city) {
                city.select_label = city.city_name + ' ' + city.city_pinyin;

                return city;
            });
        });
        var ajax_city_seo = $http.get($request_urls.citySeo);
        var ajax_city_image = $http.get($request_urls.cityImages);
        var ajax_city_product = $http.get($request_urls.getCityProducts);
        var ajax_product_group = $http.get($request_urls.getProductGroups);
        var ajax_city_article = $http.get($request_urls.getCityArticles);
        var ajax_article_column = $http.get($request_urls.cityColumn);
        var ajax_city_promotion = $http.get($request_urls.cityPromotion);

        $q.all([
            ajax_city_image, ajax_city_product, ajax_product_group, ajax_city_seo, ajax_city_article,
            ajax_article_column, ajax_city_promotion
        ]).then(function(values) {
            var key, i, len, tmp, tmp_groupid, tmp_groupkey, one_group;

            if(values[0].data.code == 200) {
                $scope.data.city = {
                    cn_name  : values[0].data.data.cn_name,
                    link_url : values[0].data.data.link_url
                };
                if(values[0].data.data.city_image) {
                    $scope.local.uploader_options.city_banner.image_url = values[0].data.data.city_image.banner_image_url;
                    $scope.local.uploader_options.city_cover.image_url = values[0].data.data.city_image.grid_image_url;
                    $scope.local.uploader_options.city_cover_app.image_url = values[0].data.data.city_image.app_image_url;
                    $scope.local.uploader_options.city_cover_strip.image_url = values[0].data.data.city_image.app_strip_image_url;
                }
                $scope.local.breadcrumb.back.part_content += values[0].data.data.cn_name;
                $scope.local.breadcrumb.back.partClickCb = function() {
                    window.open($request_urls.baseUrl + values[0].data.data.link_url, '_blank');
                };
                $scope.local.breadcrumb.body.content += values[0].data.data.cn_name;
            } else {
                $rootScope.$emit('notify', {
                    msg : values[0].data.msg
                });
            }

            if(values[1].data.code == 200) {
                $scope.data.products = values[1].data.data;
            } else {
                $rootScope.$emit('notify', {
                    msg : values[1].data.msg
                });
            }

            if(values[2].data.code == 200) {
                $scope.data.groups.user = values[2].data.data.user_defined_groups || [];

                tmp = window.location.search.substr(1).split(/&/g);
                for(key in tmp) {
                    if(tmp[key].indexOf('group_id') > -1) {
                        tmp_groupid = tmp[key].split('=')[1];
                    }
                }

                for(key in $scope.data.groups.user) {
                    if(tmp_groupid == $scope.data.groups.user[key].group_id) {
                        tmp_groupkey = key;
                    }
                    $scope.data.groups.user[key].products = sortItems($scope.data.groups.user[key].products);
                    for(i = 0, len = $scope.data.groups.user[key].products.length; i < len; i++) {
                        $scope.addProductToPool($scope.data.groups.user[key].products[i].product_id);
                    }
                }
                for(key in values[2].data.data.pre_defined_groups) { //$scope.data.groups.pre
                    one_group = values[2].data.data.pre_defined_groups[key];
                    if(one_group.type == 4) { //Top10
                        $scope.data.groups.top.push(angular.copy(one_group));
                        $scope.data.groups.top[0].products = sortItems($scope.data.groups.top[0].products);
                    } else if(one_group.type == 5) { //Hotel
                        $scope.data.groups.hotel.push(angular.copy(one_group));
                        $scope.data.groups.hotel[0].products = sortItems($scope.data.groups.hotel[0].products);
                        $scope.data.groups.hotel[0].products = $scope.data.groups.hotel[0].products.map(function(p) {
                            p.uploader = {
                                target    : $request_urls.addOrUpdateProductImage,
                                image_url : p.product_image_url,
                                beforeCb  : function(event, item) {
                                    item.formData = [
                                        {
                                            group_id   : $scope.data.groups.hotel[0].group_id,
                                            product_id : p.product_id
                                        }
                                    ];
                                }
                            };

                            return p;
                        });
                    } else if(one_group.type == 7) { //Line
                        $scope.data.groups.line.push(angular.copy(one_group));
                        $scope.data.groups.line[0].products = sortItems($scope.data.groups.line[0].products);
                        $scope.data.groups.line[0].products = $scope.data.groups.line[0].products.map(function(p) {
                            p.uploader = {
                                target    : $request_urls.addOrUpdateProductImage,
                                image_url : p.product_image_url,
                                beforeCb  : function(event, item) {
                                    item.formData = [
                                        {
                                            group_id   : $scope.data.groups.line[0].group_id,
                                            product_id : p.product_id
                                        }
                                    ];
                                }
                            };
                            p.radio_options = {
                                value  : {
                                    'value' : p.status
                                },
                                status : {
                                    name     : 'value',
                                    items    : {
                                        '1' : '编辑中',
                                        '2' : '已生效'
                                    },
                                    callback : function(status) {
                                        $scope.updateLineProduct(p.product_id, status);
                                    }
                                }
                            };

                            return p;
                        });
                    } else {
                        tmp = $scope.data.groups.pre.push(angular.copy(one_group)) - 1;
                        $scope.data.groups.pre[tmp].products = sortItems(one_group.products);
                        if(one_group.type == 2) { //只针对热门推荐
                            for(i = 0, len = one_group.products.length; i < len; i++) {
                                $scope.addProductToPool(one_group.products[i].product_id);
                            }
                        }
                    }
                }

                $scope.data.groups.app = values[2].data.data.app_defined_groups || [];
            } else {
                $rootScope.$emit('notify', {
                    msg : values[2].data.msg
                });
            }

            if(values[3].data.code == 200) {
                $scope.data.seo = values[3].data.data;
            } else {
                $rootScope.$emit('notify', {
                    msg : values[3].data.msg
                });
            }

            if(values[4].data.code == 200) {
                $scope.local.articles = values[4].data.data;
            } else {
                $rootScope.$emit('notify', {
                    msg : values[4].data.msg
                });
            }

            if(values[5].data.code == 200) {
                for(key in values[5].data.data) {
                    one_group = values[5].data.data[key];
                    if(one_group.type == '1') {
                        $scope.data.columns.experience = angular.copy(one_group);
                        $scope.data.columns.experience.columns = sortItems($scope.data.columns.experience.columns);
                        $scope.data.columns.experience.columns = $scope.data.columns.experience.columns.map(function(a) {
                            a.uploader = {
                                target    : $request_urls.articleImage + $scope.data.columns.experience.column_id,
                                image_url : a.article_image_url,
                                beforeCb  : function(event, item) {
                                    item.formData = [
                                        {
                                            column_id  : $scope.data.columns.experience.column_id,
                                            article_id : a.article_id
                                        }
                                    ];
                                }
                            };

                            return a;
                        });
                    } else if(one_group.type == '2') {
                        $scope.data.columns.itinerary = angular.copy(one_group);
                    }
                }
            } else {
                $rootScope.$emit('notify', {
                    msg : values[5].data.msg
                });
            }

            if(values[6].data.code == 200) {
                $scope.data.promotion = values[6].data.data || {};
                $scope.local.uploader_options.hotel_plus_img.image_url = $scope.data.promotion.introduction_image;
            } else {
                $rootScope.$emit('notify', {
                    msg : values[6].data.msg
                });
            }

            $timeout(function() {
                if(tmp_groupkey) {
                    $scope.local.current_menu = 2;
                    $scope.setCurrentGroup('user', tmp_groupkey);
                }
            }, 500);

            $rootScope.$emit('loadStatus', false);
            $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb);
        });
    };

    //页面操作
    $scope.setCurrentMenu = function(key) {
        $scope.local.current_menu = key;
        $scope.local.search_text = '';
    };
    $scope.setCurrentGroup = function(group_type, index) {
        $scope.local.edit[group_type] = {
            edit  : false,
            index : index
        };

        if(index > -1) { //防止删除图片时设置成-1报错
            if(group_type == 'pre') {
                $scope.local.uploader_options.pre_group_img.image_url = $scope.data.groups.pre[index].cover_image_url;
            } else if(group_type == 'user') {
                $scope.local.uploader_options.user_group_img.image_url = $scope.data.groups.user[index].cover_image_url;
            } else if(group_type == 'exp') {
                $scope.local.uploader_options.exp_group_img.image_url = $scope.data.experience_group[index].cover_image_url;
            } else if(group_type == 'app') {
                $scope.local.uploader_options.app_group_img.image_url = $scope.data.groups.app[index].cover_image_url;
            }
        }

        scrollToWatershed(group_type);
    };

    $scope.toggleGroupEdit = function(group_type) {
        if(!$scope.isGroupAvail(group_type)) return;
        var index = $scope.local.edit[group_type].index;

        if($scope.local.edit[group_type].edit == true) { //Saving
            $scope.updateGroupInfo($scope.data.groups[group_type][index]);
        }

        $scope.local.edit[group_type].edit = !$scope.local.edit[group_type].edit;
    };
    $scope.updateCitySeo = function() {
        $scope.data.seo.keywords = $scope.data.seo.keywords.replace(/，/g, ',').split(',').map(function(elem) {
            return elem.trim();
        }).filter(function(elem) {
            return elem.length > 0;
        }).join(',');

        $http.post($request_urls.citySeo, $scope.data.seo).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.basic_info.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //Product Pool
    $scope.addProductToPool = function(product_id) {
        if(!$scope.isProductInAnyGroup(product_id)) {
            $scope.local.products_in_group.push(product_id);
        }
    };
    $scope.deleteProductFromPool = function(product_id) {
        var index = $scope.local.products_in_group.indexOf(product_id);
        if(index > -1) {
            $scope.local.products_in_group.splice(index, 1);
        }
    };

    $scope.isProductInAnyGroup = function(product_id) {
        return $scope.local.products_in_group.indexOf(product_id) > -1;
    };
    $scope.isProductInThisGroup = function(group_type, product_id) {
        if(!$scope.isGroupAvail(group_type)) return;

        var index = getIndexByProp($scope.data.groups[group_type][$scope.local.edit[group_type].index].products, 'product_id', product_id);

        return index > -1;
    };

    //Product && Group
    $scope.addProductToGroup = function(group_type, product_id, product_index) {
        if(!$scope.isGroupAvail(group_type)) return;

        var index, new_product, current_group;
        index = product_index || getIndexByProp($scope.data.products, 'product_id', product_id);
        current_group = $scope.data.groups[group_type][$scope.local.edit[group_type].index];
        new_product = angular.copy($scope.data.products[index]);
        new_product.display_order = current_group.products.length + 1;
        if(group_type == 'line') {
            new_product.status = 1;
        } else {
            new_product.status = 2;
        }

        $http.post($request_urls.addProduct + current_group.group_id, new_product).success(function(data) {
            if(data.code == 200) {
                if(current_group.type == 5) {
                    new_product.uploader = {
                        target    : $request_urls.addOrUpdateProductImage,
                        image_url : '',
                        beforeCb  : function(event, item) {
                            item.formData = [
                                {
                                    group_id   : current_group.group_id,
                                    product_id : new_product.product_id
                                }
                            ];
                        }
                    };
                }
                if(current_group.type == 7) {
                    new_product.uploader = {
                        target    : $request_urls.addOrUpdateProductImage,
                        image_url : '',
                        beforeCb  : function(event, item) {
                            item.formData = [
                                {
                                    group_id   : current_group.group_id,
                                    product_id : new_product.product_id
                                }
                            ];
                        }
                    };
                    new_product.radio_options = {
                        value  : {
                            'value' : '1'
                        },
                        status : {
                            name     : 'value',
                            items    : {
                                '1' : '编辑中',
                                '2' : '已生效'
                            },
                            callback : function(status) {
                                $scope.updateLineProduct(new_product.product_id, status);
                            }
                        }
                    };
                }

                current_group.products.push(new_product);
                $scope.addProductToPool(product_id);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.addProductToGroupBySearch = function(group_type, product_id) {
        var index, current_group;
        current_group = $scope.data.groups[group_type][$scope.local.edit[group_type].index];

        //Sanity Check
        index = getIndexByProp(current_group.products, 'product_id', product_id);
        if(index != -1) {
            $rootScope.$emit('notify', {msg : '此分组已经有这个商品'});
            return;
        }
        index = getIndexByProp($scope.data.products, 'product_id', product_id);
        if(index == -1) {
            $rootScope.$emit('notify', {msg : '没有找到商品ID'});
            return;
        }

        $scope.addProductToGroup(group_type, product_id, index);
    };
    $scope.deleteProductFromGroup = function(group_type, product_id) {
        if(!$scope.isGroupAvail(group_type)) return;

        var current_group = $scope.data.groups[group_type][$scope.local.edit[group_type].index];
        var index = getIndexByProp(current_group.products, 'product_id', product_id);

        if(index > -1) {
            $http.post($request_urls.deleteProduct + current_group.group_id, {
                product_id : product_id
            }).success(function(data) {
                if(data.code == 200) {
                    current_group.products.splice(index, 1);
                    $scope.deleteProductFromPool(product_id);
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }
    };

    //Group
    $scope.isGroupAvail = function(group_type) {
        return $scope.local.group_type.indexOf(group_type) > -1 && $scope.local.edit[group_type].index > -1;
    };
    $scope.addUserGroup = function() {
        $http.post($request_urls.productGroup, {}).success(function(data) {
            if(data.code == 200) {
                $scope.setCurrentGroup('user', $scope.data.groups.user.push(data.data) - 1);

                $scope.data.groups.user[$scope.local.edit.user.index].seo = {};
                $scope.data.groups.user[$scope.local.edit.user.index].products = sortItems($scope.data.groups.user[$scope.local.edit.user.index].products);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.deleteUserGroup = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        //Remove products from pool
        var current_group = $scope.data.groups.user[index];
        for(var i = 0, len = current_group.products.length; i < len; i++) {
            $scope.deleteProductFromPool(current_group.products[i].product_id);
        }

        $http.delete($request_urls.productGroup + current_group.group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups.user.splice(index, 1);
                $scope.setCurrentGroup('user', -1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateGroupInfo = function(group) {
        $http.post($request_urls.productGroup + group.group_id, group).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.updateGroupOrder = function() {
        var order_info = [];
        for(var key in $scope.data.groups.user) {
            order_info.push({
                group_id      : $scope.data.groups.user[key].group_id,
                display_order : key
            });
        }

        $http.post($request_urls.changeProductGroupsDisplayOrder, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateGroupState = function(group_index, status_id) {
        var status = $scope.local.group_status[status_id];
        var current_group = $scope.data.groups.user[group_index];
        if(!status) return;

        $http.post($request_urls.changeProductGroupStatus + current_group.group_id, {
            status : status_id
        }).success(function(data) {
            if(data.code == 200) {
                current_group.status = status_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateProductOrder = function(group_type, product_id) {
        if(!$scope.isGroupAvail(group_type)) return;

        var current_group = $scope.data.groups[group_type][$scope.local.edit[group_type].index];
        var index = getIndexByProp(current_group.products, 'product_id', product_id);
        var min = 1;
        var max = current_group.products.length;
        var new_index = index > -1 ? current_group.products[index].display_order : '';

        if(new_index >= min && new_index <= max) { //合法操作
            var current_product = current_group.products.splice(index, 1); //Delete from origin position
            current_group.products.splice(new_index - 1, 0, current_product[0]); //Insert at new position

            var sort_info = current_group.products.map(function(elem, index) {
                elem.display_order = index + 1;
                return elem;
            });

            var postData = [];
            for(var sort_index in sort_info) {
                postData.push({
                    product_id    : sort_info[sort_index].product_id,
                    display_order : sort_info[sort_index].display_order
                });
            }

            $http.post($request_urls.changeProductDisplayOrder +
                       current_group.group_id, postData).success(function(data) {
                if(data.code == 200) {
                    current_group.products = sort_info;
                } else {
                    $rootScope.$emit('notify', {
                        msg : data.msg
                    });
                }
            });
        } else if(new_index != '') { //值不合法，恢复原来的值
            current_group.products[index].display_order = parseInt(index) + 1;
            $rootScope.$emit('notify', {
                msg : "不能大于最大值" + max
            });
        }
    };

    //酒店聚合
    $scope.updateCityPromotion = function() {
        $scope.data.promotion.promotion_id = $scope.data.promotion.promotion_id.trim();
        $http.post($request_urls.cityPromotion, $scope.data.promotion).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.city_promotion.is_edit = false;
            } else {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.viewPromotion = function(type) {
        if(type == 'new') {
            $http.post($request_urls.promotion, {
                name : $scope.data.city.cn_name + '的酒店套餐聚合页'
            }).success(function(data) {
                if(data.code == 200) {
                    $scope.data.promotion.promotion_id = data.data;
                    window.open($request_urls.editPromotion + data.data, '_blank');
                }
            });
        } else if(type == 'current') {
            window.open($request_urls.editPromotion + $scope.data.promotion.promotion_id, '_blank');
        }
    };

    //线路商品
    $scope.updateLineProduct = function(product_id, status) {
        var current_group = $scope.data.groups.line[0];

        var index = getIndexByProp(current_group.products, 'product_id', product_id);

        var product_name = '';
        if(current_group.products[index].product_name) {
            product_name = current_group.products[index].product_name;
        }

        if(!status) {
            status = current_group.products[index].status;
        }

        $http.post($request_urls.updateProduct + current_group.group_id, {
            product_name : product_name,
            product_id   : product_id,
            status       : status
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.copyLineProduct = function(product_id) {
        var current_group = $scope.data.groups.line[0];

        var index = getIndexByProp(current_group.products, 'product_id', product_id);

        var product_name = '';
        if(current_group.products[index].product_name) {
            product_name = current_group.products[index].product_name;
        }

        var tour_cities = current_group.products[index].tour_cities;
        var product_image_url = current_group.products[index].product_image_url;

        $http.post($request_urls.copyProduct + current_group.group_id, {
            product_name      : product_name,
            product_id        : product_id,
            status            : 1,
            tour_cities       : tour_cities,
            product_image_url : product_image_url,
        }).success(function(data) {
            if(data.code == 200) {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });

    };

    //Article
    $scope.updateColumnInfo = function(column_type) {
        var column = $scope.data.columns[column_type];
        $http.post($request_urls.cityColumn + column.column_id, {
            name : column.name
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {
                    msg : data.msg
                });
            }
        });
    };
    $scope.addArticleToColumn = function(column_type, article_id) {
        var column = $scope.data.columns[column_type];
        $http.post($request_urls.cityColumnRef + column.column_id, {
            article_id    : article_id,
            display_order : column.columns.length + 1
        }).success(function(data) {
            if(data.code == 200) {
                data.data.uploader = {
                    target   : $request_urls.articleImage + column.column_id,
                    beforeCb : function(event, item) {
                        item.formData = [
                            {
                                column_id  : column.column_id,
                                article_id : data.data.article_id
                            }
                        ];
                    }
                };
                column.columns.push(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.deleteArticleFromColumn = function(column_type, article_index) {
        var column = $scope.data.columns[column_type];
        var article = column.columns[article_index];
        $http.delete($request_urls.cityColumnRef + column.column_id + '&article_id=' +
                     article.article_id).success(function(data) {
            if(data.code == 200) {
                column.columns.splice(article_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateArticleOrder = function(column_type, article_id) {
        var current_column = $scope.data.columns[column_type];
        var index = getIndexByProp(current_column.columns, 'article_id', article_id);
        var min = 1;
        var max = current_column.columns.length;
        var new_index = index > -1 ? current_column.columns[index].display_order : '';

        if(new_index >= min && new_index <= max) { //合法操作
            var current_article = current_column.columns.splice(index, 1); //Delete from origin position
            current_column.columns.splice(new_index - 1, 0, current_article[0]); //Insert at new position

            var sort_info = current_column.columns.map(function(elem, index) {
                elem.display_order = index + 1;
                return elem;
            });

            var postData = [];
            for(var sort_index in sort_info) {
                postData.push({
                    article_id    : sort_info[sort_index].article_id,
                    display_order : sort_info[sort_index].display_order
                });
            }

            $http.post($request_urls.articleDisplayOrder +
                       current_column.column_id, postData).success(function(data) {
                if(data.code == 200) {
                    current_column.columns = sort_info;
                } else {
                    $rootScope.$emit('notify', {
                        msg : data.msg
                    });
                }
            });
        } else if(new_index != '') { //值不合法，恢复原来的值
            current_column.columns[index].display_order = parseInt(index) + 1;
            $rootScope.$emit('notify', {
                msg : "不能大于最大值" + max
            });
        }
    };

    //APP分组
    $scope.addAppGroup = function() {
        $http.post($request_urls.productGroup, {type : 8}).success(function(data) {
            if(data.code == 200) {
                $scope.setCurrentGroup('app', $scope.data.groups.app.push(data.data) - 1);
                $scope.data.groups.app[$scope.local.edit.app.index].products = sortItems($scope.data.groups.app[$scope.local.edit.app.index].products);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.deleteAppGroup = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;

        //Remove products from pool
        var current_group = $scope.data.groups.app[index];
        for(var i = 0, len = current_group.products.length; i < len; i++) {
            $scope.deleteProductFromPool(current_group.products[i].product_id);
        }

        $http.delete($request_urls.productGroup + current_group.group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups.app.splice(index, 1);
                $scope.setCurrentGroup('app', -1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateAppGroupState = function(group_index, status_id) {
        var status = $scope.local.group_status[status_id];
        var current_group = $scope.data.groups.app[group_index];
        if(!status) return;

        $http.post($request_urls.changeProductGroupStatus + current_group.group_id, {
            status : status_id
        }).success(function(data) {
            if(data.code == 200) {
                current_group.status = status_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateAppGroupOrder = function() {
        var order_info = [];
        for(var key in $scope.data.groups.app) {
            order_info.push({
                group_id      : $scope.data.groups.app[key].group_id,
                display_order : key
            });
        }

        $http.post($request_urls.changeProductGroupsDisplayOrder, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.updateAppProductAlias = function(product_id) {
        var current_group = $scope.data.groups.app[$scope.local.edit.app.index];

        var product_index = getIndexByProp(current_group.products, 'product_id', product_id);

        if(current_group.products[product_index].product_name.length<=20) {
            $http.post($request_urls.updateProduct + current_group.group_id, {
                product_name : current_group.products[product_index].product_name,
                product_id   : product_id,
                status       : current_group.products[product_index].status
            }).success(function(data) {
                if(data.code != 200) {
                    $rootScope.$emit('notify', {
                        msg : data.msg
                    });
                }
            });
        } else {
            $rootScope.$emit('notify', {msg : '请输入在20字以内'});
        }

    };
    function scrollToWatershed(group_type) {
        var watershed, selector;

        selector = '#' + group_type + '_group_watershed';
        //    group_type == 'pre' ? '#pre_group_watershed' : '#user_group_watershed';
        watershed = $(selector).offset().top;
        if($(window).scrollTop() < watershed) {
            $(window).scrollTop(watershed);
        }
    }

    function sortItems(oneSet) {
        oneSet.sort(function(a, b) {
            return a.display_order - b.display_order;
        });
        return reOrder(oneSet);
    }


    $scope.init();
};

app.controller('CityEditCtrl', ['$scope', '$http', '$rootScope', '$q', '$timeout', 'commonFactory', controllers.CityEditCtrl]);