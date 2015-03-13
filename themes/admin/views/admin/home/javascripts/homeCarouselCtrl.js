controllers.HomeCarouselCtrl = function($scope, $rootScope, $http, commonFactory) {
    $scope.data = {
        hotCountry : {
            1 : [],
            2 : [],
            3 : [],
            4 : [],
            5 : [],
            6 : [],
            7 : []
        }
    };
    $scope.local = {
        breadcrumb_options : {
            back : {},
            body : {
                content : '<span class="i i-eye"></span>首页预览',
                clickCb : function() {
                    window.open($request_urls.baseUrl, '_blank')
                }
            }
        },
        all_countries:[],
        country_list : {},
        group_status       : [
            {
                id   : '1',
                name : '编辑中'
            },
            {
                id   : '2',
                name : '已生效'
            }
        ],
        home_image_dnd     : {
            options  : {
                selector : '.carousel-image.home-carousel',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.images.splice(info.src_index, 1); //Remove item
                $scope.data.images.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateImageOrder();
            }
        },
        group_dnd          : {
            options  : {
                selector : '.round-card.home-group',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.groups.splice(info.src_index, 1); //Remove item
                $scope.data.groups.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateGroupOrder();
            }
        },
        section_head       : {
            title    : '首页SEO',
            is_edit  : false,
            updateCb : function() {
                if($scope.home_seo.$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope.home_seo.$valid) {
                    $scope.data.seo.keywords = $scope.data.seo.keywords.replace(/，/g, ',').split(',').map(function(elem) {
                        return elem.trim();
                    }).filter(function(elem) {
                        return elem.length > 0;
                    }).join(',');
                    $http.post($request_urls.homeSeo, $scope.data.seo).success(function(data) {
                        if(data.code == 200) {
                            $scope.local.section_head.is_edit = false;
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                } else {
                    $rootScope.$emit('notify', {msg : '请填写SEO必填项'});
                }
            },
            editCb   : function() {
                $scope.local.section_head.is_edit = true;
            }
        },
        select_tag_1 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[1], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[1].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[1][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[1].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        },
        select_tag_2 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[2], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[2].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[2][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[2].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        },
        select_tag_3 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[3], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[3].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[3][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[3].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        },
        select_tag_4 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[4], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[4].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[4][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[4].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        },
        select_tag_5 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[5], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[5].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[5][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[5].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        },
        select_tag_6 : {
            btn_text    : '添加国家',
            title_str   : 'cn_name',
            placeholder : '选择国家',
            select      : {
                placeholder : '点击选择国家',
                value_prop  : 'country_code',
                label_prop  : 'select_label'
            },
            addCb       : function(country_code, next) {
                var country_index_hot = getIndexByProp($scope.data.hotCountry[6], 'country_code', country_code);
                if(country_index_hot > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    $http.post($request_urls.hotCountry, {country_code : country_code}).success(function(data) {
                        if(data.code == 200) {
                            var country_index_all = getIndexByProp($scope.local.all_countries, 'country_code', country_code);
                            var country = $scope.local.all_countries[country_index_all];
                            $scope.data.hotCountry[6].push({
                                'country_code' : country.country_code,
                                'cn_name'      : country.cn_name
                            });
                            next();
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('删除该热门城市?')) {
                    var country_code = $scope.data.hotCountry[6][index].country_code;
                    $http.delete($request_urls.hotCountry + country_code).success(function(data) {
                        if(data.code == 200) {
                            $scope.data.hotCountry[6].splice(index, 1);
                        } else {
                            $rootScope.$emit('notify', {msg : data.msg});
                        }
                    });
                }
            }
        }
    };

    function getUploaderOptions(image) {
        return {
            target       : $request_urls.uploadHomeImage,
            image_url    : image.image_url,
            input_id     : 'change_image_' + image.id,
            show_overlay : false,
            beforeCb     : function(event, item) {
                item.formData = [
                    {
                        id : image.id
                    }
                ];
            }
        };
    }

    //初始化数据
    $scope.init = function() {
        //面包屑
        $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb_options);
        //获取首页轮播图
        $http.get($request_urls.homeCarousel).success(function(data) {
            if(data.code == 200) {
                $scope.data.images = data.data.map(function(elem, index) {
                    elem.editing = false;
                    elem.options = getUploaderOptions(elem);
                    return elem;
                });
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

        //获取国家列表
        commonFactory.getAjaxSearchCountryList().then(function(data) {
            $scope.local.all_countries = data;
            $scope.local.country_list.asia = [];
            $scope.local.country_list.europe = [];
            $scope.local.country_list.africa = [];
            $scope.local.country_list.na = [];
            $scope.local.country_list.sa = [];
            $scope.local.country_list.oceania = [];
            $scope.local.country_list.antarctica = [];

            for(var li in data) {
                var current_country = data[li];
                current_country.select_label = current_country.cn_name + ' ' + current_country.country_code;
                if(current_country.continent_id == 1){
                    $scope.local.country_list.asia.push(current_country);
                } else if(current_country.continent_id == 2){
                    $scope.local.country_list.europe.push(current_country);
                } else if(current_country.continent_id == 3){
                    $scope.local.country_list.africa.push(current_country);
                } else if(current_country.continent_id == 4){
                    $scope.local.country_list.na.push(current_country);
                } else if(current_country.continent_id == 5){
                    $scope.local.country_list.sa.push(current_country);
                } else if(current_country.continent_id == 6){
                    $scope.local.country_list.oceania.push(current_country);
                } else {
                    $scope.local.country_list.antarctica.push(current_country);
                }
            }
        });

        //获取热门国家
        $http.get($request_urls.hotCountry).success(function(data) {
            if(data.code == 200) {
                for(var ci in data.data){
                    var all_countries = data.data[ci].countries.map(function(country) {
                        return {
                            cn_name      : country.cn_name,
                            country_code : country.country_code
                        }
                    });
                    $scope.data.hotCountry[data.data[ci].continent_id] = all_countries;
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

        //获取首页分组
        $http.get($request_urls.getHomeGroups).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups = data.data;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

        $http.get($request_urls.homeSeo).success(function(data) {
            if(data.code == 200) {
                $scope.data.seo = data.data;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });

        $rootScope.$emit('loadStatus', false);
    };

    $scope.init();

    //新增分组
    $scope.addGroup = function() {
        $http.get($request_urls.addHomeGroup).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups = data.data;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //编辑分组
    $scope.editGroup = function(index) {
        window.open($request_urls.editHomeGroup + $scope.data.groups[index].group_id, '_blank');
    };
    //删除分组
    $scope.deleteGroup = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        $http.post($request_urls.deleteHomeGroup, {group_id : $scope.data.groups[index].group_id}).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups.splice(index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //更改分组状态
    $scope.changeState = function(index, status_id) {
        var enable_amount = 0;
        for(var i in $scope.data.groups) {
            if($scope.data.groups[i].status == '2') {
                enable_amount++;
            }
        }
        if(enable_amount > 7) {
            $rootScope('notify', {msg : '启用生效的分组不能超过7个'});
            return;
        }

        var postData = {
            group_id : $scope.data.groups[index].group_id,
            status   : status_id
        };
        $http.post($request_urls.changeHomeGroupStatus +
                   $scope.data.groups[index].group_id, postData).success(function(data) {
            if(data.code == 200) {
                $scope.data.groups[index].status = status_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //更改分组顺序
    $scope.updateGroupOrder = function() {
        var order_info = [];
        for(var key in $scope.data.groups) {
            order_info.push({
                group_id      : $scope.data.groups[key].group_id,
                display_order : key
            });
        }

        $http.post($request_urls.updateHomeGroupOrder, {
            group_order : order_info
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //获取分组状态名
    $scope.getGroupState = function(status_id) {
        for(var key in $scope.local.group_status) {
            if($scope.local.group_status[key].id == status_id) {
                return $scope.local.group_status[key].name;
            }
        }
    };

    //添加轮播图
    $scope.addImage = function() {
        $http.post($request_urls.addHomeImage).success(function(data) {
            if(data.code == 200) {
                var uploader_options = getUploaderOptions(data.data);
                $scope.data.images.push({
                    id            : data.data.id, //Image ID在这里,
                    editing       : false,
                    options       : uploader_options,
                    image_url     : '',
                    product_id    : data.data.product_id,
                    display_order : $scope.data.images.length
                });

                if($('.one-image-container').length > 0) {
                    var last = $('.one-image-container:nth-last-of-type(1)');
                    $('html,body').animate({scrollTop : last.height() + last.offset().top}, 1000);
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //更换图片
    $scope.changeImage = function(index) {
        $('#change_image_' + $scope.data.images[index].id).trigger('click');
    };

    //删除图片
    $scope.deleteImage = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        $http.delete($request_urls.homeCarousel + $scope.data.images[index].id).success(function(data) {
            if(data.code == 200) {
                $scope.data.images.splice(index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //编辑图片链接
    $scope.toggleImageState = function(index) {
        if($scope.data.images[index].editing == false) {
            $scope.data.images[index].editing = !$scope.data.images[index].editing;
        } else {
            $http.post($request_urls.homeCarousel, $scope.data.images[index]).success(function(data) {
                if(data.code != 200) {
                    $rootScope.$emit('notify', {msg : data.msg});
                } else {
                    $scope.data.images[index].editing = !$scope.data.images[index].editing;
                }
            });
        }
    };

    //轮播图排序
    $scope.updateImageOrder = function() {
        var order_info = [];
        for(var key in $scope.data.images) {
            order_info.push({
                id            : $scope.data.images[key].id,
                display_order : key
            });
        }
        $http.post($request_urls.updateHomeImagesOrders, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
};

app.controller('HomeCarouselCtrl', ['$scope', '$rootScope', '$http', 'commonFactory', controllers.HomeCarouselCtrl]);