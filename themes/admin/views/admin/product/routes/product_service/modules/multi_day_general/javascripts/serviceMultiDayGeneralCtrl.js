controllers.ServiceIntroduceMultiDayGeneralCtrl = function($scope, $rootScope, $route, $location, $q, $http, $sce ,commonFactory) {
    var path_name = helpers.getRouteTemplateName($route.current);

    $scope.data = {
        general_highlight : {},
        introduce         : {}
    };
    $scope.local = {
        tab_path               : 'introduce_pass_classic',
        section_head           : {
            recommend              : {
                title    : '推荐语',
                is_edit  : false,
                updateCb : function() {
                    $scope.submitRecommendChanges();
                },
                editCb   : function() {
                    $scope.local.uploader_options.avatar_img.image_url = $scope.data.introduce.brief_avatar +
                                                                         '?imageView/5/w/124/h/124';
                    $scope.local.section_head.recommend.is_edit = true;
                }
            },
            multi_day_trip_general : {
                title    : '行程概览',
                is_edit  : false,
                updateCb : function() {
                    $scope.submitTripGeneralChanges();
                },
                editCb   : function() {
                    $scope.local.section_head.multi_day_trip_general.is_edit = true;
                }
            }
        },
        uploader_options       : {
            avatar_img       : {
                target      : $request_urls.updateAvatar,
                input_id    : 'avatar_img',
                image_url   : '',
                accept_type : 'application/jpg',
                beforeCb    : function(event, item) {
                    item.formData = [
                        {
                            recommend_id : '0'
                        }
                    ];
                },
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.avatar_img.image_url = response.data + '?imageView/5/w/124/h/124';
                        $scope.data.introduce.brief_avatar = response.data;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            brief_img        : {
                target      : $request_urls.updateBriefImage,
                input_id    : 'brief_img',
                image_url   : '',
                accept_type : 'application/jpg',
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.brief_img.image_url = response.data.brief_image +
                                                                            '?imageView/5/w/1060/h/400';

                        $scope.data.introduce.brief_image = response.data.brief_image;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            brief_img_mobile : {
                target      : $request_urls.updateBriefImageMobile,
                input_id    : 'brief_img_mobile',
                image_url   : '',
                accept_type : 'application/jpg',
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.brief_img_mobile.image_url = response.data.brief_image_mobile +
                                                                                   '?imageView/5/w/768/h/416';

                        $scope.data.introduce.brief_image_mobile = response.data.brief_image_mobile;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            line_image : {
                target      : $request_urls.updateTripLineImage,
                input_id    : 'line_img',
                image_url   : '',
                accept_type : 'application/jpg',
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.line_image.image_url = response.data.line_image +
                                                                                   '?imageView/5/w/768/h/416';

                        $scope.data.introduce.line_image = response.data.line_image;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            },
            intro_image      : {
                target      : $request_urls.updateTripIntroImage,
                input_id    : 'intro_image',
                image_url   : '',
                accept_type : 'application/jpg',
                successCb   : function(event, xhr, item, response, uploader) {
                    uploader.queue = [];
                    if(response.code == 200) {
                        $scope.local.uploader_options.intro_image.image_url = response.data.trip_intro_image +
                                                                              '?imageView/5/w/1060/h/254';

                        $scope.data.introduce.brief_image = response.data.trip_intro_image;
                    } else {
                        $rootScope.$emit('notify', {msg : response.msg});
                    }
                }
            }
        },
        highlight_dnd          : {
            options  : {
                selector : '.one-highlight',
                offset   : 0
            },
            callback : function(info, dst_index) {
                $scope.data.general_highlight.highlight_refs.splice(info.src_index, 1); //Remove item
                $scope.data.general_highlight.highlight_refs.splice(dst_index, 0, info.src_item); //Insert item
                $scope.updateTripDateOrder();
                $scope.$apply();
            }
        },
        overlay                : {
            has_overlay : false
        },
        highlight_ref_template : {
            id              : "",
            highlight_id    : "",
            date            : "",
            location        : "",
            local_highlight : "",
            lodging         : ""
        },
        radio_options : {
            trip_introduction_status : {
                name  : 'status',
                items : {
                    '0' : '编辑中',
                    '1' : '已生效'
                }
            }
        },
        tour_cities : {
            btn_text    : '添加城市',
            title_str   : 'city_name',
            placeholder : '选择城市',
            select      : {
                value_prop  : 'city_code',
                label_prop  : 'select_label',
                placeholder : '点击选择城市'
            },
            addCb       : function(city_code, next) {
                if(!$scope.data.general_highlight.tour_cities){
                    $scope.data.general_highlight.tour_cities = [];
                }
                var city_index = getIndexByProp($scope.data.general_highlight.tour_cities, 'city_code', city_code);
                if(city_index > -1) {
                    $rootScope.$emit('notify', {msg : '不可重复添加城市'});
                } else {
                    city_index = getIndexByProp($scope.data.cities, 'city_code', city_code);
                    var city = $scope.data.cities[city_index];
                    $scope.data.general_highlight.tour_cities.push({
                        'city_code' : city.city_code,
                        'city_name' : city.city_name
                    });
                    next();
                }
            },
            deleteCb    : function(index) {
                if(window.confirm('取消与所选城市的关联？')) {
                    var city_code = $scope.data.cities[index].city_code;
                    $scope.data.general_highlight.tour_cities.splice(index, 1);
                }
            }
        }
    };


    $scope.init = function() {
        commonFactory.getAjaxSearchCityList().then(function(data) {
            $scope.data.cities = data.map(function(city) {
                city.select_label = city.city_name + ' ' + city.city_pinyin;

                return city;
            });
        });
        var ajax_introduce = $http.get($request_urls.multiDayIntroduce);
        var ajax_highlight = $http.get($request_urls.multiDayHighLight);
        $q.all([ajax_highlight, ajax_introduce]).then(function(values) {
            if(values[0].data.code == 200) {
                $scope.data.general_highlight = values[0].data.data;
            }
            if(values[1].data.code == 200) {
                $scope.data.introduce = values[1].data.data;
            }

            $scope.initData();
        });
    };

    $scope.initData = function() {
        if(!$scope.data.general_highlight || Object.keys($scope.data.general_highlight).length == 0) {
            $scope.data.general_highlight = {
                id                : "",
                product_id        : 0,
                total_days        : "",
                distance          : "",
                highlight_summary : ["", "", ""],
                start_location    : "",
                finish_location   : "",
                suitable_time     : "",
                highlight_refs    : []
            };
        }

        if(!$scope.data.introduce || Object.keys($scope.data.introduce).length == 0) {
            $scope.data.introduce = {
                product_id         : 0,
                brief_author       : "",
                brief_avatar       : "",
                brief_title        : "",
                brief_description  : "",
                brief_image        : "",
                brief_image_mobile : "",
                trip_intro_image   : "",
                status             : "0"
            };
        }

        $scope.initIntroData();
        $scope.initHighLightData();
    };

    $scope.initIntroData = function() {
        $scope.local.uploader_options.brief_img.image_url =
        $scope.data.introduce.brief_image && $scope.data.introduce.brief_image.length > 0 ? $scope.data.introduce.brief_image +
                                                       '?imageView/5/w/1060/h/400' : "";
        $scope.local.uploader_options.intro_image.image_url =
        $scope.data.introduce.trip_intro_image && $scope.data.introduce.trip_intro_image.length > 0 ? $scope.data.introduce.trip_intro_image +
                                                            '?imageView/5/w/1060/h/254' : "";
        $scope.local.uploader_options.brief_img_mobile.image_url =

        $scope.data.introduce.brief_image_mobile && $scope.data.introduce.brief_image_mobile.length > 0 ?
        $scope.data.introduce.brief_image_mobile + '?imageView/5/w/768/h/416' : "";
        $scope.local.uploader_options.line_image.image_url =
        $scope.data.introduce.line_image && $scope.data.introduce.line_image.length > 0 ?
        $scope.data.introduce.line_image + '?imageView/5/w/768/h/416' : "";
    };

    $scope.initHighLightData = function() {
        if($scope.data.general_highlight.highlight_summary.length < 3) {
            for(var i = 0; i < (3 - $scope.data.general_highlight.highlight_summary.length); i++) {
                $scope.data.general_highlight.highlight_summary.push("");
            }
        }

        for(var i in $scope.data.general_highlight.highlight_refs) {
            $scope.data.general_highlight.highlight_refs[i].display_highlight = $scope.data.general_highlight.highlight_refs[i].local_highlight.split(/\r\n|\r|\n/g);
        }
    };

    $scope.submitRecommendChanges = function() {
        $http.post($request_urls.multiDayIntroduce, angular.copy($scope.data.introduce)).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.recommend.is_edit = false;
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.submitTripGeneralChanges = function() {
        var post_data = angular.copy($scope.data.general_highlight);
        var summary = '';
        for(var i in post_data.highlight_summary) {
            if(post_data.highlight_summary[i].length > 0) {
                summary += post_data.highlight_summary[i];
            }
            if(i != post_data.highlight_summary.length - 1) {
                summary += ';';
            }
        }
        post_data.highlight_summary = summary;
        $http.post($request_urls.multiDayHighLight, post_data).success(function(data) {
            if(data.code == 200) {
                $scope.local.section_head.multi_day_trip_general.is_edit = false;
                $scope.data.general_highlight = data.data;
                $scope.initHighLightData();
            } else {
                $rootScope.$emit('notify', {msg : data.msg});
            }
        });
    };

    $scope.updateTripDateOrder = function() {
        var index = 1;
        for(var i in $scope.data.general_highlight.highlight_refs) {
            $scope.data.general_highlight.highlight_refs[i].date = index;
            index++;
        }
    };

    $scope.deleteHighlight = function(index) {
        if(!window.confirm("删除后不可恢复。\n 点击‘确认’删除。")) {
            return;
        }

        $scope.data.general_highlight.highlight_refs.splice(index, 1);
        $scope.updateTripDateOrder();
    };

    $scope.addHighlight = function() {
        var tmp = angular.copy($scope.local.highlight_ref_template);
        tmp.highlight_id = $scope.data.general_highlight.id;
        tmp.date = ($scope.data.general_highlight.highlight_refs.length + 1).toString();
        $scope.data.general_highlight.highlight_refs.push(tmp);
        $scope.editHighLight($scope.data.general_highlight.highlight_refs.length - 1);
    };

    $scope.editHighLight = function(index) {
        $scope.local.overlay.has_overlay = true;
        $scope.local.editing_index = index;
        $scope.local.editing_highlight = angular.copy($scope.data.general_highlight.highlight_refs[index]);
    };

    $scope.toggleOverlay = function() {
        $scope.local.overlay.has_overlay = !$scope.local.overlay.has_overlay;
        if($scope.local.editing_highlight.id.length == 0) {
            $scope.data.general_highlight.highlight_refs.splice($scope.local.editing_index, 1);
        }
        $scope.clearEditingHighLight();
    };

    $scope.saveHighlight = function() {
        if($scope.local.editing_highlight.local_highlight.length > 250) {
            alert("行程亮点的字数请不要超过250字");
            return;
        }

        $scope.local.editing_highlight.display_highlight = $scope.local.editing_highlight.local_highlight.split(/\r\n|\r|\n/g);
        $scope.data.general_highlight.highlight_refs[$scope.local.editing_index] = angular.copy($scope.local.editing_highlight);
        $scope.clearEditingHighLight();
    };

    $scope.clearEditingHighLight = function() {
        $scope.local.editing_index = -1;
        $scope.local.editing_highlight = {};
        $scope.local.overlay.has_overlay = false;
    };

    $scope.init();
};

app.controller('ServiceIntroduceMultiDayGeneralCtrl', [
    '$scope', '$rootScope', '$route', '$location', '$q', '$http', '$sce', 'commonFactory',
    controllers.ServiceIntroduceMultiDayGeneralCtrl
]);