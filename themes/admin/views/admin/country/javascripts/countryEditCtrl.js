controllers.CountryEditCtrl = function($scope, $rootScope, $http, $q, $timeout) {
    var watershed;
    $scope.data = {
        seo       : {
            title       : '',
            description : '',
            keywords    : ''
        },
        all_group : [],
        ad_group  : [],
        all_tabs  : []
    };
    $scope.local = {
        all_ads_in_tab        : [],//所有tab中的广告条
        all_groups_in_tab     : [],//所有tab中的分组
        all_articles_in_group : [], //所有已经被分配的分组
        all_cities_in_group   : [], //所有已经被分组的城市
        all_groups_in_group   : [], //所有已经被分配的分组
        search_text        : '',
        menus              : {
            '1' : {
                label   : '基本信息',
                loading : false
            },
            '2' : {
                label   : '分组运营',
                loading : false
            },
            '3' : {
                label   : '广告条',
                loading : false
            },
            '4' : {
                label   : '主页运营',
                loading : false
            }
        },
        current_menu       : '1',
        group_switch       : {
            value   : {
                'value' : ''
            },
            options : {
                name  : 'value',
                items : {
                    '1' : '商品',
                    '2' : '线路',
                    '3' : '文章',
                    '4' : '城市',
                    '5' : '分组'
                }
            }
        },
        group_status       : {
            '1' : {
                label : '编辑中'
            },
            '2' : {
                label : '已生效'
            }
        },
        edit_group         : false,
        current_group_i    : -1, //目前分组的index，注意和分组ID没有关系
        current_tab_i      : 0,
        group_elem         : {
            id            : '',
            group_id      : '',
            display_order : '',
            type          : ''
        },
        dnd                : {
            options  : {
                selector : '.one-block',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.all_group.splice(info.src_index, 1); //Remove item
                $scope.data.all_group.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateGroupOrder();
            }
        },
        input_tag          : {
            btn_text    : '添加',
            title_str   : 'title',
            placeholder : 'SEO关键词',
            cb          : function(tag, next) {
                next(false, {
                    title : tag
                });
            }
        },
        section_head       : {
            title    : '基本信息',
            is_edit  : false,
            updateCb : function() {
                if($scope.country_seo.$pristine) {
                    $scope.local.section_head.is_edit = false;
                } else if($scope.country_seo.$valid) {
                    $scope.data.seo.keywords = $scope.data.seo.keywords.replace(/，/g, ',').split(',').map(function(elem) {
                        return elem.trim();
                    }).filter(function(elem) {
                        return elem.length > 0;
                    }).join(',');
                    $http.post($request_urls.countrySeo, $scope.data.seo).success(function(data) {
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
        breadcrumb_options : {
            back : {
                part_content : '<span class="i i-eye"></span> '
            },
            body : {
                content : '编辑国家 － '
            }
        },
        uploader_options   : {
            country_cover  : {
                target    : $request_urls.updateOneCountryCover,
                image_url : ''
            },
            country_mobile : {
                target    : $request_urls.updateOneCountryMobileCover,
                image_url : ''
            },
            group_cover    : {
                target    : $request_urls.countryGroupCover,
                input_id  : 'group_upload',
                image_url : '',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            group_id : $scope.data.all_group[$scope.local.current_group_i].group_id
                        }
                    ];
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $rootScope.$emit('notify', {msg : response.msg});
                        $scope.data.all_group[$scope.local.current_group_i].cover_image_url = response.data;
                        $scope.local.uploader_options.group_cover.image_url = response.data;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        }
    };

    $scope.init = function() {
        var ajax_countryInfo = $http.get($request_urls.fetchOneCountry);
        var ajax_countrySeo = $http.get($request_urls.countrySeo);
        var ajax_allGroups = $http.get($request_urls.countryGroup);
        var ajax_countryTab = $http.get($request_urls.countryTab);
        var ajax_countryArticle = $http.get($request_urls.getCountryArticles);

        //var ajax_allGroups = $http.get($request_urls.cityGroups);
        var ajax_allCities = $http.get($request_urls.fetchOneCountryCities);

        $q.all([
            ajax_countryInfo, ajax_countrySeo, ajax_allGroups, ajax_countryTab, ajax_countryArticle, ajax_allCities
        ]).then(function(values) {
            if(values[0].data.code == 200) {
                $scope.data.country = values[0].data.data;
                $scope.data.country.country_image = $scope.data.country.country_image ||
                                                    {cover_url : '', mobile_url : ''};

                $scope.local.uploader_options.country_cover.image_url = $scope.data.country.country_image.cover_url;
                $scope.local.uploader_options.country_mobile.image_url = $scope.data.country.country_image.mobile_url;
            } else {
                $rootScope.$emit('notify', {msg : values[0].data.msg});
            }

            if(values[1].data.code == 200) {
                $scope.data.seo = values[1].data.data;
            } else {
                $rootScope.$emit('notify', {msg : values[3].data.msg});
            }

            if(values[2].data.code == 200) {
                var groups = values[2].data.data || [];
                for(var i in groups) {
                    if(groups[i].type == '6') {
                        $scope.data.ad_group.push(angular.copy(groups[i]));
                        $scope.data.ad_group.map(function(ad) {
                            ad.uploader = {
                                target    : $request_urls.countryGroupCover,
                                image_url : ad.cover_image_url,
                                beforeCb  : function(event, item) {
                                    item.formData = [
                                        {
                                            group_id : ad.group_id,
                                        }
                                    ];
                                },
                                successCb   : function(event, xhr, item, response, uploader) {
                                    uploader.queue = [];
                                    $rootScope.$emit('notify', {msg : response.msg});
                                    if(response.code == 200) {
                                        ad.cover_image_url = response.data;
                                        ad.uploader.image_url = response.data;
                                    }
                                }
                            };
                        });
                    } else {
                        $scope.data.all_group.push(angular.copy(groups[i]));
                    }
                }
                for(var index in $scope.data.all_group) {
                    $scope.data.all_group[index].type_set = true;
                    if($scope.data.all_group[index].type == '5') {
                        $scope.data.all_group[index].refs_order = $scope.data.all_group[index].refs.map(function(elem, group_index) {
                            return {
                                id            : elem.id,
                                group_name    : elem.group_name,
                                display_order : group_index + 1
                            };
                        });
                        var group_refs = $scope.data.all_group[index].refs;
                        for(var gi in group_refs) {
                            if($scope.local.all_groups_in_group.indexOf(group_refs[gi].id) == -1) {
                                $scope.local.all_groups_in_group.push(group_refs[gi].id);
                            }
                        }
                    } else if($scope.data.all_group[index].type == '4') {
                        $scope.data.all_group[index].refs_order = $scope.data.all_group[index].refs.map(function(elem, city_index) {
                            return {
                                id            : elem.id,
                                display_order : city_index + 1
                            };
                        });
                        var group_refs = $scope.data.all_group[index].refs;
                        for(var gi in group_refs) {
                            if($scope.local.all_cities_in_group.indexOf(group_refs[gi].id) == -1) {
                                $scope.local.all_cities_in_group.push(group_refs[gi].id);
                            }
                        }
                    } else if($scope.data.all_group[index].type == '3') {
                        $scope.data.all_group[index].refs_order = $scope.data.all_group[index].refs.map(function(elem, article_index) {
                            return {
                                id            : elem.id,
                                article_name  : elem.article_name,
                                image_url     : elem.image_url,
                                uploader      : {
                                    target    : $request_urls.CountryRefCover,
                                    image_url : elem.image_url,
                                    beforeCb  : function(event, item) {
                                        item.formData = [
                                            {
                                                group_id : elem.group_id,
                                                id       : elem.id
                                            }
                                        ]
                                    }
                                },
                                status        : elem.status,
                                display_order : article_index + 1
                            };
                        });
                        var group_refs = $scope.data.all_group[index].refs;
                        for(var gi in group_refs) {
                            if($scope.local.all_articles_in_group.indexOf(group_refs[gi].id) == -1) {
                                $scope.local.all_articles_in_group.push(group_refs[gi].id);
                            }
                        }
                    } else if($scope.data.all_group[index].type == '2'){
                        $scope.data.all_group[index].refs_order = $scope.data.all_group[index].refs.map(function(elem, product_index) {
                            return {
                                id            : elem.id,
                                product_name  : elem.product_name,
                                name          : elem.name,
                                image_url     : elem.image_url,
                                uploader      : {
                                    target    : $request_urls.CountryRefCover,
                                    image_url : elem.image_url,
                                    beforeCb  : function(event, item) {
                                        item.formData = [
                                            {
                                                group_id : elem.group_id,
                                                id       : elem.id
                                            }
                                        ]
                                    }
                                },
                                status        : elem.status,
                                display_order : product_index + 1
                            };
                        });
                    } else {
                        $scope.data.all_group[index].refs_order = $scope.data.all_group[index].refs.map(function(elem, product_index) {
                            return {
                                id            : elem.id,
                                product_name  : elem.product_name,
                                display_order : product_index + 1
                            };
                        });
                    }
                }
                ;
            } else {
                $rootScope.$emit('notify', {msg : '1'});
            }

            if(values[3].data.code == 200) {
                $scope.data.tabs = values[3].data.data;
                $scope.data.all_tabs = values[3].data.data;
                for(var tab_index in $scope.data.tabs) {
                    $scope.data.tabs[tab_index].groups_order = $scope.data.tabs[tab_index].groups.map(function(child, child_index) {
                        return {
                            group_id      : child.group_id,
                            name          : child.name,
                            summary       : child.summary,
                            type          : child.type,
                            display_order : child_index + 1
                        }
                    });
                    for(var ci in $scope.data.tabs[tab_index].groups) {
                        if($scope.data.tabs[tab_index].groups[ci].type != 6 &&
                           $scope.local.all_groups_in_tab.indexOf($scope.data.tabs[tab_index].groups[ci].group_id) ==
                           -1) {
                            $scope.local.all_groups_in_tab.push($scope.data.tabs[tab_index].groups[ci].group_id);
                        }
                        if($scope.data.tabs[tab_index].groups[ci].type == 6 &&
                           $scope.local.all_ads_in_tab.indexOf($scope.data.tabs[tab_index].groups[ci].group_id) == -1) {
                            $scope.local.all_ads_in_tab.push($scope.data.tabs[tab_index].groups[ci].group_id);
                        }
                    }
                }
            } else {
                $rootScope.$emit('notify', {msg : values[3].data.msg});
            }

            if(values[4].data.code == 200) {
                $scope.data.articles = values[4].data.data;
            } else {
                $rootScope.$emit('notify', {msg : values[4].data.msg});
            }

            if(values[5].data.code == 200) {
                $scope.data.cities = values[5].data.data;
            } else {
                $rootScope.$emit('notify', {msg : values[5].data.msg});
            }


            $scope.local.breadcrumb_options.back.part_content += $scope.data.country.cn_name;
            $scope.local.breadcrumb_options.back.partClickCb = function() {
                window.open($request_urls.baseUrl + $scope.data.country.link_url, '_blank')
            };
            $scope.local.breadcrumb_options.body.content += $scope.data.country.cn_name;

            $rootScope.$emit('loadStatus', false);
            $rootScope.$emit('setBreadcrumb', $scope.local.breadcrumb_options);

            watershed = $('#citygroup_watershed').offset().top;
        });
    };

    function scrollToWatershed() {
        if($(window).scrollTop() < watershed) {
            $(window).scrollTop(watershed);
        }
    }

    function getChildIndex(elem_id, one_group) {
        var elem_index = -1;
        for(var i = 0, len = one_group.refs_order.length; i < len; i++) {
            if(one_group.refs_order[i].id == elem_id) {
                elem_index = i;
                break;
            }
        }
        return elem_index;
    }

    //tab分组索引
    function getTabGroupIndex(group_id, one_tab) {
        var group_index = -1;
        for(var i = 0, len = one_tab.groups_order.length; i < len; i++) {
            if(one_tab.groups_order[i].group_id == group_id) {
                group_index = i;
                break;
            }
        }
        return group_index;
    }

    function reorderTabGroup(one_tab) {
        one_tab.groups_order.sort(function(a, b) {
            return a.display_order - b.display_order;
        });
        return one_tab.groups_order.map(function(elem, index) {
            elem.display_order = index + 1;
            return elem;
        });
    }

    function reorderTab(all_tabs) {

        all_tabs.sort(function(a, b) {
            return a.display_order - b.display_order;
        });
        return all_tabs.map(function(elem, index) {
            elem.display_order = index + 1;
            return elem;
        });
    }

    //分组内元素排序
    function reorderGroup(one_group) {
        one_group.refs_order.sort(function(a, b) {
            return a.display_order - b.display_order;
        });
        return one_group.refs_order.map(function(elem, index) {
            elem.display_order = index + 1;
            return elem;
        });
    }

    //页面操作
    $scope.setCurrentMenu = function(key) {
        $scope.local.current_menu = key;
    };

    //分组运营

    //添加分组
    $scope.addGroup = function(type) {
        $http.post($request_urls.countryGroup, {type : type ? type : ''}).success(function(data) {
            if(data.code == 200) {
                var new_group = data.data;
                if(new_group.type != 6) {
                    new_group.type_set = false;
                    new_group.refs_order = [];
                    var length = $scope.data.all_group.push(new_group);
                    $scope.setCurrentGroup(length - 1);
                    //$timeout(function() {
                    //    $(window).scrollTop($('.citygroups-list-container .one-block:nth-last-of-type(1)').offset().top);
                    //}, 50);
                } else {
                    new_group.uploader = {
                        target    : $request_urls.countryGroupCover,
                        image_url : new_group.cover_image_url,
                        beforeCb  : function(event, item) {
                            item.formData = [
                                {
                                    group_id : new_group.group_id,
                                }
                            ];
                        }
                    };
                    $scope.data.ad_group.push(new_group);
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除分组
    $scope.deleteGroup = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        $http.delete($request_urls.countryGroup + '&group_id=' +
                     $scope.data.all_group[index].group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.all_group.splice(index, 1);
                //防止删除后继续显示现在的分组内容，设置成不显示任何分组
                $scope.setCurrentGroup(-1);
            } else {
                $rootScope.$emit('notify', {
                    msg : '删除失败'
                });
            }
        });
    };
    //保存分组类型
    $scope.saveGroupType = function() {
        $scope.data.all_group[$scope.local.current_group_i].type = $scope.local.group_switch.value.value;
        $scope.data.all_group[$scope.local.current_group_i].type_set = true;
        $scope.local.group_switch.value.value = '';
        //不要觉得底下这一行怪怪的，为了保证radio-switch 是展开的，就这么做了，如果有别人看到了这一行。。莫怪。。。
        $('.radio-switch').addClass('expand');
        $scope.updateGroupInfo($scope.local.current_group_i);
    };
    //更新分组信息
    $scope.updateGroupInfo = function(i) {
        $http.post($request_urls.countryGroup + '&group_id=' +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, {
            type          : $scope.data.all_group[i].type,
            name          : $scope.data.all_group[i].name,
            brief         : $scope.data.all_group[i].brief,
            summary       : $scope.data.all_group[i].summary,
            description   : $scope.data.all_group[i].name.description,
            tab_id        : $scope.data.all_group[i].name.tab_id,
            display_order : $scope.data.all_group[i].display_order,
            link_url      : $scope.data.all_group[i].link_url,
            city_code     : $scope.data.all_group[i].city_code
        }).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //更新分组状态
    $scope.updateGroupState = function(group_index, status_id) {
        var status = $scope.local.group_status[status_id];
        var current_group = $scope.data.all_group[group_index];
        if(!status) return;
        if( current_group.tab_id != 0 && current_group.status == 2){
            $rootScope.$emit('notify', {msg : '请先将该分组从tab 中删除'});
        } else {
            $http.post($request_urls.changeCountryGroupStatus + current_group.group_id, {
                status : status_id
            }).success(function(data) {
                if(data.code == 200) {
                    current_group.status = status_id;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
        }


    };
    //切换分组
    $scope.setCurrentGroup = function(i) {
        $scope.local.edit_group = false;
        $scope.local.current_group_i = i;
        if(i > -1) { //防止删除图片时设置成-1报错
            $scope.local.uploader_options.group_cover.image_url = $scope.data.all_group[i].cover_image_url;
        }
        scrollToWatershed();
    };
    //切换编辑状态
    $scope.toggleGroupEdit = function() {
        if($scope.local.edit_group) {
            $scope.updateGroupInfo($scope.local.current_group_i);
        }
        $scope.local.edit_group = !$scope.local.edit_group;
    };
    //调整分组顺序
    $scope.updateGroupOrder = function() {
        var order_info = [];
        for(var key in $scope.data.all_group) {
            order_info.push({
                group_id      : $scope.data.all_group[key].group_id,
                display_order : key
            });
        }

        $http.post($request_urls.changeCountryGroupsDisplayOrder, order_info).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //添加成员组
    $scope.addGroupToGroup = function(id) {
        var new_elem = $scope.local.group_elem;
        new_elem.id = id;
        new_elem.group_id = $scope.data.all_group[$scope.local.current_group_i].group_id;
        new_elem.display_order = $scope.data.all_group[$scope.local.current_group_i].refs_order.length + 1;
        new_elem.type = $scope.data.all_group[$scope.local.current_group_i].type;
        $http.post($request_urls.countryGroupRef + new_elem.group_id, new_elem).success(function(data) {
            if(data.code == 200) {
                $scope.data.all_group[$scope.local.current_group_i].refs_order.push(data.data);
                $scope.local.all_groups_in_group.push(id);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除成员组
    $scope.deleteGroupFromGroup = function(id) {
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        $http.delete($request_urls.countryGroupRef + current_group.group_id + '&id=' + id).success(function(data) {
            if(data.code == 200) {
                var order_index = getIndexByProp(current_group.refs_order, 'id', id);
                $scope.data.all_group[$scope.local.current_group_i].refs_order.splice(order_index, 1);
                $scope.data.all_group[$scope.local.current_group_i].refs_order = reorderGroup($scope.data.all_group[$scope.local.current_group_i]);

                var group_index = $scope.local.all_groups_in_group.indexOf(id);
                $scope.local.all_groups_in_group.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //成员组排序
    $scope.updateDoubleGroupOrder = function(group) {
        var group_index = $scope.local.current_group_i;
        var ref_index = getChildIndex(group.id, $scope.data.all_group[group_index]);
        $scope.data.all_group[group_index].refs_order.splice(ref_index, 1);
        $scope.data.all_group[group_index].refs_order.splice(group.display_order - 1, 0, group);
        $scope.data.all_group[group_index].refs_order = reorderGroup($scope.data.all_group[group_index]);
        $http.post($request_urls.changeRefDisplayOrder +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, $scope.data.all_group[group_index].refs_order).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.isGroupInGroup = function(group_id) {
        if($scope.local.current_group_i == -1 || $scope.data.all_group[$scope.local.current_group_i].type != 5) return;

        return getChildIndex(group_id, $scope.data.all_group[$scope.local.current_group_i]) > -1;
    };
    $scope.isGroupInAllGroups = function(group_id) {
        return $scope.local.all_groups_in_group.indexOf(group_id) > -1;
    };

    //添加城市
    $scope.addCityToGroup = function(id) {
        var new_elem = $scope.local.group_elem;
        new_elem.id = id;
        new_elem.group_id = $scope.data.all_group[$scope.local.current_group_i].group_id;
        new_elem.display_order = $scope.data.all_group[$scope.local.current_group_i].refs_order.length + 1;
        new_elem.type = $scope.data.all_group[$scope.local.current_group_i].type;
        $http.post($request_urls.countryGroupRef + new_elem.group_id, new_elem).success(function(data) {
            if(data.code == 200) {
                $scope.data.all_group[$scope.local.current_group_i].refs_order.push(data.data);
                $scope.local.all_cities_in_group.push(id);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除城市
    $scope.deleteCityFromGroup = function(id) {
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        $http.delete($request_urls.countryGroupRef + current_group.group_id + '&id=' + id).success(function(data) {
            if(data.code == 200) {
                var order_index = getIndexByProp(current_group.refs_order, 'id', id);
                $scope.data.all_group[$scope.local.current_group_i].refs_order.splice(order_index, 1);
                $scope.data.all_group[$scope.local.current_group_i].refs_order = reorderGroup($scope.data.all_group[$scope.local.current_group_i]);

                var group_index = $scope.local.all_cities_in_group.indexOf(id);
                $scope.local.all_cities_in_group.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //城市排序
    $scope.updateCityGroupOrder = function(city) {
        var group_index = $scope.local.current_group_i;
        var ref_index = getChildIndex(city.id, $scope.data.all_group[group_index]);
        $scope.data.all_group[group_index].refs_order.splice(ref_index, 1);
        $scope.data.all_group[group_index].refs_order.splice(city.display_order - 1, 0, city);
        $scope.data.all_group[group_index].refs_order = reorderGroup($scope.data.all_group[group_index]);
        $http.post($request_urls.changeRefDisplayOrder +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, $scope.data.all_group[group_index].refs_order).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.getCityName = function(city_code) {
        for(var key in $scope.data.cities) {
            if($scope.data.cities[key].city_code == city_code) {
                return $scope.data.cities[key].cn_name;
            }
        }
    };
    $scope.isCityInGroup = function(city_code) {
        if($scope.local.current_group_i == -1 || $scope.data.all_group[$scope.local.current_group_i].type != 4) return;

        return getChildIndex(city_code, $scope.data.all_group[$scope.local.current_group_i]) > -1;
    };
    $scope.isCityInAllGroups = function(city_code) {
        return $scope.local.all_cities_in_group.indexOf(city_code) > -1;
    };

    //添加文章
    $scope.addArticleToGroup = function(id) {
        var new_elem = $scope.local.group_elem;
        new_elem.id = id;
        new_elem.group_id = $scope.data.all_group[$scope.local.current_group_i].group_id;
        new_elem.display_order = $scope.data.all_group[$scope.local.current_group_i].refs_order.length + 1;
        new_elem.type = $scope.data.all_group[$scope.local.current_group_i].type;
        new_elem.status = 1;
        $http.post($request_urls.countryGroupRef + new_elem.group_id, new_elem).success(function(data) {
            if(data.code == 200) {
                var new_article = data.data;
                new_article.uploader = {
                    target    : $request_urls.CountryRefCover,
                    image_url : new_article.image_url,
                    beforeCb  : function(event, item) {
                        item.formData = [
                            {
                                group_id : new_elem.group_id,
                                id       : new_article.id
                            }
                        ]
                    }
                };
                $scope.data.all_group[$scope.local.current_group_i].refs_order.push(new_article);
                $scope.local.all_articles_in_group.push(id);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除文章
    $scope.deleteArticleFromGroup = function(id) {
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        $http.delete($request_urls.countryGroupRef + current_group.group_id + '&id=' + id).success(function(data) {
            if(data.code == 200) {
                var order_index = getIndexByProp(current_group.refs_order, 'id', id);
                $scope.data.all_group[$scope.local.current_group_i].refs_order.splice(order_index, 1);
                $scope.data.all_group[$scope.local.current_group_i].refs_order = reorderGroup($scope.data.all_group[$scope.local.current_group_i]);

                var group_index = $scope.local.all_articles_in_group.indexOf(id);
                $scope.local.all_articles_in_group.splice(group_index, 1);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //更改文章状态
    $scope.updateArticleState = function(article, status_id) {
        var status = $scope.local.group_status[status_id];
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        if(!status) return;
        article.status = status_id;
        $http.post($request_urls.countryGroupRef + current_group.group_id, {
            id            : article.id,
            display_order : article.display_order,
            status        : article.status
        }).success(function(data) {
            if(data.code == 200) {
                var article_index = getIndexByProp(current_group.refs_order, 'id', article.id);
                current_group.refs_order[article_index].status = status_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //文章排序
    $scope.updateArticleGroupOrder = function(article) {
        var group_index = $scope.local.current_group_i;
        var ref_index = getChildIndex(article.id, $scope.data.all_group[group_index]);
        $scope.data.all_group[group_index].refs_order.splice(ref_index, 1);
        $scope.data.all_group[group_index].refs_order.splice(article.display_order - 1, 0, article);
        $scope.data.all_group[group_index].refs_order = reorderGroup($scope.data.all_group[group_index]);
        var postData = [];
        for(var current_order in $scope.data.all_group[group_index].refs_order) {
            postData.push(
                {
                    id            : $scope.data.all_group[group_index].refs_order[current_order].id,
                    display_order : $scope.data.all_group[group_index].refs_order[current_order].display_order
                }
            )
        }
        $http.post($request_urls.changeRefDisplayOrder +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, postData).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.isArticleInGroup = function(article_id) {
        if($scope.local.current_group_i == -1 || $scope.data.all_group[$scope.local.current_group_i].type != 3) return;

        return getChildIndex(article_id, $scope.data.all_group[$scope.local.current_group_i]) > -1;
    };
    $scope.isArticleInAllGroups = function(article_id) {
        return $scope.local.all_articles_in_group.indexOf(article_id) > -1;
    };

    //添加商品
    $scope.addProductToGroup = function(id) {
        var new_elem = $scope.local.group_elem;
        new_elem.id = id;
        new_elem.group_id = $scope.data.all_group[$scope.local.current_group_i].group_id;
        new_elem.display_order = $scope.data.all_group[$scope.local.current_group_i].refs_order.length + 1;
        new_elem.type = $scope.data.all_group[$scope.local.current_group_i].type;
        $http.post($request_urls.countryGroupRef + new_elem.group_id, new_elem).success(function(data) {
            if(data.code == 200) {
                $scope.data.all_group[$scope.local.current_group_i].refs_order.push(data.data);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除商品
    $scope.deleteProductFromGroup = function(id) {
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        $http.delete($request_urls.countryGroupRef + current_group.group_id + '&id=' + id).success(function(data) {
            if(data.code == 200) {
                var order_index = getIndexByProp(current_group.refs_order, 'id', id);
                $scope.data.all_group[$scope.local.current_group_i].refs_order.splice(order_index, 1);
                $scope.data.all_group[$scope.local.current_group_i].refs_order = reorderGroup($scope.data.all_group[$scope.local.current_group_i]);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //商品排序
    $scope.updateProductGroupOrder = function(product) {
        var group_index = $scope.local.current_group_i;
        var ref_index = getChildIndex(product.id, $scope.data.all_group[group_index]);
        $scope.data.all_group[group_index].refs_order.splice(ref_index, 1);
        $scope.data.all_group[group_index].refs_order.splice(product.display_order - 1, 0, product);
        $scope.data.all_group[group_index].refs_order = reorderGroup($scope.data.all_group[group_index]);
        $http.post($request_urls.changeRefDisplayOrder +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, $scope.data.all_group[group_index].refs_order).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //添加线路
    $scope.addLineToGroup = function(id) {
        var new_elem = $scope.local.group_elem;
        new_elem.id = id;
        new_elem.group_id = $scope.data.all_group[$scope.local.current_group_i].group_id;
        new_elem.display_order = $scope.data.all_group[$scope.local.current_group_i].refs_order.length + 1;
        new_elem.type = $scope.data.all_group[$scope.local.current_group_i].type;
        new_elem.status = 1;
        $http.post($request_urls.countryGroupRef + new_elem.group_id, new_elem).success(function(data) {
            if(data.code == 200) {
                var new_line = data.data;
                new_line.uploader = {
                    target    : $request_urls.CountryRefCover,
                    image_url : new_line.image_url,
                    beforeCb  : function(event, item) {
                        item.formData = [
                            {
                                group_id : new_elem.group_id,
                                id       : new_line.id
                            }
                        ]
                    }
                };
                $scope.data.all_group[$scope.local.current_group_i].refs_order.push(new_line);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除线路
    $scope.deleteLineFromGroup = function(id) {
        var current_group = $scope.data.all_group[$scope.local.current_group_i];
        $http.delete($request_urls.countryGroupRef + current_group.group_id + '&id=' + id).success(function(data) {
            if(data.code == 200) {
                var order_index = getIndexByProp(current_group.refs_order, 'id', id);
                $scope.data.all_group[$scope.local.current_group_i].refs_order.splice(order_index, 1);
                $scope.data.all_group[$scope.local.current_group_i].refs_order = reorderGroup($scope.data.all_group[$scope.local.current_group_i]);
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //线路排序
    $scope.updateLineGroupOrder = function(product) {
        var group_index = $scope.local.current_group_i;
        var ref_index = getChildIndex(product.id, $scope.data.all_group[group_index]);
        $scope.data.all_group[group_index].refs_order.splice(ref_index, 1);
        $scope.data.all_group[group_index].refs_order.splice(product.display_order - 1, 0, product);
        $scope.data.all_group[group_index].refs_order = reorderGroup($scope.data.all_group[group_index]);
        var postData = [];
        for(var current_order in $scope.data.all_group[group_index].refs_order) {
            postData.push(
                {
                    id            : $scope.data.all_group[group_index].refs_order[current_order].id,
                    display_order : $scope.data.all_group[group_index].refs_order[current_order].display_order
                }
            )
        }
        $http.post($request_urls.changeRefDisplayOrder +
                   $scope.data.all_group[$scope.local.current_group_i].group_id, postData).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //更新线路信息
    $scope.updateLineInfo = function(child, status_id) {
        if(status_id){
            var status = $scope.local.group_status[status_id];
            if(!status) return;
            child.status = status_id;
        }
        var current_group = $scope.data.all_group[$scope.local.current_group_i];

        $http.post($request_urls.countryGroupRef + current_group.group_id, {
            display_order : child.display_order,
            id            : child.id,
            name          : child.name,
            status        : child.status
        }).success(function(data) {
            if(data.code == 200) {
                if(status_id){
                    var child_index = getIndexByProp(current_group.refs_order, 'id', child.id);
                    current_group.refs_order[child_index].status = status_id;
                }
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    //新增广告条
    $scope.addAd = function() {
        $scope.addGroup(6);
    };
    //更新广告条信息
    $scope.updateAdInfo = function(group_id) {
        var index = getIndexByProp($scope.data.ad_group, 'group_id', group_id);
        var postData = $scope.data.ad_group[index];
        var temp_uploader = postData.uploader;
        delete postData.uploader;
        $http.post($request_urls.countryGroup + '&group_id=' + group_id, postData).success(function(data) {
                if(data.code == 200) {
                    $scope.data.ad_group[index].uploader = temp_uploader;
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
    };
    //更新广告条状态
    $scope.updateAdStatus = function(group_id, status_id) {
        var index = getIndexByProp($scope.data.ad_group, 'group_id', group_id);
        var status = $scope.local.group_status[status_id];
        if(!status) return;

        $http.post($request_urls.changeCountryGroupStatus + group_id, {
            status : status_id
        }).success(function(data) {
            if(data.code == 200) {
                $scope.data.ad_group[index].status = status_id;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //删除广告条
    $scope.deleteAd = function(group_id) {
        if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
        var index = getIndexByProp($scope.data.ad_group, 'group_id', group_id);
        $http.delete($request_urls.countryGroup + '&group_id=' + group_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.ad_group.splice(index, 1);
                //防止删除后继续显示现在的分组内容，设置成不显示任何分组
                $scope.setCurrentGroup(-1);
            } else {
                $rootScope.$emit('notify', {
                    msg : '删除失败'
                });
            }
        });
    };

    //主页运营
    $scope.switchTab = function(index) {
        $scope.local.current_tab_i = index;
    };
    //新增tab
    $scope.addIndexTab = function() {
        var display_order = $scope.data.tabs.length + 1;
        $http.post($request_urls.countryTab, {display_order : display_order}).success(function(data) {
            if(data.code == 200) {
                var new_tab = data.data;
                new_tab.groups_order = [];
                var length = $scope.data.tabs.push(new_tab);
                $scope.local.current_tab_i = $scope.data.tabs.length - 1;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    $scope.deleteIndexTab = function() {
        if($scope.data.tabs.length > 1) {
            if(!window.confirm("删除后不可恢复。\n点击'确认'删除。")) return;
            $http.delete($request_urls.countryTab + '&tab_id=' +
                         $scope.data.tabs[$scope.local.current_tab_i].tab_id).success(function(data) {
                if(data.code == 200) {
                    $scope.data.all_tabs.splice($scope.local.current_tab_i, 1);
                    $scope.switchTab(0);
                    $scope.data.all_tabs = reorderTab($scope.data.all_tabs);
                } else {
                    $rootScope.$emit('notify', {
                        msg : '删除失败'
                    });
                }
            });
        } else {
            $rootScope.$emit('notify', {msg : '至少保留一个Tab'});
        }
    };
    //更新tab信息
    $scope.updateTabInfo = function() {
        $http.post($request_urls.countryTab + '&tab_id=' + $scope.data.tabs[$scope.local.current_tab_i].tab_id,
            $scope.data.tabs[$scope.local.current_tab_i]).success(function(data) {
                if(data.code != 200) {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
    };
    //更新tab状态
    $scope.updateTabStatus = function(status_id) {
        var status = $scope.local.group_status[status_id];
        if(!status) return;

        $scope.data.tabs[$scope.local.current_tab_i].status = status_id;
        $scope.updateTabInfo();
    };

    //tab删除分组
    $scope.deleteGroupFromTab = function(group_id) {
        var group_index = getIndexByProp($scope.data.tabs[$scope.local.current_tab_i].groups_order, 'group_id', group_id);
        var group = $scope.data.tabs[$scope.local.current_tab_i].groups_order[group_index];
        group.tab_id = 0;
        $http.post($request_urls.countryGroup + '&group_id=' + group.group_id,{
            type          : group.type,
            name          : group.name,
            brief         : group.brief,
            summary       : group.summary,
            description   : group.name.description,
            tab_id        : group.name.tab_id,
            display_order : group.display_order,
            link_url      : group.link_url,
            city_code     : group.city_code
        }).success(function(data) {
                if(data.code == 200) {
                    var order_index = getIndexByProp($scope.data.tabs[$scope.local.current_tab_i].groups_order, 'group_id', group_id);
                    $scope.data.tabs[$scope.local.current_tab_i].groups_order.splice(order_index, 1);
                    $scope.data.tabs[$scope.local.current_tab_i].groups_order = reorderTabGroup($scope.data.tabs[$scope.local.current_tab_i]);

                    if(group.type != 6) {
                        var tab_group_index = $scope.local.all_groups_in_tab.indexOf(group_id);
                        $scope.local.all_groups_in_tab.splice(tab_group_index, 1);
                    } else {
                        var tab_ad_index = $scope.local.all_ads_in_tab.indexOf(group_id);
                        $scope.local.all_ads_in_tab.splice(tab_ad_index, 1);
                    }
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });

    };
    //tab新增分组
    $scope.addGroupToTab = function(group) {
        group.tab_id = $scope.data.tabs[$scope.local.current_tab_i].tab_id;
        group.display_order = $scope.data.tabs[$scope.local.current_tab_i].groups_order.length + 1;
        $http.post($request_urls.countryGroup + '&group_id=' + group.group_id,{
            type          : group.type,
            name          : group.name,
            brief         : group.brief,
            summary       : group.summary,
            description   : group.description,
            tab_id        : group.tab_id,
            display_order : group.display_order,
            link_url      : group.link_url,
            city_code     : group.city_code
        }).success(function(data) {
                if(data.code == 200) {
                    $scope.data.tabs[$scope.local.current_tab_i].groups_order.push(group);
                    if(group.type != 6) {
                        $scope.local.all_groups_in_tab.push(group.group_id);
                    } else {
                        $scope.local.all_ads_in_tab.push(group.group_id);
                    }
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
    };
    //tab内排序
    $scope.updateTabGroupOrder = function(group) {
        var tab_index = $scope.local.current_tab_i;
        var group_index = getTabGroupIndex(group.group_id, $scope.data.tabs[tab_index]);
        $scope.data.tabs[tab_index].groups_order.splice(group_index, 1);
        $scope.data.tabs[tab_index].groups_order.splice(group.display_order - 1, 0, group);
        $scope.data.tabs[tab_index].groups_order = reorderTabGroup($scope.data.tabs[tab_index]);
        var postData = [];
        for(var current_order in $scope.data.tabs[tab_index].groups_order) {
            postData.push(
                {
                    group_id      : $scope.data.tabs[tab_index].groups_order[current_order].group_id,
                    display_order : $scope.data.tabs[tab_index].groups_order[current_order].display_order
                }
            )
        }
        $http.post($request_urls.changeCountryGroupsDisplayOrder, postData).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };
    //更改tab排序
    $scope.updateTabOrder = function() {
        var current_tab = $scope.data.tabs[$scope.local.current_tab_i];
        $scope.data.all_tabs.splice($scope.local.current_tab_i, 1);
        $scope.data.all_tabs.splice(current_tab.display_order - 1, 0, current_tab);
        var tab_order = reorderTab($scope.data.all_tabs);

        $http.post($request_urls.changeCountryTabsDisplayOrder, tab_order).success(function(data) {
            if(data.code != 200) {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.isGroupInTab = function(group_id) {
        return getTabGroupIndex(group_id, $scope.data.tabs[$scope.local.current_tab_i]) > -1;
    };
    $scope.isGroupInAllTabs = function(group_id) {
        return $scope.local.all_groups_in_tab.indexOf(group_id) > -1;
    };
    $scope.isAdInTab = function(group_id) {
        return getTabGroupIndex(group_id, $scope.data.tabs[$scope.local.current_tab_i]) > -1;
    };
    $scope.isAdInAllTabs = function(group_id) {
        return $scope.local.all_ads_in_tab.indexOf(group_id) > -1;
    };

    $scope.init();
};

app.controller('CountryEditCtrl', ['$scope', '$rootScope', '$http', '$q', '$timeout', controllers.CountryEditCtrl]);