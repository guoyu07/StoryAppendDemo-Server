controllers.HotelRoomCtrl = function($scope, $rootScope, $route, $http, $q, FileUploader) {

    $scope.local = {
        service_items    : [],
        current_edit     : -1,
        selected_service : [],
        uploader_options : {
            update_cover : {
                target    : $request_urls.updateRoomImage,
                image_url : '',
                input_id  : 'img-upload',
                beforeCb  : function(event, item) {
                    item.formData = [
                        {
                            room_type_id  : $scope.data.room_list[$scope.local.current_edit].room_type_id,
                            image_id      : '',
                            display_order : $scope.data.room_list[$scope.local.current_edit].images.length + 1
                        }
                    ];
                },
                successCb : function(event, xhr, item, response, uploader) {
                    if(response.code == 200) {
                        $scope.local.uploader_options.update_cover.image_url = response.data.image_url;
                        $scope.local.uploader_options.update_cover.in_progress = false;

                        $scope.data.room_list[$scope.local.current_edit].images.push(response.data);
                    } else {
                        alert(response.msg);
                    }
                }
            }
        }
    };
    $scope.onlyNumbers = /^\d+$/;
    $scope.data = {};


    $scope.init = function() {
        $scope.data.room_list = angular.copy($route.current.locals.loadData.rooms);
        for(var room_index in $scope.data.room_list){
            $scope.data.room_list[room_index].bed_policy_md = decomposeMarkdown($scope.data.room_list[room_index].bed_policy_md);
            $scope.data.room_list[room_index].breakfast_md = decomposeMarkdown($scope.data.room_list[room_index].breakfast_md);
        }
        $scope.local.service_items = angular.copy($route.current.locals.loadData.room_services);
        $scope.local.path_name = helpers.getRouteTemplateName($route.current);
        $scope.local.back_url = window.location.href;
    };

    $scope.editRoom = function(index) {
        $scope.local.current_edit = index;
    };

    $scope.submitChanges = function() {
        var added_policy_tips = false;
        var i = $scope.local.current_edit;
        var post_data = angular.copy($scope.data.room_list[i]);
        post_data.bed_policy_md = composeMarkdown(post_data.bed_policy_md);
        post_data.breakfast_md = composeMarkdown(post_data.breakfast_md);
        for(var p in post_data.policies) {
            if(post_data.policies[p].hasOwnProperty("age_1") &&
               post_data.policies[p].hasOwnProperty("age_2")) {
                post_data.policies[p].age_range = post_data.policies[p].age_1 + "-" +
                                                  post_data.policies[p].age_2;

            } else {
                post_data.policies[p].policy = post_data.policy_tips;
                added_policy_tips = true;
            }
        }

        if(!added_policy_tips && post_data.hasOwnProperty('policy_tips') &&
           post_data.policy_tips.length > 0) {
            var tmp_policy = {
                policy_id    : "",
                room_type_id : post_data.room_type_id,
                age_range    : "",
                policy       : post_data.policy_tips
            }
            post_data.policies.push(tmp_policy);
        }

        $http.post($request_urls.hotelRoomType +
                   post_data.room_type_id, post_data).success(function(data) {
                if(data.code == 200) {
                    $scope.local.current_edit = -1;
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
            });
    };

    $scope.delImage = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $http.post($request_urls.deleteRoomImage, {
            image_id : $scope.data.room_list[$scope.local.current_edit].images[index].image_id
        }).success(function(data) {
                if(data.code == 200) {
                    $scope.data.room_list[$scope.local.current_edit].images.splice(index, 1);
                } else {
                    $rootScope.$emit('notify', {msg : data.msg});
                }
            });
    };

    $scope.toggleService = function(service_id, service_name) {
        var service_index = $scope.data.room_list[$scope.local.current_edit].selected_service.indexOf(service_id);
        if(service_index > -1) {
            $scope.data.room_list[$scope.local.current_edit].selected_service.splice(service_index, 1);
            for(var i in $scope.data.room_list[$scope.local.current_edit].services) {
                if($scope.data.room_list[$scope.local.current_edit].services[i].service_id == service_id) {
                    $scope.data.room_list[$scope.local.current_edit].services.splice(i, 1);
                    break;
                }
            }
        } else {
            var new_service = {
                product_id   : $scope.data.room_list[$scope.local.current_edit].product_id,
                room_type_id : $scope.data.room_list[$scope.local.current_edit].room_type_id,
                service_id   : service_id,
                service_info : "",
                name         : service_name
            };

            $scope.data.room_list[$scope.local.current_edit].services.push(new_service);
            $scope.data.room_list[$scope.local.current_edit].selected_service.push(service_id);
        }
    };

    $scope.addPolicy = function() {
        var tmp_policy = {
            policy_id    : "",
            room_type_id : $scope.data.room_list[$scope.local.current_edit].room_type_id,
            age_range    : "",
            policy       : "",
            age_1        : 0,
            age_2        : 0
        }
        $scope.data.room_list[$scope.local.current_edit].policies.push(tmp_policy);
    };

    $scope.delRoom = function(index, room_type_id) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $http.delete($request_urls.hotelRoomType + room_type_id).success(function(data) {
            if(data.code == 200) {
                alert(data.msg);
                $scope.data.room_list.splice(index, 1);
            }
        });
    };

    $scope.addRoom = function() {
        $http.post($request_urls.hotelRoomType, {}).success(function(data) {
            if(data.code == 200) {
                var new_room = angular.copy(data.data);
                new_room.services = [];
                new_room.images = [];
                new_room.policies = [];
                new_room.selected_service = [];
                new_room.policy_tips = "";
                if (!$scope.data.room_list) {
                    $scope.data.room_list = [];
                }
                $scope.data.room_list.push(new_room);
                $scope.local.current_edit = $scope.data.room_list.length - 1;
            } else {
                alert(data.msg);
            }
        });
    };

    $scope.backToList = function() {
        location.reload();
    };

    $scope.delPolicy = function(index) {
        if(!window.confirm("删除后不可恢复。\n点击‘确认’删除。"))
            return;

        $scope.data.room_list[$scope.local.current_edit].policies.splice(index, 1);
    };

    var image_filter = function(item) {
        var type = '|' + item.type.toLowerCase().slice(item.type.lastIndexOf('/') + 1) + '|';
        return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
    };

    $scope.uploader = new FileUploader({
        url   : $request_urls.updateRoomImage,
        scope : $scope
    });
    $scope.uploader.filters.push({
        name : 'imagesOnly',
        fn   : image_filter
    });
    $scope.uploader.onSuccessItem = function(item, response) {
        $scope.local.uploader.queue = [];
        if(response.code == 200) {
            var result = angular.copy(response.data);
            $scope.data.room_list[$scope.local.current_edit].images.push(result);
            alert("图片上传成功");
        } else {
            alert(response.msg);
        }
    };
    $scope.uploader.onBeforeUploadItem = function(item) {
        item.formData = [
            {
                room_type_id  : $scope.data.room_list[$scope.local.current_edit].room_type_id,
                image_id      : '',
                display_order : $scope.data.room_list[$scope.local.current_edit].images.length + 1
            }
        ];
    };
    $scope.uploader.onAfterAddingFile = function(item) {
        item.upload();
    };

    $scope.triggerUpload = function() {
        $('#img-upload').trigger('click');
    };

    $scope.dndOptions = {
        selector : '.carousel-image',
        offset   : 0
    };

    $scope.dndCallback = function(info, dstIndex) {
        $scope.data.room_list[$scope.local.current_edit].images.splice(info.srcIndex, 1); //Remove img item
        $scope.data.room_list[$scope.local.current_edit].images.splice(dstIndex, 0, info.srcItem); //Add img item
        $scope.updateImageOrder();
    };

    $scope.updateImageOrder = function() {
        var order_info = $scope.data.room_list[$scope.local.current_edit].images.map(function(elem, index) {
            return {
                display_order : index,
                image_id      : elem.image_id
            };
        });

        return $http.post($request_urls.updateRoomImageOrder, order_info);
    };

    $scope.init();
};

app.controller('HotelRoomCtrl', [
    '$scope', '$rootScope', '$route', '$http', '$q', 'FileUploader', controllers.HotelRoomCtrl
]);