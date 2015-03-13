var editProductTripPlanCtrl = function($scope, $rootScope, FileUploader, $route, $http, $sce) {
    var default_traffic = {
        trans_type  : '0',
        has_traffic : '0',
        description : ''
    };
    $scope.data = {};
    //景点图文
    $scope.planPointImages = [];
    $scope.local = {
        map_url       : request_urls.viewMap,
        current_day   : 0,
        current_step  : 0,
        current_point : {},
        base_info     : {
            is_edit : false
        },
        radio_options : {
            is_online   : {
                name  : 'is_online',
                items : {
                    '0' : '不上线',
                    '1' : '上线'
                }
            },
            has_traffic : {
                name  : 'has_traffic',
                items : {
                    '0' : '否',
                    '1' : '是'
                }
            }
        },
        dnd_options   : {
            plan_days   : {
                options  : {
                    selector : '.one-day',
                    offset   : 0
                },
                callback : function(info, dst_index) {
                    $scope.data.plan_days.splice(info.src_index, 1); //Remove item
                    $scope.data.plan_days.splice(dst_index, 0, info.src_item); //Insert item
                    $scope.updateDayOrder();
                }
            },
            plan_points : {
                options  : {
                    selector : '.one-item',
                    offset   : 0
                },
                callback : function(info, dst_index) {
                    if($scope.local.current_step == 1) {
                        var points = $scope.data.plan_days[$scope.local.current_day].points;
                        points.splice(info.src_index, 1); //Remove item
                        points.splice(dst_index, 0, info.src_item); //Insert item
                        $scope.updatePointsOrder();
                    }
                }
            }
        },
        title_set     : {
            titles       : [
                {title : '行程概述', has_content : false},
                {title : '行程安排', has_content : false},
                {title : '交通方式', has_content : false}
            ],
            switch_title : function(index) {
                if($scope.local.current_step == index || !$scope.local.title_set.titles[index].has_content) {
                    return false;
                } else {
                    $scope.local.current_step = index;
                }
            }
        },
        overlays      : {
            has_overlay  : false,
            overlay_type : null
        },
        traffic_type  : {
            '1' : '驾车',
            '2' : '公交地铁',
            '3' : '步行'
        }
    };

    $scope.uploader = new FileUploader({
        url   : request_urls.planPointImage,
        scope : $scope
    });
    $scope.uploader.filters.push({
        name : 'imagesOnly',
        fn   : function(item) {
            var type = '|' + item.type.toLowerCase().slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
    $scope.uploader.onSuccessItem = function(item, response) {
        $scope.uploader.queue = [];
        if(response.code == 200) {
            $scope.local.current_point.point_images[$scope.local.current_image_index].image_url = response.data.image_url;
        } else {
            alert(response.data);
        }
    };
    $scope.uploader.onBeforeUploadItem = function(item) {
        var current_image = $scope.local.current_point.point_images[$scope.local.current_image_index];
        item.formData = [
            {
                point_id : current_image.point_id,
                image_id : current_image.image_id
            }
        ];
    };
    $scope.uploader.onAfterAddingFile = function(item) {
        item.upload();
    };

    $scope.init = function() {
        $scope.data = $route.current.locals.loadData;

        if($scope.data.plan_days.length > 0) {
            $scope.switchDays(0);
            $scope.initPoints();
        } else {
            $scope.switchDays(-1);
        }
    };

    $scope.saveOperation = function() {
        $http.post(request_urls.changeToOnline, $scope.data.is_online).success(function(data) {
            if(data.code == 200) {
                $scope.local.base_info.is_edit = false;
            } else {
                alert(data.msg);
            }
        });
    };
    $scope.editOperation = function() {
        $scope.local.base_info.is_edit = true;
    };

    $scope.addDay = function() {
        //add a new day
        $http.post(request_urls.planInfo, {
            display_order : $scope.data.plan_days.length
        }).success(function(data) {
            if(data.code == 200) {
                data.data.points = [];
                data.data.traffic = [];
                $scope.data.plan_days.push(data.data);
                $scope.switchDays($scope.data.plan_days.length - 1);
            } else {
                alert(data.msg);
            }
        });
    };
    $scope.delDay = function($index) {
        //delete a specific day
        if(!window.confirm('确认删除第' + ($index + 1) + '天，名为“' + $scope.data.plan_days[$index].title + '”的行程安排')) return;
        $http.delete(request_urls.planInfo + $scope.data.plan_days[$index].plan_id).success(function(data) {
            if(data.code == 200) {
                $scope.data.plan_days.splice($index, 1);
                $scope.switchDays(-1);
                $scope.updateDayOrder();
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.sortDays = function(dont_sort) {
        if(!dont_sort) {
            $scope.data.plan_days = $scope.data.plan_days.sort(function(a, b) {
                return a.display_order - b.display_order;
            });
        }
        $scope.data.plan_days = $scope.data.plan_days.map(function(elem, index) {
            elem.display_order = index + 1;
            return elem;
        });
    };
    $scope.updateDayOrder = function() {
        //update day order
        $scope.sortDays(true);
        $http.post(request_urls.changePlanOrder, $scope.data.plan_days).success(function(data) {
            if(data.code == 200) {
                $scope.switchDays(-1);
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.hasContent = function(day_index) {
        if(day_index == -1) {
            $scope.local.title_set.titles[0].has_content = false;
            $scope.local.title_set.titles[1].has_content = false;
            $scope.local.title_set.titles[2].has_content = false;
        } else {
            var current_day = day_index > -1 ? $scope.data.plan_days[day_index] :
                              $scope.data.plan_days[$scope.local.current_day];

            $scope.local.title_set.titles[0].has_content = current_day.title && current_day.description;
            $scope.local.title_set.titles[1].has_content = !!current_day.points.length;
            $scope.local.title_set.titles[2].has_content = !!current_day.traffic.length;
        }
    };
    $scope.switchDays = function(index) {
        //TODO: need check whether current day has saved then switch day?
        $scope.local.current_day = index;
        $scope.local.current_step = 0;
        $scope.hasContent(index);
    };

    $scope.savePlanPoint = function(cb) {
        var current_day = $scope.data.plan_days[$scope.local.current_day];
        $http.post(request_urls.savePlanPoints, current_day).success(function(data) {
            alert(data.msg);
            if(data.code == 200) {
                $scope.initPoints();
                cb && cb();
            }
        });
    };

    $scope.stepToNext = function() {
        function done() {
            $scope.hasContent($scope.local.current_day);
            if($scope.local.current_step < 2) {
                $scope.local.current_step += 1;
            }
        }

        var current_day = $scope.data.plan_days[$scope.local.current_day];
        var current_pid = current_day.plan_id;

        if($scope.local.current_step == 0) {
            $http.post(request_urls.planInfo + current_pid, current_day).success(function(data) {
                if(data.code == 200) {
                    done();
                } else {
                    alert(data.msg);
                }
            });
        } else if($scope.local.current_step == 1) {
            $scope.savePlanPoint(done);
        } else if($scope.local.current_step == 2) {
            $scope.saveTraffic(current_day);
            $http.post(request_urls.savePlanTraffic + current_pid, current_day.traffic).success(function(data) {
                alert(data.msg);
                if(data.code == 200) {
                    done();
                }
            });
        }
    };

    $scope.getPointIndexByPointId = function(point_id, current_day) {
        for(var i = 0, len = current_day.points.length; i < len; i++) {
            if(current_day.points[i].point_id == point_id) return i;
        }
        return -1;
    };
    $scope.initTraffic = function(current_day) {
        var i, len;
        for(i = 0, len = current_day.traffic.length; i < len; i++) {
            var index = $scope.getPointIndexByPointId(current_day.traffic[i].from_point, current_day);
            if(index != -1) {
                current_day.points[index].traffic = {
                    trans_type  : current_day.traffic[i].trans_type,
                    has_traffic : '1',
                    description : current_day.traffic[i].description
                };
            }
        }
        for(i = 0, len = current_day.points.length; i < len; i++) {
            if(!current_day.points[i].traffic && i < len - 1) {
                current_day.points[i].traffic = angular.copy(default_traffic);
            }
        }
    };
    $scope.saveTraffic = function(current_day) {
        var i, len;
        var traffic = [];

        for(i = 0, len = current_day.points.length - 1; i < len; i++) {
            traffic.push({
                plan_id     : current_day.plan_id,
                to_point    : current_day.points[i + 1].point_id,
                trans_type  : current_day.points[i].traffic.trans_type,
                from_point  : current_day.points[i].point_id,
                description : current_day.points[i].traffic.description
            });
        }
        current_day.traffic = traffic;
    };
    $scope.initPoints = function() {
        $http.get(request_urls.getTripPlan).success(function(data) {
            if(data.code == 200) {
                var i, len, current_day;
                $scope.data.plan_days = data.data.data;

                for(i = 0, len = $scope.data.plan_days.length; i < len; i++) {
                    current_day = $scope.data.plan_days[i];
                    $scope.initTraffic(current_day);
                    current_day.points.forEach(function(elem, index) {
                        if(elem.type == 4) { //商品
                            current_day.points[index].id_set = elem.the_id.split(';');
                            current_day.points[index].alias_set = elem.the_alias.split(';');
                        }
                    });
                }
            }
        });
    };

    $scope.sortPoints = function(dont_sort, current_day) {
        var points = current_day > -1 ? $scope.data.plan_days[current_day].points :
                     $scope.data.plan_days[$scope.local.current_day].points;
        if(!dont_sort) {
            points = points.sort(function(a, b) {
                return a.display_order - b.display_order;
            });
        }
        points = points.map(function(elem, index) {
            elem.display_order = index + 1;
            return elem;
        });
    };
    $scope.updatePointsOrder = function() {
        //update points order
        $scope.sortPoints(true);
        var day = $scope.data.plan_days[$scope.local.current_day];
        $http.post(request_urls.changePlanPointOrder + day.plan_id, day.points).success(function(data) {
            if(data.code != 200) {
                alert(data.msg);
            }
        });
    };

    $scope.updateSet = function(product_id) {
        var index = $scope.local.current_point.id_set.indexOf(product_id);

        if(index > -1) {
            $scope.local.current_point.id_set.splice(index, 1);
            $scope.local.current_point.alias_set.splice(index, 1);
        } else {
            $scope.local.current_point.id_set.push(product_id);
            for(var i = 0, len = $scope.data.product_list.length; i < len; i++) {
                if($scope.data.product_list[i].product_id == product_id) {
                    $scope.local.current_point.alias_set.push($scope.data.product_list[i].description.name);
                    break;
                }
            }
        }
    };

    $scope.overlayCancel = function() {
        $scope.local.overlays.has_overlay = false;
        $scope.local.overlays.overlay_type = null;
    };
    $scope.overlayConfirm = function() {
        var current_day = $scope.data.plan_days[$scope.local.current_day];
        var current_point;
        var current_index = $scope.local.current_point_index;
        var type = $scope.local.current_point_type;

        if($scope.local.current_point_index == -1) {
            current_day.points.push({});
            current_index = current_day.points.length - 1;
        }
        current_point = current_day.points[current_index];

        if(type == 4) {
            $scope.local.current_point.the_id = $scope.local.current_point.id_set.join(';');
            $scope.local.current_point.the_alias = $scope.local.current_point.alias_set.join(';');
        }

        if(type == 5) { //交通
            current_point.traffic = angular.copy($scope.local.current_point);
        } else { //其他，交通是在页面保存时修改
            if(type == 1 || type == 3 || type == 4) {
                current_day.points[current_index] = angular.copy($scope.local.current_point);
            } else if(type == 2) {
                //the_alias, latlng写回$scope.data里，而point_images要保存到point_image表那里，要分开保存
                var reg = /\，/;
                if(reg.test($scope.local.current_point.latlng)) {
                    $scope.local.current_point.latlng = $scope.local.current_point.latlng.replace('，', ',');
                }
                current_day.points[current_index] = angular.copy($scope.local.current_point);
                //TODO: 当编辑多个景点时，因为要保存planPoint时候才进行保存操作，
                // 需要一个对象可以保存多个景点的point_images，而每个point_images里又有多个图文，上传多个景点时候有问题
                //Done: 每个景点操作结束时候就保存
                if(!$scope.local.new_scenic) {
                    $scope.savePointImages();
                }
            }
        }

        $scope.overlayCancel();
    };

    //保存景点图文
    $scope.savePointImages = function() {
        $http.post(request_urls.planPointImages, $scope.local.current_point.point_images).success(function(data) {
            if(data.code !== 200) {
                alert(data.msg);
            }
        });
    };

    $scope.editItem = function(type, index) {
        var current_day = $scope.data.plan_days[$scope.local.current_day];
        $scope.local.current_point_type = type;
        if(index > -1) { //Edit
            $scope.local.current_point_index = index;
            $scope.local.current_point =
            type == 5 ? angular.copy(current_day.points[index].traffic) : angular.copy(current_day.points[index]);
            if(type == 2) {
                $scope.local.new_scenic = false;
                $scope.getPointImages(current_day.points[index].point_id);
            }
        } else { //New - Init Data
            $scope.local.current_point_index = -1;
            $scope.local.current_point = {
                type          : type,
                plan_id       : current_day.plan_id,
                point_id      : '',
                the_id        : '',
                the_alias     : '',
                description   : '',
                display_order : current_day.points.length,
                traffic       : angular.copy(default_traffic)
            };
            if(type == 4) {
                $scope.local.current_point.id_set = [];
                $scope.local.current_point.alias_set = [];
            }
            if(type == 2) {
                $scope.local.current_point.point_images = [];
                $scope.local.new_scenic = true;
            }
        }
        $scope.local.overlays.has_overlay = !!type;
        $scope.local.overlays.overlay_type = !!type ? type : undefined;
    };
    $scope.deleteItem = function(index) {
        if(!window.confirm('确认删除这个点么？')) return;
        var points = $scope.data.plan_days[$scope.local.current_day].points;
        $http.post(request_urls.deletePlanPoint + points[index].point_id).success(function(data) {
            if(data.code == 200) {
                points.splice(index, 1);
                $scope.updatePointsOrder();
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.changePointImage = function(index) {
        $scope.local.current_image_index = index;
        $('#slide-upload').trigger('click');
    };

    $scope.getPointImages = function(point_id) {
        var url = request_urls.planPointImages + point_id;
        $http.get(url).success(function(data) {
            if(data.code == 200) {
                $scope.local.current_point.point_images = data.data;
            } else {
                alert(data.msg);
                return false;
            }
        });
    };

    $scope.addPointImage = function() {
        $scope.local.current_point.point_images.push({
            image_id      : '',
            display_order : $scope.local.current_point.point_images.length + 1,
            image_url     : '',
            point_id      : $scope.local.current_point.point_id,
            title         : '',
            description   : ''
        })
    };

    $scope.delPointImage = function(index) {
        if(!window.confirm('确认删除这条景点图文？')) return;
        $scope.local.current_point.point_images.splice(index, 1);
    };

    $scope.viewMap = function() {
        var url, reg = /\，/, map = document.getElementById('map-url'), latlng = $scope.local.current_point.latlng;
        if(reg.test(latlng)) {
            latlng = latlng.replace('，', ',');
        }
        latlng = latlng.split(',');
        url = $scope.local.map_url + '?longitude=' + latlng[0] + '&latitude=' + latlng[1];
        map.href = url;
    };

    $scope.init();
};

angular.module('ProductEditApp').controller('editProductTripPlanCtrl', [
    '$scope', '$rootScope', 'FileUploader', '$route', '$http', '$sce', editProductTripPlanCtrl
]);